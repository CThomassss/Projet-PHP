<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO joueurs (nom, prenom, numero_licence, date_naissance, taille, poids, poste_prefere, statut) 
                VALUES (:nom, :prenom, :numero_licence, :date_naissance, :taille, :poids, :poste_prefere, 'Actif')";
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'numero_licence' => $_POST['numero_licence'],
            'date_naissance' => $_POST['date_naissance'],
            'taille' => $_POST['taille'],
            'poids' => $_POST['poids'],
            'poste_prefere' => $_POST['poste_prefere']
        ]);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du joueur']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
