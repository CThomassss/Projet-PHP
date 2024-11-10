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
        if (ajouterJoueur($pdo, $_POST)) {
            header('Location: liste.php');
            exit();
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout du joueur: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un joueur</title>
</head>
<body>
    <h1>Ajouter un joueur</h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div>
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <div>
            <label for="numero_licence">Numéro de licence:</label>
            <input type="text" id="numero_licence" name="numero_licence" required>
        </div>
        <div>
            <label for="date_naissance">Date de naissance:</label>
            <input type="date" id="date_naissance" name="date_naissance" required>
        </div>
        <div>
            <label for="taille">Taille (cm):</label>
            <input type="number" id="taille" name="taille" required>
        </div>
        <div>
            <label for="poids">Poids (kg):</label>
            <input type="number" id="poids" name="poids" required>
        </div>
        <div>
            <label for="statut">Statut:</label>
            <select id="statut" name="statut" required>
                <option value="Actif">Actif</option>
                <option value="Blessé">Blessé</option>
                <option value="Suspendu">Suspendu</option>
                <option value="Absent">Absent</option>
            </select>
        </div>
        <div>
            <label for="poste_prefere">Poste préféré:</label>
            <input type="text" id="poste_prefere" name="poste_prefere" required>
        </div>
        <div>
            <label for="commentaires">Commentaires:</label>
            <textarea id="commentaires" name="commentaires"></textarea>
        </div>
        <button type="submit">Ajouter</button>
        <a href="liste.php">Annuler</a>
    </form>
</body>
</html>
