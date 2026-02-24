<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Admin/Dashboard', [
            'total_leads' => Lead::count(),
            'last_7_days_leads' => Lead::where('created_at', '>=', now()->subDays(7))->count(),
            'origins_leads' => $this->getLeadsByOrigin(),
        ]);
    }

    private function getLeadsByOrigin()
    {
        $leads = Lead::select('origins')->get();

        $counter = [];

        foreach ($leads as $lead) {
            foreach ($lead->origins ?? [] as $origin) {

                $origin = strtoupper($origin);

                if (!isset($counter[$origin])) {
                    $counter[$origin] = 0;
                }

                $counter[$origin]++;
            }
        }

        arsort($counter);

        return $counter;
    }

}
