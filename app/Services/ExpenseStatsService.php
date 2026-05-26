<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseStatsService
{
    /**
     * Obtenir toutes les statistiques pour le tableau de bord.
     *
     * @param \App\Models\User $user
     * @param Carbon|null $date
     * @return array
     */
    public function getDashboardStats($user, Carbon $date = null): array
    {
        $date = $date ?? Carbon::now();
        $year = $date->year;
        $month = $date->month;

        // 1. Total dépensé ce mois-ci
        $total = $user->expenses()->forMonth($year, $month)->sum('amount');

        // 2. Dépenses par catégorie (Pie Chart)
        $expensesByCategory = $user->expenses()
            ->forMonth($year, $month)
            ->with('category')
            ->get()
            ->groupBy(function ($expense) {
                return $expense->category_id ?? 'other';
            });

        $categoryLabels = [];
        $categoryAmounts = [];
        $categoryColors = [];

        // Récupérer la catégorie "Autre" par défaut pour les dépenses orphelines
        $defaultOther = Category::where('is_system', true)->where('name', 'Autre')->first();
        $otherName = $defaultOther ? $defaultOther->name : 'Autre';
        $otherColor = $defaultOther ? $defaultOther->color : '#546E7A';

        $tempStats = [];

        foreach ($expensesByCategory as $catId => $expenses) {
            $sum = $expenses->sum('amount');
            if ($catId === 'other') {
                $name = $otherName;
                $color = $otherColor;
            } else {
                $category = $expenses->first()->category;
                $name = $category ? $category->name : $otherName;
                $color = $category ? $category->color : $otherColor;
            }

            if (isset($tempStats[$name])) {
                $tempStats[$name]['amount'] += $sum;
            } else {
                $tempStats[$name] = [
                    'amount' => $sum,
                    'color' => $color
                ];
            }
        }

        foreach ($tempStats as $name => $data) {
            $categoryLabels[] = $name;
            $categoryAmounts[] = $data['amount'];
            $categoryColors[] = $data['color'];
        }

        // 3. Dépenses quotidiennes (Bar Chart)
        $daysInMonth = $date->daysInMonth;
        $dailyLabels = [];
        $dailyAmounts = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dailyLabels[] = $d;
            $dailyAmounts[$d] = 0;
        }

        $expensesByDay = $user->expenses()
            ->forMonth($year, $month)
            ->select(DB::raw("strftime('%d', spent_at) as day"), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('day')
            ->get();

        foreach ($expensesByDay as $expenseDay) {
            $dayNum = (int)$expenseDay->day;
            if (isset($dailyAmounts[$dayNum])) {
                $dailyAmounts[$dayNum] = (int)$expenseDay->total_amount;
            }
        }

        // Réordonner les montants quotidiens pour correspondre aux labels
        $orderedDailyAmounts = array_values($dailyAmounts);

        // 4. Top 5 dernières dépenses
        $recentExpenses = $user->expenses()
            ->with('category')
            ->latest('spent_at')
            ->latest('id')
            ->take(5)
            ->get();

        // 5. Nombre total de catégories actives de l'utilisateur
        $categoriesCount = Category::where('is_system', true)
            ->orWhere('user_id', $user->id)
            ->count();

        return [
            'total' => (int)$total,
            'category_labels' => $categoryLabels,
            'category_amounts' => $categoryAmounts,
            'category_colors' => $categoryColors,
            'daily_labels' => $dailyLabels,
            'daily_amounts' => $orderedDailyAmounts,
            'recent_expenses' => $recentExpenses,
            'categories_count' => $categoriesCount,
        ];
    }
}
