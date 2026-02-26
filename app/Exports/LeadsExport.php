<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class LeadsExport implements FromCollection
{
   protected $leads;

   public function __construct(Collection $leads)
   {
       $this->leads = $leads;
   }

    public function collection()
    {
        return $this->leads->map(function ($lead) {
            return [
                'ID' => $lead->id,
                'Name' => $lead->name,
                'Email' => $lead->email,
                'Origins' => implode(', ', $lead->origins),
                'Created At' => $lead->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
