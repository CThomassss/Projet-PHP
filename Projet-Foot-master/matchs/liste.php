<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

$matchs = getMatchs($pdo);
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Matchs</title>
</head>
<body>
    <h1>Liste des Matchs</h1>
    <a href="ajouter.php">Ajouter un match</a>

    <h2>Matchs à venir</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Équipe adverse</th>
                <th>Lieu</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs as $match): ?>
                <?php if ($match['date'] >= $today): ?>
                <tr>
                    <td><?= htmlspecialchars($match['date']) ?></td>
                    <td><?= htmlspecialchars($match['heure']) ?></td>
                    <td><?= htmlspecialchars($match['equipe_adverse']) ?></td>
                    <td><?= htmlspecialchars($match['lieu']) ?></td>
                    <td>
                        <a href="modifier.php?id=<?= $match['id'] ?>">Modifier</a>
                        <a href="supprimer.php?id=<?= $match['id'] ?>" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?')">
                            Supprimer
                        </a>
                        <a href="../selections/selectionner.php?match_id=<?= $match['id'] ?>">
                            Sélectionner les joueurs
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Matchs passés</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Équipe adverse</th>
                <th>Lieu</th>
                <th>Résultat</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs as $match): ?>
                <?php if ($match['date'] < $today): ?>
                <tr>
                    <td><?= htmlspecialchars($match['date']) ?></td>
                    <td><?= htmlspecialchars($match['heure']) ?></td>
                    <td><?= htmlspecialchars($match['equipe_adverse']) ?></td>
                    <td><?= htmlspecialchars($match['lieu']) ?></td>
                    <td><?= htmlspecialchars($match['resultat'] ?? 'Non renseigné') ?></td>
                    <td>
                        <a href="modifier.php?id=<?= $match['id'] ?>">Modifier</a>
                        <a href="supprimer.php?id=<?= $match['id'] ?>" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?')">
                            Supprimer
                        </a>
                        <a href="../evaluations/evaluer.php?match_id=<?= $match['id'] ?>">
                            Évaluer les joueurs
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
