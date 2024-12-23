<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: liste.php');
    exit();
}

$match = getMatchById($pdo, $id);
if (!$match) {
    header('Location: liste.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (supprimerMatch($pdo, $id)) {
            header('Location: liste.php');
            exit();
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de la suppression du match: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un match</title>
</head>
<body>
    <h1>Supprimer un match</h1>
    
    <p>Êtes-vous sûr de vouloir supprimer le match contre 
       <?= htmlspecialchars($match['equipe_adverse']) ?> 
       du <?= htmlspecialchars($match['date']) ?> ?</p>
    
    <form method="POST">
        <button type="submit">Oui, supprimer</button>
        <a href="liste.php">Non, annuler</a>
    </form>
</body>
</html>
