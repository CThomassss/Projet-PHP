<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('ID du match non fourni');
    }

    // Vérifier d'abord si le match existe
    $stmt = $pdo->prepare("SELECT id FROM matchs WHERE id = ?");
    $stmt->execute([$data['id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Match non trouvé');
    }

    // Supprimer d'abord les références dans la feuille de match
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
