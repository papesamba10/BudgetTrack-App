<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    private function getFilteredExpenses(Request $request)
    {
        $user = $request->user();
        $query = $user->expenses()->with('category')->latest('spent_at')->latest('id');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('spent_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('spent_at', '<=', $request->end_date);
        }
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }
        if ($request->filled('keyword')) {
            $query->where('note', 'like', '%' . $request->keyword . '%');
        }

        return $query->get();
    }

    public function csv(Request $request)
    {
        $expenses = $this->getFilteredExpenses($request);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="depenses_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($expenses) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fputs($handle, "\xEF\xBB\xBF");
            // En-têtes
            fputcsv($handle, ['Date', 'Catégorie', 'Montant (XOF)', 'Note'], ';');
            foreach ($expenses as $expense) {
                fputcsv($handle, [
                    $expense->spent_at->format('d/m/Y'),
                    $expense->category ? $expense->category->name : 'Autre',
                    $expense->amount,
                    $expense->note ?? '',
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf(Request $request)
    {
        $expenses = $this->getFilteredExpenses($request);
        $total = $expenses->sum('amount');

        $pdf = Pdf::loadView('exports.pdf', [
            'expenses' => $expenses,
            'total' => $total,
            'generated_at' => now()->format('d/m/Y H:i'),
            'user' => $request->user(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('depenses_' . now()->format('Y-m-d') . '.pdf');
    }
}
