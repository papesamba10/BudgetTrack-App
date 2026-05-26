<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Séparer les catégories système et personnalisées pour l'affichage
        $systemCategories = Category::where('is_system', true)->get();
        $customCategories = Category::where('user_id', $user->id)->get();

        return view('categories.index', [
            'systemCategories' => $systemCategories,
            'customCategories' => $customCategories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'], // Format #RRGGBB
            'icon' => ['required', 'string', 'max:50'],
        ], [
            'name.required' => 'Le nom de la catégorie est requis.',
            'color.required' => 'La couleur est requise.',
            'color.regex' => 'Le format de la couleur doit être HEX (ex: #FF5733).',
            'icon.required' => 'L\'icône est requise.',
        ]);

        $request->user()->categories()->create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'icon' => $validated['icon'],
            'is_system' => false,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie personnalisée créée avec succès.');
    }

    public function update(Request $request, Category $category)
    {
        // Interdire de modifier les catégories système
        if ($category->is_system || $category->user_id !== $request->user()->id) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['required', 'string', 'max:50'],
        ], [
            'name.required' => 'Le nom de la catégorie est requis.',
            'color.required' => 'La couleur est requise.',
            'color.regex' => 'Le format de la couleur doit être HEX (ex: #FF5733).',
            'icon.required' => 'L\'icône est requise.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie personnalisée mise à jour.');
    }

    public function destroy(Request $request, Category $category)
    {
        // Interdire de supprimer les catégories système
        if ($category->is_system || $category->user_id !== $request->user()->id) {
            abort(403, 'Action non autorisée.');
        }

        // Trouver la catégorie système par défaut "Autre" pour y réassigner les dépenses
        $otherCategory = Category::where('is_system', true)->where('name', 'Autre')->first();
        $otherId = $otherCategory ? $otherCategory->id : null;

        // Mettre à jour les dépenses associées (inclut les soft-deleted)
        $category->expenses()->withTrashed()->update(['category_id' => $otherId]);

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée. Ses dépenses ont été réassignées à "Autre".');
    }
}
