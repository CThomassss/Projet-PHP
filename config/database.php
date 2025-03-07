<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    $host = 'mysql-gestionnaireequipeginerceolin.alwaysdata.net';
    $dbname = 'gestionnaireequipeginerceolin_database';
    $user = '395101';  // Assurez-vous que c'est le bon utilisateur
    $pass = 'XakCVDp6Es&rzk1m7wgX';      // Laissez vide si pas de mot de passe
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch(PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
    die("La connexion à la base de données a échoué.");
}
?>
