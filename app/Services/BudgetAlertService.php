<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;

class BudgetAlertService
{
    /**
     * Vérifier les budgets de l'utilisateur pour un mois donné et retourner les alertes.
     *
     * @param \App\Models\User $user
     * @param Carbon|null $date
     * @return array
     */
    public function checkAlerts($user, Carbon $date = null): array
    {
        $date = $date ?? Carbon::now();
        $year = $date->year;
        $month = $date->month;

        $alerts = [];

        // 1. Vérification du budget global
        $globalBudget = $user->budgets()->globalForMonth($year, $month)->first();
        if ($globalBudget && $globalBudget->amount > 0) {
            $totalSpent = $user->expenses()->forMonth($year, $month)->sum('amount');
            $ratio = $totalSpent / $globalBudget->amount;

            if ($ratio >= 1.0) {
                $alerts[] = [
                    'type' => 'exceeded',
                    'scope' => 'global',
                    'category_id' => null,
                    'category_name' => 'Global',
                    'percentage' => round($ratio * 100),
                    'spent' => $totalSpent,
                    'limit' => $globalBudget->amount,
                    'message' => "Attention ! Vous avez dépassé votre budget global mensuel de " . number_format($globalBudget->amount, 0, ',', ' ') . " XOF (Dépensé : " . number_format($totalSpent, 0, ',', ' ') . " XOF).",
                ];
            } elseif ($ratio >= 0.8) {
                $alerts[] = [
                    'type' => 'warning',
                    'scope' => 'global',
                    'category_id' => null,
                    'category_name' => 'Global',
                    'percentage' => round($ratio * 100),
                    'spent' => $totalSpent,
                    'limit' => $globalBudget->amount,
                    'message' => "Attention ! Vous avez utilisé " . round($ratio * 100) . "% de votre budget global mensuel (Dépensé : " . number_format($totalSpent, 0, ',', ' ') . " XOF sur " . number_format($globalBudget->amount, 0, ',', ' ') . " XOF).",
                ];
            }
        }

        // 2. Vérification des budgets par catégorie
        $categoryBudgets = $user->budgets()
            ->whereNotNull('category_id')
            ->whereYear('month', $year)
            ->whereMonth('month', $month)
            ->with('category')
            ->get();

        foreach ($categoryBudgets as $budget) {
            $category = $budget->category;
            if (!$category) {
                continue;
            }

            $spent = $user->expenses()
                ->forMonth($year, $month)
                ->where('category_id', $budget->category_id)
                ->sum('amount');

            if ($budget->amount > 0) {
                $ratio = $spent / $budget->amount;

                if ($ratio >= 1.0) {
                    $alerts[] = [
                        'type' => 'exceeded',
                        'scope' => 'category',
                        'category_id' => $budget->category_id,
                        'category_name' => $category->name,
                        'percentage' => round($ratio * 100),
                        'spent' => $spent,
                        'limit' => $budget->amount,
                        'message' => "Budget dépassé pour la catégorie '{$category->name}' ! Vous avez dépensé " . number_format($spent, 0, ',', ' ') . " XOF sur une limite de " . number_format($budget->amount, 0, ',', ' ') . " XOF.",
                    ];
                } elseif ($ratio >= 0.8) {
                    $alerts[] = [
                        'type' => 'warning',
                        'scope' => 'category',
                        'category_id' => $budget->category_id,
                        'category_name' => $category->name,
                        'percentage' => round($ratio * 100),
                        'spent' => $spent,
                        'limit' => $budget->amount,
                        'message' => "Seuil d'alerte atteint pour '{$category->name}' : " . round($ratio * 100) . "% dépensé (" . number_format($spent, 0, ',', ' ') . " XOF sur " . number_format($budget->amount, 0, ',', ' ') . " XOF).",
                    ];
                }
            }
        }

        return $alerts;
    }
}
