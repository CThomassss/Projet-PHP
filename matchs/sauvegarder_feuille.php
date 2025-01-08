<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$match_id = $data['match_id'] ?? null;
$titulaires = $data['titulaires'] ?? [];
$remplacants = $data['remplacants'] ?? [];

if (!$match_id) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Supprimer les anciennes sélections
    $stmt = $pdo->prepare("DELETE FROM feuille_match WHERE match_id = ?");
    $stmt->execute([$match_id]);
    
    // Insérer les titulaires
    $stmt = $pdo->prepare("INSERT INTO feuille_match (match_id, joueur_id, titulaire) VALUES (?, ?, 1)");
    foreach ($titulaires as $joueur_id) {
        $stmt->execute([$match_id, $joueur_id]);
    }
    
    // Insérer les remplaçants
    $stmt = $pdo->prepare("INSERT INTO feuille_match (match_id, joueur_id, remplacant) VALUES (?, ?, 1)");
    foreach ($remplacants as $joueur_id) {
        $stmt->execute([$match_id, $joueur_id]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
