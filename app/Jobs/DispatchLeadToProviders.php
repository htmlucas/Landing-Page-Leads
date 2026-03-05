<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\MailProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class DispatchLeadToProviders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function handle(): void
    {
        $providers = MailProvider::active()->get();

        foreach ($providers as $provider) {

            SyncLeadToProvider::dispatch(
                $this->lead,
                $provider
            )->onQueue('provider-'.$provider->name);
        }
    }
}
