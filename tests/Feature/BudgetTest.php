<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\User;
use App\Services\BudgetAlertService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
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
        Category::create([
            'name' => 'Alimentation',
            'color' => '#E57373',
            'icon' => 'restaurant',
            'is_system' => true,
        ]);
    }

    public function test_user_can_set_global_budget()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->post('/budgets', [
            'amount' => 100000,
            'month' => now()->format('Y-m'),
        ]);

        $response->assertRedirect('/budgets?month=' . now()->format('Y-m'));
        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'amount' => 100000,
            'category_id' => null,
        ]);
    }

    public function test_budget_alert_service_detects_warning_and_exceeded_limits()
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::where('name', 'Alimentation')->first();

        // 1. Set global budget to 10000
        Budget::create([
            'user_id' => $user->id,
            'amount' => 10000,
            'category_id' => null,
            'month' => now()->startOfMonth()->format('Y-m-d'),
        ]);

        // 2. Add expense of 8500 (85% of budget)
        Expense::create([
            'user_id' => $user->id,
            'amount' => 8500,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
        ]);

        $alertService = new BudgetAlertService();
        $alerts = $alertService->checkAlerts($user);

        $this->assertCount(1, $alerts);
        $this->assertEquals('warning', $alerts[0]['type']);

        // 3. Add another expense of 2000 (total 10500, > 10000)
        Expense::create([
            'user_id' => $user->id,
            'amount' => 2000,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
        ]);

        $alerts = $alertService->checkAlerts($user);
        $this->assertCount(1, $alerts);
        $this->assertEquals('exceeded', $alerts[0]['type']);
    }
}
