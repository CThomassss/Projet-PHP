<?php
require_once '../config/database.php';

function evaluerJoueurs($pdo, $match_id, $joueur_id, $note) {
    try {
        $stmt = $pdo->prepare("INSERT INTO evaluations (match_id, joueur_id, note) VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE note = VALUES(note)");
        $stmt->execute([$match_id, $joueur_id, $note]);

        return ['success' => true, 'message' => 'Évaluation enregistrée avec succès'];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de l\'évaluation des joueurs'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluer les Joueurs</title>
</head>
<body>
    <h1>Évaluer les Joueurs</h1>
    <form action="evaluerJoueurs.php" method="post">
        <label for="match_id">ID du Match:</label>
        <input type="text" id="match_id" name="match_id" required>
        
        <label for="joueur_id">ID du Joueur:</label>
        <input type="text" id="joueur_id" name="joueur_id" required>
        
        <label for="note">Note (sur 10):</label>
        <input type="number" id="note" name="note" min="0" max="10" required>
        
        <button type="submit">Évaluer</button>
    </form>
</body>
</html>