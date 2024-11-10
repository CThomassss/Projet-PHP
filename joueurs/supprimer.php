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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (supprimerJoueur($pdo, $id)) {
            header('Location: liste.php');
            exit();
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de la suppression du joueur: " . $e->getMessage();
    }
}

$joueur = getJoueurById($pdo, $id);
if (!$joueur) {
    header('Location: liste.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un joueur</title>
</head>
<body>
    <h1>Supprimer un joueur</h1>
    <p>Êtes-vous sûr de vouloir supprimer le joueur <?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?> ?</p>
    
    <form method="POST">
        <button type="submit">Oui, supprimer</button>
        <a href="liste.php">Non, annuler</a>
    </form>
</body>
</html>
