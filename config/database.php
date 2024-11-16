<?php
try {
    $host = 'localhost';
    $dbname = 'gestion_equipe_rugby';
    $user = 'root';  // Assurez-vous que c'est le bon utilisateur
    $pass = '';      // Laissez vide si pas de mot de passe
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch(PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
    die("La connexion à la base de données a échoué.");
}
?>
