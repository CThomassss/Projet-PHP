<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $joueur_id = $_GET['joueur_id'];
    $match_id = $_GET['match_id'];

    $stmt = $pdo->prepare("
        SELECT temps_jeu, essais, passes_decisives, metres_gagnes, 
               defenseurs_battus, plaquages_reussis, turnovers_gagnes
        FROM statistiques_joueur 
        WHERE joueur_id = ? AND match_id = ?
    ");
    
    $stmt->execute([$joueur_id, $match_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stats) {
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'stats' => [
                'temps_jeu' => 0,
                'essais' => 0,
                'passes_decisives' => 0,
                'metres_gagnes' => 0,
                'defenseurs_battus' => 0,
                'plaquages_reussis' => 0,
                'turnovers_gagnes' => 0
            ]
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
    ]);
}
