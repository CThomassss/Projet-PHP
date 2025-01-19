<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    // Récupérer les données du formulaire
    $match_id = intval($_POST['match_id']);
    $joueur_id = intval($_POST['joueur_id']);
    $temps_jeu = intval($_POST['temps_jeu']);
    $essais = intval($_POST['essais']);
    $passes_decisives = intval($_POST['passes_decisives']);
    $metres_gagnes = intval($_POST['metres_gagnes']);
    $defenseurs_battus = intval($_POST['defenseurs_battus']);
    $plaquages_reussis = intval($_POST['plaquages_reussis']);
    $turnovers_gagnes = intval($_POST['turnovers_gagnes']);

    // Vérifier si une entrée existe déjà
    $stmt = $pdo->prepare("SELECT id FROM statistiques_joueur WHERE joueur_id = ? AND match_id = ?");
    $stmt->execute([$joueur_id, $match_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Mise à jour
        $stmt = $pdo->prepare("UPDATE statistiques_joueur SET 
            temps_jeu = ?,
            essais = ?,
            passes_decisives = ?,
            metres_gagnes = ?,
            defenseurs_battus = ?,
            plaquages_reussis = ?,
            turnovers_gagnes = ?
            WHERE joueur_id = ? AND match_id = ?");

        $stmt->execute([
            $temps_jeu,
            $essais,
            $passes_decisives,
            $metres_gagnes,
            $defenseurs_battus,
            $plaquages_reussis,
            $turnovers_gagnes,
            $joueur_id,
            $match_id
        ]);
    } else {
        // Nouvelle insertion
        $stmt = $pdo->prepare("INSERT INTO statistiques_joueur 
            (joueur_id, match_id, temps_jeu, essais, passes_decisives, 
             metres_gagnes, defenseurs_battus, plaquages_reussis, turnovers_gagnes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $joueur_id,
            $match_id,
            $temps_jeu,
            $essais,
            $passes_decisives,
            $metres_gagnes,
            $defenseurs_battus,
            $plaquages_reussis,
            $turnovers_gagnes
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Statistiques sauvegardées avec succès'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()
    ]);
}
