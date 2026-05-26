@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')

{{-- Alertes de budget --}}
@if(count($alerts) > 0)
<div style="margin-bottom: 20px;">
    @foreach($alerts as $alert)
    <div class="alert {{ $alert['type'] === 'exceeded' ? 'alert-error' : 'alert-warning' }}" role="alert">
        <span class="material-symbols-outlined">{{ $alert['type'] === 'exceeded' ? 'error' : 'warning' }}</span>
        <span>{{ $alert['message'] }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Cartes de synthèse --}}
<div class="stats-grid">
    <div class="card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span class="material-symbols-outlined" style="color:var(--md-sys-color-primary);font-size:24px;">receipt_long</span>
            <span style="font-size:13px;color:var(--md-sys-color-outline);font-weight:500;">Dépensé ce mois</span>
        </div>
        <div class="amount-display">{{ number_format($stats['total'], 0, ',', ' ') }}</div>
        <div style="font-size:12px;color:var(--md-sys-color-outline);margin-top:2px;">XOF — {{ $currentMonthLabel }}</div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span class="material-symbols-outlined" style="color:{{ $ratio >= 1 ? 'var(--color-budget-exceeded)' : ($ratio >= 0.8 ? 'var(--color-budget-warn)' : 'var(--color-budget-ok)') }};font-size:24px;">account_balance_wallet</span>
            <span style="font-size:13px;color:var(--md-sys-color-outline);font-weight:500;">Budget restant</span>
        </div>
        @if($globalBudgetAmount > 0)
            <div class="amount-display" style="color:{{ $ratio >= 1 ? 'var(--color-budget-exceeded)' : ($ratio >= 0.8 ? 'var(--color-budget-warn)' : 'var(--color-budget-ok)') }}">
                {{ number_format(max($globalBudgetAmount - $stats['total'], 0), 0, ',', ' ') }}
            </div>
            <div style="font-size:12px;color:var(--md-sys-color-outline);margin-top:2px;">XOF sur {{ number_format($globalBudgetAmount, 0, ',', ' ') }} XOF</div>
            <div class="progress-bar-wrap" style="margin-top:12px;">
                <div class="progress-bar-fill" style="width:{{ min(round($ratio * 100), 100) }}%;background:{{ $ratio >= 1 ? 'var(--color-budget-exceeded)' : ($ratio >= 0.8 ? 'var(--color-budget-warn)' : 'var(--color-budget-ok)') }};"></div>
            </div>
            <div style="font-size:11px;color:var(--md-sys-color-outline);margin-top:4px;">{{ min(round($ratio * 100), 100) }}% utilisé</div>
        @else
            <div style="font-size:14px;color:var(--md-sys-color-outline);margin-top:8px;">Aucun budget défini</div>
            <a href="{{ route('budgets.index') }}" class="btn btn-text btn-sm" style="margin-top:8px;padding-left:0;">Définir un budget →</a>
        @endif
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span class="material-symbols-outlined" style="color:var(--md-sys-color-primary);font-size:24px;">category</span>
            <span style="font-size:13px;color:var(--md-sys-color-outline);font-weight:500;">Catégories actives</span>
        </div>
        <div class="amount-display">{{ $stats['categories_count'] }}</div>
        <div style="font-size:12px;color:var(--md-sys-color-outline);margin-top:2px;">catégories disponibles</div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span class="material-symbols-outlined" style="color:var(--md-sys-color-primary);font-size:24px;">trending_down</span>
            <span style="font-size:13px;color:var(--md-sys-color-outline);font-weight:500;">Dépenses récentes</span>
        </div>
        <div class="amount-display">{{ $stats['recent_expenses']->count() }}</div>
        <div style="font-size:12px;color:var(--md-sys-color-outline);margin-top:2px;">dernières transactions</div>
    </div>
</div>

{{-- Graphiques --}}
<div class="charts-grid">
    <div class="card">
        <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:16px;font-weight:600;margin-bottom:16px;color:var(--md-sys-color-on-surface);">
            Dépenses par catégorie
        </h2>
        @if(count($stats['category_amounts']) > 0)
            <canvas id="pieChart" height="260"></canvas>
        @else
            <div style="text-align:center;padding:40px 0;color:var(--md-sys-color-outline);">
                <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;">pie_chart</span>
                Aucune dépense ce mois
            </div>
        @endif
    </div>

    <div class="card">
        <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:16px;font-weight:600;margin-bottom:16px;color:var(--md-sys-color-on-surface);">
            Dépenses quotidiennes — {{ $currentMonthLabel }}
        </h2>
        @if(array_sum($stats['daily_amounts']) > 0)
            <canvas id="barChart" height="260"></canvas>
        @else
            <div style="text-align:center;padding:40px 0;color:var(--md-sys-color-outline);">
                <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;">bar_chart</span>
                Aucune dépense ce mois
            </div>
        @endif
    </div>
</div>

{{-- Dépenses récentes --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:16px;font-weight:600;">Dépenses récentes</h2>
        <a href="{{ route('expenses.index') }}" class="btn btn-text btn-sm">Voir tout →</a>
    </div>

    @if($stats['recent_expenses']->count() > 0)
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>Note</th>
                    <th style="text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['recent_expenses'] as $expense)
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
                    <td style="color:var(--md-sys-color-outline);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $expense->note ?? '—' }}</td>
                    <td style="text-align:right;font-weight:700;color:var(--md-sys-color-primary);">{{ number_format($expense->amount, 0, ',', ' ') }} <span style="font-size:11px;font-weight:400;">XOF</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:32px 0;color:var(--md-sys-color-outline);">
        <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;">receipt_long</span>
        Aucune dépense enregistrée.
        <div style="margin-top:12px;">
            <button onclick="openModal('modal-add-expense')" class="btn btn-primary">
                <span class="material-symbols-outlined">add</span> Ajouter une dépense
            </button>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
@if(count($stats['category_amounts']) > 0)
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: @json($stats['category_labels']),
        datasets: [{
            data: @json($stats['category_amounts']),
            backgroundColor: @json($stats['category_colors']),
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { family: 'Noto Sans', size: 12 }, padding: 16 }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return ' ' + ctx.label + ': ' + new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' XOF';
                    }
                }
            }
        }
    }
});
@endif

@if(array_sum($stats['daily_amounts']) > 0)
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: @json($stats['daily_labels']),
        datasets: [{
            label: 'Dépenses (XOF)',
            data: @json($stats['daily_amounts']),
            backgroundColor: 'rgba(0, 106, 96, 0.7)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(190,201,199,0.4)' },
                ticks: {
                    callback: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v)
                }
            },
            x: { grid: { display: false } }
        }
    }
});
@endif
</script>
@endpush
