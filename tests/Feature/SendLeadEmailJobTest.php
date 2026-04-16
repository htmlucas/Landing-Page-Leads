<?php

namespace Tests\Feature;

use App\Jobs\DispatchLeadToProviders;
use App\Mail\LeadWelcomeMail;
use App\Services\AntiSpamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendLeadEmailJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_email_is_sent_when_lead_is_created()
    {
        Mail::fake();

        $this->mock(AntiSpamService::class, function ($mock) {
            $mock->shouldReceive('check')
            ->once()
            ->andReturn(true);
        });

        $this->post('/subscribe',[
            'email'=> 'teste@example.com',
            'name' => 'Teste',
            'phone' => '1234567890',
            'consent' => true,
            'origin' => 'landing',
            'hp' => '',
            'recaptcha_token' => 'fake-token',
        ]);

        Mail::assertSent(LeadWelcomeMail::class);
    }

    public function test_email_is_sent_when_lead_is_created_using_queue()
    {
        Queue::fake();

        $this->mock(AntiSpamService::class, function ($mock) {
            $mock->shouldReceive('check')
            ->once()
            ->andReturn(true);
        });

        $this->post('/subscribe',[
            'email'=> 'teste@example.com',
            'name' => 'Teste',
            'phone' => '1234567890',
            'consent' => true,
            'origin' => 'landing',
            'hp' => '',
            'recaptcha_token' => 'fake-token',
        ]);

        Queue::assertPushed(DispatchLeadToProviders::class);
    }

}
