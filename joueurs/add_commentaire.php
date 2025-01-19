<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['joueur_id']) || !isset($_POST['commentaire'])) {
        throw new Exception('Données manquantes');
    }

    $stmt = $pdo->prepare("INSERT INTO commentaires_joueurs (joueur_id, commentaire, date_creation) VALUES (?, ?, NOW())");
    $stmt->execute([$_POST['joueur_id'], $_POST['commentaire']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
