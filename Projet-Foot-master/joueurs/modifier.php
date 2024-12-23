<?php
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Session expirée',
            'redirect' => '../login.php'
        ]);
    } else {
        header('Location: ../login.php');
    }
    exit();
}

require_once '../config/database.php';
require_once '../lib/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // S'assurer qu'il n'y a pas de sortie avant
    if (ob_get_length()) ob_clean();
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('ID invalide');
        }

        $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
        $data = array_map('trim', $_POST);
        $success = false;

        switch ($type) {
            case 'statut':
                // Ne mettre à jour que le statut
                $sql = "UPDATE joueurs SET statut = :statut WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':statut' => $_POST['statut'],
                    ':id' => $id
                ]);
                $success = true;
                break;
            case 'poste':
                // Ne mettre à jour que le poste
                $sql = "UPDATE joueurs SET poste_prefere = :poste WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':poste' => $_POST['poste_prefere'],
                    ':id' => $id
                ]);
                $success = true;
                break;
            case 'commentaires':
                // Ne mettre à jour que les commentaires
                $sql = "UPDATE joueurs SET commentaires = :commentaires WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':commentaires' => $_POST['commentaires'],
                    ':id' => $id
                ]);
                $success = true;
                break;
            case 'infos':
                // Mise à jour des informations générales
                $sql = "UPDATE joueurs SET 
                    nom = :nom,
                    prenom = :prenom,
                    numero_licence = :numero_licence,
                    date_naissance = :date_naissance,
                    taille = :taille,
                    poids = :poids
                    WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $_POST['nom'],
                    ':prenom' => $_POST['prenom'],
                    ':numero_licence' => $_POST['numero_licence'],
                    ':date_naissance' => $_POST['date_naissance'],
                    ':taille' => $_POST['taille'],
                    ':poids' => $_POST['poids'],
                    ':id' => $id
                ]);
                $success = true;
                break;
            default:
                throw new Exception('Type de modification invalide');
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Modification réussie' : 'Échec de la modification'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

// Si ce n'est pas une requête AJAX, on charge le joueur pour l'affichage du formulaire
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: liste.php');
    exit();
}

$joueur = getJoueurById($pdo, $id);
if (!$joueur) {
    header('Location: liste.php');
    exit();
}

// Si ce n'est pas une requête POST, afficher le formulaire HTML normal
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un joueur</title>
</head>
<body>
    <h1>Modifier un joueur</h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($joueur['nom']) ?>" required>
        </div>
        <div>
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($joueur['prenom']) ?>" required>
        </div>
        <div>
            <label for="numero_licence">Numéro de licence:</label>
            <input type="text" id="numero_licence" name="numero_licence" value="<?= htmlspecialchars($joueur['numero_licence']) ?>" required>
        </div>
        <div>
            <label for="date_naissance">Date de naissance:</label>
            <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($joueur['date_naissance']) ?>" required>
        </div>
        <div>
            <label for="taille">Taille (cm):</label>
            <input type="number" id="taille" name="taille" value="<?= htmlspecialchars($joueur['taille']) ?>" required>
        </div>
        <div>
            <label for="poids">Poids (kg):</label>
            <input type="number" id="poids" name="poids" value="<?= htmlspecialchars($joueur['poids']) ?>" required>
        </div>
        <div>
            <label for="statut">Statut:</label>
            <select id="statut" name="statut" required>
                <?php foreach (['Actif', 'Blessé', 'Suspendu', 'Absent'] as $statut): ?>
                    <option value="<?= $statut ?>" <?= $joueur['statut'] === $statut ? 'selected' : '' ?>>
                        <?= $statut ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="poste_prefere">Poste préféré:</label>
            <input type="text" id="poste_prefere" name="poste_prefere" value="<?= htmlspecialchars($joueur['poste_prefere']) ?>" required>
        </div>
        <div>
            <label for="commentaires">Commentaires:</label>
            <textarea id="commentaires" name="commentaires"><?= htmlspecialchars($joueur['commentaires']) ?></textarea>
        </div>
        <button type="submit">Modifier</button>
        <a href="liste.php">Annuler</a>
    </form>
</body>
</html>
