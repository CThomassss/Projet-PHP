<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('ID du joueur non fourni');
    }

    // Vérifier d'abord si le joueur existe
    $stmt = $pdo->prepare("SELECT id FROM joueurs WHERE id = ?");
    $stmt->execute([$data['id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Joueur non trouvé');
    }

    // Supprimer le joueur
    $stmt = $pdo->prepare("DELETE FROM joueurs WHERE id = ?");
    $stmt->execute([$data['id']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
