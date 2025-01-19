<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

if (!isset($_SESSION['utilisateur_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if (!isset($_GET['match_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Match ID requis']);
    exit;
}

try {
    $match_id = intval($_GET['match_id']);
    
    // Récupérer les titulaires
    $stmt = $pdo->prepare("
        SELECT j.id, j.nom, j.prenom, j.poste_prefere
        FROM joueurs j
        JOIN feuille_match fm ON j.id = fm.joueur_id
        WHERE fm.match_id = ? AND fm.titulaire = 1
        ORDER BY j.nom, j.prenom
    ");
    $stmt->execute([$match_id]);
    $titulaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les remplaçants
    $stmt = $pdo->prepare("
        SELECT j.id, j.nom, j.prenom, j.poste_prefere
        FROM joueurs j
        JOIN feuille_match fm ON j.id = fm.joueur_id
        WHERE fm.match_id = ? AND fm.remplacant = 1
        ORDER BY j.nom, j.prenom
    ");
    $stmt->execute([$match_id]);
    $remplacants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'titulaires' => $titulaires,
        'remplacants' => $remplacants
    ]);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération de la composition']);
}
