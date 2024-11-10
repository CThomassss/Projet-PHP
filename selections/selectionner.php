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

$match = getMatchById($pdo, $match_id);
if (!$match) {
    header('Location: ../matchs/liste.php');
    exit();
}

$message = '';
$joueurs = getJoueursActifs($pdo);
$selection_existante = getSelectionMatch($pdo, $match_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selections = [];
    $nombre_titulaires = 0;
    
    foreach ($_POST['selections'] ?? [] as $joueur_id => $data) {
        if (!empty($data['statut'])) {
            $selections[] = [
                'joueur_id' => $joueur_id,
                'statut' => $data['statut'],
                'poste_occupe' => $data['poste_occupe'] ?? ''
            ];
            if ($data['statut'] === 'Titulaire') {
                $nombre_titulaires++;
            }
        }
    }
    
    if ($nombre_titulaires < 15) {
        $message = "Erreur: Il faut sélectionner au moins 15 joueurs titulaires.";
    } else {
        try {
            if (sauvegarderSelection($pdo, $match_id, $selections)) {
                header('Location: ../matchs/liste.php');
                exit();
            }
        } catch (Exception $e) {
            $message = "Erreur lors de la sauvegarde: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sélection des joueurs pour le match</title>
</head>
<body>
    <h1>Sélection des joueurs pour le match contre <?= htmlspecialchars($match['equipe_adverse']) ?></h1>
    <?php if ($message): ?>
        <div style="color: red;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <table border="1">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Poste préféré</th>
                    <th>Statut</th>
                    <th>Poste occupé</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($joueurs as $joueur): ?>
                <?php 
                    $selection = array_filter($selection_existante, 
                        fn($s) => $s['joueur_id'] == $joueur['id']);
                    $selection = reset($selection);
                ?>
                <tr>
                    <td><?= htmlspecialchars($joueur['nom']) ?></td>
                    <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                    <td><?= htmlspecialchars($joueur['poste_prefere']) ?></td>
                    <td>
                        <select name="selections[<?= $joueur['id'] ?>][statut]">
                            <option value="">Non sélectionné</option>
                            <option value="Titulaire" <?= ($selection && $selection['statut'] === 'Titulaire') ? 'selected' : '' ?>>
                                Titulaire
                            </option>
                            <option value="Remplaçant" <?= ($selection && $selection['statut'] === 'Remplaçant') ? 'selected' : '' ?>>
                                Remplaçant
                            </option>
                        </select>
                    </td>
                    <td>
                        <input type="text" 
                               name="selections[<?= $joueur['id'] ?>][poste_occupe]"
                               value="<?= htmlspecialchars($selection['poste_occupe'] ?? '') ?>">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Enregistrer la sélection</button>
        <a href="../matchs/liste.php">Annuler</a>
    </form>
</body>
</html>
