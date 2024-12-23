<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: liste.php');
    exit();
}

$match = getMatchById($pdo, $id);
if (!$match) {
    header('Location: liste.php');
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (modifierMatch($pdo, $id, $_POST)) {
            $_SESSION['success_message'] = "Match modifié avec succès.";
            if ($_POST['resultat']) {
                header('Location: ../evaluations/evaluer.php?match_id=' . $id);
            } else {
                header('Location: liste.php');
            }
            exit();
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de la modification du match: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un match</title>
</head>
<body>
    <h1>Modifier un match</h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" 
                   value="<?= htmlspecialchars($match['date']) ?>" required>
        </div>
        <div>
            <label for="heure">Heure:</label>
            <input type="time" id="heure" name="heure" 
                   value="<?= htmlspecialchars($match['heure']) ?>" required>
        </div>
        <div>
            <label for="equipe_adverse">Équipe adverse:</label>
            <input type="text" id="equipe_adverse" name="equipe_adverse" 
                   value="<?= htmlspecialchars($match['equipe_adverse']) ?>" required>
        </div>
        <div>
            <label for="lieu">Lieu:</label>
            <select id="lieu" name="lieu" required>
                <option value="Domicile" <?= $match['lieu'] === 'Domicile' ? 'selected' : '' ?>>
                    Domicile
                </option>
                <option value="Extérieur" <?= $match['lieu'] === 'Extérieur' ? 'selected' : '' ?>>
                    Extérieur
                </option>
            </select>
        </div>
        <div>
            <label for="resultat">Résultat:</label>
            <select id="resultat" name="resultat">
                <option value="">Non renseigné</option>
                <option value="Victoire" <?= $match['resultat'] === 'Victoire' ? 'selected' : '' ?>>
                    Victoire
                </option>
                <option value="Défaite" <?= $match['resultat'] === 'Défaite' ? 'selected' : '' ?>>
                    Défaite
                </option>
                <option value="Nul" <?= $match['resultat'] === 'Nul' ? 'selected' : '' ?>>
                    Nul
                </option>
            </select>
        </div>
        <button type="submit">Modifier</button>
        <a href="liste.php">Annuler</a>
    </form>
</body>
</html>
