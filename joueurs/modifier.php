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

$joueur = getJoueurById($pdo, $id);
if (!$joueur) {
    header('Location: liste.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (modifierJoueur($pdo, $id, $_POST)) {
            header('Location: liste.php');
            exit();
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de la modification du joueur: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un joueur</title>
</head>
<body>
    <h1>Modifier un joueur</h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($joueur['nom']) ?>" required>
        </div>
        <div>
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($joueur['prenom']) ?>" required>
        </div>
        <div>
            <label for="numero_licence">Numéro de licence:</label>
            <input type="text" id="numero_licence" name="numero_licence" value="<?= htmlspecialchars($joueur['numero_licence']) ?>" required>
        </div>
        <div>
            <label for="date_naissance">Date de naissance:</label>
            <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($joueur['date_naissance']) ?>" required>
        </div>
        <div>
            <label for="taille">Taille (cm):</label>
            <input type="number" id="taille" name="taille" value="<?= htmlspecialchars($joueur['taille']) ?>" required>
        </div>
        <div>
            <label for="poids">Poids (kg):</label>
            <input type="number" id="poids" name="poids" value="<?= htmlspecialchars($joueur['poids']) ?>" required>
        </div>
        <div>
            <label for="statut">Statut:</label>
            <select id="statut" name="statut" required>
                <?php foreach (['Actif', 'Blessé', 'Suspendu', 'Absent'] as $statut): ?>
                    <option value="<?= $statut ?>" <?= $joueur['statut'] === $statut ? 'selected' : '' ?>>
                        <?= $statut ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="poste_prefere">Poste préféré:</label>
            <input type="text" id="poste_prefere" name="poste_prefere" value="<?= htmlspecialchars($joueur['poste_prefere']) ?>" required>
        </div>
        <div>
            <label for="commentaires">Commentaires:</label>
            <textarea id="commentaires" name="commentaires"><?= htmlspecialchars($joueur['commentaires']) ?></textarea>
        </div>
        <button type="submit">Modifier</button>
        <a href="liste.php">Annuler</a>
    </form>
</body>
</html>
