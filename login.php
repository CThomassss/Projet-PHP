<?php
require_once 'config/database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['nom_utilisateur'];
    $password = $_POST['mot_de_passe'];
    
    $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nom_utilisateur'];
        header('Location: home.php');
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

<?php require_once 'templates/footer.php'; ?>
