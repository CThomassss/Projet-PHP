<?php
try {
    $host = 'frida.o2switch.net';
    $dbname = 'ceqe8591_Gestionnaire_equipe';
    $user = 'ceqe8591_Gestion_Equipe';  // Assurez-vous que c'est le bon utilisateur
    $pass = 'Clem1406';      // Laissez vide si pas de mot de passe
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch(PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
    die("La connexion à la base de données a échoué.");
}
?>
