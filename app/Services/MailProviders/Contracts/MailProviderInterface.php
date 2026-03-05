<?php

namespace App\Services\MailProviders\Contracts;

use App\Models\Lead;

interface MailProviderInterface
{
    public function addLead(Lead $lead): void;
}