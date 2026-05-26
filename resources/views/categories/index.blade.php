@extends('layouts.app')
@section('title', 'Catégories')

@section('content')

{{-- Catégories système --}}
<div class="card" style="margin-bottom:20px;">
    <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;margin-bottom:16px;">
        <span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;">lock</span>Catégories système
    </h2>
    <p style="font-size:13px;color:var(--md-sys-color-outline);margin-bottom:16px;">Ces catégories sont prédéfinies et ne peuvent pas être modifiées ni supprimées.</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
        @foreach($systemCategories as $cat)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;background:var(--md-sys-color-surface-variant);">
            <span style="width:36px;height:36px;background:{{ $cat->color }};border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <span class="material-symbols-outlined" style="color:white;font-size:18px;">{{ $cat->icon ?? 'category' }}</span>
            </span>
            <span style="font-weight:500;font-size:14px;">{{ $cat->name }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Catégories personnalisées --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;">
            <span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;">tune</span>Mes catégories
        </h2>
        <button onclick="openModal('modal-add-category')" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined">add</span>Nouvelle catégorie
        </button>
    </div>

    @if($customCategories->count() > 0)
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;">
        @foreach($customCategories as $cat)
        <div class="card" style="display:flex;align-items:center;gap:12px;padding:14px 16px;">
            <span style="width:40px;height:40px;background:{{ $cat->color }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span class="material-symbols-outlined" style="color:white;font-size:20px;">{{ $cat->icon ?? 'category' }}</span>
            </span>
            <span style="font-weight:500;font-size:14px;flex:1;">{{ $cat->name }}</span>
            <div style="display:flex;gap:2px;">
                <button onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->color }}', '{{ $cat->icon }}')"
                    class="btn btn-text btn-sm" title="Modifier">
                    <span class="material-symbols-outlined">edit</span>
                </button>
                <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Supprimer cette catégorie ? Ses dépenses seront réassignées à \"Autre\".')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-text btn-sm" style="color:var(--md-sys-color-error);" title="Supprimer">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:40px 0;color:var(--md-sys-color-outline);">
        <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.5;">category</span>
        <p style="margin-bottom:16px;">Aucune catégorie personnalisée.</p>
        <button onclick="openModal('modal-add-category')" class="btn btn-primary">
            <span class="material-symbols-outlined">add</span>Créer ma première catégorie
        </button>
    </div>
    @endif
</div>

{{-- Modal Ajout --}}
<div class="modal-overlay" id="modal-add-category">
    <div class="modal">
        <h3 class="modal-title">Nouvelle catégorie</h3>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nom *</label>
                <input type="text" name="name" class="form-input" required placeholder="Ex: Abonnements" value="{{ old('name') }}">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Couleur *</label>
                <div style="display:flex;gap:12px;align-items:center;">
                    <input type="color" name="color" class="form-input" style="height:44px;padding:4px;width:80px;cursor:pointer;" value="{{ old('color', '#006A60') }}">
                    <span style="font-size:13px;color:var(--md-sys-color-outline);">Choisissez une couleur représentative</span>
                </div>
                @error('color')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Icône (Material Symbol) *</label>
                <input type="text" name="icon" class="form-input" required placeholder="Ex: shopping_cart" value="{{ old('icon', 'category') }}">
                <div style="font-size:11px;color:var(--md-sys-color-outline);margin-top:4px;">
                    Consultez <a href="https://fonts.google.com/icons" target="_blank" style="color:var(--md-sys-color-primary);">fonts.google.com/icons</a> pour les noms d'icônes.
                </div>
                @error('icon')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal('modal-add-category')" class="btn btn-text">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span>Créer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Édition --}}
<div class="modal-overlay" id="modal-edit-category">
    <div class="modal">
        <h3 class="modal-title">Modifier la catégorie</h3>
        <form method="POST" id="form-edit-category">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nom *</label>
                <input type="text" name="name" id="edit-cat-name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Couleur *</label>
                <input type="color" name="color" id="edit-cat-color" class="form-input" style="height:44px;padding:4px;width:80px;cursor:pointer;">
            </div>
            <div class="form-group">
                <label class="form-label">Icône *</label>
                <input type="text" name="icon" id="edit-cat-icon" class="form-input" required>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal('modal-edit-category')" class="btn btn-text">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span>Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded', () => openModal('modal-add-category'));</script>
@endif

@endsection

@push('scripts')
<script>
function openEditCategoryModal(id, name, color, icon) {
    document.getElementById('form-edit-category').action = '/categories/' + id;
    document.getElementById('edit-cat-name').value = name;
    document.getElementById('edit-cat-color').value = color;
    document.getElementById('edit-cat-icon').value = icon;
    openModal('modal-edit-category');
}
</script>
@endpush
