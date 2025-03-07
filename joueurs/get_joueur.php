<?php
require_once '../config/database.php';
require_once '../lib/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID du joueur non fourni');
    }

    $joueur = getJoueurById($pdo, $_GET['id']);
    
    if (!$joueur) {
        throw new Exception('Joueur non trouvé');
    }

    // Convertir la taille en centimètres si elle est en mètres
    if ($joueur['taille'] < 3) { // Si la taille est en mètres (ex: 1.75)
        $joueur['taille'] = round($joueur['taille'] * 100); // Conversion en centimètres
    }

    echo json_encode([
        'success' => true,
        'nom' => $joueur['nom'],
        'prenom' => $joueur['prenom'],
        'numero_licence' => $joueur['numero_licence'],
        'date_naissance' => $joueur['date_naissance'],
        'taille' => $joueur['taille'],
        'poids' => $joueur['poids'],
        'statut' => $joueur['statut'],
        'poste_prefere' => $joueur['poste_prefere'],
        'commentaires' => $joueur['commentaires'] ?? ''
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 