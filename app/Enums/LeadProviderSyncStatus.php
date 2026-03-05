<?php

namespace App\Enums;

enum LeadProviderSyncStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SYNCED = 'synced';
    case FAILED = 'failed';
}