<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index()
    {
        $activity = Activity::all();

        return Inertia::render('Auth/Admin/Audit', [
            'audits' => $activity
        ]);
    }
}
