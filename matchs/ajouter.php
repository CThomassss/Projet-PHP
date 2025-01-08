<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération des données du formulaire
        $date = $_POST['date'] ?? '';
        $heure = $_POST['heure'] ?? '';
        $equipe_adverse = $_POST['equipe_adverse'] ?? '';
        $lieu = $_POST['lieu'] ?? '';

        // Vérification que toutes les données nécessaires sont présentes
        if (empty($date) || empty($heure) || empty($equipe_adverse) || empty($lieu)) {
            echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
            exit;
        }

        // Préparation et exécution de la requête
        $stmt = $pdo->prepare("INSERT INTO matchs (date, heure, equipe_adverse, lieu) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$date, $heure, $equipe_adverse, $lieu]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Match ajouté avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du match']);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage()); // Log l'erreur sur le serveur
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de l\'ajout du match: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
