<?php

namespace App\Http\Controllers;

use App\Services\ExpenseStatsService;
use App\Services\BudgetAlertService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ExpenseStatsService $statsService;
    protected BudgetAlertService $alertService;

    public function __construct(ExpenseStatsService $statsService, BudgetAlertService $alertService)
    {
        $this->statsService = $statsService;
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Permettre de filtrer sur un mois spécifique via la requête (par défaut le mois en cours)
        $monthInput = $request->input('month');
        $date = $monthInput ? Carbon::parse($monthInput . '-01') : Carbon::now();

        $stats = $this->statsService->getDashboardStats($user, $date);
        $alerts = $this->alertService->checkAlerts($user, $date);

        // Récupérer le budget global du mois pour la barre de progression
        $globalBudget = $user->budgets()->globalForMonth($date->year, $date->month)->first();
        $globalBudgetAmount = $globalBudget ? $globalBudget->amount : 0;
        $ratio = $globalBudgetAmount > 0 ? min($stats['total'] / $globalBudgetAmount, 2.0) : 0; // plafonner le ratio visuellement pour la progress bar

        return view('dashboard', [
            'stats' => $stats,
            'alerts' => $alerts,
            'globalBudgetAmount' => $globalBudgetAmount,
            'ratio' => $ratio,
            'currentMonth' => $date->format('Y-m'),
            'currentMonthLabel' => $date->translatedFormat('F Y'),
        ]);
    }
}
