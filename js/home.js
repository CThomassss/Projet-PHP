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
    
    modal.style.display = 'block';
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
