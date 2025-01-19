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
    if (match === null) {
        const modal = document.getElementById('modalMatch');
        document.getElementById('formAjoutMatch').reset();
        modal.style.display = 'block';
    } else {
        // Ouvrir directement le modal de modification
        modifierMatch(event, match);
    }
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

        // Afficher le modal
        modal.style.display = 'block';
    } catch (error) {
        console.error('Erreur lors de l\'ouverture du modal:', error);
        console.error('Match data:', match);
    }
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
    modal.style.display = 'none';
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
    modal.style.display = 'block';
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
            document.getElementById(modalId).style.display = 'none';
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
    
    // Remplir les informations du match
    document.getElementById('equipe_adverse_feuille').textContent = match.equipe_adverse;
    document.getElementById('date_feuille').textContent = new Date(match.date).toLocaleDateString();
    document.getElementById('heure_feuille').textContent = match.heure;
    document.getElementById('lieu_feuille').textContent = match.lieu;

    // Charger les joueurs disponibles
    chargerJoueursDisponibles(match.id);
    
    // Initialiser le drag & drop
    initializeDragAndDrop();
    
    modal.style.display = 'block';
}

function initializeDragAndDrop() {
    const joueurs = document.querySelectorAll('.joueur-item');
    const zones = document.querySelectorAll('.joueurs-list');

    joueurs.forEach(joueur => {
        joueur.setAttribute('draggable', true);
        joueur.addEventListener('dragstart', handleDragStart);
        joueur.addEventListener('dragend', handleDragEnd);
    });

    zones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
    });
}

function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', e.target.id);
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDrop(e) {
    e.preventDefault();
    const joueurId = e.dataTransfer.getData('text/plain');
    const joueur = document.getElementById(joueurId);
    const zone = e.target.closest('.joueurs-list');
    
    if (zone && joueur) {
        // Vérifier les limites (15 titulaires max, 8 remplaçants max)
        if (zone.id === 'joueursTitulaires' && zone.children.length >= 15) {
            alert('Maximum 15 titulaires atteint');
            return;
        }
        if (zone.id === 'joueursRemplacants' && zone.children.length >= 8) {
            alert('Maximum 8 remplaçants atteint');
            return;
        }
        
        zone.appendChild(joueur);
        
        // Cacher le message "empty" si présent
        const emptyMessage = zone.querySelector('.empty-message');
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
    }
}

function sauvegarderComposition() {
    const titulaires = [...document.getElementById('joueursTitulaires').children]
        .filter(el => el.classList.contains('joueur-item'))
        .map(el => el.dataset.joueurId);
    
    const remplacants = [...document.getElementById('joueursRemplacants').children]
        .filter(el => el.classList.contains('joueur-item'))
        .map(el => el.dataset.joueurId);
    
    // TODO: Ajouter l'appel API pour sauvegarder la composition
    console.log('Titulaires:', titulaires);
    console.log('Remplaçants:', remplacants);
    
    alert('Composition sauvegardée !');
    fermerModalFeuilleMatch();
}

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
                    joueurElement.id = `joueur-${joueur.id}`; // Ajouter un ID unique
                    joueurElement.setAttribute('draggable', true); // Rendre l'élément draggable
                    joueurElement.dataset.joueurId = joueur.id; // Stocker l'ID du joueur
                    joueurElement.innerHTML = `
                        <span class="joueur-nom">${joueur.nom} ${joueur.prenom}</span>
                        <span class="joueur-poste">${joueur.poste_prefere}</span>
                    `;
                    
                    // Ajouter les événements de drag
                    joueurElement.addEventListener('dragstart', handleDragStart);
                    joueurElement.addEventListener('dragend', handleDragEnd);
                    
                    container.appendChild(joueurElement);
                });

                // Initialiser les zones de drop
                initializeDropZones();
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function initializeDropZones() {
    const zones = ['joueursDisponibles', 'joueursTitulaires', 'joueursRemplacants'];
    
    zones.forEach(zoneId => {
        const zone = document.getElementById(zoneId);
        if (zone) {
            zone.addEventListener('dragover', handleDragOver);
            zone.addEventListener('drop', handleDrop);
        }
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
    const zoneDepot = e.target.closest('.joueurs-list');
    
    if (joueurElement && zoneDepot) {
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
