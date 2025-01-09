<?php
session_start();

// Debug de session
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './config/database.php';

// Fonction pour générer un jeton CSRF
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérification du jeton CSRF
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des entrées utilisateur avec FILTER_SANITIZE_SPECIAL_CHARS
    $nom_utilisateur = filter_input(INPUT_POST, 'nom_utilisateur', FILTER_SANITIZE_SPECIAL_CHARS);
    $mot_de_passe = filter_input(INPUT_POST, 'mot_de_passe', FILTER_SANITIZE_SPECIAL_CHARS);
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Vérification du jeton CSRF
    if (!verify_csrf_token($csrf_token)) {
        die('Erreur CSRF');
    }

    // Limite le nombre de tentatives de connexion
    if (isset($_SESSION['attempts']) && $_SESSION['attempts'] >= 3) {
        $error = "Trop de tentatives de connexion. Veuillez réessayer plus tard.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, mot_de_passe FROM utilisateurs WHERE nom_utilisateur = ?");
            $stmt->execute([$nom_utilisateur]);
            $utilisateur = $stmt->fetch();

            // Vérification du mot de passe
            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Démarrer une nouvelle session propre
                session_regenerate_id(true);
                $_SESSION['utilisateur_id'] = $utilisateur['id'];

                // Réinitialisation du compteur de tentatives de connexion
                unset($_SESSION['attempts']);

                // Debug
                error_log("Session créée pour l'utilisateur ID: " . $utilisateur['id']);
                
                // Redirection vers home.php
                header('Location: ./home.php');
                exit();
            } else {
                // Incrémenter le compteur de tentatives de connexion
                $_SESSION['attempts'] = ($_SESSION['attempts'] ?? 0) + 1;
                $error = "Identifiants invalides";
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion à la base de données.";
            error_log($e->getMessage());
        }
    }
}
?>

<link rel="stylesheet" href="/Projet%20PHP/css/login.css">
<div class="login-container">
    <style>
        body {
            background-image: url('lib/logo_alpha7.png');
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
    </style>
    <h1>Connexion</h1>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login-form">
        <!-- Champ pour le jeton CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

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
