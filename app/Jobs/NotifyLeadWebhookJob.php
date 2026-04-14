<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyLeadWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public Lead $lead)
    {
    }

    public function handle(): void
    {
        $url = config('leads.webhook_url');

        if (!$url) {
            Log::warning('Webhook URL not configured, skipping lead notification', [
                'lead_id' => $this->lead->id,
            ]);

            return;
        }

        $payload = $this->lead->toArray();
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $secret = config('leads.webhook_secret', '');
        $signature = hash_hmac('sha256', $body, $secret);

        $response = Http::withHeaders([
            'X-Signature' => $signature,
        ])->withBody($body, 'application/json')->post($url);

        if (!$response->successful()) {
            throw new \RuntimeException(
                sprintf('Webhook POST failed with status %s: %s', $response->status(), $response->body())
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Falha ao notificar webhook de novo lead', [
            'lead_id' => $this->lead->id,
            'webhook_url' => config('leads.webhook_url'),
            'error' => $exception->getMessage(),
        ]);
    }
}
