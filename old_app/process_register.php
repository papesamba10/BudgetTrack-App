<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($prenom) && !empty($nom) && !empty($email) && !empty($password)) {
        try {
            $checkEmail = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
            $checkEmail->execute([$email]);
            
            if ($checkEmail->rowCount() > 0) {
                die("Erreur : Cet email est déjà utilisé. <a href='register.php'>Réessayer</a>");
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $insert = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
            $insert->execute([$nom, $prenom, $email, $passwordHash]);

            echo "Inscription réussie ! 🎉 <a href='login.php'>Se connecter</a>";

        } catch (PDOException $e) {
            die("Erreur lors de l'inscription : " . $e->getMessage());
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>