<?php
session_start();

// Debug de session
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './config/database.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $mot_de_passe = trim($_POST['mot_de_passe']);

    // Valider les entrées
    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Préparer une requête SQL sécurisée
        $sql = "SELECT id, nom_utilisateur, mot_de_passe FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur";
        
        if ($stmt = $pdo->prepare($sql)) {
            // Lier les variables à la déclaration préparée en tant que paramètres
            $stmt->bindParam(":nom_utilisateur", $nom_utilisateur, PDO::PARAM_STR);

            // Tenter d'exécuter la déclaration préparée
            if ($stmt->execute()) {
                // Vérifier si le nom d'utilisateur existe, si oui, vérifier le mot de passe
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $nom_utilisateur = $row["nom_utilisateur"];
                        $hashed_password = $row["mot_de_passe"];
                        if (password_verify($mot_de_passe, $hashed_password)) {
                            // Le mot de passe est correct, alors démarrer une nouvelle session
                            session_start();
                            
                            // Stocker les données dans les variables de session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nom_utilisateur"] = $nom_utilisateur;                            
                            
                            // Rediriger l'utilisateur vers la page de bienvenue
                            header("location: home.php");
                        } else {
                            // Afficher un message d'erreur si le mot de passe n'est pas valide
                            $error = "Le mot de passe que vous avez entré n'est pas valide.";
                        }
                    }
                } else {
                    // Afficher un message d'erreur si le nom d'utilisateur n'existe pas
                    $error = "Aucun compte trouvé avec ce nom d'utilisateur.";
                }
            } else {
                $error = "Oups! Quelque chose a mal tourné. Veuillez réessayer plus tard.";
            }

            // Fermer la déclaration
            unset($stmt);
        }
    }

    // Fermer la connexion
    unset($pdo);
}
?>

<!-- Inclusion du fichier CSS -->
<link rel="stylesheet" href="/Projet%20PHP/css/login.css">

<!-- Conteneur de connexion -->
<div class="login-container">
    <style>
        body {
            background-image: url('lib/logo_alpha7.png');
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
    </style>
    <h1>Connexion</h1>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login-form">
        <div class="form-group">
            <label for="nom_utilisateur">Nom d'utilisateur:</label>
            <input type="text" id="nom_utilisateur" name="nom_utilisateur" required>
        </div>
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe:</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <button type="submit" class="btn-submit">Se connecter</button>
    </form>
    <div class="signup-link">
        <p>Pas encore de compte ? <a href="signup.php">S'inscrire</a></p>
    </div>
</div>