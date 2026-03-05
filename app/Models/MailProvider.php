<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailProvider extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'list_id',
        'is_active'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
