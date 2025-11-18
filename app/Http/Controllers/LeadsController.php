<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Enums\LeadOrigin;
use Illuminate\Validation\Rules\Enum;

class LeadsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:leads,email',
            'origin' => ['required', new Enum(LeadOrigin::class)],
            'consent' => 'nullable|boolean',
        ]);

        // Here you would typically save the lead to the database.
        Lead::create($validated);

        return redirect()->route('campaign')->with('greet', 'Thank you for subscribing!');
    }
}
