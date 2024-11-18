<?php
session_start();

// Debug de session
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = $_POST['nom_utilisateur'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT id, mot_de_passe FROM utilisateurs WHERE nom_utilisateur = ?");
        $stmt->execute([$nom_utilisateur]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Démarrer une nouvelle session propre
            session_regenerate_id(true);
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            
            // Debug
            error_log("Session créée pour l'utilisateur ID: " . $utilisateur['id']);
            
            // Redirection vers home.php
            header('Location: ./home.php');
            exit();
        } else {
            $error = "Identifiants invalides";
        }
    } catch (PDOException $e) {
        $error = "Erreur de connexion";
        error_log($e->getMessage());
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
