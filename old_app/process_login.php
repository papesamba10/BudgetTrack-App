<?php
// On active l'affichage des erreurs pour être tranquille
ini_set('display_errors', 1);
error_reporting(E_ALL);

// On démarre la session pour maintenir l'utilisateur connecté
session_start();

// On inclut la connexion à la base de données
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données du formulaire
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        try {
            // Rechercher l'utilisateur avec cet email
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Si l'utilisateur existe dans la base
            if ($user) {
                // On vérifie si le mot de passe tapé correspond au mot de passe haché
                if (password_verify($password, $user['mot_de_passe'])) {
                    
                    // On stocke les infos de l'utilisateur dans la session
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['user_nom'] = $user['nom'];

                    // Connexion réussie !
                    echo "Connexion réussie ! Bienvenue " . $_SESSION['user_prenom'] . " 🎉";
                    // Plus tard, on activera la redirection vers le tableau de bord :
                    // header('Location: dashboard.php');
                    
                } else {
                    echo "Mot de passe incorrect. <a href='login.php'>Réessayer</a>";
                }
            } else {
                echo "Aucun compte trouvé avec cet email. <a href='login.php'>Réessayer</a>";
            }

        } catch (PDOException $e) {
            die("Erreur lors de la connexion : " . $e->getMessage());
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
} else {
    header('Location: login.php');
    exit();
}
?>