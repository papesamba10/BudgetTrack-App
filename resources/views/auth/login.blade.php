<x-guest-layout>
    @section('title', 'Connexion')
    <div class="form-header">
        <h2>Connexion</h2>
        <p>Bienvenue ! Connectez-vous à votre compte.</p>
    </div>

    @if (session('status'))
        <div style="background:#f0faf8;border:1px solid #006A60;border-radius:8px;padding:12px 16px;margin-bottom:20px;color:#006A60;font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="width:100%;">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Adresse e-mail</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="votre@email.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6F7977;cursor:pointer;">
                <input type="checkbox" name="remember" style="accent-color:#006A60;"> Se souvenir de moi
            </label>
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Se connecter</button>

        @if (Route::has('register'))
            <div class="divider">— ou —</div>
            <div style="text-align:center;font-size:14px;color:#6F7977;">
                Pas encore de compte ? <a class="auth-link" href="{{ route('register') }}">S'inscrire</a>
            </div>
        @endif
    </form>
</x-guest-layout>
