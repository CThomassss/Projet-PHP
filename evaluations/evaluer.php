<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

$match_id = $_GET['match_id'] ?? null;
if (!$match_id) {
    header('Location: ../matchs/liste.php');
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST['notes'] as $joueur_id => $note) {
            if ($note !== '') {
                $stmt = $pdo->prepare("INSERT INTO evaluations (match_id, joueur_id, note) VALUES (?, ?, ?)");
                $stmt->execute([$match_id, $joueur_id, $note]);
            }
        }
        $_SESSION['success_message'] = "Évaluations enregistrées avec succès.";
        header('Location: ../matchs/liste.php');
        exit();
    } catch (PDOException $e) {
        $message = "Erreur lors de l'enregistrement des évaluations: " . $e->getMessage();
    }
}

// Récupérer les joueurs qui ont participé au match
$stmt = $pdo->prepare("SELECT j.* FROM joueurs j 
                      INNER JOIN participations p ON j.id = p.joueur_id 
                      WHERE p.match_id = ?");
$stmt->execute([$match_id]);
$joueurs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Évaluer les joueurs</title>
</head>
<body>
    <h1>Évaluer les joueurs</h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($joueurs as $joueur): ?>
            <div>
                <label for="note_<?= $joueur['id'] ?>">
                    <?= htmlspecialchars($joueur['nom']) ?> <?= htmlspecialchars($joueur['prenom']) ?>:
                </label>
                <select id="note_<?= $joueur['id'] ?>" name="notes[<?= $joueur['id'] ?>]" required>
                    <option value="">Choisir une note</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        <?php endforeach; ?>
        <button type="submit">Enregistrer les évaluations</button>
        <a href="../matchs/liste.php">Annuler</a>
    </form>
</body>
</html>
