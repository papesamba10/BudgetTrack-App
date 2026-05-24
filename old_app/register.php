<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BudgetTrack - Inscription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 400px; }
        h2 { color: #005088; text-align: center; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group i { position: absolute; left: 15px; top: 12px; color: #005088; }
        input { width: 100%; padding: 10px 10px 10px 40px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #11caa0; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.3s; }
        button:hover { background-color: #0ea885; }
        .link { text-align: center; margin-top: 20px; font-size: 14px; }
        .link a { color: #005088; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Créer un compte</h2>
    <form action="process_register.php" method="POST">
        <div class="input-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="prenom" placeholder="Prénom" required>
        </div>
        <div class="input-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="nom" placeholder="Nom" required>
        </div>
        <div class="input-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Mot de passe" required>
        </div>
        <button type="submit">S'INSCRIRE</button>
    </form>
    <div class="link">Déjà inscrit ? <a href="login.php">Se connecter</a></div>
</div>

</body>
</html>