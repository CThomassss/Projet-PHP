// Gestion des onglets
document.addEventListener('DOMContentLoaded', function() {
    initializeTabButtons();
    initializeCategoryButtons();
});

function initializeTabButtons() {
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.matches-container').forEach(c => c.classList.remove('active'));
            
            button.classList.add('active');
            document.querySelector(`.matches-container.${button.dataset.tab}`).classList.add('active');
        });
    });
}

// Fonctions pour les matchs
function ouvrirModalMatch(match = null) {
    const modal = document.getElementById('modalMatch');
    if (match === null) {
        document.getElementById('formAjoutMatch').reset();
    }
    modal.classList.add('active');
}

function fermerModalMatch() {
    const modal = document.getElementById('modalMatch');
    modal.classList.remove('active');
}

function ouvrirModalScore(match) {
    console.log('Match data received:', match); // Débogage

    try {
        // Si match est une chaîne JSON, la parser
        if (typeof match === 'string') {
            match = JSON.parse(match);
        }

        const modal = document.getElementById('modalScore');
        if (!modal) {
            console.error('Modal element not found');
            return;
        }

        // Remplir les champs du modal avec les données du match
        document.getElementById('score_match_id').value = match.id;
        document.getElementById('score_equipe_adverse_display').textContent = match.equipe_adverse;
        document.getElementById('score_date_display').textContent = new Date(match.date).toLocaleDateString();
        document.getElementById('score_lieu_display').textContent = match.lieu;
        document.getElementById('score_resultat').value = match.resultat || '';

        // Charger la composition de l'équipe
        chargerCompositionMatch(match.id);

        // Afficher le modal
        modal.classList.add('active');
    } catch (error) {
        console.error('Erreur lors de l\'ouverture du modal:', error);
        console.error('Match data:', match);
    }
}

function chargerCompositionMatch(matchId) {
    fetch(`matchs/get_composition.php?match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les joueurs dans la modal de résultat
                const titulairesContainer = document.getElementById('score_titulaires');
                const remplacantsContainer = document.getElementById('score_remplacants');
                
                // Afficher les titulaires
                titulairesContainer.innerHTML = '<h4>Titulaires</h4>';
                data.titulaires.forEach(joueur => {
                    const joueurElement = document.createElement('div');
                    joueurElement.className = 'player-item';
                    joueurElement.innerHTML = `
                        <span class="player-name">${joueur.nom} ${joueur.prenom}</span>
                        <span class="player-poste">${joueur.poste}</span>
                    `;
                    titulairesContainer.appendChild(joueurElement);
                });

                // Afficher les remplaçants
                remplacantsContainer.innerHTML = '<h4>Remplaçants</h4>';
                data.remplacants.forEach(joueur => {
                    const joueurElement = document.createElement('div');
                    joueurElement.className = 'player-item';
                    joueurElement.innerHTML = `
                        <span class="player-name">${joueur.nom} ${joueur.prenom}</span>
                        <span class="player-poste">${joueur.poste}</span>
                    `;
                    remplacantsContainer.appendChild(joueurElement);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function afficherJoueurs(containerId, joueurs) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    if (joueurs.length === 0) {
        container.innerHTML = '<p class="empty-message">Aucun joueur sélectionné</p>';
        return;
    }

    joueurs.forEach(joueur => {
        const joueurElement = document.createElement('div');
        joueurElement.className = 'player-item';
        joueurElement.innerHTML = `
            <span class="player-name">${joueur.nom} ${joueur.prenom}</span>
            <span class="player-poste">${joueur.poste_prefere}</span>
        `;
        
        // Ajouter l'événement de clic pour ouvrir le modal des statistiques
        joueurElement.addEventListener('click', () => {
            ouvrirModalStatsJoueur(joueur, document.getElementById('score_match_id').value);
        });
        
        container.appendChild(joueurElement);
    });
}

function ouvrirModalStatsJoueur(joueur, matchId) {
    const modal = document.getElementById('modalStatsJoueur');
    const joueurInfo = document.getElementById('joueurMatchInfo');
    
    // Afficher les informations du joueur
    joueurInfo.innerHTML = `
        <p><strong>${joueur.nom} ${joueur.prenom}</strong></p>
        <p>Poste: ${joueur.poste_prefere}</p>
    `;
    
    // Remplir les champs cachés
    document.getElementById('stats_match_id').value = matchId;
    document.getElementById('stats_joueur_id').value = joueur.id;
    
    // Charger les statistiques existantes si disponibles
    chargerStatsJoueur(joueur.id, matchId);
    
    modal.style.display = 'block';
}

function chargerStatsJoueur(joueurId, matchId) {
    fetch(`matchs/get_stats_joueur.php?joueur_id=${joueurId}&match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                // Remplir le formulaire avec les statistiques existantes
                Object.keys(data.stats).forEach(key => {
                    const input = document.getElementById(key);
                    if (input) {
                        input.value = data.stats[key];
                    }
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

async function sauvegarderStatsJoueur(event) {
    event.preventDefault();
    
    try {
        const formData = new FormData(document.getElementById('formStatsJoueur'));
        
        const response = await fetch('matchs/save_stats_joueur.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            fermerModalStatsJoueur();
            // Optionnel : recharger les statistiques affichées
            chargerCompositionMatch(formData.get('match_id'));
        } else {
            alert(data.message || 'Erreur lors de la sauvegarde des statistiques');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde des statistiques');
    }
}

function fermerModalStatsJoueur() {
    const modal = document.getElementById('modalStatsJoueur');
    modal.style.display = 'none';
}

async function modifierScore(event) {
    event.preventDefault();
    
    try {
        const formData = new FormData(document.getElementById('formModifierScore'));
        
        const response = await fetch('matchs/update_score.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            // Fermer le modal et rafraîchir la page
            fermerModalScore();
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la mise à jour du score');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour du score');
    }
}

function fermerModalScore() {
    const modal = document.getElementById('modalScore');
    modal.classList.remove('active');
}

// Fonctions pour les joueurs
function modifierJoueur(joueur) {
    const modal = document.getElementById('modalJoueur');
    const form = document.getElementById('formModifierJoueur');
    
    Object.keys(joueur).forEach(key => {
        const input = form.elements[key];
        if (input) {
            input.value = joueur[key];
        }
    });
    
    // Charger les commentaires du joueur
    chargerCommentaires(joueur.id);
    
    modal.style.display = 'block';
}

function ouvrirModalAjoutJoueur() {
    const modal = document.getElementById('modalJoueur');
    const form = document.getElementById('formModifierJoueur');
    
    // Réinitialiser le formulaire
    form.reset();
    
    // Retirer l'ID du joueur pour indiquer qu'il s'agit d'un nouvel enregistrement
    document.getElementById('joueur_id').value = '';
    
    // Afficher le modal
    modal.classList.add('active'); // Changé de style.display = 'block' à classList.add('active')
}

function fermerModalJoueur() {
    const modal = document.getElementById('modalJoueur');
    modal.classList.remove('active'); // Changé de style.display = 'none' à classList.remove('active')
}

function supprimerJoueur(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?')) {
        fetch('joueurs/delete_joueur.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression du joueur');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression du joueur');
        });
    }
}

// ...existing code for all player-related functions...

async function sauvegarderJoueur(event) {
    event.preventDefault();
    
    try {
        const formData = new FormData(document.getElementById('formModifierJoueur'));
        
        const response = await fetch('joueurs/save_joueur.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            // Fermer le modal et rafraîchir la page
            fermerModalJoueur();
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la sauvegarde du joueur');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde du joueur');
    }
}

// Fonctions pour les statistiques
function ouvrirModalStats() {
    document.getElementById('modalStats').style.display = 'block';
}

function fermerModalStats() {
    document.getElementById('modalStats').style.display = 'none';
}

// Gestion des catégories
function initializeCategoryButtons() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const playerRows = document.querySelectorAll('.players-table tbody tr');

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const selectedCategory = button.dataset.category;
            
            playerRows.forEach(row => {
                row.style.display = selectedCategory === 'all' || row.dataset.category === selectedCategory ? '' : 'none';
            });
        });
    });
}

// Gestionnaire de clics global pour les modals
window.onclick = function(event) {
    const modals = ['modalMatch', 'modalScore', 'modalFeuilleMatch', 'modalJoueur', 'modalStats'];
    modals.forEach(modalId => {
        if (event.target == document.getElementById(modalId)) {
            document.getElementById(modalId).classList.remove('active');
        }
    });
}

// ...existing code...

function modifierMatch(event, match) {
    // Empêcher la propagation de l'événement
    event.preventDefault();
    event.stopPropagation();
    
    // Ouvrir le modal de modification
    const modal = document.getElementById('modalEditMatch');
    
    // Remplir le formulaire avec les données du match
    document.getElementById('edit_match_id').value = match.id;
    document.getElementById('edit_date').value = match.date;
    document.getElementById('edit_heure').value = match.heure;
    document.getElementById('edit_equipe_adverse').value = match.equipe_adverse;
    document.getElementById('edit_lieu').value = match.lieu;
    
    // Afficher le modal
    modal.style.display = 'block';
}

function fermerModalEditMatch() {
    const modal = document.getElementById('modalEditMatch');
    modal.style.display = 'none';
}

async function sauvegarderModifMatch(event) {
    event.preventDefault();
    
    try {
        const formData = new FormData(document.getElementById('formEditMatch'));
        
        const response = await fetch('matchs/update_match.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            fermerModalEditMatch();
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la modification du match');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du match');
    }
}

function supprimerMatch() {
    const matchId = document.getElementById('edit_match_id').value;
    
    if (confirm('Êtes-vous sûr de vouloir supprimer ce match ?')) {
        fetch('matchs/delete_match.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: matchId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fermerModalEditMatch();
                window.location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression du match');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression du match');
        });
    }
}

// ...existing code...

function posterCommentaire() {
    const joueurId = document.getElementById('joueur_id').value;
    const commentaireInput = document.getElementById('commentaire');
    const commentaireTexte = commentaireInput.value;

    if (!commentaireTexte.trim()) {
        alert('Le commentaire ne peut pas être vide.');
        return;
    }

    const formData = new FormData();
    formData.append('joueur_id', joueurId);
    formData.append('commentaire', commentaireTexte);

    fetch('joueurs/add_commentaire.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ajouter le nouveau commentaire à la liste
            const maintenant = new Date();
            const commentaireElement = document.createElement('li');
            commentaireElement.innerHTML = `
                <p>${commentaireTexte}</p>
                <span class="date">Posté le ${maintenant.toLocaleDateString()} à ${maintenant.toLocaleTimeString()}</span>
            `;
            document.getElementById('commentaires').prepend(commentaireElement);
            commentaireInput.value = '';
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du commentaire');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout du commentaire');
    });
}

function chargerCommentaires(joueurId) {
    fetch(`joueurs/get_commentaires.php?joueur_id=${joueurId}`)
        .then(response => response.json())
        .then(data => {
            const listeCommentaires = document.getElementById('commentaires');
            listeCommentaires.innerHTML = ''; // Vider la liste existante
            
            if (data.success && data.commentaires) {
                data.commentaires.forEach(commentaire => {
                    const commentaireElement = document.createElement('li');
                    commentaireElement.innerHTML = `
                        <p>${commentaire.commentaire}</p>
                        <span class="date">Posté le ${new Date(commentaire.date_creation).toLocaleDateString()} 
                        à ${new Date(commentaire.date_creation).toLocaleTimeString()}</span>
                    `;
                    listeCommentaires.appendChild(commentaireElement);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// ...existing code...

function ouvrirFeuilleMatch(match) {
    const modal = document.getElementById('modalFeuilleMatch');
    
    // Stocker l'ID du match dans un champ caché
    const matchId = match.id;
    modal.querySelector('.modal-content').setAttribute('data-match-id', matchId);
    
    // Remplir les informations du match
    document.getElementById('equipe_adverse_feuille').textContent = match.equipe_adverse;
    document.getElementById('date_feuille').textContent = new Date(match.date).toLocaleDateString();
    document.getElementById('heure_feuille').textContent = match.heure;
    document.getElementById('lieu_feuille').textContent = match.lieu;

    // Charger les joueurs disponibles
    chargerJoueursDisponibles(matchId);
    
    // Charger la composition existante si disponible
    chargerCompositionMatch(matchId);
    
    modal.style.display = 'block';
    
    // Initialiser le drag and drop après l'affichage du modal
    setTimeout(() => {
        initializeDropZones();
        verifierDragAndDrop();
    }, 500);
}

function initializeDragAndDrop() {
    // Sélectionner tous les éléments joueur
    const joueurs = document.querySelectorAll('.joueur-item');
    const zones = document.querySelectorAll('.joueurs-list');

    joueurs.forEach(joueur => {
        // Configurer les attributs et événements de drag pour chaque joueur
        joueur.setAttribute('draggable', true);
        joueur.addEventListener('dragstart', handleDragStart);
        joueur.addEventListener('dragend', handleDragEnd);
    });

    zones.forEach(zone => {
        // Configurer les événements pour les zones de dépôt
        zone.addEventListener('dragenter', handleDragEnter);
        zone.addEventListener('dragleave', handleDragLeave);
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
    });
}

function handleDragStart(e) {
    e.stopPropagation();
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', e.target.id);
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    e.stopPropagation();
    e.target.classList.remove('dragging');
    document.querySelectorAll('.joueurs-list').forEach(zone => {
        zone.classList.remove('drag-over');
    });
}

function handleDragEnter(e) {
    e.preventDefault();
    e.target.closest('.joueurs-list')?.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.target.closest('.joueurs-list')?.classList.remove('drag-over');
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const zone = e.target.closest('.joueurs-list');
    if (!zone) return;
    
    zone.classList.remove('drag-over');
    const joueurId = e.dataTransfer.getData('text/plain');
    const joueur = document.getElementById(joueurId);
    
    if (!joueur || !zone) return;

    // Vérifier les limites de joueurs
    const maxJoueurs = zone.dataset.max ? parseInt(zone.dataset.max) : Infinity;
    const joueursActuels = zone.querySelectorAll('.joueur-item').length;
    
<<<<<<< HEAD
    // Récupérer tous les joueurs des différentes zones
    const composition = {
        match_id: Number(matchId),
        titulaires: [],
        remplacants: []
    };

    // Récupérer les titulaires de chaque poste
    ['premiere-ligne', 'deuxieme-ligne', 'troisieme-ligne', 'demis', 'trois-quarts', 'arriere'].forEach(poste => {
        const joueurs = [...document.getElementById(`titulaires-${poste}`).children]
            .filter(el => el.classList.contains('joueur-item'))
            .map(el => ({
                joueur_id: Number(el.dataset.joueurId),
                titulaire: 1,
                remplacant: 0
            }));
        composition.titulaires.push(...joueurs);
    });

    // Récupérer les remplaçants
    ['premiere-ligne', 'deuxieme-ligne', 'troisieme-ligne', 'backs'].forEach(poste => {
        const joueurs = [...document.getElementById(`remplacants-${poste}`).children]
            .filter(el => el.classList.contains('joueur-item'))
            .map(el => ({
                joueur_id: Number(el.dataset.joueurId),
                titulaire: 0,
                remplacant: 1
            }));
        composition.remplacants.push(...joueurs);
    });

    // Vérifier les limites
    if (!verifierLimitesComposition(composition)) {
        alert('Veuillez respecter les limites du nombre de joueurs par poste');
        return;
    }

    // Envoyer la composition
    fetch('matchs/sauvegarder_composition.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(composition)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Composition sauvegardée avec succès !');
            fermerModalFeuilleMatch();
        } else {
            alert(data.message || 'Erreur lors de la sauvegarde de la composition');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde de la composition');
    });
}

function verifierLimitesComposition(composition) {
    const limites = {
        titulaires: {
            premiere_ligne: 3,
            deuxieme_ligne: 2,
            troisieme_ligne: 3,
            demis: 2,
            trois_quarts: 4,
            arriere: 1
        },
        remplacants: {
            premiere_ligne: 2,
            deuxieme_ligne: 1,
            troisieme_ligne: 2,
            backs: 3
        }
    };

    // Vérifier les titulaires
    for (const [poste, limite] of Object.entries(limites.titulaires)) {
        if (composition.titulaires.filter(j => j.poste === poste).length > limite) {
            return false;
        }
    }

    // Vérifier les remplaçants
    for (const [poste, limite] of Object.entries(limites.remplacants)) {
        if (composition.remplacants.filter(j => j.poste === poste).length > limite) {
            return false;
        }
    }

    return true;
=======
    if (joueursActuels >= maxJoueurs) {
        alert(`Maximum ${maxJoueurs} joueurs dans cette zone`);
        return;
    }

    // Déplacer le joueur
    zone.appendChild(joueur);

    // Cacher le message "empty" s'il existe
    const emptyMessage = zone.querySelector('.empty-message');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
>>>>>>> 547820bf5c0368e562e5988519a94626cc6b410a
}

// Assurer que le drag and drop est initialisé après le chargement des joueurs
function chargerJoueursDisponibles(matchId) {
    fetch(`matchs/get_joueurs_disponibles.php?match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('joueursDisponibles');
                container.innerHTML = ''; // Vider la liste existante
                
                data.joueurs.forEach(joueur => {
                    const joueurElement = document.createElement('div');
                    joueurElement.className = 'joueur-item';
                    joueurElement.id = `joueur-${joueur.id}`;
                    joueurElement.dataset.joueurId = joueur.id;
                    joueurElement.dataset.poste = joueur.poste_prefere.toLowerCase().replace(/\s+/g, '-');
                    joueurElement.innerHTML = `
                        <span class="joueur-nom">${joueur.nom} ${joueur.prenom}</span>
                        <span class="joueur-poste">${joueur.poste_prefere}</span>
                    `;
<<<<<<< HEAD
                    
                    // Ajouter les événements de drag and drop
                    joueurElement.addEventListener('dragstart', function(e) {
                        e.dataTransfer.setData('text/plain', this.id);
                        this.classList.add('dragging');
                    });
                    
                    joueurElement.addEventListener('dragend', function() {
                        this.classList.remove('dragging');
                    });
                    
=======
>>>>>>> 547820bf5c0368e562e5988519a94626cc6b410a
                    container.appendChild(joueurElement);
                });

                // Initialiser le drag and drop après avoir chargé les joueurs
                initializeDragAndDrop();
                initializeMatchCategoryButtons();
<<<<<<< HEAD
                
                // Initialiser les zones de drop
                initializeDropZones();
                
                console.log("Drag and drop initialisé pour les joueurs disponibles");
=======
>>>>>>> 547820bf5c0368e562e5988519a94626cc6b410a
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function initializeMatchCategoryButtons() {
    const categoryButtons = document.querySelectorAll('.category-filters-match .category-btn');
    const joueurs = document.querySelectorAll('#joueursDisponibles .joueur-item');

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Gérer l'état actif des boutons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const selectedCategory = button.dataset.category;
            
            // Filtrer les joueurs
            joueurs.forEach(joueur => {
                if (selectedCategory === 'all') {
                    joueur.style.display = '';
                } else {
                    const posteJoueur = joueur.dataset.poste;
                    joueur.style.display = matchPosteToCategory(posteJoueur, selectedCategory) ? '' : 'none';
                }
            });
        });
    });
}

function matchPosteToCategory(poste, category) {
    const categories = {
        'premiere-ligne': ['pilier', 'talonneur'],
        'deuxieme-ligne': ['deuxième-ligne'],
        'troisieme-ligne': ['troisième-ligne', 'flanker', 'numéro-8'],
        'demis': ['demi-de-mêlée', 'demi-d\'ouverture'],
        'trois-quarts': ['centre', 'ailier'],
        'arriere': ['arrière']
    };
    
    return categories[category]?.some(p => poste.includes(p.toLowerCase())) || false;
}

function initializeDropZones() {
    // Sélectionner toutes les zones de dépôt dans la feuille de match
    const zones = document.querySelectorAll('.joueurs-list');
    
    zones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
    });
}

function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', e.target.id);
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    document.querySelectorAll('.joueur-item').forEach(item => {
        item.classList.remove('drag-over');
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDrop(e) {
    e.preventDefault();
    const joueurId = e.dataTransfer.getData('text/plain');
    const joueurElement = document.getElementById(joueurId);
    
    // Trouver la zone de dépôt la plus proche
    let zoneDepot = e.target;
    if (!zoneDepot.classList.contains('joueurs-list')) {
        zoneDepot = e.target.closest('.joueurs-list');
    }
    
    if (joueurElement && zoneDepot) {
        // Vérifier les limites pour les titulaires et remplaçants
        if (zoneDepot.id === 'joueursTitulaires') {
            const nombreTitulaires = zoneDepot.querySelectorAll('.joueur-item').length;
            if (nombreTitulaires >= 15) {
                alert('Maximum 15 titulaires atteint');
                return;
            }
        }
        else if (zoneDepot.id === 'joueursRemplacants') {
            const nombreRemplacants = zoneDepot.querySelectorAll('.joueur-item').length;
            if (nombreRemplacants >= 8) {
                alert('Maximum 8 remplaçants atteint');
                return;
            }
        }
        // Vérifier les limites pour les postes spécifiques
        else if (zoneDepot.hasAttribute('data-max')) {
            const maxJoueurs = parseInt(zoneDepot.getAttribute('data-max'));
            const nombreJoueurs = zoneDepot.querySelectorAll('.joueur-item').length;
            if (nombreJoueurs >= maxJoueurs) {
                alert(`Maximum ${maxJoueurs} joueur(s) atteint pour ce poste`);
                return;
            }
        }

        // Supprimer le message "empty" s'il existe
        const emptyMessage = zoneDepot.querySelector('.empty-message');
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }

        // Déplacer le joueur
        zoneDepot.appendChild(joueurElement);
    }
}

// ...existing code...

function fermerModalFeuilleMatch() {
    const modal = document.getElementById('modalFeuilleMatch');
    if (modal) {
        modal.style.display = 'none';
        
        // Réinitialiser les listes
        const titulaires = document.getElementById('joueursTitulaires');
        const remplacants = document.getElementById('joueursRemplacants');
        const disponibles = document.getElementById('joueursDisponibles');
        
        if (titulaires) titulaires.innerHTML = '<p class="empty-message">Glissez les joueurs ici</p>';
        if (remplacants) remplacants.innerHTML = '<p class="empty-message">Glissez les joueurs ici</p>';
        if (disponibles) disponibles.innerHTML = '';
    }
}

// ...existing code...

async function ajouterMatch(event) {
    event.preventDefault();
    
    try {
        const formData = new FormData(document.getElementById('formAjoutMatch'));
        
        const response = await fetch('matchs/ajouter.php', { // Changé de create_match.php à ajouter.php
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            // Fermer le modal et rafraîchir la page
            fermerModalMatch();
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la création du match');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la création du match');
    }
}

// ...existing code...

// Fonction pour vérifier et réinitialiser le drag and drop
function verifierDragAndDrop() {
    console.log("Vérification du drag and drop...");
    
    // Vérifier si les éléments draggable sont correctement initialisés
    const joueurs = document.querySelectorAll('.joueur-item');
    joueurs.forEach(joueur => {
        if (!joueur.getAttribute('draggable')) {
            console.log("Réinitialisation de l'élément draggable:", joueur.id);
            joueur.setAttribute('draggable', true);
            
            // Supprimer les anciens écouteurs d'événements pour éviter les doublons
            joueur.removeEventListener('dragstart', handleDragStart);
            joueur.removeEventListener('dragend', handleDragEnd);
            
            // Ajouter les nouveaux écouteurs d'événements
            joueur.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', this.id);
                this.classList.add('dragging');
            });
            
            joueur.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
        }
    });
    
    // Réinitialiser les zones de drop
    initializeDropZones();
}

// Ajouter un bouton pour réinitialiser le drag and drop
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un bouton de réinitialisation dans la feuille de match
    const modalFeuilleMatch = document.getElementById('modalFeuilleMatch');
    if (modalFeuilleMatch) {
        const buttonsContainer = modalFeuilleMatch.querySelector('.buttons-container');
        if (buttonsContainer) {
            const resetButton = document.createElement('button');
            resetButton.className = 'btn-secondary';
            resetButton.textContent = 'Réinitialiser le drag and drop';
            resetButton.onclick = verifierDragAndDrop;
            buttonsContainer.appendChild(resetButton);
        }
    }
});
