<?php

namespace Database\Seeders;

use App\Models\MailProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

class MailChimpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MailProvider::create([
            'name' => 'mailchimp',
            'api_key' => env('MAILCHIMP_API_KEY'),
            'server_prefix' => env('MAILCHIMP_SERVER_PREFIX'),
            'list_id' => env('MAILCHIMP_LIST_ID'),
        ]);
    }
}
