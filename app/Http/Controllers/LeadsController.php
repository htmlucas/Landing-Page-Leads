<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Exports\LeadsExport;
use App\Http\Requests\LeadsRequest;
use App\Jobs\ExportLeadsJob;
use App\Jobs\SendLeadEmail;
use App\Services\AntiSpamService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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

    public function export(Request $request)
    {
        $limitForSync = config('leads.export_sync_limit', 1000);

        $leadsSelected = Lead::whereIn('id', $request->ids)->get();

        if($leadsSelected->isEmpty()) {
            return redirect()->route('admin.leads')->withErrors(['export_error' => 'No leads selected for export.']);
        }

        if($leadsSelected->count() > $limitForSync) {
            dispatch(new ExportLeadsJob($request->input('ids'), auth()->id()))->afterCommit();

            return redirect()->route('admin.leads')->with('message', 'Your export is being processed. You will receive an email with the download link once it\'s ready.');
        }

        
        return Excel::download(new LeadsExport($leadsSelected), 'leads.xlsx');
    }
}
