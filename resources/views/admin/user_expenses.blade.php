@extends('layouts.app')
@section('title', 'Dépenses de ' . $targetUser->name)

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outlined btn-sm">
        <span class="material-symbols-outlined">arrow_back</span>Retour aux utilisateurs
    </a>
</div>

<div class="card" style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="width:56px;height:56px;background:var(--md-sys-color-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:22px;font-weight:700;">
            {{ substr($targetUser->name, 0, 1) }}
        </div>
        <div>
            <div style="font-size:20px;font-weight:700;font-family:'Noto Sans Display',sans-serif;">{{ $targetUser->name }}</div>
            <div style="color:var(--md-sys-color-outline);">{{ $targetUser->email }}</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:28px;font-weight:700;color:var(--md-sys-color-primary);">{{ number_format($total, 0, ',', ' ') }} XOF</div>
            <div style="font-size:13px;color:var(--md-sys-color-outline);">Total toutes périodes confondues</div>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;margin-bottom:16px;">
        Historique des dépenses <span style="color:var(--md-sys-color-outline);font-size:14px;font-weight:400;">({{ $expenses->total() }})</span>
    </h2>

    @if($expenses->count() > 0)
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>Note</th>
                    <th style="text-align:right;">Montant (XOF)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
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
                    <td style="color:var(--md-sys-color-outline);">{{ $expense->note ?? '—' }}</td>
                    <td style="text-align:right;font-weight:700;color:var(--md-sys-color-primary);">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $expenses->links() }}</div>
    @else
    <div style="text-align:center;padding:40px 0;color:var(--md-sys-color-outline);">Cet utilisateur n'a aucune dépense.</div>
    @endif
</div>
@endsection
