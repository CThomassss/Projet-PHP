<?php
function getJoueurs($pdo) {
    $sql = "SELECT * FROM joueurs ORDER BY nom, prenom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getJoueurById($pdo, $id) {
    $sql = "SELECT * FROM joueurs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function ajouterJoueur($pdo, $data) {
    $sql = "INSERT INTO joueurs (nom, prenom, numero_licence, date_naissance, taille, poids, statut, commentaires, poste_prefere) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['numero_licence'],
        $data['date_naissance'],
        $data['taille'],
        $data['poids'],
        $data['statut'],
        $data['commentaires'],
        $data['poste_prefere']
    ]);
}

function modifierJoueur($pdo, $id, $data) {
    $sql = "UPDATE joueurs SET 
            nom = ?, prenom = ?, numero_licence = ?, date_naissance = ?, 
            taille = ?, poids = ?, statut = ?, commentaires = ?, poste_prefere = ? 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    try {
        return $stmt->execute([
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['numero_licence'] ?? null,
            $data['date_naissance'] ?? null,
            $data['taille'] ?? null,
            $data['poids'] ?? null,
            $data['statut'] ?? null,
            $data['commentaires'] ?? null,
            $data['poste_prefere'] ?? null,
            $id
        ]);
    } catch (PDOException $e) {
        error_log("Erreur SQL: " . $e->getMessage());
        return false;
    }
}

function modifierStatutJoueur($pdo, $id, $data) {
    $sql = "UPDATE joueurs SET statut = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    try {
        return $stmt->execute([$data['statut'], $id]);
    } catch (PDOException $e) {
        error_log("Erreur SQL: " . $e->getMessage());
        return false;
    }
}

function supprimerJoueur($pdo, $id) {
    $sql = "DELETE FROM joueurs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

function getMatchs($pdo) {
    $sql = "SELECT * FROM matchs ORDER BY date, heure";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchById($pdo, $id) {
    $sql = "SELECT * FROM matchs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function ajouterMatch($pdo, $data) {
    $sql = "INSERT INTO matchs (date, heure, equipe_adverse, lieu, resultat) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['date'],
        $data['heure'],
        $data['equipe_adverse'],
        $data['lieu'],
        $data['resultat'] ?? null
    ]);
}

function modifierMatch($pdo, $id, $data) {
    $sql = "UPDATE matchs SET date = ?, heure = ?, equipe_adverse = ?, 
            lieu = ?, resultat = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['date'],
        $data['heure'],
        $data['equipe_adverse'],
        $data['lieu'],
        $data['resultat'] ?? null,
        $id
    ]);
}

function supprimerMatch($pdo, $id) {
    $sql = "DELETE FROM matchs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

function getJoueursActifs($pdo) {
    $sql = "SELECT * FROM joueurs WHERE statut = 'Actif' ORDER BY nom, prenom";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSelectionMatch($pdo, $match_id) {
    $sql = "SELECT s.*, j.nom, j.prenom, j.poste_prefere 
            FROM selections s 
            JOIN joueurs j ON s.joueur_id = j.id 
            WHERE s.match_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$match_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sauvegarderSelection($pdo, $match_id, $selections) {
    try {
        $pdo->beginTransaction();
        
        // Supprime les sélections existantes pour ce match
        $sql = "DELETE FROM selections WHERE match_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$match_id]);
        
        // Insère les nouvelles sélections
        $sql = "INSERT INTO selections (match_id, joueur_id, statut, poste_occupe) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($selections as $selection) {
            $stmt->execute([
                $match_id,
                $selection['joueur_id'],
                $selection['statut'],
                $selection['poste_occupe']
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function getStatistiques($pdo) {
    // Statistiques des matchs
    $stats = [
        'total_matchs' => 0,
        'victoires' => 0,
        'defaites' => 0,
        'nuls' => 0,
        'points_marques' => 0,
        'points_encaisses' => 0,
        'pourcentage_victoires' => 0,
        'moyenne_points_marques' => 0,
        'moyenne_points_encaisses' => 0,
        'joueurs_absents' => 0
    ];

    // Récupérer tous les matchs terminés (avec un résultat)
    $stmt = $pdo->prepare("SELECT resultat FROM matchs WHERE resultat IS NOT NULL");
    $stmt->execute();
    $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stats['total_matchs'] = count($matchs);

    foreach ($matchs as $match) {
        if (!empty($match['resultat'])) {
            list($points_equipe, $points_adversaire) = explode('-', $match['resultat']);
            
            $stats['points_marques'] += (int)$points_equipe;
            $stats['points_encaisses'] += (int)$points_adversaire;

            if ((int)$points_equipe > (int)$points_adversaire) {
                $stats['victoires']++;
            } elseif ((int)$points_equipe < (int)$points_adversaire) {
                $stats['defaites']++;
            } else {
                $stats['nuls']++;
            }
        }
    }

    // Calcul des moyennes et pourcentages
    if ($stats['total_matchs'] > 0) {
        $stats['moyenne_points_marques'] = round($stats['points_marques'] / $stats['total_matchs'], 2);
        $stats['moyenne_points_encaisses'] = round($stats['points_encaisses'] / $stats['total_matchs'], 2);
        $stats['pourcentage_victoires'] = round(($stats['victoires'] / $stats['total_matchs']) * 100, 1);
    }

    // Compter les joueurs absents
    $stmt = $pdo->prepare("SELECT COUNT(*) as absents FROM joueurs WHERE statut = 'Absent'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['joueurs_absents'] = $result['absents'];

    return $stats;
}
