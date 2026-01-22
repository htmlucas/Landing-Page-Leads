<?php

namespace App\Jobs;

use App\Mail\LeadWelcomeMail;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLeadEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public Lead $lead)
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->lead->email)
            ->send(new LeadWelcomeMail($this->lead));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha ao enviar e-mail do lead', [
            'lead_id' => $this->lead->id,
            'email' => $this->lead->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
