<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$match_id = $_GET['match_id'] ?? null;

if (!$match_id) {
    echo json_encode(['success' => false, 'message' => 'ID du match manquant']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT j.*, 
            CASE 
                WHEN fm.titulaire = 1 THEN 'titulaire'
                WHEN fm.remplacant = 1 THEN 'remplacant'
                ELSE 'disponible'
            END as statut
        FROM joueurs j
        LEFT JOIN feuille_match fm ON j.id = fm.joueur_id AND fm.match_id = ?
        WHERE j.statut = 'Actif'
        ORDER BY j.nom, j.prenom
    ");
    
    $stmt->execute([$match_id]);
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'joueurs' => $joueurs]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
