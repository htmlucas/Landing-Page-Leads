<?php

namespace App\Jobs;

use App\Exports\LeadsExport;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadsExportNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class ExportLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ids;
    protected $userId ;

    public function __construct($ids, $userId )
    {
        $this->ids = $ids;
        $this->userId  = $userId ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $leads = Lead::whereIn('id', $this->ids)->get();
        $user = User::find($this->userId);

        $fileName = 'exports/leads_' . now()->timestamp . '.xlsx';

        Excel::store(
            new LeadsExport($leads), 
            $fileName, 
            'public'
        );

        $url = URL::temporarySignedRoute(
            'admin.leads.download',
            now()->addHours(24),
            ['path' => $fileName]
        );

        $user->notify(new LeadsExportNotification($url));
    }
}
