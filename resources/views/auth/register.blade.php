<x-guest-layout>
    @section('title', 'Inscription')
    <div class="form-header">
        <h2>Créer un compte</h2>
        <p>Rejoignez BudgetTrack et maîtrisez vos finances.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" style="width:100%;">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Nom complet</label>
            <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Votre prénom et nom">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Adresse e-mail</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="votre@email.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="8 caractères minimum">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="margin-bottom:24px;">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Répétez votre mot de passe">
            @error('password_confirmation')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-primary">Créer mon compte</button>

        <div class="divider">— ou —</div>
        <div style="text-align:center;font-size:14px;color:#6F7977;">
            Déjà inscrit ? <a class="auth-link" href="{{ route('login') }}">Se connecter</a>
        </div>
    </form>
</x-guest-layout>
