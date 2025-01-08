<?php
require_once '../config/database.php';

function getMatchStats() {
    global $pdo;
    $queryMatchStats = "SELECT 
        COUNT(*) as total_matchs,
        SUM(CASE WHEN resultat = 'Victoire' THEN 1 ELSE 0 END) as victoires,
        SUM(CASE WHEN resultat = 'Défaite' THEN 1 ELSE 0 END) as defaites,
        SUM(CASE WHEN resultat = 'Nul' THEN 1 ELSE 0 END) as nuls
    FROM matchs";
    $statsResult = $pdo->query($queryMatchStats);
    return $statsResult->fetch();
}

function getPlayerStats() {
    global $pdo;
    $queryPlayerStats = "SELECT 
        j.id, j.nom, j.prenom, j.statut,
        COUNT(DISTINCT t.match_id) as titularisations,
        COUNT(DISTINCT r.match_id) as remplacements,
        AVG(e.note) as moyenne_evaluation,
        SUM(CASE WHEN m.resultat = 'Victoire' THEN 1 ELSE 0 END) as victoires_joueur,
        COUNT(DISTINCT m.id) as total_matchs_joues
    FROM joueurs j
    LEFT JOIN selections t ON j.id = t.joueur_id AND t.statut = 'Titulaire'
    LEFT JOIN selections r ON j.id = r.joueur_id AND r.statut = 'Remplaçant'
    LEFT JOIN evaluations e ON j.id = e.joueur_id
    LEFT JOIN matchs m ON (t.match_id = m.id OR r.match_id = m.id)
    GROUP BY j.id";
    return $pdo->query($queryPlayerStats);
}
?>