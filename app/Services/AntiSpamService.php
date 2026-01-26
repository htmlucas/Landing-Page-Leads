<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AntiSpamService
{
    public function check(Request $request): bool
    {
        // Honeypot
        if (!empty($request->hp)) {
            Log::channel('spam')->info('Spam detectado (honeypot)', [
                'ip' => $request->ip(),
            ]);

            return false;
        }

        // reCAPTCHA
        $recaptcha = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'  => env('RECAPTCHA_SECRET_KEY'),
                'response'=> $request->recaptcha_token,
                'remoteip'=> $request->ip(),
            ]
        )->json();

        if (empty($recaptcha['success']) || ($recaptcha['score'] ?? 0) < 0.5) {
            Log::channel('spam')->info('Spam detectado (recaptcha)', [
                'ip' => $request->ip(),
                'score' => $recaptcha['score'] ?? null,
            ]);

            return false;
        }

        return true;
    }

}