<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Page d'accueil - redirection vers dashboard si connecté
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dépenses
    Route::get('/depenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/depenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/depenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/depenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Corbeille
    Route::get('/depenses/corbeille', [ExpenseController::class, 'trash'])->name('expenses.trash');
    Route::post('/depenses/{id}/restaurer', [ExpenseController::class, 'restore'])->name('expenses.restore');
    Route::delete('/depenses/{id}/supprimer', [ExpenseController::class, 'forceDelete'])->name('expenses.force-delete');

    // Catégories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'storeOrUpdate'])->name('budgets.store');

    // Export
    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
});

// Routes admin (auth + admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/utilisateurs/{user}', [AdminController::class, 'showUser'])->name('user.show');
});

require __DIR__.'/auth.php';
