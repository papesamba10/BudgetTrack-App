@extends('layouts.app')
@section('title', 'Mes dépenses')

@section('content')

{{-- Filtres --}}
<div class="card" style="margin-bottom:20px;">
    <details>
        <summary style="cursor:pointer;font-weight:600;font-size:14px;display:flex;align-items:center;gap:8px;padding:4px 0;">
            <span class="material-symbols-outlined">filter_list</span>
            Filtrer les dépenses
            @if(array_filter($filters, fn($v) => $v !== null && $v !== ''))
                <span style="background:var(--md-sys-color-primary);color:white;border-radius:100px;padding:2px 8px;font-size:11px;">Actif</span>
            @endif
        </summary>
        <form method="GET" action="{{ route('expenses.index') }}" style="margin-top:16px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Catégorie</label>
                    <select name="category_id" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Date début</label>
                    <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Montant min (XOF)</label>
                    <input type="number" name="min_amount" class="form-input" value="{{ request('min_amount') }}" min="0">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Montant max (XOF)</label>
                    <input type="number" name="max_amount" class="form-input" value="{{ request('max_amount') }}" min="0">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Mot-clé (note)</label>
                    <input type="text" name="keyword" class="form-input" value="{{ request('keyword') }}" placeholder="Rechercher...">
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:16px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined">search</span>Appliquer
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outlined btn-sm">Réinitialiser</a>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <a href="{{ route('export.csv', request()->all()) }}" class="btn btn-text btn-sm">
                        <span class="material-symbols-outlined">download</span>CSV
                    </a>
                    <a href="{{ route('export.pdf', request()->all()) }}" class="btn btn-text btn-sm">
                        <span class="material-symbols-outlined">picture_as_pdf</span>PDF
                    </a>
                </div>
            </div>
        </form>
    </details>
</div>

{{-- Liste des dépenses --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;">
            Dépenses <span style="color:var(--md-sys-color-outline);font-size:14px;font-weight:400;">({{ $expenses->total() }} résultat(s))</span>
        </h2>
        <button onclick="openModal('modal-add-expense')" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined">add</span>Nouvelle dépense
        </button>
    </div>

    @if($expenses->count() > 0)
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>Note</th>
                    <th style="text-align:right;">Montant (XOF)</th>
                    <th style="text-align:center;">Actions</th>
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
                        <span style="color:var(--md-sys-color-outline);font-size:12px;">Sans catégorie</span>
                        @endif
                    </td>
                    <td style="color:var(--md-sys-color-outline);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $expense->note }}">
                        {{ $expense->note ?? '—' }}
                    </td>
                    <td style="text-align:right;font-weight:700;color:var(--md-sys-color-primary);">
                        {{ number_format($expense->amount, 0, ',', ' ') }}
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;justify-content:center;gap:4px;">
                            <button onclick="openEditModal({{ $expense->id }}, {{ $expense->amount }}, '{{ $expense->category_id }}', '{{ $expense->spent_at->format('Y-m-d') }}', {{ json_encode($expense->note) }})"
                                class="btn btn-text btn-sm" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Déplacer cette dépense dans la corbeille ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-text btn-sm" style="color:var(--md-sys-color-error);" title="Supprimer">
                                    <span class="material-symbols-outlined">delete</span>
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
        <span class="material-symbols-outlined" style="font-size:56px;display:block;margin-bottom:12px;opacity:0.5;">receipt_long</span>
        <p style="font-size:16px;margin-bottom:16px;">Aucune dépense trouvée.</p>
        <button onclick="openModal('modal-add-expense')" class="btn btn-primary">
            <span class="material-symbols-outlined">add</span>Ajouter votre première dépense
        </button>
    </div>
    @endif
</div>

{{-- FAB --}}
<button class="fab" onclick="openModal('modal-add-expense')">
    <span class="material-symbols-outlined">add</span>
    Nouvelle dépense
</button>

{{-- Modal Ajout dépense --}}
<div class="modal-overlay" id="modal-add-expense">
    <div class="modal">
        <h3 class="modal-title">Ajouter une dépense</h3>
        <form method="POST" action="{{ route('expenses.store') }}" id="form-add-expense">
            @csrf
            <div class="form-group">
                <label class="form-label">Montant (XOF) *</label>
                <input type="number" name="amount" class="form-input" min="1" required placeholder="Ex: 5000" value="{{ old('amount') }}">
                @error('amount')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Catégorie *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Sélectionner une catégorie</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Date *</label>
                <input type="date" name="spent_at" class="form-input" required value="{{ old('spent_at', date('Y-m-d')) }}">
                @error('spent_at')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Note (optionnelle)</label>
                <textarea name="note" class="form-textarea" rows="3" placeholder="Décrivez cette dépense...">{{ old('note') }}</textarea>
                @error('note')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal('modal-add-expense')" class="btn btn-text">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Édition dépense --}}
<div class="modal-overlay" id="modal-edit-expense">
    <div class="modal">
        <h3 class="modal-title">Modifier la dépense</h3>
        <form method="POST" id="form-edit-expense">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Montant (XOF) *</label>
                <input type="number" name="amount" id="edit-amount" class="form-input" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Catégorie *</label>
                <select name="category_id" id="edit-category" class="form-select" required>
                    <option value="">Sélectionner une catégorie</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date *</label>
                <input type="date" name="spent_at" id="edit-date" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Note (optionnelle)</label>
                <textarea name="note" id="edit-note" class="form-textarea" rows="3"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal('modal-edit-expense')" class="btn btn-text">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span>Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded', () => openModal('modal-add-expense'));</script>
@endif

@endsection

@push('scripts')
<script>
function openEditModal(id, amount, categoryId, date, note) {
    document.getElementById('form-edit-expense').action = '/depenses/' + id;
    document.getElementById('edit-amount').value = amount;
    document.getElementById('edit-category').value = categoryId;
    document.getElementById('edit-date').value = date;
    document.getElementById('edit-note').value = note || '';
    openModal('modal-edit-expense');
}
</script>
@endpush
