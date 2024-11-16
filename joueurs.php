<?php
require_once 'config/database.php';
require_once 'lib/functions.php';

$joueurs = getJoueurs($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Joueurs - Rugby Manager</title>
    <link rel="stylesheet" href="css/joueurs.css">
</head>
<body>
    <div id="container">
        <div id="listeJoueurs">
            <h1>Gestion des Joueurs</h1>
            <a href="joueurs/ajouter.php" class="btn btn-primary">Ajouter un nouveau joueur</a>
            <table>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($joueurs as $joueur): ?>
                    <tr data-joueur-id="<?= $joueur['id'] ?>">
                        <td><?= htmlspecialchars($joueur['nom']) ?></td>
                        <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                        <td><?= htmlspecialchars($joueur['numero_licence']) ?></td>
                        <td><?= htmlspecialchars($joueur['date_naissance']) ?></td>
                        <td><?= htmlspecialchars($joueur['taille']) ?> cm</td>
                        <td><?= htmlspecialchars($joueur['poids']) ?> kg</td>
                        <td><?= htmlspecialchars($joueur['statut']) ?></td>
                        <td><?= htmlspecialchars($joueur['poste_prefere']) ?></td>
                        <td>
                            <button onclick="afficherMenu(<?= $joueur['id'] ?>)" class="btn btn-secondary">Modifier</button>
                            <a href="joueurs/supprimer.php?id=<?= $joueur['id'] ?>" class="btn btn-danger">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="menuDroit">
            <div id="menuModification" class="menu-modification">
                <h3>Modification du joueur</h3> <!-- Changé de h2 à h3 pour meilleure hiérarchie -->
                <div id="optionsModification">
                    <button onclick="modifierInfosGenerales()" class="btn btn-secondary">Informations générales</button>
                    <button onclick="modifierStatut()" class="btn btn-secondary">Statut</button>
                    <button onclick="modifierPoste()" class="btn btn-secondary">Poste</button>
                    <button onclick="modifierCommentaires()" class="btn btn-secondary">Commentaires</button>
                </div>
                <div id="formContent"></div>
                <div class="btn-fermer">
                    <button onclick="fermerMenu()" class="btn btn-primary">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function afficherMenu(joueurId) {
            const container = document.getElementById('container');
            const listeJoueurs = document.getElementById('listeJoueurs');
            const menuDroit = document.getElementById('menuDroit');
            const menuModification = document.getElementById('menuModification');

            menuModification.classList.add('active');
            menuModification.dataset.joueurId = joueurId;

            if (window.innerWidth > 1024) {
                listeJoueurs.style.flex = '3';
                menuDroit.style.flex = '1';
            } else {
                container.style.transform = 'translateX(-100%)';
            }
        }

        function fermerMenu() {
            const container = document.getElementById('container');
            const listeJoueurs = document.getElementById('listeJoueurs');
            const menuDroit = document.getElementById('menuDroit');
            const menuModification = document.getElementById('menuModification');

            menuModification.classList.remove('active');
            document.getElementById('formContent').innerHTML = '';

            if (window.innerWidth > 1024) {
                listeJoueurs.style.flex = '1';
                menuDroit.style.flex = '0';
            } else {
                container.style.transform = 'translateX(0)';
            }
        }

        async function modifierInfosGenerales() {
            const joueurId = document.getElementById('menuModification').dataset.joueurId;
            const formContent = document.getElementById('formContent');
            
            // Charger d'abord les données du joueur
            const response = await fetch(`joueurs/get_joueur.php?id=${joueurId}`);
            const joueur = await response.json();
            
            formContent.innerHTML = `
                <form onsubmit="sauvegarderModifications(event, 'infos')">
                    <div class="form-group">
                        <label for="nom">Nom:</label>
                        <input type="text" id="nom" name="nom" value="${joueur.nom}" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom:</label>
                        <input type="text" id="prenom" name="prenom" value="${joueur.prenom}" required>
                    </div>
                    <div class="form-group">
                        <label for="numero_licence">Numéro licence:</label>
                        <input type="text" id="numero_licence" name="numero_licence" value="${joueur.numero_licence}" required>
                    </div>
                    <div class="form-group">
                        <label for="date_naissance">Date de naissance:</label>
                        <input type="date" id="date_naissance" name="date_naissance" value="${joueur.date_naissance}" required>
                    </div>
                    <div class="form-group">
                        <label for="taille">Taille (cm):</label>
                        <input type="number" id="taille" name="taille" value="${joueur.taille}" required>
                    </div>
                    <div class="form-group">
                        <label for="poids">Poids (kg):</label>
                        <input type="number" id="poids" name="poids" value="${joueur.poids}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            `;
        }

        async function modifierStatut() {
            const joueurId = document.getElementById('menuModification').dataset.joueurId;
            const formContent = document.getElementById('formContent');
            
            // Charger d'abord les données du joueur
            const response = await fetch(`joueurs/get_joueur.php?id=${joueurId}`);
            const joueur = await response.json();
            
            formContent.innerHTML = `
                <form onsubmit="sauvegarderModifications(event, 'statut')">
                    <div class="form-group">
                        <label for="statut">Statut:</label>
                        <select id="statut" name="statut" required>
                            <option value="Actif" ${joueur.statut === 'Actif' ? 'selected' : ''}>Actif</option>
                            <option value="Blessé" ${joueur.statut === 'Blessé' ? 'selected' : ''}>Blessé</option>
                            <option value="Suspendu" ${joueur.statut === 'Suspendu' ? 'selected' : ''}>Suspendu</option>
                            <option value="Absent" ${joueur.statut === 'Absent' ? 'selected' : ''}>Absent</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            `;
        }

        async function modifierPoste() {
            const joueurId = document.getElementById('menuModification').dataset.joueurId;
            const formContent = document.getElementById('formContent');
            
            // Charger les données du joueur
            const response = await fetch(`joueurs/get_joueur.php?id=${joueurId}`);
            const joueur = await response.json();
            
            formContent.innerHTML = `
                <form onsubmit="sauvegarderModifications(event, 'poste')">
                    <div class="form-group">
                        <label for="poste_prefere">Poste préféré:</label>
                        <select id="poste_prefere" name="poste_prefere" required>
                            <option value="Pilier" ${joueur.poste_prefere === 'Pilier' ? 'selected' : ''}>Pilier</option>
                            <option value="Talonneur" ${joueur.poste_prefere === 'Talonneur' ? 'selected' : ''}>Talonneur</option>
                            <option value="Deuxième ligne" ${joueur.poste_prefere === 'Deuxième ligne' ? 'selected' : ''}>Deuxième ligne</option>
                            <option value="Troisième ligne" ${joueur.poste_prefere === 'Troisième ligne' ? 'selected' : ''}>Troisième ligne</option>
                            <option value="Demi de mêlée" ${joueur.poste_prefere === 'Demi de mêlée' ? 'selected' : ''}>Demi de mêlée</option>
                            <option value="Demi d'ouverture" ${joueur.poste_prefere === 'Demi d\'ouverture' ? 'selected' : ''}>Demi d'ouverture</option>
                            <option value="Centre" ${joueur.poste_prefere === 'Centre' ? 'selected' : ''}>Centre</option>
                            <option value="Ailier" ${joueur.poste_prefere === 'Ailier' ? 'selected' : ''}>Ailier</option>
                            <option value="Arrière" ${joueur.poste_prefere === 'Arrière' ? 'selected' : ''}>Arrière</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            `;
        }

        async function modifierCommentaires() {
            const joueurId = document.getElementById('menuModification').dataset.joueurId;
            const formContent = document.getElementById('formContent');
            
            // Charger les données du joueur
            const response = await fetch(`joueurs/get_joueur.php?id=${joueurId}`);
            const joueur = await response.json();
            
            formContent.innerHTML = `
                <form onsubmit="sauvegarderModifications(event, 'commentaires')">
                    <div class="form-group">
                        <label for="commentaires">Commentaires:</label>
                        <textarea id="commentaires" name="commentaires" rows="5">${joueur.commentaires || ''}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            `;
        }

        function sauvegarderModifications(event, type) {
            event.preventDefault();
            const joueurId = document.getElementById('menuModification').dataset.joueurId;
            const formData = new FormData(event.target);
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `joueurs/modifier.php?id=${joueurId}&type=${type}`, true);
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            window.location.reload();
                        } else {
                            alert(response.message || 'Erreur lors de la sauvegarde');
                        }
                    } catch (e) {
                        console.error('Réponse serveur:', xhr.responseText);
                        alert('Erreur lors du traitement de la réponse');
                    }
                } else {
                    alert('Erreur lors de la requête: ' + xhr.status);
                }
            };
            
            xhr.onerror = function() {
                alert('Erreur de connexion au serveur');
            };
            
            xhr.send(formData);
        }

        window.addEventListener('resize', () => {
            const container = document.getElementById('container');
            const listeJoueurs = document.getElementById('listeJoueurs');
            const menuDroit = document.getElementById('menuDroit');
            const menuModification = document.getElementById('menuModification');

            if (window.innerWidth <= 1024) {
                if (menuModification.classList.contains('active')) {
                    container.style.transform = 'translateX(-100%)';
                }
                listeJoueurs.style.flex = '1';
                menuDroit.style.flex = '1';
            } else {
                container.style.transform = 'translateX(0)';
                if (menuModification.classList.contains('active')) {
                    listeJoueurs.style.flex = '3';
                    menuDroit.style.flex = '1';
                } else {
                    listeJoueurs.style.flex = '1';
                    menuDroit.style.flex = '0';
                }
            }
        });
    </script>
</body>
</html>