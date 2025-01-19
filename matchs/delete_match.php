<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        throw new Exception('ID du match non fourni');
    }

    // Vérifier si le match existe et récupérer sa date et heure
    $stmt = $pdo->prepare("SELECT id, date, heure FROM matchs WHERE id = ?");
    $stmt->execute([$data['id']]);
    $match = $stmt->fetch();

    if (!$match) {
        throw new Exception('Match non trouvé');
    }

    // Vérifier si le match est déjà passé
    $currentDateTime = new DateTime();
    $matchDateTime = new DateTime($match['date'] . ' ' . $match['heure']);

    if ($matchDateTime < $currentDateTime) {
        throw new Exception('Impossible de supprimer un match qui est passé.');
    }

    // Supprimer les références dans la feuille de match
    $stmt = $pdo->prepare("DELETE FROM feuille_match WHERE match_id = ?");
    $stmt->execute([$data['id']]);

    // Supprimer le match
    $stmt = $pdo->prepare("DELETE FROM matchs WHERE id = ?");
    $stmt->execute([$data['id']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
