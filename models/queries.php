<?php
require_once __DIR__ . '/../config/database.php';

// Requêtes pour les matchs
function getUpcomingMatches($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM matchs WHERE date >= CURDATE() ORDER BY date ASC, heure ASC LIMIT 5");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getPastMatches($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM matchs WHERE date < CURDATE() ORDER BY date DESC, heure DESC LIMIT 5");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Requêtes pour les utilisateurs
function getUserById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT nom_utilisateur FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Requêtes pour les joueurs
function getAllJoueurs($pdo) {
    try {
        // Ajout du calcul de la catégorie basé sur l'âge
        $stmt = $pdo->prepare("
            SELECT 
                *,
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 6 THEN 'U6'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 8 THEN 'U8'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 10 THEN 'U10'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 12 THEN 'U12'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 14 THEN 'U14'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 16 THEN 'U16'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 18 THEN 'U18'
                    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 20 THEN 'U20'
                    ELSE 'Seniors'
                END as categorie 
            FROM joueurs 
            ORDER BY nom, prenom");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log l'erreur et retourne un tableau vide
        error_log("Erreur lors de la récupération des joueurs : " . $e->getMessage());
        return [];
    }
}

function getJoueursByMatch($pdo, $matchId) {
    // Récupère tous les joueurs disponibles (statut Actif)
    $stmt = $pdo->prepare("
        SELECT 
            j.*,
            CASE 
                WHEN fm.type IS NOT NULL THEN fm.type
                ELSE 'disponible'
            END as statut_match,
            CASE 
                WHEN fm.joueur_id IS NOT NULL THEN 1
                ELSE 0
            END as est_selectionne
        FROM joueurs j
        LEFT JOIN feuille_match fm ON j.id = fm.joueur_id AND fm.match_id = :match_id
        WHERE j.statut = 'Actif'
        ORDER BY est_selectionne DESC, j.nom, j.prenom
    ");
    
    $stmt->execute(['match_id' => $matchId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Requêtes pour les statistiques
function getTeamStats($pdo) {
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_matchs,
        SUM(CASE WHEN resultat LIKE '%-%' AND CAST(SUBSTRING_INDEX(resultat, '-', 1) AS SIGNED) > CAST(SUBSTRING_INDEX(resultat, '-', -1) AS SIGNED) THEN 1 ELSE 0 END) as victoires,
        SUM(CASE WHEN resultat LIKE '%-%' AND CAST(SUBSTRING_INDEX(resultat, '-', 1) AS SIGNED) = CAST(SUBSTRING_INDEX(resultat, '-', -1) AS SIGNED) THEN 1 ELSE 0 END) as nuls,
        SUM(CASE WHEN resultat LIKE '%-%' AND CAST(SUBSTRING_INDEX(resultat, '-', 1) AS SIGNED) < CAST(SUBSTRING_INDEX(resultat, '-', -1) AS SIGNED) THEN 1 ELSE 0 END) as defaites,
        SUM(CASE WHEN resultat LIKE '%-%' THEN CAST(SUBSTRING_INDEX(resultat, '-', 1) AS SIGNED) ELSE 0 END) as points_marques,
        SUM(CASE WHEN resultat LIKE '%-%' THEN CAST(SUBSTRING_INDEX(resultat, '-', -1) AS SIGNED) ELSE 0 END) as points_encaisses
        FROM matchs 
        WHERE date < CURDATE()");
    $stmt->execute();
    $stats = $stmt->fetch();

    // Compter les joueurs absents
    $stmt = $pdo->prepare("SELECT COUNT(*) as joueurs_absents FROM joueurs WHERE statut = 'Absent'");
    $stmt->execute();
    $absents = $stmt->fetch();
    $stats['joueurs_absents'] = $absents['joueurs_absents'];

    // Calcul du pourcentage de victoires
    $stats['pourcentage_victoires'] = $stats['total_matchs'] > 0 
        ? round(($stats['victoires'] / $stats['total_matchs']) * 100) 
        : 0;

    // Calcul des moyennes
    $stats['moyenne_points_marques'] = $stats['total_matchs'] > 0 
        ? round($stats['points_marques'] / $stats['total_matchs'], 1) 
        : 0;
    $stats['moyenne_points_encaisses'] = $stats['total_matchs'] > 0 
        ? round($stats['points_encaisses'] / $stats['total_matchs'], 1) 
        : 0;

    return $stats;
}

function getPlayerStats($pdo) {
    $stmt = $pdo->prepare("SELECT 
        j.nom,
        j.prenom,
        COUNT(DISTINCT fm.match_id) as matchs_joues,
        SUM(COALESCE(s.temps_jeu, 0)) as temps_jeu,
        SUM(COALESCE(s.essais, 0)) as essais,
        SUM(COALESCE(s.passes_decisives, 0)) as passes_decisives,
        SUM(COALESCE(s.plaquages_reussis, 0)) as plaquages,
        SUM(COALESCE(s.turnovers_gagnes, 0)) as turnovers,
        SUM(COALESCE(s.metres_gagnes, 0)) as metres_gagnes,
        SUM(COALESCE(s.defenseurs_battus, 0)) as defenseurs_battus
        FROM joueurs j
        LEFT JOIN feuille_match fm ON j.id = fm.joueur_id
        LEFT JOIN statistiques_joueur s ON j.id = s.joueur_id
        GROUP BY j.id, j.nom, j.prenom
        ORDER BY j.nom");
    $stmt->execute();
    return $stmt->fetchAll();
}
