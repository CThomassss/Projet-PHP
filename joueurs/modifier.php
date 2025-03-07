<?php
session_start();
require_once '../config/database.php';
require_once '../lib/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    $id = $_GET['id'] ?? null;
    $type = $_GET['type'] ?? null;

    if (!$id || !$type) {
        throw new Exception('Paramètres manquants');
    }

    // Vérifier si le joueur existe
    $joueur = getJoueurById($pdo, $id);
    if (!$joueur) {
        throw new Exception('Joueur non trouvé');
    }

    $data = [];
    switch ($type) {
        case 'infos':
            if (isset($_POST['nom'])) $data['nom'] = $_POST['nom'];
            if (isset($_POST['prenom'])) $data['prenom'] = $_POST['prenom'];
            if (isset($_POST['numero_licence'])) $data['numero_licence'] = $_POST['numero_licence'];
            if (isset($_POST['date_naissance'])) $data['date_naissance'] = $_POST['date_naissance'];
            if (isset($_POST['taille'])) {
                $taille = str_replace(',', '.', $_POST['taille']);
                $taille = floatval($taille);
                if ($taille < 3) {
                    $taille = round($taille * 100);
                }
                $data['taille'] = $taille;
            }
            if (isset($_POST['poids'])) {
                $poids = str_replace(',', '.', $_POST['poids']);
                $data['poids'] = floatval($poids);
            }
            break;

        case 'statut':
            $data = [
                'statut' => $_POST['statut'] ?? null
            ];
            break;

        case 'poste':
            $data = [
                'poste_prefere' => $_POST['poste_prefere'] ?? null
            ];
            break;

        case 'commentaires':
            $data = [
                'commentaires' => $_POST['commentaires'] ?? null
            ];
            break;

        default:
            throw new Exception('Type de modification non valide');
    }

    if (modifierJoueur($pdo, $id, $data)) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Erreur lors de la modification');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 