<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LeadOrigin;

class Lead extends Model
{
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
}
