<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // Vérifier que l'identifiant est fourni
    if (!isset($_POST['id'])) {
        throw new Exception("L'identifiant du match est requis");
    }

    $id = $_POST['id'];

    // Préparer et exécuter la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM matchs WHERE id = ?");
    $stmt->execute([$id]);

    // Vérifier si la suppression a réussi
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Match supprimé avec succès']);
    } else {
        throw new Exception("Aucun match trouvé avec cet identifiant");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
