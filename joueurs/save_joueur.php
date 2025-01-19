<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // Récupérer les données du formulaire
    $id = $_POST['id'] ?? null;
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $numero_licence = $_POST['numero_licence'];
    $date_naissance = $_POST['date_naissance'];
    // S'assurer que la taille est en centimètres
    $taille = $_POST['taille'] ? intval($_POST['taille']) : null;
    $poids = $_POST['poids'] ? $_POST['poids'] : null;
    $statut = $_POST['statut'];
    $poste_prefere = $_POST['poste_prefere'];

    if ($id) {
        // Modification d'un joueur existant
        $stmt = $pdo->prepare("UPDATE joueurs SET 
            nom = ?, 
            prenom = ?, 
            numero_licence = ?, 
            date_naissance = ?, 
            taille = ?, 
            poids = ?, 
            statut = ?, 
            poste_prefere = ? 
            WHERE id = ?");
        
        $stmt->execute([
            $nom, 
            $prenom, 
            $numero_licence, 
            $date_naissance, 
            $taille, 
            $poids, 
            $statut, 
            $poste_prefere, 
            $id
        ]);
    } else {
        // Création d'un nouveau joueur
        $stmt = $pdo->prepare("INSERT INTO joueurs 
            (nom, prenom, numero_licence, date_naissance, taille, poids, statut, poste_prefere) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $nom, 
            $prenom, 
            $numero_licence, 
            $date_naissance, 
            $taille, 
            $poids, 
            $statut, 
            $poste_prefere
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Joueur sauvegardé avec succès'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()
    ]);
}
