@extends('layouts.app')
@section('title', 'Administration')

@section('content')

<div class="stats-grid" style="margin-bottom:24px;">
    <div class="card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span class="material-symbols-outlined" style="color:var(--md-sys-color-primary);font-size:24px;">people</span>
            <span style="font-size:13px;color:var(--md-sys-color-outline);font-weight:500;">Utilisateurs inscrits</span>
        </div>
        <div class="amount-display">{{ $totalUsers }}</div>
    </div>
</div>

<div class="card">
    <h2 style="font-family:'Noto Sans Display',sans-serif;font-size:18px;font-weight:600;margin-bottom:16px;">
        <span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;">group</span>Liste des utilisateurs
    </h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Dépenses</th>
                    <th>Inscrit le</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td style="color:var(--md-sys-color-outline);font-size:12px;">{{ $user->id }}</td>
                    <td style="font-weight:500;">{{ $user->name }}</td>
                    <td style="color:var(--md-sys-color-outline);">{{ $user->email }}</td>
                    <td>
                        <span style="padding:4px 10px;border-radius:100px;font-size:12px;font-weight:500;background:{{ $user->isAdmin() ? 'var(--md-sys-color-primary)' : 'var(--md-sys-color-secondary-container)' }};color:{{ $user->isAdmin() ? 'white' : 'var(--md-sys-color-on-secondary-container)' }};">
                            {{ $user->isAdmin() ? 'Admin' : 'Utilisateur' }}
                        </span>
                    </td>
                    <td>{{ $user->expenses_count }}</td>
                    <td style="color:var(--md-sys-color-outline);font-size:13px;">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.user.show', $user) }}" class="btn btn-text btn-sm">
                            <span class="material-symbols-outlined">visibility</span>Voir dépenses
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection
