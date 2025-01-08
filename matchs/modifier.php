<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $resultat = $_POST['resultat'] ?? null;

        if (!$id || !$resultat) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            exit;
        }

        // Vérifier format du score (xx-xx)
        if (!preg_match('/^\d{1,3}-\d{1,3}$/', $resultat)) {
            echo json_encode(['success' => false, 'message' => 'Format de score invalide']);
            exit;
        }

        // Mettre à jour uniquement le résultat
        $stmt = $pdo->prepare("UPDATE matchs SET resultat = ? WHERE id = ?");
        $result = $stmt->execute([$resultat, $id]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
