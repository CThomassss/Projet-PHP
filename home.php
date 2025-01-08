<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {  // Changé de 'user_id' à 'utilisateur_id'
    header('Location: login.php');
    exit();
}

// Ajouter ces lignes avant d'utiliser getStatistiques()
require_once './config/database.php';
require_once './lib/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
<div class="app">
	
	<div class="app-body">
		<div class="app-body-main-content">
			<section class="stats-section">
			    <h2>Statistiques de l'équipe</h2>
			    <?php
			    $stats = getStatistiques($pdo);
			    ?>
			    <div class="stats-grid">
			        <div class="stat-card">
			            <h3>Matchs</h3>
			            <div class="stat-value"><?= $stats['total_matchs'] ?></div>
			            <div class="stat-details">
			                <span>V: <?= $stats['victoires'] ?></span>
			                <span>N: <?= $stats['nuls'] ?></span>
			                <span>D: <?= $stats['defaites'] ?></span>
			            </div>
			        </div>
			        <div class="stat-card">
			            <h3>Victoires</h3>
			            <div class="stat-value"><?= $stats['pourcentage_victoires'] ?>%</div>
			            <div class="stat-label">Pourcentage de victoires</div>
			        </div>
			        <div class="stat-card">
			            <h3>Points</h3>
			            <div class="stat-value">+<?= $stats['points_marques'] ?> / -<?= $stats['points_encaisses'] ?></div>
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
			</section>
			
			<section class="transfer-section">
				<div class="transfer-section-header">
					<h2>Liste des Joueurs</h2>
					<div class="filter-options">
						<button class="icon-button">
							<i class="ph-funnel"></i>
						</button>
						<button class="icon-button">
							<i class="ph-plus"></i>
						</button>
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
							</tr>
						</thead>
						<tbody>
							<?php 
							require_once './config/database.php';
							require_once './lib/functions.php';
							$joueurs = getJoueurs($pdo);
							foreach ($joueurs as $joueur): 
							?>
							<tr>
								<td><?= htmlspecialchars($joueur['nom']) ?></td>
								<td><?= htmlspecialchars($joueur['prenom']) ?></td>
								<td><?= htmlspecialchars($joueur['numero_licence']) ?></td>
								<td><?= htmlspecialchars($joueur['date_naissance']) ?></td>
								<td><?= htmlspecialchars((string)($joueur['taille'] ?? '')) ?> cm</td>
								<td><?= htmlspecialchars((string)($joueur['poids'] ?? '')) ?> kg</td>
								<td><?= htmlspecialchars($joueur['statut']) ?></td>
								<td><?= htmlspecialchars((string)($joueur['poste_prefere'] ?? '')) ?></td>
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
    const modals = ['modalMatch', 'modalScore', 'modalFeuilleMatch'];
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
</script>
</body>
</html>