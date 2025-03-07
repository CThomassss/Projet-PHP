<?php
// S'assurer que $stats est disponible
if (!isset($stats)) {
    require_once '../config/database.php';
    require_once '../lib/functions.php';
    $stats = getStatistiques($pdo);
}
?>

<style>
/* Styles pour le modal des statistiques */
#modalStats .modal-content {
    max-height: 90vh;
    overflow-y: auto;
}

.stats-detailed {
    padding: 20px;
}

.stats-players-table-container {
    margin-top: 20px;
    overflow-x: auto;
}

.stats-players-table {
    width: 100%;
    border-collapse: collapse;
}

.stats-players-table th,
.stats-players-table td {
    padding: 10px;
    border: 1px solid var(--c-gray-600);
}

/* Style pour la scrollbar */
#modalStats .modal-content::-webkit-scrollbar {
    width: 8px;
}

#modalStats .modal-content::-webkit-scrollbar-track {
    background: var(--c-gray-800);
}

#modalStats .modal-content::-webkit-scrollbar-thumb {
    background: var(--c-gray-600);
    border-radius: 4px;
}

/* Style pour la liste des commentaires */
#commentaires {
            margin-top: 15px;
            list-style: none;
            padding: 0;
        }

        #commentaires li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        #commentaires .date {
            font-size: 0.8em;
            color: gray;
        }
</style>

<!-- Modal Ajout Match -->
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

<!-- Modal Modification Match -->
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

<!-- Modal Modification Matchs -->
<div id="modalEditMatch" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fermerModalEditMatch()">&times;</span>
        <h2>Modifier le match</h2>
        <form id="formEditMatch" onsubmit="sauvegarderModifMatch(event)">
            <input type="hidden" id="edit_match_id" name="id">
            <div class="form-group">
                <label for="edit_date">Date:</label>
                <input type="date" id="edit_date" name="date" required>
            </div>
            <div class="form-group">
                <label for="edit_heure">Heure:</label>
                <input type="time" id="edit_heure" name="heure" required>
            </div>
            <div class="form-group">
                <label for="edit_equipe_adverse">Équipe adverse:</label>
                <input type="text" id="edit_equipe_adverse" name="equipe_adverse" required>
            </div>
            <div class="form-group">
                <label for="edit_lieu">Lieu:</label>
                <select id="edit_lieu" name="lieu" required>
                    <option value="Domicile">Domicile</option>
                    <option value="Exterieur">Extérieur</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
        </form>
        <!-- Bouton Supprimer -->
        <button class="btn-delete" onclick="supprimerMatch()">Supprimer le match</button>
    </div>
</div>


<!-- Modal Score -->
<div id="modalScore" class="modal">
    <div class="modal-content modal-large">
        <span class="close" onclick="fermerModalScore()">&times;</span>
        <div class="modal-inner-content">
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

                <div class="composition-section">
                    <div class="players-columns">
                        <div class="titulaires-column">
                            <h3>Titulaires</h3>
                            <div class="players-list" id="score_titulaires">
                                <!-- Liste des titulaires -->
                            </div>
                        </div>
                        <div class="remplacants-column">
                            <h3>Remplaçants</h3>
                            <div class="players-list" id="score_remplacants">
                                <!-- Liste des remplaçants -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="buttons-container">
            <button type="submit" class="btn-submit" form="formModifierScore">Enregistrer le score</button>
        </div>
    </div>
</div>

<!-- Modal Feuille de Match -->
<div id="modalFeuilleMatch" class="modal">
    <div class="modal-content modal-large">
        <span class="close" onclick="fermerModalFeuilleMatch()">&times;</span>
        <div class="modal-inner-content">
            <h2>Feuille de match</h2>
            <div class="match-info-header">
                <p>Match contre <span id="equipe_adverse_feuille"></span></p>
                <p>Le <span id="date_feuille"></span> à <span id="heure_feuille"></span></p>
                <p>Lieu : <span id="lieu_feuille"></span></p>
            </div>
            <div class="composition-match">
                <div class="joueurs-selection">
                    <h3>Joueurs disponibles</h3>
                    <div class="category-filters-match">
                        <button class="category-btn active" data-category="all">Tous</button>
                        <button class="category-btn" data-category="premiere-ligne">Première ligne</button>
                        <button class="category-btn" data-category="deuxieme-ligne">Deuxième ligne</button>
                        <button class="category-btn" data-category="troisieme-ligne">Troisième ligne</button>
                        <button class="category-btn" data-category="demis">Demis</button>
                        <button class="category-btn" data-category="trois-quarts">Trois-quarts</button>
                        <button class="category-btn" data-category="arriere">Arrière</button>
                    </div>
                    <div class="joueurs-list" id="joueursDisponibles">
                        <!-- Les joueurs seront chargés dynamiquement ici -->
                    </div>
                </div>
                <div class="equipe-composition">
                    <div class="titulaires">
                        <h3>Titulaires</h3>
                        <div class="composition-categories">
                            <div class="poste-section">
                                <h4>Première ligne</h4>
                                <div class="joueurs-list" id="titulaires-premiere-ligne" data-max="3">
                                    <p class="empty-message">Glissez les joueurs ici (max 3)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Deuxième ligne</h4>
                                <div class="joueurs-list" id="titulaires-deuxieme-ligne" data-max="2">
                                    <p class="empty-message">Glissez les joueurs ici (max 2)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Troisième ligne</h4>
                                <div class="joueurs-list" id="titulaires-troisieme-ligne" data-max="3">
                                    <p class="empty-message">Glissez les joueurs ici (max 3)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Demis</h4>
                                <div class="joueurs-list" id="titulaires-demis" data-max="2">
                                    <p class="empty-message">Glissez les joueurs ici (max 2)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Trois-quarts</h4>
                                <div class="joueurs-list" id="titulaires-trois-quarts" data-max="4">
                                    <p class="empty-message">Glissez les joueurs ici (max 4)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Arrière</h4>
                                <div class="joueurs-list" id="titulaires-arriere" data-max="1">
                                    <p class="empty-message">Glissez les joueurs ici (max 1)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="remplacants">
                        <h3>Remplaçants</h3>
                        <div class="composition-categories">
                            <div class="poste-section">
                                <h4>Première ligne</h4>
                                <div class="joueurs-list" id="remplacants-premiere-ligne" data-max="2">
                                    <p class="empty-message">Glissez les joueurs ici (max 2)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Deuxième ligne</h4>
                                <div class="joueurs-list" id="remplacants-deuxieme-ligne" data-max="1">
                                    <p class="empty-message">Glissez les joueurs ici (max 1)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Troisième ligne</h4>
                                <div class="joueurs-list" id="remplacants-troisieme-ligne" data-max="2">
                                    <p class="empty-message">Glissez les joueurs ici (max 2)</p>
                                </div>
                            </div>
                            <div class="poste-section">
                                <h4>Demis/Trois-quarts/Arrière</h4>
                                <div class="joueurs-list" id="remplacants-backs" data-max="3">
                                    <p class="empty-message">Glissez les joueurs ici (max 3)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="buttons-container">
            <button class="btn-submit" onclick="sauvegarderComposition()">Enregistrer la composition</button>
        </div>
    </div>
</div>

<!-- Modal Joueur -->
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
                <input type="text" id="taille" name="taille" pattern="[0-9]+([.,][0-9]+)?" required>
            </div>
            <div class="form-group">
                <label for="poids">Poids (kg):</label>
                <input type="text" id="poids" name="poids" pattern="[0-9]+([.,][0-9]+)?" required>
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
                <select id="poste_prefere" name="poste_prefere" required>
                    <option value="Pilier">Pilier</option>
                    <option value="Talonneur">Talonneur</option>
                    <option value="Deuxième ligne">Deuxième ligne</option>
                    <option value="Troisième ligne">Troisième ligne</option>
                    <option value="Demi de mêlée">Demi de mêlée</option>
                    <option value="Demi d'ouverture">Demi d'ouverture</option>
                    <option value="Centre">Centre</option>
                    <option value="Ailier">Ailier</option>
                    <option value="Arrière">Arrière</option>
                </select>
            </div>
            <div class="form-group">
                <label for="commentaires">Commentaires (optionnel):</label>
                <textarea id="commentaires" name="commentaires" rows="5" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #2b2b2b; color: #fff;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px;">Sauvegarder</button>
        </form>
    </div>
</div>


<script>

    // Fonction pour ajouter un commentaire
    function posterCommentaire() {
        const commentaireInput = document.getElementById('commentaire');
        const commentaireTexte = commentaireInput.value;

        if (!commentaireTexte.trim()) {
            alert('Le commentaire ne peut pas être vide.');
            return;
        }

        // Obtenir la date et l'heure actuelles
        const maintenant = new Date();
        const dateTexte = maintenant.toLocaleDateString();
        const heureTexte = maintenant.toLocaleTimeString();

        // Créer un élément pour le commentaire
        const commentaireElement = document.createElement('li');
        commentaireElement.innerHTML = `
            <p>${commentaireTexte}</p>
            <span class="date">Posté le ${dateTexte} à ${heureTexte}</span>
        `;

        // Ajouter le commentaire à la liste
        const listeCommentaires = document.getElementById('commentaires');
        listeCommentaires.appendChild(commentaireElement);

        // Réinitialiser le champ de commentaire
        commentaireInput.value = '';
    }
</script>



<!-- Modal Statistiques -->
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
                            <span class="stat-value"><?= htmlspecialchars($stats['total_matchs']) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Victoires</span>
                            <span class="stat-value"><?= htmlspecialchars($stats['victoires']) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Défaites</span>
                            <span class="stat-value"><?= htmlspecialchars($stats['defaites']) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Nuls</span>
                            <span class="stat-value"><?= htmlspecialchars($stats['nuls']) ?></span>
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

<!-- Modal Statistiques Joueur -->
<div id="modalStatsJoueur" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fermerModalStatsJoueur()">&times;</span>
        <h2>Statistiques du joueur</h2>
        <div id="joueurMatchInfo"></div>
        <form id="formStatsJoueur" onsubmit="sauvegarderStatsJoueur(event)">
            <input type="hidden" id="stats_match_id" name="match_id">
            <input type="hidden" id="stats_joueur_id" name="joueur_id">
            
            <div class="form-group">
                <label for="temps_jeu">Temps de jeu (minutes):</label>
                <input type="number" id="temps_jeu" name="temps_jeu" min="0" max="80" required>
            </div>
            
            <div class="form-group">
                <label for="essais">Essais:</label>
                <input type="number" id="essais" name="essais" min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="passes_decisives">Passes décisives:</label>
                <input type="number" id="passes_decisives" name="passes_decisives" min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="plaquages_reussis">Plaquages réussis:</label>
                <input type="number" id="plaquages_reussis" name="plaquages_reussis" min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="turnovers_gagnes">Turnovers gagnés:</label>
                <input type="number" id="turnovers_gagnes" name="turnovers_gagnes" min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="metres_gagnes">Mètres gagnés:</label>
                <input type="number" id="metres_gagnes" name="metres_gagnes" min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="defenseurs_battus">Défenseurs battus:</label>
                <input type="number" id="defenseurs_battus" name="defenseurs_battus" min="0" value="0">
            </div>
            
            <button type="submit" class="btn-submit">Enregistrer les statistiques</button>
        </form>
    </div>
</div>
