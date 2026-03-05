<?php

namespace App\Services\MailProviders;

use App\Models\Lead;
use App\Services\MailProviders\Contracts\MailProviderInterface;

class ActiveCampaignProvider implements MailProviderInterface
{
    public function addLead(Lead $lead): void
    {
        // Implementação para adicionar o lead ao ActiveCampaign
    }
}