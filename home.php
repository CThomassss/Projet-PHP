<!-- ============================================
   1. INITIALISATION ET CONFIGURATION
============================================ -->
<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit();
}

require_once './config/database.php';
require_once './lib/functions.php';
require_once './models/queries.php';

// Récupération des informations utilisateur
$utilisateur = getUserById($pdo, $_SESSION['utilisateur_id']);

// Récupération des matchs
$upcoming_matches = getUpcomingMatches($pdo);
$past_matches = getPastMatches($pdo);

// Récupération des joueurs
$joueurs = getAllJoueurs($pdo);

// Récupération des statistiques
$stats = getTeamStats($pdo);
$stats['joueurs'] = getPlayerStats($pdo);
?>

<!-- ============================================
   2. EN-TÊTE HTML
============================================ -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>
    body {
        background-image: url('lib/logo_alpha7.png');
	    background-attachment: fixed;
        background-repeat: no-repeat;
        
    }
    </style>
</head>

<!-- ============================================
   3. STRUCTURE PRINCIPALE
============================================ -->
<body>
<div class="app">
    <!-- Message de bienvenue -->
    <div class="welcome-message">
        <h1><span class="welcome-text">Bienvenue</span> <?= htmlspecialchars($utilisateur['nom_utilisateur']) ?><span class="welcome-text"> !</span></h1>
    </div>
    
    <div class="app-body">
        <!-- Contenu principal -->
        <div class="app-body-main-content">
            <!-- Section Statistiques -->
            <section class="stats-section">
                <div class="stats-background">
                    <div class="stats-content">
                        <div class="stats-header">
                            <h2>Statistiques de l'équipe</h2>
                            <button class="stats-details-button" onclick="ouvrirModalStats()">
                                Plus de statistiques
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3>Matchs</h3>
                                <div class="stat-value"><?= $stats['total_matchs'] ?></div>
                                <div class="stat-details">
                                    <span style="color: #F2EBBF;">V: <?= $stats['victoires'] ?></span>
                                    <span>N: <?= $stats['nuls'] ?></span>
                                    <span style="color: #F06060;">D: <?= $stats['defaites'] ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <h3>Victoires</h3>
                                <div class="stat-value"><?= $stats['pourcentage_victoires'] ?>%</div>
                                <div class="stat-label">Pourcentage de victoires</div>
                            </div>
                            <div class="stat-card">
                                <h3>Points</h3>
                                <div class="stat-value <?= (strlen($stats['points_marques']) + strlen($stats['points_encaisses']) > 6) ? 'large-numbers' : '' ?>">
                                    <span style="color: #F2EBBF;">+<?= $stats['points_marques'] ?></span> / 
                                    <span style="color: #F06060;">-<?= $stats['points_encaisses'] ?></span>
                                </div>
                                <div class="stat-details">
                                    <span>Marqués/Encaissés</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <h3>Moyennes</h3>
                                <div class="stat-details">
                                    <span>Marqués: <?= $stats['moyenne_points_marques'] ?></span>
                                    <span>Encaissés: <?= $stats['moyenne_points_encaisses'] ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <h3>Effectif</h3>
                                <div class="stat-value"><?= $stats['joueurs_absents'] ?></div>
                                <div class="stat-label">Joueurs absents</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Séparateur -->
            <div class="section-separator"></div>
            
            <!-- Section Liste des Joueurs -->
            <section class="transfer-section">
                <div class="transfer-section-header">
                    <div class="header-left">
                        <h2>Liste des Joueurs</h2>
                        <?php if (empty($joueurs)): ?>
                        <div class="alert alert-warning">
                            Aucun joueur trouvé ou erreur lors du chargement des données.
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="header-right">
                        <button class="add-player-button" onclick="ouvrirModalAjoutJoueur()">
                            <i class="fas fa-plus"></i>
                            Ajouter un joueur
                        </button>
                    </div>
                </div>
                <div class="category-filters">
                    <button class="category-btn active" data-category="all">Tous</button>
                    <button class="category-btn" data-category="U6">U6</button>
                    <button class="category-btn" data-category="U8">U8</button>
                    <button class="category-btn" data-category="U10">U10</button>
                    <button class="category-btn" data-category="U12">U12</button>
                    <button class="category-btn" data-category="U14">U14</button>
                    <button class="category-btn" data-category="U16">U16</button>
                    <button class="category-btn" data-category="U18">U18</button>
                    <button class="category-btn" data-category="U20">U20</button>
                    <button class="category-btn" data-category="Seniors">Seniors</button>
                </div>
                <div class="transfers">
                    <table class="players-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Numéro de licence</th>
                                <th>Date de naissance</th>
                                <th>Taille</th>
                                <th>Poids</th>
                                <th>Statut</th>
                                <th>Poste préféré</th>
                                <th>Actions</th> <!-- Nouvelle colonne -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($joueurs)):
                                foreach ($joueurs as $joueur): 
                                    // Vérification des données nulles
                                    $taille = !empty($joueur['taille']) ? $joueur['taille'] . ' cm' : 'Non renseigné';
                                    $poids = !empty($joueur['poids']) ? $joueur['poids'] . ' kg' : 'Non renseigné';
                                    $poste = !empty($joueur['poste_prefere']) ? $joueur['poste_prefere'] : 'Non renseigné';
                            ?>
                                <tr data-category="<?= htmlspecialchars($joueur['categorie']) ?>">
                                    <td><?= htmlspecialchars($joueur['nom']) ?></td>
                                    <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                                    <td><?= htmlspecialchars($joueur['numero_licence']) ?></td>
                                    <td><?= htmlspecialchars($joueur['date_naissance']) ?></td>
                                    <td><?= htmlspecialchars($taille) ?></td>
                                    <td><?= htmlspecialchars($poids) ?></td>
                                    <td><?= htmlspecialchars($joueur['statut']) ?></td>
                                    <td><?= htmlspecialchars($poste) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <i onclick='modifierJoueur(<?= json_encode($joueur) ?>)' class="fas fa-pen edit-icon"></i>
                                            <i onclick='supprimerJoueur(<?= $joueur["id"] ?>)' class="fas fa-times delete-icon"></i>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Barre latérale -->
        <div class="app-body-sidebar">
            <!-- Section Matchs -->
            <section class="match-section">
                <h2>Matchs</h2>
                <div class="matches-header">
                    <div class="matches-tabs">
                        <button class="tab-button active" data-tab="upcoming">À venir</button>
                        <button class="tab-button" data-tab="past">Passés</button>
                    </div>
                    <button class="add-match-button" onclick="ouvrirModalMatch()">
                        <i class="ph-plus"></i>
                        Ajouter un match
                    </button>
                </div>
                
                <div class="matches-container upcoming active">
                    <?php foreach($upcoming_matches as $match): ?>
                    <div class="match-card">
                        <span class="match-edit" onclick="modifierMatch(event, <?= htmlspecialchars(json_encode($match)) ?>)">
                            <i class="fas fa-pen edit-icon"></i>
                        </span>
                        <div class="match-card-content" onclick="ouvrirFeuilleMatch(<?= htmlspecialchars(json_encode($match)) ?>)">
                            <!-- Ajout de l'ID du match dans un attribut data -->
                            <input type="hidden" class="match-id" value="<?= $match['id'] ?>">
                            <div class="match-date">
                                <?= date('d M Y', strtotime($match['date'])) ?>
                            </div>
                            <div class="match-teams">
                                <span class="team-home">Notre équipe</span>
                                <span class="vs">VS</span>
                                <span class="team-away"><?= htmlspecialchars($match['equipe_adverse']) ?></span>
                            </div>
                            <div class="match-info">
                                <span class="match-time"><?= date('H:i', strtotime($match['heure'])) ?></span>
                                <span class="match-location"><?= htmlspecialchars($match['lieu']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="matches-container past">
                    <?php foreach($past_matches as $match): 
                        $matchData = [
                            'id' => $match['id'],
                            'date' => $match['date'],
                            'equipe_adverse' => $match['equipe_adverse'],
                            'lieu' => $match['lieu'],
                            'resultat' => $match['resultat']
                        ];
                    ?>
                    <div class="match-card">
                        <!-- Icône de modification séparée du contenu -->
                        <i class="fas fa-pen edit-icon match-edit" onclick="modifierMatch(event, <?= htmlspecialchars(json_encode($matchData)) ?>)"></i>
                        <!-- Contenu du match avec son propre gestionnaire de clic -->
                        <div class="match-card-content" onclick='ouvrirModalScore(<?= json_encode($matchData) ?>)'>
                            <div class="match-date">
                                <?= date('d M Y', strtotime($match['date'])) ?>
                            </div>
                            <div class="match-teams">
                                <span class="team-home">Notre équipe</span>
                                <span class="score"><?= htmlspecialchars($match['resultat']) ?></span>
                                <span class="team-away"><?= htmlspecialchars($match['equipe_adverse']) ?></span>
                            </div>
                            <div class="match-info">
                                <span class="match-location"><?= htmlspecialchars($match['lieu']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- ============================================
   4. MODALS
============================================ -->
<?php require_once 'components/modals.php'; ?>

<!-- ============================================
   5. SCRIPTS
============================================ -->
<script src="js/home.js"></script>

</body>
</html>