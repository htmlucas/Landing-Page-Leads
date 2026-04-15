<?php



namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Str;

class TokenService
{
    public function generateToken(): array
    {
        $plainToken = Str::random(64); // token que vai pro usuário

        $hashedToken = hash('sha256', $plainToken);

        return [
            'plain' => $plainToken,
            'hashed' => $hashedToken,
        ];
    }

    public function anonymizeLead(Lead $lead): void
    {
        $newEmail = substr($lead->email, 0, 4) . '...' . substr($lead->email, -4);
        
        $lead->update([
            'name' => null,
            'email' => $newEmail,
            'phone' => null,
        ]);

        $lead->delete();

    }
}