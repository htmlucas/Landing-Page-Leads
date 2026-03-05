<?php

namespace App\Models;

use App\Enums\LeadProviderSyncStatus;
use Illuminate\Database\Eloquent\Model;

class LeadProviderSync extends Model
{
    protected $fillable = [
        'lead_id',
        'provider',
        'provider_id',
        'status',
        'last_error',
        'attempts',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
        'status' => LeadProviderSyncStatus::class,
    ];
}
