@extends('layouts.app')
@section('title', 'Corbeille')

@section('content')
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div>
            <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;">Corbeille</h2>
            <p style="font-size:13px;color:var(--md-sys-color-outline);margin-top:4px;">Les dépenses supprimées peuvent être restaurées ou supprimées définitivement.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outlined btn-sm">
            <span class="material-symbols-outlined">arrow_back</span>Retour
        </a>
    </div>

    @if($expenses->count() > 0)
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date suppression</th>
                    <th>Date dépense</th>
                    <th>Catégorie</th>
                    <th style="text-align:right;">Montant (XOF)</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr style="opacity:0.75;">
                    <td style="color:var(--md-sys-color-outline);font-size:12px;">{{ $expense->deleted_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $expense->spent_at->format('d/m/Y') }}</td>
                    <td>
                        @if($expense->category)
                        <span class="cat-badge" style="background:{{ $expense->category->color }};">
                            <span class="material-symbols-outlined" style="font-size:14px;">{{ $expense->category->icon ?? 'category' }}</span>
                            {{ $expense->category->name }}
                        </span>
                        @else
                        <span style="color:var(--md-sys-color-outline);font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:700;">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;justify-content:center;gap:4px;">
                            <form method="POST" action="{{ route('expenses.restore', $expense->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-text btn-sm" title="Restaurer" style="color:var(--color-budget-ok);">
                                    <span class="material-symbols-outlined">restore_from_trash</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('expenses.force-delete', $expense->id) }}" onsubmit="return confirm('Supprimer définitivement cette dépense ? Cette action est irréversible.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-text btn-sm" style="color:var(--md-sys-color-error);" title="Supprimer définitivement">
                                    <span class="material-symbols-outlined">delete_forever</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $expenses->links() }}</div>
    @else
    <div style="text-align:center;padding:48px 0;color:var(--md-sys-color-outline);">
        <span class="material-symbols-outlined" style="font-size:56px;display:block;margin-bottom:12px;opacity:0.5;">delete_sweep</span>
        <p style="font-size:16px;">La corbeille est vide.</p>
    </div>
    @endif
</div>
@endsection
