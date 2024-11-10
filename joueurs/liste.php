<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

$joueurs = getJoueurs($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Joueurs</title>
</head>
<body>
    <h1>Liste des Joueurs</h1>
    <a href="ajouter.php">Ajouter un joueur</a>
    
    <table border="1">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Numéro de licence</th>
                <th>Statut</th>
                <th>Poste préféré</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joueurs as $joueur): ?>
            <tr>
                <td><?= htmlspecialchars($joueur['nom']) ?></td>
                <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                <td><?= htmlspecialchars($joueur['numero_licence']) ?></td>
                <td><?= htmlspecialchars($joueur['statut']) ?></td>
                <td><?= htmlspecialchars($joueur['poste_prefere']) ?></td>
                <td>
                    <a href="modifier.php?id=<?= $joueur['id'] ?>">Modifier</a>
                    <a href="supprimer.php?id=<?= $joueur['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
