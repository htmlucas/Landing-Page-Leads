<?php

namespace App\Jobs;

use App\Enums\LeadProviderSyncStatus;
use App\Models\Lead;
use App\Models\LeadProviderSync;
use App\Models\MailProvider;
use App\Services\MailProviders\MailProviderFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class SyncLeadToProvider implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function __construct(
        public Lead $lead, 
        public MailProvider $provider
    ){}

    public function handle(): void
    {
        #   Circuit Breaker
        #   Se o provider estiver marcado off, não tenta sincronizar

        $breakerKey = "provider-down-{$this->provider->id}";

        if (cache()->has($breakerKey)) {
            Log::info('Lead synchronization failed - Provider is offline', [
                'lead_id' => $this->lead->id,
                'provider' => $this->provider->name
            ]);
            return;
        }


        #   Rate limit por provider
        $rateKey = "provider-rate-{$this->provider->id}";

        if (RateLimiter::tooManyAttempts($rateKey, 10)) {

            $seconds = RateLimiter::availableIn($rateKey);

            Log::warning('Provider rate limit reached', [
                'provider' => $this->provider->name,
                'wait_seconds' => $seconds
            ]);

            $this->release($seconds);

            return;
        }

        RateLimiter::hit($rateKey, 60);
        
        #   Cria ou recupera o registro de sync
        $sync = LeadProviderSync::firstOrNew([
            'lead_id' => $this->lead->id,
            'provider_id' => $this->provider->id,
        ]);

        #   Incrementa tentativas

        $attempts = ($sync->attempts ?? 0) + 1;

        Log::info('Lead sync attempt', [
            'lead_id' => $this->lead->id,
            'provider' => $this->provider->name,
            'attempt' => $attempts
        ]);

        #   Marca como processing
        $sync->fill([
            'provider' => $this->provider->name ?? 'unknown',
            'status' => LeadProviderSyncStatus::PROCESSING,
            'attempts' => $attempts,
        ]);

        $sync->save();

        try {

            $start = microtime(true);

            $service = MailProviderFactory::make($this->provider);

            $service->addLead($this->lead);

            $duration = microtime(true) - $start;

            Log::info('Lead sent to provider', [
                'lead_id' => $this->lead->id,
                'provider' => $this->provider->name,
                'duration_ms' => $duration * 1000
            ]);
            
            # sucesso
            $sync->update([
                'status' => LeadProviderSyncStatus::SYNCED,
                'synced_at' => now(),
                'last_error' => null,
            ]);

        } catch (\Throwable $e) {

            Log::error('Lead synchronization failed', [
                'lead_id' => $this->lead->id,
                'provider' => $this->provider->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

        #   Circuit breaker, se falhou marca provider como offline
            cache()->put(
                $breakerKey,
                true,
                now()->addMinutes(5)
            );

            Log::warning('Circuit breaker opened for provider', [
                'provider' => $this->provider->name,
                'duration_minutes' => 5
            ]);

        #   Falha no envio, marca o sync como failed e salva o erro
            $sync->update([
                'status' => LeadProviderSyncStatus::FAILED,
                'last_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
