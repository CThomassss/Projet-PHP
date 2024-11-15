<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'config/database.php';
    
    $nom_utilisateur = $_POST['nom_utilisateur'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    $sql = "SELECT id, nom_utilisateur, mot_de_passe FROM utilisateurs WHERE nom_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom_utilisateur]);
    $utilisateur = $stmt->fetch();
    
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
        header('Location: index.php');
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>
   <link rel="stylesheet" href="/Projet%20PHP/css/login.css">
<div class="login-container">
    <h1>Connexion</h1>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="login-form">
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

<?php require_once 'templates/footer.php'; ?>
