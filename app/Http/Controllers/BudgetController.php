<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Récupérer le mois sélectionné ou le mois en cours par défaut
        $monthInput = $request->input('month');
        $date = $monthInput ? Carbon::parse($monthInput . '-01') : Carbon::now();
        $monthDate = $date->copy()->startOfMonth()->format('Y-m-d');

        // Récupérer le budget global
        $globalBudget = $user->budgets()->globalForMonth($date->year, $date->month)->first();
        $globalSpent = $user->expenses()->forMonth($date->year, $date->month)->sum('amount');

        // Récupérer toutes les catégories
        $categories = Category::where('is_system', true)
            ->orWhere('user_id', $user->id)
            ->get();

        // Récupérer les budgets par catégorie existants pour ce mois
        $categoryBudgets = $user->budgets()
            ->whereNotNull('category_id')
            ->whereYear('month', $date->year)
            ->whereMonth('month', $date->month)
            ->get()
            ->keyBy('category_id');

        // Construire la liste des catégories avec leurs budgets et dépenses actuelles
        $categoriesStats = [];
        foreach ($categories as $category) {
            $budget = $categoryBudgets->get($category->id);
            $spent = $user->expenses()
                ->forMonth($date->year, $date->month)
                ->where('category_id', $category->id)
                ->sum('amount');

            $categoriesStats[] = [
                'category' => $category,
                'budget_amount' => $budget ? $budget->amount : 0,
                'spent' => $spent,
                'ratio' => $budget && $budget->amount > 0 ? min($spent / $budget->amount, 2.0) : 0,
                'percentage' => $budget && $budget->amount > 0 ? round(($spent / $budget->amount) * 100) : 0,
            ];
        }

        return view('budgets.index', [
            'globalBudget' => $globalBudget,
            'globalSpent' => $globalSpent,
            'globalRatio' => $globalBudget && $globalBudget->amount > 0 ? min($globalSpent / $globalBudget->amount, 2.0) : 0,
            'globalPercentage' => $globalBudget && $globalBudget->amount > 0 ? round(($globalSpent / $globalBudget->amount) * 100) : 0,
            'categoriesStats' => $categoriesStats,
            'categories' => $categories,
            'currentMonth' => $date->format('Y-m'),
            'currentMonthLabel' => $date->translatedFormat('F Y'),
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'], // Format YYYY-MM
            'amount' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ], [
            'month.required' => 'Le mois est obligatoire.',
            'month.regex' => 'Le mois doit être au format AAAA-MM.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.integer' => 'Le montant doit être un entier.',
            'amount.min' => 'Le montant ne peut pas être négatif.',
            'category_id.exists' => 'La catégorie n\'existe pas.',
        ]);

        $monthDate = Carbon::parse($validated['month'] . '-01')->format('Y-m-d');
        
        $request->user()->budgets()->updateOrCreate(
            [
                'month' => $monthDate,
                'category_id' => $validated['category_id'] ?? null,
            ],
            [
                'amount' => $validated['amount'],
            ]
        );

        return redirect()->route('budgets.index', ['month' => $validated['month']])
            ->with('success', 'Budget mis à jour avec succès.');
    }
}
