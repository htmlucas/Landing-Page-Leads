<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Enums\LeadOrigin;
use App\Http\Requests\LeadsRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendLeadEmail;
use App\Services\AntiSpamService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeadsController extends Controller
{

    public function index(Request $request)
    {
        $query = Lead::query();

        // Filtro por Data
        $query->when($request->date_from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->date_from);
        });

        $query->when($request->date_to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->date_to);
        });

        // Filtro por Origem
        $query->when($request->origin, function ($q) use ($request) {
            $q->whereJsonContains('origins', $request->origin);
        });

        // Filtro por Email
        $query->when($request->email, function ($q) use ($request) {
            $q->where('email', 'like', '%' . $request->email . '%');
        });

        $pageSize = $request->get('page_size',10);

        $leads = $query
            ->orderByDesc('created_at')
            ->paginate($pageSize)
            ->withQueryString();

        return Inertia::render('Auth/Admin/Lead', [
            'leads' => $leads,
            'filters' => $request->only([
                'date_from', 
                'date_to', 
                'origin', 
                'email'
            ]),
        ]);
    }

    public function store(LeadsRequest $request, AntiSpamService $antiSpamService)
    {

        $allowDuplicates = config('leads.allow_duplicates'); // configuração do administrador que sera implementado futuramente.

        if(!$antiSpamService->check($request)) {
            return redirect()->route('campaign')->withErrors(['spam' => 'Your submission was detected as spam.']);
        }

        $result = DB::transaction(function () use ($request, $allowDuplicates,) {
            //$request['origin'] = 'LANDING'; // Definido temporariamente, futuramente sera pego do request.
            $lead = Lead::where('email', $request['email'])->first();

            if ($lead && !$allowDuplicates) {
                // Já existe → update

                $origins = $lead->origins ?? [];

                if (!in_array($request['origin'], $origins)) {
                    $origins[] = $request['origin'];
                }

                $lead->update([
                    'name' => $request['name'] ?? $lead->name,
                    'phone' => $request['phone'] ?? $lead->phone,
                    'origins' => $origins,
                ]);

                return ['lead' => $lead, 'already_exists' => true];
            }

            $lead = Lead::create([
                ...$request->validated(),
                'origins' => [$request['origin']]
            ]);

            SendLeadEmail::dispatch($lead)->afterCommit();

            return ['lead' => $lead, 'already_exists' => false];
        });

        return redirect()->route('campaign')->with('greet', 'Thank you for subscribing!');
    }
}
