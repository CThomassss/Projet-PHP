<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';  // Fixed path to database.php
require_once 'lib/functions.php';    // Added functions include

// Récupérer tous les matchs
$matchs = getMatchs($pdo);  // Using the getMatchs function from functions.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Matchs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Calendrier des Matchs</h1>

    <table class="match-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Équipe Adverse</th>
                <th>Lieu</th>
                <th>Résultat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs as $match) : ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($match['date'])); ?></td>
                <td><?php echo date('H:i', strtotime($match['heure'])); ?></td>
                <td><?php echo htmlspecialchars($match['equipe_adverse']); ?></td>
                <td><?php echo htmlspecialchars($match['lieu']); ?></td>
                <td><?php echo !empty($match['resultat']) ? htmlspecialchars($match['resultat']) : 'À venir'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
