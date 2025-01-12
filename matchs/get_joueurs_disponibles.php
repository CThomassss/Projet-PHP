<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../models/queries.php';

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
    $joueurs = getJoueursByMatch($pdo, $match_id);
    
    echo json_encode([
        'success' => true,
        'joueurs' => $joueurs
    ]);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des joueurs']);
}
