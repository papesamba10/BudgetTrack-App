<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de dépenses — BudgetTrack</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #161D1C; font-size: 12px; }
        .header { background: #006A60; color: white; padding: 20px 24px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0 0 4px; }
        .header p { margin: 0; font-size: 11px; opacity: 0.85; }
        .user-info { margin-bottom: 16px; padding: 12px; background: #f0faf8; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #DAE5E2; padding: 10px 12px; font-size: 11px; font-weight: 600; text-align: left; }
        td { padding: 9px 12px; border-bottom: 1px solid #BEC9C7; font-size: 11px; }
        tr:last-child td { border-bottom: none; }
        .total-row { background: #f0faf8; font-weight: 700; }
        .amount { text-align: right; font-weight: 600; color: #006A60; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; color: white; font-size: 10px; }
        .footer { text-align: center; color: #6F7977; font-size: 10px; margin-top: 24px; border-top: 1px solid #BEC9C7; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Rapport de dépenses — BudgetTrack</h1>
        <p>Généré le {{ $generated_at }} | Utilisateur : {{ $user->name }}</p>
    </div>

    <div class="user-info">
        <strong>Compte :</strong> {{ $user->email }} &nbsp;|&nbsp;
        <strong>Nombre de dépenses :</strong> {{ $expenses->count() }} &nbsp;|&nbsp;
        <strong>Total :</strong> {{ number_format($total, 0, ',', ' ') }} XOF
    </div>

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
                        <span class="badge" style="background:{{ $expense->category->color }};">{{ $expense->category->name }}</span>
                    @else
                        <span style="color:#6F7977;">—</span>
                    @endif
                </td>
                <td>{{ $expense->note ?? '—' }}</td>
                <td class="amount">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align:right;font-weight:700;">TOTAL</td>
                <td class="amount" style="font-size:14px;">{{ number_format($total, 0, ',', ' ') }} XOF</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">BudgetTrack — Application de gestion des dépenses personnelles | UNCHK 2026</div>
</body>
</html>
