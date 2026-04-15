<?php

namespace App\Jobs;

use App\Mail\LeadDeletionRequestMail;
use App\Models\DataDeletionRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDeletionLeadEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue ,Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;


    public function __construct(public DataDeletionRequest $deletionRequest, public string $token)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->deletionRequest->email)
            ->send(new LeadDeletionRequestMail($this->deletionRequest, $this->token));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha ao enviar e-mail de solicitação de exclusão', [
            'email' => $this->deletionRequest->email,
            'token' => $this->token,
            'error' => $exception->getMessage(),
        ]);
    }
}
