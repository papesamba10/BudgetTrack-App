<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Category::create([
            'name' => 'Autre',
            'color' => '#546E7A',
            'icon' => 'category',
            'is_system' => true,
        ]);
    }

    public function test_standard_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_user_expenses()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'user']);
        
        $category = Category::where('name', 'Autre')->first();
        Expense::create([
            'user_id' => $targetUser->id,
            'amount' => 1500,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
            'note' => 'Target user expense',
        ]);

        $response = $this->actingAs($admin)->get("/admin/utilisateurs/{$targetUser->id}");

        $response->assertStatus(200);
        $response->assertSee('Target user expense');
    }
}
