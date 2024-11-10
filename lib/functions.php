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
    return $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['numero_licence'],
        $data['date_naissance'],
        $data['taille'],
        $data['poids'],
        $data['statut'],
        $data['commentaires'],
        $data['poste_prefere'],
        $id
    ]);
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
