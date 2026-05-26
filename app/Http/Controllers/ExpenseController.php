<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Requête de base pour les dépenses non supprimées de l'utilisateur
        $query = $user->expenses()->with('category')->latest('spent_at')->latest('id');

        // Filtrage par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par plage de dates
        if ($request->filled('start_date')) {
            $query->whereDate('spent_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('spent_at', '<=', $request->end_date);
        }

        // Filtrage par montant min/max
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Recherche par mot-clé dans la note
        if ($request->filled('keyword')) {
            $query->where('note', 'like', '%' . $request->keyword . '%');
        }

        // Pagination
        $expenses = $query->paginate(10)->withQueryString();

        // Récupérer toutes les catégories pour le formulaire de filtre et de création
        $categories = Category::where('is_system', true)
            ->orWhere('user_id', $user->id)
            ->get();

        return view('expenses.index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => $request->all(),
        ]);
    }

    public function store(StoreExpenseRequest $request)
    {
        $request->user()->expenses()->create($request->validated());

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        // Vérifier que la dépense appartient à l'utilisateur
        if ($expense->user_id !== $request->user()->id) {
            abort(403, 'Action non autorisée.');
        }

        $expense->update($request->validated());

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        // Vérifier la propriété
        if ($expense->user_id !== $request->user()->id) {
            abort(403, 'Action non autorisée.');
        }

        $expense->delete(); // Soft delete

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense déplacée dans la corbeille.');
    }

    public function trash(Request $request)
    {
        $user = $request->user();
        $expenses = $user->expenses()->onlyTrashed()->with('category')->latest('deleted_at')->paginate(10);

        return view('expenses.trash', [
            'expenses' => $expenses,
        ]);
    }

    public function restore(Request $request, $id)
    {
        $user = $request->user();
        $expense = $user->expenses()->onlyTrashed()->findOrFail($id);

        $expense->restore();

        return redirect()->route('expenses.trash')
            ->with('success', 'Dépense restaurée avec succès.');
    }

    public function forceDelete(Request $request, $id)
    {
        $user = $request->user();
        $expense = $user->expenses()->onlyTrashed()->findOrFail($id);

        $expense->forceDelete();

        return redirect()->route('expenses.trash')
            ->with('success', 'Dépense supprimée définitivement.');
    }
}
