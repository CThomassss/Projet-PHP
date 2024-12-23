<?php
require_once('../includes/db.php');

// Statistiques générales des matchs
$queryMatchStats = "SELECT 
    COUNT(*) as total_matchs,
    SUM(CASE WHEN score_equipe > score_adversaire THEN 1 ELSE 0 END) as victoires,
    SUM(CASE WHEN score_equipe < score_adversaire THEN 1 ELSE 0 END) as defaites,
    SUM(CASE WHEN score_equipe = score_adversaire THEN 1 ELSE 0 END) as nuls
FROM matchs";
$statsResult = $conn->query($queryMatchStats);
$stats = $statsResult->fetch_assoc();

// Statistiques par joueur
$queryPlayerStats = "SELECT 
    j.id, j.nom, j.prenom, j.statut, j.poste_prefere,
    COUNT(DISTINCT t.match_id) as titularisations,
    COUNT(DISTINCT r.match_id) as remplacements,
    AVG(e.note) as moyenne_evaluation,
    SUM(CASE WHEN m.score_equipe > m.score_adversaire THEN 1 ELSE 0 END) as victoires_joueur,
    COUNT(DISTINCT m.id) as total_matchs_joues
FROM joueurs j
LEFT JOIN titulaires t ON j.id = t.joueur_id
LEFT JOIN remplacants r ON j.id = r.joueur_id
LEFT JOIN evaluations e ON j.id = e.joueur_id
LEFT JOIN matchs m ON (t.match_id = m.id OR r.match_id = m.id)
GROUP BY j.id";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistiques</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Statistiques de l'Équipe</h1>
    
    <!-- Statistiques générales -->
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
                <td><?= $stats['total_matchs'] ?></td>
                <td><?= $stats['victoires'] ?> (<?= round(($stats['victoires']/$stats['total_matchs'])*100, 2) ?>%)</td>
                <td><?= $stats['defaites'] ?> (<?= round(($stats['defaites']/$stats['total_matchs'])*100, 2) ?>%)</td>
                <td><?= $stats['nuls'] ?> (<?= round(($stats['nuls']/$stats['total_matchs'])*100, 2) ?>%)</td>
            </tr>
        </table>
    </div>

    <!-- Statistiques par joueur -->
    <div class="stats-joueurs">
        <h2>Statistiques par Joueur</h2>
        <table>
            <tr>
                <th>Joueur</th>
                <th>Statut</th>
                <th>Poste</th>
                <th>Titularisations</th>
                <th>Remplacements</th>
                <th>Moyenne Éval.</th>
                <th>% Victoires</th>
            </tr>
            <?php
            $playerStatsResult = $conn->query($queryPlayerStats);
            while($player = $playerStatsResult->fetch_assoc()) {
                $victoryPercentage = $player['total_matchs_joues'] > 0 ? 
                    round(($player['victoires_joueur']/$player['total_matchs_joues'])*100, 2) : 0;
                echo "<tr>
                    <td>{$player['nom']} {$player['prenom']}</td>
                    <td>{$player['statut']}</td>
                    <td>{$player['poste_prefere']}</td>
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
