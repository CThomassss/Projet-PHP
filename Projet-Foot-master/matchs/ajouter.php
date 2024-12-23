<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (ajouterMatch($pdo, $_POST)) {
            header('Location: liste.php');
            exit();
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout du match: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un match</title>
</head>
<body>
    <h1>Ajouter un match</h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="heure">Heure:</label>
            <input type="time" id="heure" name="heure" required>
        </div>
        <div>
            <label for="equipe_adverse">Équipe adverse:</label>
            <input type="text" id="equipe_adverse" name="equipe_adverse" required>
        </div>
        <div>
            <label for="lieu">Lieu:</label>
            <select id="lieu" name="lieu" required>
                <option value="Domicile">Domicile</option>
                <option value="Extérieur">Extérieur</option>
            </select>
        </div>
        <button type="submit">Ajouter</button>
        <a href="liste.php">Annuler</a>
    </form>
</body>
</html>
