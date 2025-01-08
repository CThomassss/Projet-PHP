<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {  // Changé de 'user_id' à 'utilisateur_id'
    header('Location: login.php');
    exit();
}

// Ajouter ces lignes avant d'utiliser getStatistiques()
require_once './config/database.php';
require_once './lib/functions.php';

// Récupérer les informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT nom_utilisateur FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['utilisateur_id']]);
$utilisateur = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body {
        background-image: url('lib/logo_alpha7.png');
	    background-attachment: fixed;
        background-repeat: no-repeat;
        
    }
    </style>
</head>
<body>
<div class="app">
    <!-- Modifier le message de bienvenue pour utiliser nom_utilisateur -->
    <div class="welcome-message">
        <h1><span class="welcome-text">Bienvenue</span> <?= htmlspecialchars($utilisateur['nom_utilisateur']) ?><span class="welcome-text"> !</span></h1>
    </div>
	
	<div class="app-body">
		<div class="app-body-main-content">
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
                        <?php
                        $stats = getStatistiques($pdo);
                        ?>
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
            
            <div class="section-separator"></div>
            
            <section class="transfer-section">
				<div class="transfer-section-header">
					<h2>Liste des Joueurs</h2>
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
							require_once './config/database.php';
							require_once './lib/functions.php';
							$joueurs = getJoueurs($pdo);
							foreach ($joueurs as $joueur): 
							?>
								<tr data-category="<?= htmlspecialchars($joueur['categorie']) ?>">
									<td><?= htmlspecialchars($joueur['nom']) ?></td>
									<td><?= htmlspecialchars($joueur['prenom']) ?></td>
									<td><?= htmlspecialchars($joueur['numero_licence']) ?></td>
									<td><?= htmlspecialchars($joueur['date_naissance']) ?></td>
									<td><?= htmlspecialchars((string)($joueur['taille'] ?? '')) ?> cm</td>
									<td><?= htmlspecialchars((string)($joueur['poids'] ?? '')) ?> kg</td>
									<td><?= htmlspecialchars($joueur['statut']) ?></td>
									<td><?= htmlspecialchars((string)($joueur['poste_prefere'] ?? '')) ?></td>
									<td>
										<i onclick='modifierJoueur(<?= json_encode($joueur) ?>)' class="fas fa-pen edit-icon"></i>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</section>
		</div>
		<div class="app-body-sidebar">
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
					<?php
					require_once './config/database.php';
					$stmt = $pdo->prepare("SELECT * FROM matchs WHERE date >= CURDATE() ORDER BY date ASC, heure ASC LIMIT 5");
					$stmt->execute();
					$upcoming_matches = $stmt->fetchAll();
					
					foreach($upcoming_matches as $match): ?>
					<div class="match-card" onclick="ouvrirModalMatch(<?= htmlspecialchars(json_encode($match)) ?>)">
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
					<?php endforeach; ?>
				</div>

				<div class="matches-container past">
					<?php
					$stmt = $pdo->prepare("SELECT * FROM matchs WHERE date < CURDATE() ORDER BY date DESC, heure DESC LIMIT 5");
					$stmt->execute();
					$past_matches = $stmt->fetchAll();
					
					foreach($past_matches as $match): ?>
					<div class="match-card" onclick="ouvrirModalScore(<?= htmlspecialchars(json_encode($match)) ?>)">
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
					<?php endforeach; ?>
				</div>
			</section>
		</div>
	</div>
</div>

<!-- Modal pour l'ajout de match -->
<div id="modalMatch" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fermerModalMatch()">&times;</span>
        <h2>Ajouter un nouveau match</h2>
        <form id="formAjoutMatch" onsubmit="ajouterMatch(event)">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="heure">Heure:</label>
                <input type="time" id="heure" name="heure" required>
            </div>
            <div class="form-group">
                <label for="equipe_adverse">Équipe adverse:</label>
                <input type="text" id="equipe_adverse" name="equipe_adverse" required>
            </div>
            <div class="form-group">
                <label for="lieu">Lieu:</label>
                <select id="lieu" name="lieu" required>
                    <option value="Domicile">Domicile</option>
                    <option value="Exterieur">Extérieur</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Ajouter</button>
        </form>
    </div>
</div>

<!-- Ajouter ce nouveau modal avant la fermeture du body -->
<div id="modalModifierMatch" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fermerModalModifierMatch()">&times;</span>
        <h2>Détails du match</h2>
        <form id="formModifierMatch" onsubmit="modifierMatch(event)">
            <input type="hidden" id="match_id" name="id">
            <div class="match-details">
                <div class="equipes">
                    <span class="team-home">Notre équipe</span>
                    <span class="vs">VS</span>
                    <span class="team-away" id="equipe_adverse_display"></span>
                </div>
                <div class="date-lieu">
                    <span id="date_display"></span>
                    <span id="lieu_display"></span>
                </div>
            </div>
            <div class="score-section">
                <h3>Score</h3>
                <div class="form-group score-inputs">
                    <input type="text" id="resultat" name="resultat" placeholder="Score (ex: 24-12)" pattern="[0-9]+-[0-9]+" title="Format: xx-xx">
                </div>
            </div>
            <button type="submit" class="btn-submit">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Modifier le modal pour le score -->
<div id="modalScore" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fermerModalScore()">&times;</span>
        <h2>Résultat du match</h2>
        <form id="formModifierScore" onsubmit="modifierScore(event)">
            <input type="hidden" id="score_match_id" name="id">
            <div class="match-details">
                <div class="equipes">
                    <span class="team-home">Notre équipe</span>
                    <span class="vs">VS</span>
                    <span class="team-away" id="score_equipe_adverse_display"></span>
                </div>
                <div class="date-lieu">
                    <span id="score_date_display"></span>
                    <span id="score_lieu_display"></span>
                </div>
            </div>
            <div class="score-section">
                <h3>Score</h3>
                <div class="form-group score-inputs">
                    <input type="text" id="score_resultat" name="resultat" placeholder="Score (ex: 24-12)" pattern="[0-9]+-[0-9]+" title="Format: xx-xx" required>
                </div>
            </div>
            <button type="submit" class="btn-submit">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Ajouter avant la fermeture de body -->
<div id="modalFeuilleMatch" class="modal">
    <div class="modal-content modal-large">
        <span class="close" onclick="fermerModalFeuilleMatch()">&times;</span>
        <h2>Feuille de match</h2>
        <div class="match-info-header">
            <div class="match-details">
                <span id="fm_date_match"></span>
                <span id="fm_equipe_adverse"></span>
                <span id="fm_lieu"></span>
            </div>
        </div>
        <form id="formFeuilleMatch" onsubmit="sauvegarderFeuilleMatch(event)">
            <input type="hidden" id="fm_match_id" name="match_id">
            <div class="joueurs-selection">
                <div class="titulaires">
                    <h3>Titulaires</h3>
                    <div class="joueurs-list" id="liste_titulaires"></div>
                </div>
                <div class="remplacants">
                    <h3>Remplaçants</h3>
                    <div class="joueurs-list" id="liste_remplacants"></div>
                </div>
                <div class="autres-joueurs">
                    <h3>Joueurs disponibles</h3>
                    <div class="joueurs-list" id="liste_disponibles"></div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit">Enregistrer la feuille de match</button>
            </div>
        </form>
    </div>
</div>

<!-- Ajouter le modal de modification des joueurs avant la fermeture du body -->
<div id="modalJoueur" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fermerModalJoueur()">&times;</span>
        <h2>Modifier le joueur</h2>
        <form id="formModifierJoueur" onsubmit="sauvegarderJoueur(event)">
            <input type="hidden" id="joueur_id" name="id">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="numero_licence">Numéro de licence:</label>
                <input type="text" id="numero_licence" name="numero_licence" required>
            </div>
            <div class="form-group">
                <label for="date_naissance">Date de naissance:</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
            </div>
            <div class="form-group">
                <label for="taille">Taille (cm):</label>
                <input type="number" id="taille" name="taille" min="0" max="300">
            </div>
            <div class="form-group">
                <label for="poids">Poids (kg):</label>
                <input type="number" id="poids" name="poids" min="0" max="200">
            </div>
            <div class="form-group">
                <label for="statut">Statut:</label>
                <select id="statut" name="statut" required>
                    <option value="Actif">Actif</option>
                    <option value="Blessé">Blessé</option>
                    <option value="Suspendu">Suspendu</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>
            <div class="form-group">
                <label for="poste_prefere">Poste préféré:</label>
                <select id="poste_prefere" name="poste_prefere">
                    <option value="Gardien">Gardien</option>
                    <option value="Défenseur">Défenseur</option>
                    <option value="Milieu">Milieu</option>
                    <option value="Attaquant">Attaquant</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Ajouter avant la fermeture du body -->
<div id="modalStats" class="modal">
    <div class="modal-content modal-large">
        <span class="close" onclick="fermerModalStats()">&times;</span>
        <h2>Statistiques détaillées</h2>
        
        <div class="stats-detailed">
            <div class="stats-section-detailed">
                <h3>Statistiques d'équipe</h3>
                
                <div class="stats-category">
                    <h4>Générales</h4>
                    <div class="stats-grid-detailed">
                        <div class="stat-item">
                            <span class="stat-label">Matchs joués</span>
                            <span class="stat-value"><?= $stats['total_matchs'] ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Victoires</span>
                            <span class="stat-value"><?= $stats['victoires'] ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Défaites</span>
                            <span class="stat-value"><?= $stats['defaites'] ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Nuls</span>
                            <span class="stat-value"><?= $stats['nuls'] ?></span>
                        </div>
                    </div>
                </div>

                <div class="stats-category">
                    <h4>Attaque</h4>
                    <div class="stats-grid-detailed">
                        <div class="stat-item">
                            <span class="stat-label">Essais marqués</span>
                            <span class="stat-value"><?= $stats['essais_marques'] ?? 0 ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Transformations réussies</span>
                            <span class="stat-value"><?= $stats['transformations_reussies'] ?? 0 ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Pénalités réussies</span>
                            <span class="stat-value"><?= $stats['penalites_reussies'] ?? 0 ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Drops réussis</span>
                            <span class="stat-value"><?= $stats['drops_reussis'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>

                <div class="stats-category">
                    <h4>Défense</h4>
                    <div class="stats-grid-detailed">
                        <div class="stat-item">
                            <span class="stat-label">Essais encaissés</span>
                            <span class="stat-value"><?= $stats['essais_encaisses'] ?? 0 ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Plaquages réussis</span>
                            <span class="stat-value"><?= $stats['plaquages_reussis'] ?? 0 ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Turnovers gagnés</span>
                            <span class="stat-value"><?= $stats['turnovers_gagnes'] ?? 0 ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Pénalités concédées</span>
                            <span class="stat-value"><?= $stats['penalites_concedees'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-section-detailed">
                <h3>Statistiques individuelles</h3>
                <div class="stats-players-table-container">
                    <table class="stats-players-table">
                        <thead>
                            <tr>
                                <th>Joueur</th>
                                <th>Matchs</th>
                                <th>Titularisations</th>
                                <th>Temps de jeu</th>
                                <th>Essais</th>
                                <th>Passes décisives</th>
                                <th>Plaquages</th>
                                <th>Turnovers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['joueurs'] ?? [] as $joueur): ?>
                            <tr>
                                <td><?= htmlspecialchars($joueur['nom']) ?></td>
                                <td><?= $joueur['matchs_joues'] ?? 0 ?></td>
                                <td><?= $joueur['titularisations'] ?? 0 ?></td>
                                <td><?= $joueur['temps_jeu'] ?? '0:00' ?></td>
                                <td><?= $joueur['essais'] ?? 0 ?></td>
                                <td><?= $joueur['passes_decisives'] ?? 0 ?></td>
                                <td><?= $joueur['plaquages'] ?? 0 ?></td>
                                <td><?= $joueur['turnovers'] ?? 0 ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ajouter le JavaScript à la fin du body -->
<script>
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons and containers
        document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.matches-container').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked button and corresponding container
        button.classList.add('active');
        document.querySelector(`.matches-container.${button.dataset.tab}`).classList.add('active');
    });
});

function ouvrirModalMatch(match = null) {
    if (match === null) {
        // C'est un nouvel ajout
        const modal = document.getElementById('modalMatch');
        // Réinitialiser le formulaire
        document.getElementById('formAjoutMatch').reset();
        modal.style.display = 'block';
    } else {
        // C'est une modification/consultation existante
        const modal = document.getElementById('modalFeuilleMatch');
        document.getElementById('fm_match_id').value = match.id;
        document.getElementById('fm_date_match').textContent = new Date(match.date).toLocaleDateString() + ' ' + match.heure;
        document.getElementById('fm_equipe_adverse').textContent = match.equipe_adverse;
        document.getElementById('fm_lieu').textContent = match.lieu;
        
        // Charger les joueurs
        chargerJoueursDisponibles(match.id);
        
        modal.style.display = 'block';
    }
}

// Mettre à jour le gestionnaire de clics en dehors pour inclure tous les modals
window.onclick = function(event) {
    const modals = ['modalMatch', 'modalScore', 'modalFeuilleMatch', 'modalJoueur'];
    modals.forEach(modalId => {
        if (event.target == document.getElementById(modalId)) {
            document.getElementById(modalId).style.display = 'none';
        }
    });
}

async function chargerJoueursDisponibles(matchId) {
    try {
        const response = await fetch(`matchs/get_joueurs_disponibles.php?match_id=${matchId}`);
        const data = await response.json();
        
        const listeTitulaires = document.getElementById('liste_titulaires');
        const listeRemplacants = document.getElementById('liste_remplacants');
        const listeDisponibles = document.getElementById('liste_disponibles');
        
        listeTitulaires.innerHTML = '';
        listeRemplacants.innerHTML = '';
        listeDisponibles.innerHTML = '';
        
        data.joueurs.forEach(joueur => {
            const joueurElement = creerElementJoueur(joueur);
            if (joueur.statut === 'titulaire') {
                listeTitulaires.appendChild(joueurElement);
            } else if (joueur.statut === 'remplacant') {
                listeRemplacants.appendChild(joueurElement);
            } else {
                listeDisponibles.appendChild(joueurElement);
            }
        });
        
        rendreJoueursDraggable();
    } catch (error) {
        console.error('Erreur:', error);
    }
}

function creerElementJoueur(joueur) {
    const div = document.createElement('div');
    div.className = 'joueur-item';
    div.draggable = true;
    div.dataset.joueurId = joueur.id;
    div.innerHTML = `
        <span class="joueur-nom">${joueur.nom} ${joueur.prenom}</span>
        <span class="joueur-poste">${joueur.poste_prefere}</span>
    `;
    return div;
}

function rendreJoueursDraggable() {
    const joueursItems = document.querySelectorAll('.joueur-item');
    const zones = document.querySelectorAll('.joueurs-list');
    
    joueursItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
    
    zones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
    });
}

async function sauvegarderFeuilleMatch(event) {
    event.preventDefault();
    const matchId = document.getElementById('fm_match_id').value;
    const titulaires = [...document.getElementById('liste_titulaires').children].map(el => el.dataset.joueurId);
    const remplacants = [...document.getElementById('liste_remplacants').children].map(el => el.dataset.joueurId);
    
    try {
        const response = await fetch('matchs/sauvegarder_feuille.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                match_id: matchId,
                titulaires,
                remplacants
            })
        });
        
        const data = await response.json();
        if (data.success) {
            fermerModalFeuilleMatch();
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la sauvegarde');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde de la feuille de match');
    }
}

function fermerModalMatch() {
    document.getElementById('modalMatch').style.display = 'none';
}

function fermerModalModifierMatch() {
    document.getElementById('modalModifierMatch').style.display = 'none';
}

async function ajouterMatch(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('matchs/ajouter.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du match');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout du match');
    }
}

async function modifierMatch(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('matchs/modifier.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la modification du match');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du match');
    }
}

// Remplacer ouvrirModalMatch par ouvrirModalScore pour les matchs passés
function ouvrirModalScore(match) {
    const modal = document.getElementById('modalScore');
    document.getElementById('score_match_id').value = match.id;
    document.getElementById('score_equipe_adverse_display').textContent = match.equipe_adverse;
    document.getElementById('score_date_display').textContent = new Date(match.date).toLocaleDateString() + ' ' + match.heure;
    document.getElementById('score_lieu_display').textContent = match.lieu;
    document.getElementById('score_resultat').value = match.resultat || '';
    modal.style.display = 'block';
}

function fermerModalScore() {
    document.getElementById('modalScore').style.display = 'none';
}

async function modifierScore(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('matchs/modifier.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la modification du score');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du score');
    }
}

// Mise à jour du gestionnaire de clics en dehors
window.onclick = function(event) {
    if (event.target == document.getElementById('modalMatch')) {
        fermerModalMatch();
    }
    if (event.target == document.getElementById('modalScore')) {
        fermerModalScore();
    }
}

// Fonctions pour le drag and drop
function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', e.target.dataset.joueurId);
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    document.querySelectorAll('.joueurs-list').forEach(list => {
        list.classList.remove('drag-over');
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    e.currentTarget.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    const joueurId = e.dataTransfer.getData('text/plain');
    const joueurElement = document.querySelector(`[data-joueur-id="${joueurId}"]`);
    const targetList = e.currentTarget;
    
    // Vérifie les limites (15 titulaires maximum, 8 remplaçants maximum)
    if (targetList.id === 'liste_titulaires' && targetList.children.length >= 15) {
        alert('Maximum 15 titulaires atteint');
        return;
    }
    if (targetList.id === 'liste_remplacants' && targetList.children.length >= 8) {
        alert('Maximum 8 remplaçants atteint');
        return;
    }

    targetList.appendChild(joueurElement);
    targetList.classList.remove('drag-over');
}

function rendreJoueursDraggable() {
    const joueursItems = document.querySelectorAll('.joueur-item');
    const zones = document.querySelectorAll('.joueurs-list');
    
    joueursItems.forEach(item => {
        item.setAttribute('draggable', true);
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
    
    zones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('dragleave', handleDragLeave);
        zone.addEventListener('drop', handleDrop);
    });
}

function modifierJoueur(joueur) {
    const modal = document.getElementById('modalJoueur');
    const form = document.getElementById('formModifierJoueur');
    
    // Remplir le formulaire avec les données du joueur
    Object.keys(joueur).forEach(key => {
        const input = form.elements[key];
        if (input) {
            input.value = joueur[key];
        }
    });
    
    modal.style.display = 'block';
}

function fermerModalJoueur() {
    document.getElementById('modalJoueur').style.display = 'none';
}

async function sauvegarderJoueur(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('joueurs/modifier.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la modification du joueur');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du joueur');
    }
}

// Mettre à jour le gestionnaire de clics en dehors
window.onclick = function(event) {
    const modals = ['modalMatch', 'modalScore', 'modalFeuilleMatch', 'modalJoueur', 'modalStats'];
    modals.forEach(modalId => {
        if (event.target == document.getElementById(modalId)) {
            document.getElementById(modalId).style.display = 'none';
        }
    });
}

function ouvrirModalStats() {
    document.getElementById('modalStats').style.display = 'block';
}

function fermerModalStats() {
    document.getElementById('modalStats').style.display = 'none';
}

// Ajouter cette nouvelle fonction pour le filtrage des catégories
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const playerRows = document.querySelectorAll('.players-table tbody tr');

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Retirer la classe active de tous les boutons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            
            // Ajouter la classe active au bouton cliqué
            button.classList.add('active');
            
            const selectedCategory = button.dataset.category;
            
            // Afficher/masquer les lignes selon la catégorie
            playerRows.forEach(row => {
                if (selectedCategory === 'all' || row.dataset.category === selectedCategory) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>

<!-- Modifier les styles CSS à la fin du fichier -->
<style>
/* Ajouter ces styles CSS */
.welcome-message {
    padding: 1.5rem;
    margin-bottom: 1rem;
    text-align: center;
}

.welcome-message h1 {
    color: #8CBEB2;
    font-size: 2rem;
    margin: 0;
    font-weight: 600;
}

.welcome-message .welcome-text {
    color: var(--c-text-primary); /* Blanc */
}

.btn-edit {
    background: var(--c-gray-700);
    color: var(--c-text-primary);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-edit:hover {
    background: var(--c-gray-600);
}

/* Nouveaux styles pour le modal joueur */
#modalJoueur .modal-content {
    max-width: 600px;
    width: 90%;
    margin: 5vh auto; /* Remonter le modal (avant c'était 10vh) */
    max-height: 90vh;
    overflow-y: auto;
    padding: 2rem;
}

#modalJoueur .form-group {
    margin-bottom: 1.5rem;
}

#modalJoueur .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--c-text-secondary);
}

#modalJoueur .form-group input,
#modalJoueur .form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--c-gray-600);
    background: var(--c-gray-700);
    color: var(--c-text-primary);
    border-radius: 4px;
    font-size: 1rem;
}

#modalJoueur .modal-content h2 {
    margin-bottom: 2rem;
    font-size: 1.5rem;
    color: var(--c-text-primary);
}

#modalJoueur .btn-submit {
    margin-top: 2rem;
    padding: 1rem;
    font-size: 1.1rem;
}

/* Style pour la scrollbar du modal */
#modalJoueur .modal-content::-webkit-scrollbar {
    width: 8px;
}

#modalJoueur .modal-content::-webkit-scrollbar-track {
    background: var(--c-gray-800);
}

#modalJoueur .modal-content::-webkit-scrollbar-thumb {
    background: var(--c-gray-600);
    border-radius: 4px;
}

/* Nouveaux styles pour les titres */
h2, h3 {
    color: #8CBEB2;
}

.stat-card h3 {
    color: var(--c-text-tertiary); /* Garder la couleur d'origine pour les titres des statistiques */
}

#modalJoueur .modal-content h2,
#modalMatch .modal-content h2,
#modalScore .modal-content h2,
#modalFeuilleMatch .modal-content h2 {
    color: #8CBEB2;
    margin-bottom: 2rem;
    font-size: 1.5rem;
}

/* Ajouter ces styles pour l'alignement des titres */
.stats-section h2,
.transfer-section-header h2 {
    padding-left: 1rem;
    margin: 0;
    font-size: 1.5rem;
}

.stats-section {
    padding-top: 1rem;
    padding-bottom: 2rem;
}

.transfer-section {
    margin-top: 1rem; /* Réduit la marge du haut */
}

.transfer-section-header {
    padding: 1rem;
    margin-bottom: 1rem;
}

/* Style pour l'icône de modification */
.edit-icon {
    color: var(--c-text-primary);
    cursor: pointer;
    transition: color 0.3s ease;
    font-size: 1.1rem;
}

.edit-icon:hover {
    color: #8CBEB2;
}

/* Centrer l'icône dans la colonne */
.players-table td:last-child {
    text-align: center;
    width: 50px; /* Largeur fixe pour la colonne des actions */
}
</style>
</body>
</html>