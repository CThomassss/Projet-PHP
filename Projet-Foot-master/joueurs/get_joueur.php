
<?php
require_once '../config/database.php';
require_once '../lib/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

$joueur = getJoueurById($pdo, $id);
if (!$joueur) {
    http_response_code(404);
    echo json_encode(['error' => 'Joueur non trouvé']);
    exit;
}

// Assainir les données pour JSON
$joueur = array_map('htmlspecialchars', $joueur);

header('Content-Type: application/json');
echo json_encode($joueur);