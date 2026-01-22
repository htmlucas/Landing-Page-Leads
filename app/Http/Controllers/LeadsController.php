<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Enums\LeadOrigin;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendLeadEmail;
use Illuminate\Support\Facades\DB;

class LeadsController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar reCAPTCHA
        $recaptcha = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->recaptcha_token,
                'remoteip' => $request->ip(),
            ]
        )->json();

        // 2. Verificar score do Google
        if (!$recaptcha['success'] || $recaptcha['score'] < 0.5) {
            // opcional: logar tentativa
            Log::channel('spam')->info("reCAPTCHA bloqueado", [
                "ip" => $request->ip(),
                "score" => $recaptcha['score'],
                "payload" => $request->all()
            ]);

            return response()->json(['ok' => true], 200);
        }
        
        // Se o honeypot vier preenchido → SPAM
        if (!empty($request->hp)) {
            Log::channel('spam')->info('Spam detectado', [
                'ip' => $request->ip(),
                'payload' => $request->all()
            ]);

            return response()->json(['ok' => true], 200);
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:leads,email',
            'name' => 'nullable|string|min:2|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]{6,20}$/',
            'origin' => ['required', new Enum(LeadOrigin::class)],
            'consent' => 'accepted',
        ]);


        DB::transaction(function () use ($validated) {
            $lead = Lead::create($validated);

            SendLeadEmail::dispatch($lead)->afterCommit();
        });

        return redirect()->route('campaign')->with('greet', 'Thank you for subscribing!');
    }
}
