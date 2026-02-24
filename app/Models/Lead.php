<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LeadOrigin;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Lead extends Model
{
    use LogsActivity;

    protected $fillable = [        
        'email',
        'name',
        'phone',
        'consent',
        'origins'
    ];

    protected $casts = [
        'origins' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'phone', 'consent', 'origins']);
    }
}
