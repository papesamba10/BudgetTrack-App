@extends('layouts.app')
@section('title', 'Budget')

@section('content')

{{-- Sélecteur de mois --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('budgets.index') }}" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <label class="form-label" style="margin:0;">Mois affiché :</label>
        <input type="month" name="month" class="form-input" style="width:200px;" value="{{ $currentMonth }}">
        <button type="submit" class="btn btn-primary btn-sm">Afficher</button>
        <span style="font-size:18px;font-weight:600;color:var(--md-sys-color-primary);margin-left:auto;">{{ $currentMonthLabel }}</span>
    </form>
</div>

{{-- Budget global --}}
<div class="card" style="margin-bottom:20px;">
    <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;margin-bottom:16px;">
        <span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;">account_balance_wallet</span>Budget global mensuel
    </h2>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:center;">
        <div>
            @if($globalBudget && $globalBudget->amount > 0)
                <div style="font-size:32px;font-weight:700;color:{{ $globalPercentage >= 100 ? 'var(--color-budget-exceeded)' : ($globalPercentage >= 80 ? 'var(--color-budget-warn)' : 'var(--color-budget-ok)') }};">
                    {{ number_format($globalSpent, 0, ',', ' ') }} <span style="font-size:14px;font-weight:400;color:var(--md-sys-color-outline);">XOF dépensés</span>
                </div>
                <div style="color:var(--md-sys-color-outline);font-size:14px;margin-top:4px;">
                    sur {{ number_format($globalBudget->amount, 0, ',', ' ') }} XOF — {{ $globalPercentage }}%
                </div>
                <div class="progress-bar-wrap" style="margin-top:12px;">
                    <div class="progress-bar-fill" style="width:{{ min($globalPercentage, 100) }}%;background:{{ $globalPercentage >= 100 ? 'var(--color-budget-exceeded)' : ($globalPercentage >= 80 ? 'var(--color-budget-warn)' : 'var(--color-budget-ok)') }};"></div>
                </div>
                @if($globalPercentage >= 100)
                    <div class="alert alert-error" style="margin-top:12px;">
                        <span class="material-symbols-outlined">error</span>Budget dépassé de {{ number_format($globalSpent - $globalBudget->amount, 0, ',', ' ') }} XOF !
                    </div>
                @elseif($globalPercentage >= 80)
                    <div class="alert alert-warning" style="margin-top:12px;">
                        <span class="material-symbols-outlined">warning</span>Vous avez utilisé {{ $globalPercentage }}% de votre budget global.
                    </div>
                @endif
            @else
                <div style="color:var(--md-sys-color-outline);font-size:15px;">Aucun budget global défini pour ce mois.</div>
            @endif
        </div>

        <div>
            <form method="POST" action="{{ route('budgets.store') }}">
                @csrf
                <input type="hidden" name="month" value="{{ $currentMonth }}">
                <input type="hidden" name="category_id" value="">
                <div class="form-group">
                    <label class="form-label">Définir / modifier le budget global (XOF)</label>
                    <input type="number" name="amount" class="form-input" min="0" placeholder="Ex: 300000"
                        value="{{ $globalBudget ? $globalBudget->amount : '' }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined">save</span>
                    {{ $globalBudget ? 'Mettre à jour' : 'Définir' }}
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Budget par catégorie --}}
<div class="card">
    <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;margin-bottom:16px;">
        <span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;">category</span>Budget par catégorie
    </h2>

    @foreach($categoriesStats as $stat)
    <div style="display:grid;grid-template-columns:200px 1fr 240px;gap:16px;align-items:center;padding:16px 0;border-bottom:1px solid var(--md-sys-color-outline-variant);">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="width:36px;height:36px;background:{{ $stat['category']->color }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span class="material-symbols-outlined" style="color:white;font-size:18px;">{{ $stat['category']->icon ?? 'category' }}</span>
            </span>
            <div>
                <div style="font-weight:500;font-size:14px;">{{ $stat['category']->name }}</div>
                <div style="font-size:12px;color:var(--md-sys-color-outline);">Dépensé : {{ number_format($stat['spent'], 0, ',', ' ') }} XOF</div>
            </div>
        </div>
        <div>
            @if($stat['budget_amount'] > 0)
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width:{{ min($stat['percentage'], 100) }}%;background:{{ $stat['percentage'] >= 100 ? 'var(--color-budget-exceeded)' : ($stat['percentage'] >= 80 ? 'var(--color-budget-warn)' : 'var(--md-sys-color-primary)') }};"></div>
                </div>
                <div style="font-size:11px;color:var(--md-sys-color-outline);margin-top:4px;">
                    {{ $stat['percentage'] }}% — Limite : {{ number_format($stat['budget_amount'], 0, ',', ' ') }} XOF
                </div>
            @else
                <div style="font-size:13px;color:var(--md-sys-color-outline);">Pas de limite définie</div>
            @endif
        </div>
        <div>
            <form method="POST" action="{{ route('budgets.store') }}" style="display:flex;gap:8px;align-items:center;">
                @csrf
                <input type="hidden" name="month" value="{{ $currentMonth }}">
                <input type="hidden" name="category_id" value="{{ $stat['category']->id }}">
                <input type="number" name="amount" class="form-input" min="0" placeholder="Limite XOF"
                    value="{{ $stat['budget_amount'] ?: '' }}" style="flex:1;">
                <button type="submit" class="btn btn-outlined btn-sm">
                    <span class="material-symbols-outlined">save</span>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@endsection
