<?php

require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Sécurisation des données
        $nom_utilisateur = htmlspecialchars($_POST["username"]);
        $email = htmlspecialchars($_POST["email"]);
        $mot_de_passe = password_hash($_POST["password"], PASSWORD_DEFAULT);

        // Modification de la requête pour correspondre aux noms des colonnes
        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (:nom_utilisateur, :email, :mot_de_passe)");
        $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->execute();

        echo "<script>alert('Inscription réussie!');</script>";
        header("location: login.php");
    } catch(PDOException $e) {
        echo "<script>alert('Erreur: " . $e->getMessage() . "');</script>";
    }
    $conn = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/PROJET-FOOT/Projet-Foot-master/css/signup.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
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
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
