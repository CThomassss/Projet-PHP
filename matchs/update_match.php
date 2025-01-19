<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // Vérifier que toutes les données requises sont présentes
    $requiredFields = ['id', 'date', 'heure', 'equipe_adverse', 'lieu'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            throw new Exception("Le champ $field est requis");
        }
    }

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("UPDATE matchs SET date = ?, heure = ?, equipe_adverse = ?, lieu = ? WHERE id = ?");
    $stmt->execute([
        $_POST['date'],
        $_POST['heure'],
        $_POST['equipe_adverse'],
        $_POST['lieu'],
        $_POST['id']
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
