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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['match_id']) || !is_numeric($data['match_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID du match manquant ou invalide']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Supprimer les anciennes entrées pour ce match
    $stmt = $pdo->prepare("DELETE FROM feuille_match WHERE match_id = ?");
    $stmt->execute([$data['match_id']]);

    // Préparer la requête d'insertion sans la colonne poste
    $stmt = $pdo->prepare("
        INSERT INTO feuille_match (match_id, joueur_id, titulaire, remplacant) 
        VALUES (?, ?, ?, ?)
    ");

    // Insérer les titulaires
    foreach ($data['titulaires'] as $joueur) {
        $stmt->execute([
            $data['match_id'],
            $joueur['joueur_id'],
            $joueur['titulaire'],
            $joueur['remplacant']
        ]);
    }

    // Insérer les remplaçants
    foreach ($data['remplacants'] as $joueur) {
        $stmt->execute([
            $data['match_id'],
            $joueur['joueur_id'],
            $joueur['titulaire'],
            $joueur['remplacant']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Composition sauvegardée avec succès']);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde de la composition: ' . $e->getMessage()
    ]);
}
