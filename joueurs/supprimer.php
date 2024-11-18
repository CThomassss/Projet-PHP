<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

require_once '../config/database.php';

// Simplifier la vérification pour accepter à la fois GET et POST
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM joueurs WHERE id = ?");
        $success = $stmt->execute([intval($id)]);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
}
?>
