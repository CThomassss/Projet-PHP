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
        const modal = document.getElementById('modalFeuilleMatch');
        document.getElementById('fm_match_id').value = match.id;
        document.getElementById('fm_date_match').textContent = new Date(match.date).toLocaleDateString() + ' ' + match.heure;
        document.getElementById('fm_equipe_adverse').textContent = match.equipe_adverse;
        document.getElementById('fm_lieu').textContent = match.lieu;
        
        chargerJoueursDisponibles(match.id);
        modal.style.display = 'block';
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

// Fonctions pour la feuille de match
function creerElementJoueur(joueur) {
    const div = document.createElement('div');
    div.className = 'joueur-item';
    div.draggable = true;
    div.dataset.joueurId = joueur.id;
    div.innerHTML = `
        <div class="joueur-details">
            <div class="joueur-nom">${joueur.nom} ${joueur.prenom}</div>
            <div class="joueur-poste">${joueur.poste_prefere || ''}</div>
        </div>
    `;
    return div;
}

async function chargerJoueursDisponibles(matchId) {
    try {
        const response = await fetch(`matchs/get_joueurs_disponibles.php?match_id=${matchId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Erreur lors du chargement des joueurs');
        }

        const listeTitulaires = document.getElementById('liste_titulaires');
        const listeRemplacants = document.getElementById('liste_remplacants');
        const listeDisponibles = document.getElementById('liste_disponibles');
        
        // Vider les listes
        listeTitulaires.innerHTML = '';
        listeRemplacants.innerHTML = '';
        listeDisponibles.innerHTML = '';

        // Log pour débugger
        console.log('Joueurs reçus:', data.joueurs);
        
        // Distribuer les joueurs dans les bonnes listes
        data.joueurs.forEach(joueur => {
            const joueurElement = creerElementJoueur(joueur);
            
            switch (joueur.statut_match) {
                case 'titulaire':
                    listeTitulaires.appendChild(joueurElement);
                    break;
                case 'remplacant':
                    listeRemplacants.appendChild(joueurElement);
                    break;
                default:
                    if (!joueur.est_selectionne) {
                        listeDisponibles.appendChild(joueurElement);
                    }
                    break;
            }
        });
        
        rendreJoueursDraggable();
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des joueurs');
    }
}

function rendreJoueursDraggable() {
    const joueursItems = document.querySelectorAll('.joueur-item');
    const zones = document.querySelectorAll('.joueurs-list');
    
    joueursItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
    
    zones.forEach(zone => {
        zone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragleave', (e) => {
            zone.classList.remove('drag-over');
        });
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
    });
}

// ...existing code for all drag and drop functions...

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
