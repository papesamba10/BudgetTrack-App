<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create standard categories
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

    public function test_user_can_view_expense_index()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get('/depenses');

        $response->assertStatus(200);
    }

    public function test_user_can_create_expense()
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::where('name', 'Alimentation')->first();

        $response = $this->actingAs($user)->post('/depenses', [
            'amount' => 5000,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
            'note' => 'Restaurant test',
        ]);

        $response->assertRedirect('/depenses');
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 5000,
            'category_id' => $category->id,
            'note' => 'Restaurant test',
        ]);
    }

    public function test_user_can_soft_delete_expense()
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::where('name', 'Alimentation')->first();
        $expense = Expense::create([
            'user_id' => $user->id,
            'amount' => 3000,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
            'note' => 'Test soft delete',
        ]);

        $response = $this->actingAs($user)->delete("/depenses/{$expense->id}");

        $response->assertRedirect('/depenses');
        $this->assertSoftDeleted($expense);
    }

    public function test_user_can_restore_deleted_expense()
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::where('name', 'Alimentation')->first();
        $expense = Expense::create([
            'user_id' => $user->id,
            'amount' => 3000,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
            'note' => 'Test restore',
        ]);
        $expense->delete();

        $response = $this->actingAs($user)->post("/depenses/{$expense->id}/restaurer");

        $response->assertRedirect('/depenses/corbeille');
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_can_permanently_delete_expense()
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::where('name', 'Alimentation')->first();
        $expense = Expense::create([
            'user_id' => $user->id,
            'amount' => 3000,
            'category_id' => $category->id,
            'spent_at' => now()->format('Y-m-d'),
            'note' => 'Test force delete',
        ]);
        $expense->delete();

        $response = $this->actingAs($user)->delete("/depenses/{$expense->id}/supprimer");

        $response->assertRedirect('/depenses/corbeille');
        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }
}
