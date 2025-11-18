<?php

namespace App\Http\Controllers;

use App\Enums\LeadOrigin;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        return inertia('Campaign', [
            'lead_origin' => LeadOrigin::LANDING->value
        ]);
    }
}
