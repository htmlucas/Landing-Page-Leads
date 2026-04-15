<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class DataDeletionRequest extends Model
{
    protected $fillable = [        
        'email',
        'token',
        'expires_at',
        'used_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['email', 'token', 'expires_at', 'used_at']);
    }
}
