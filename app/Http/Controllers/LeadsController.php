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
use App\Enums\LeadOrigin;
use App\Http\Requests\LeadsUpdateRequest;
use App\Http\Requests\RequestDeletionRequest;
use App\Jobs\DispatchLeadToProviders;
use App\Jobs\NotifyLeadWebhookJob;
use App\Jobs\SendDeletionLeadEmail;
use App\Jobs\SyncLeadToProvider;
use App\Models\DataDeletionRequest;
use App\Services\TokenService;

class LeadsController extends Controller
{
    public $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

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

        // Filtro por deletados
        $query->when($request->trashed, function ($q) use ($request){
            if($request->trashed === 'with') {
                $q->withTrashed();
            } elseif($request->trashed === 'only') {
                $q->onlyTrashed();
            }
        });

        $pageSize = $request->get('page_size',10);

        $leads = $query
            ->orderByDesc('created_at')
            ->paginate($pageSize)
            ->withQueryString();

        return Inertia::render('Auth/Admin/Leads/Index', [
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
            DispatchLeadToProviders::dispatch($lead)->afterCommit();

            if (config('leads.webhook_url')) {
                NotifyLeadWebhookJob::dispatch($lead)->afterCommit();
            }

            return ['lead' => $lead, 'already_exists' => false];
        });

        return redirect()->route('campaign')->with('greet', 'Thank you for subscribing!');
    }

    public function edit(Lead $lead)
    {
        return Inertia::render('Auth/Admin/Leads/Edit', [
            'lead' => $lead,
            'availableOrigins' => LeadOrigin::options()
        ]);
    }

    public function update($lead_id, LeadsUpdateRequest $request)
    {
        
        DB::transaction(function () use ($request, $lead_id,) {

            $lead = Lead::findOrFail($lead_id);

            $lead->update($request->only('name', 'email', 'phone', 'origins'));

        });

        return redirect()->route('admin.leads')->with('message', 'Lead updated successfully.');
    }

    public function delete()
    {
        return inertia('DeleteLead');
    }

    public function requestDeletion(RequestDeletionRequest $request, AntiSpamService $antiSpamService)
    {

        if(!$antiSpamService->check($request)) {
            return redirect()->route('delete-lead')->withErrors(['spam' => 'Your request was detected as spam.']);
        }

        $tokenData = $this->tokenService->generateToken();

        $dataDeletionRequest = DataDeletionRequest::create([
            'email' => $request->email,
            'token' => $tokenData['hashed'],
            'expires_at' => now()->addHours(1),
        ]);

        SendDeletionLeadEmail::dispatch($dataDeletionRequest, $tokenData['plain'])->afterCommit();

        return redirect()->route('delete-lead')->with('message', 'If this email exists, you will receive instructions.');
    }

    public function confirmDeletion($token)
    {
        $hashedToken = hash('sha256', $token);

        $deletionRequest = DataDeletionRequest::where('token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$deletionRequest) {
            return redirect('/deletion-error?reason=invalid');
        }

        if ($deletionRequest->used_at) {
        return redirect('/deletion-error?reason=used');
            }

        if ($deletionRequest->expires_at < now()) {
            return redirect('/deletion-error?reason=expired');
        }

        $lead = Lead::where('email', $deletionRequest->email)->first();

        if ($lead) {
            $this->tokenService->anonymizeLead($lead);
        }

        $deletionRequest->update([
            'used_at' => now(),
        ]);

        activity()->withProperties(['email' => $deletionRequest->email,'action' => 'data_deletion',])->log('Lead data anonymized');

        return inertia('Deletion/Success');
    }

    public function deletionErrorPage(Request $request)
    {
         return Inertia::render('Deletion/Error', [
            'reason' => $request->query('reason')
        ]);
    }

    public function destroy($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);
        $lead->delete();

        return redirect()->route('admin.leads')->with('message', 'Lead deleted successfully.');
    }

    public function restore($lead_id)
    {
        $lead = Lead::withTrashed()->findOrFail($lead_id);
        $lead->restore();

        return redirect()->route('admin.leads')->with('message', 'Lead restored successfully.');
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
