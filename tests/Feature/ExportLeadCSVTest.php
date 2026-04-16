<?php

namespace Tests\Feature;

use App\Jobs\ExportLeadsJob;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ExportLeadCSVTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_can_export_csv()
    {
        $role = Role::factory()->create(['name' => 'admin']);

        $user = User::factory()->create([
            'role_id' => $role->id
        ]);
        
        $user->load('role');

        $this->actingAs($user);

        $leads = Lead::factory()->count(2)->create();

        $response = $this->post('admin/leads/export',[
            'ids' => $leads->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200);

        $response->assertHeader('content-disposition');
    }

    public function test_export_largest_dataset_dispatches_job()
    {
        Queue::fake();

        //config('leads.export_sync_limit', 2);

        $role = Role::factory()->create(['name' => 'admin']);

        $user = User::factory()->create([
            'role_id' => $role->id
        ]);
        
        $user->load('role');

        $this->actingAs($user);

        $leads = Lead::factory()->count(3)->create();

        $response = $this->post('admin/leads/export',[
            'ids' => $leads->pluck('id')->toArray(),
        ]);

        $response->assertStatus(302);

        Queue::assertPushed(ExportLeadsJob::class);

    }

    public function text_export_fails_with_no_leads()
    {
        $role = Role::factory()->create(['name' => 'admin']);

        $user = User::factory()->create([
            'role_id' => $role->id
        ]);
        
        $user->load('role');

        $this->actingAs($user);

        $response = $this->post('admin/leads/export',[
            'ids' => [],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('export_error');
    }
}
