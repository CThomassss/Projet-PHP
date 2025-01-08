<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once 'statistiques.controller.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistiques</title>
    <link rel="stylesheet" href="../css/statistiques.css">
</head>
<body>
    <h1>Statistiques de l'Équipe</h1>
    
    <div class="stats-generales">
        <h2>Statistiques Générales</h2>
        <table>
            <tr>
                <th>Total Matchs</th>
                <th>Victoires</th>
                <th>Défaites</th>
                <th>Nuls</th>
            </tr>
            <tr>
                <td><?= $matchStats['total_matchs'] ?></td>
                <td><?= $matchStats['victoires'] ?> (<?= $matchStats['total_matchs'] > 0 ? round(($matchStats['victoires']/$matchStats['total_matchs'])*100, 2) : 0 ?>%)</td>
                <td><?= $matchStats['defaites'] ?> (<?= $matchStats['total_matchs'] > 0 ? round(($matchStats['defaites']/$matchStats['total_matchs'])*100, 2) : 0 ?>%)</td>
                <td><?= $matchStats['nuls'] ?> (<?= $matchStats['total_matchs'] > 0 ? round(($matchStats['nuls']/$matchStats['total_matchs'])*100, 2) : 0 ?>%)</td>
            </tr>
        </table>
    </div>

    <div class="stats-joueurs">
        <h2>Statistiques par Joueur</h2>
        <table>
            <tr>
                <th>Joueur</th>
                <th>Statut</th>
                <th>Titularisations</th>
                <th>Remplacements</th>
                <th>Moyenne Éval.</th>
                <th>% Victoires</th>
            </tr>
            <?php
            while($player = $playerStatsResult->fetch()) {
                $victoryPercentage = $player['total_matchs_joues'] > 0 ?
                round(($player['victoires_joueur']/$player['total_matchs_joues'])*100, 2) : 0;
                echo "<tr>
                    <td>{$player['nom']} {$player['prenom']}</td>
                    <td>{$player['statut']}</td>
                    <td>{$player['titularisations']}</td>
                    <td>{$player['remplacements']}</td>
                    <td>" . round($player['moyenne_evaluation'], 2) . "</td>
                    <td>{$victoryPercentage}%</td>
                </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>