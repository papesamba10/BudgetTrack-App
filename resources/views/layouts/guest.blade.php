<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BudgetTrack')</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --md-sys-color-primary: #006A60;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #74F8E5;
            --md-sys-color-error: #BA1A1A;
            --md-sys-color-surface: #F4FBF8;
            --md-sys-color-on-surface: #161D1C;
            --md-sys-color-surface-variant: #DAE5E2;
            --md-sys-color-outline: #6F7977;
            --md-sys-color-outline-variant: #BEC9C7;
            --md-sys-color-background: #F4FBF8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Noto Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #006A60 0%, #00403A 40%, #001D1A 100%);
        }
        .auth-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .auth-brand {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            color: white;
        }
        .brand-logo {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .brand-logo .material-symbols-outlined { font-size: 40px; color: #74F8E5; }
        .brand-title { font-family: 'Noto Sans Display', sans-serif; font-size: 40px; font-weight: 700; margin-bottom: 12px; }
        .brand-subtitle { font-size: 16px; opacity: 0.8; text-align: center; max-width: 320px; line-height: 1.6; }
        .brand-features { margin-top: 48px; display: flex; flex-direction: column; gap: 16px; }
        .brand-feature { display: flex; align-items: center; gap: 12px; font-size: 14px; opacity: 0.85; }
        .brand-feature .material-symbols-outlined { font-size: 20px; color: #74F8E5; }

        .auth-form-panel {
            width: 480px;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }
        .form-header { text-align: center; margin-bottom: 32px; }
        .form-header h2 { font-family: 'Noto Sans Display', sans-serif; font-size: 28px; font-weight: 700; color: #161D1C; margin-bottom: 8px; }
        .form-header p { color: #6F7977; font-size: 14px; }
        .form-group { margin-bottom: 16px; width: 100%; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #6F7977; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 13px 16px;
            border: 1.5px solid #BEC9C7;
            border-radius: 10px;
            font-family: 'Noto Sans', sans-serif;
            font-size: 15px; color: #161D1C;
            background: white;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-input:focus { border-color: #006A60; box-shadow: 0 0 0 3px rgba(0,106,96,0.12); }
        .form-error { color: #BA1A1A; font-size: 12px; margin-top: 4px; }
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: #006A60;
            color: white;
            border: none;
            border-radius: 100px;
            font-family: 'Noto Sans', sans-serif;
            font-size: 15px; font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary:hover { background: #00574f; box-shadow: 0 4px 16px rgba(0,106,96,0.4); }
        .auth-link { color: #006A60; text-decoration: none; font-size: 14px; font-weight: 500; }
        .auth-link:hover { text-decoration: underline; }
        .divider { text-align: center; color: #BEC9C7; font-size: 13px; margin: 20px 0; }
        @media (max-width: 900px) {
            .auth-brand { display: none; }
            .auth-form-panel { width: 100%; }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-brand">
        <div class="brand-logo">
            <span class="material-symbols-outlined">savings</span>
        </div>
        <div class="brand-title">BudgetTrack</div>
        <div class="brand-subtitle">Prenez le contrôle de vos finances personnelles, simplement et efficacement.</div>
        <div class="brand-features">
            <div class="brand-feature">
                <span class="material-symbols-outlined">receipt_long</span>
                Suivi de toutes vos dépenses quotidiennes
            </div>
            <div class="brand-feature">
                <span class="material-symbols-outlined">account_balance_wallet</span>
                Définissez et respectez vos budgets mensuels
            </div>
            <div class="brand-feature">
                <span class="material-symbols-outlined">bar_chart</span>
                Visualisez vos tendances de dépenses
            </div>
            <div class="brand-feature">
                <span class="material-symbols-outlined">warning</span>
                Alertes avant dépassement de budget
            </div>
        </div>
    </div>

    <div class="auth-form-panel">
        {{ $slot }}
    </div>
</div>
</body>
</html>
