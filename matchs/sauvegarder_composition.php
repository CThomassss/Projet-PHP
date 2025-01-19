<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log pour déboguer
error_log("=== Début de la requête ===");
error_log("Session ID: " . session_id());
error_log("Utilisateur ID: " . ($_SESSION['utilisateur_id'] ?? 'non défini'));

if (!isset($_SESSION['utilisateur_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Récupérer les données brutes
$rawData = file_get_contents('php://input');
error_log("Données brutes reçues : " . $rawData);

$data = json_decode($rawData, true);
error_log("Données décodées : " . print_r($data, true));

// Vérification des données
error_log("Validation des données :");
error_log("match_id présent : " . (isset($data['match_id']) ? 'oui' : 'non'));
error_log("match_id valeur : " . ($data['match_id'] ?? 'non défini'));
error_log("titulaires présent : " . (isset($data['titulaires']) ? 'oui' : 'non'));
error_log("remplacants présent : " . (isset($data['remplacants']) ? 'oui' : 'non'));

if (!isset($data['match_id']) || !is_numeric($data['match_id']) || 
    !isset($data['titulaires']) || !is_array($data['titulaires']) || 
    !isset($data['remplacants']) || !is_array($data['remplacants'])) {
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Données manquantes ou invalides',
        'received' => $data,
        'validation' => [
            'match_id_exists' => isset($data['match_id']),
            'match_id_numeric' => isset($data['match_id']) ? is_numeric($data['match_id']) : false,
            'titulaires_exists' => isset($data['titulaires']),
            'titulaires_array' => isset($data['titulaires']) ? is_array($data['titulaires']) : false,
            'remplacants_exists' => isset($data['remplacants']),
            'remplacants_array' => isset($data['remplacants']) ? is_array($data['remplacants']) : false
        ]
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $match_id = intval($data['match_id']);

    // Nettoyer les anciennes entrées
    $stmt = $pdo->prepare("DELETE FROM feuille_match WHERE match_id = ?");
    $stmt->execute([$match_id]);

    // Préparer la requête d'insertion
    $stmt = $pdo->prepare("
        INSERT INTO feuille_match (match_id, joueur_id, titulaire, remplacant) 
        VALUES (?, ?, ?, ?)
    ");

    // Insérer les titulaires
    foreach ($data['titulaires'] as $joueurId) {
        if (is_numeric($joueurId)) {
            $stmt->execute([
                $match_id,
                intval($joueurId),
                1,
                0
            ]);
        }
    }

    // Insérer les remplaçants
    foreach ($data['remplacants'] as $joueurId) {
        if (is_numeric($joueurId)) {
            $stmt->execute([
                $match_id,
                intval($joueurId),
                0,
                1
            ]);
        }
    }

    $pdo->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Composition sauvegardée avec succès'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde de la composition: ' . $e->getMessage()
    ]);
}
