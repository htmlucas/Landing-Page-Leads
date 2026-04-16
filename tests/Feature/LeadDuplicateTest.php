<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\AntiSpamService;

class LeadDuplicateTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function teste_it_does_not_create_duplicate_leads()
    {
        Lead::factory()->create([
            'email' => 'teste@example.com',
        ]);

        $this->mock(AntiSpamService::class, function ($mock) {
            $mock->shouldReceive('check')
            ->once()
            ->andReturn(true);
        });

        $response = $this->post('/subscribe',[
            'email'=> 'teste@example.com',
            'name' => 'Teste',
            'phone' => '1234567890',
            'consent' => true,
            'origin' => 'landing',
            'hp' => '',
            'recaptcha_token' => 'fake-token',
        ]);

        $this->assertDatabaseCount('leads', 1);
    }
}
