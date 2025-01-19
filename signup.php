<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Vérification des champs vides
        if (empty($_POST["username"]) || empty($_POST["email"]) || empty($_POST["password"])) {
            throw new Exception("Tous les champs sont obligatoires");
        }

        // Sécurisation des données
        $nom_utilisateur = htmlspecialchars(trim($_POST["username"]));
        $email = htmlspecialchars(trim($_POST["email"]));
        $mot_de_passe = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

        // Vérification si l'utilisateur existe déjà
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur OR email = :email");
        $check->execute([':nom_utilisateur' => $nom_utilisateur, ':email' => $email]);
        if ($check->rowCount() > 0) {
            throw new Exception("Cet utilisateur ou email existe déjà");
        }

        // Insertion de l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (:nom_utilisateur, :email, :mot_de_passe)");
        $result = $stmt->execute([
            ':nom_utilisateur' => $nom_utilisateur,
            ':email' => $email,
            ':mot_de_passe' => $mot_de_passe
        ]);

        if ($result) {
            $_SESSION['message'] = 'Inscription réussie!';
            header("Location: login.php");
            exit();
        } else {
            throw new Exception("Erreur lors de l'inscription");
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/css/signup.css">
</head>
<body>
<style>
    body {
        background-image: url('lib/logo_alpha7.png');
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>
<div class="container">
    <h2>S'inscrire</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>    
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Sign Up">
        </div>
        <p>Vous avez déjà un compte ? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>