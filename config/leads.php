<?php

return [
    'allow_duplicates' => false,
    'export_sync_limit' => 1000,
    'webhook_url' => env('LEADS_WEBHOOK_URL'),
    'webhook_secret' => env('LEADS_WEBHOOK_SECRET'),
];
