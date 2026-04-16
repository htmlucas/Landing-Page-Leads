<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\AntiSpamService;

class LeadStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_store_creates_a_lead()
    {
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

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('leads', [
            'email' => 'teste@example.com',
        ]);
    }
}
