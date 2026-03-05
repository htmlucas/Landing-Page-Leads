<?php

namespace App\Services\MailProviders;

use App\Models\MailProvider;

class MailProviderFactory 
{
    public static function make(MailProvider $provider)
    {
        return match ($provider->name) {

            'mailchimp' =>
                new MailchimpProvider(
                    $provider->api_key,
                    $provider->list_id
                ),

            default =>
                throw new \Exception("Provider not supported"),
        };
    }
}