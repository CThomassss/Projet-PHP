<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['joueur_id'])) {
        throw new Exception('ID du joueur non fourni');
    }

    $stmt = $pdo->prepare("SELECT * FROM commentaires_joueurs WHERE joueur_id = ? ORDER BY date_creation DESC");
    $stmt->execute([$_GET['joueur_id']]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'commentaires' => $commentaires
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
