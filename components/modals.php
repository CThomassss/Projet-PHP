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

<!-- Modal Score -->
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

<!-- Modal Feuille de Match -->
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
                    <h3>Titulaires (max 15)</h3>
                    <div class="joueurs-list" id="liste_titulaires"></div>
                </div>
                <div class="remplacants">
                    <h3>Remplaçants (max 8)</h3>
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

            <div class="form-group">
    <label for="commentaire">Commentaire</label>
    <textarea id="commentaire" name="commentaire" rows="4" required style="width: 100%; resize: vertical;"></textarea>
</div>

            <button type="submit" class="btn-submit">Enregistrer</button>
        </form>
    </div>
</div>

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
