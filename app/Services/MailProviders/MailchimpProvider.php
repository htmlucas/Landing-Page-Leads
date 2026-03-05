<?php

namespace App\Services\MailProviders;

use App\Models\Lead;
use App\Services\MailProviders\Contracts\MailProviderInterface;
use Illuminate\Support\Facades\Http;

class MailchimpProvider implements MailProviderInterface
{

    protected $apiKey;
    protected $listId;

    public function __construct($apiKey, $listId)
    {
        $this->apiKey = $apiKey;
        $this->listId = $listId;
    }

    public function addLead(Lead $lead): void
    {
        $dc = substr($this->apiKey,strpos($this->apiKey,'-')+1);

        Http::withBasicAuth('anystring', $this->apiKey)
            ->post("https://{$dc}.api.mailchimp.com/3.0/lists/{$this->listId}/members",[
                'email_address' => $lead->email,
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $lead->name,
                ],
            ]);
    }
}