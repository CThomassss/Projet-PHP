# Projet : Gestion d'une Équipe de Rugby

Ce guide vous aidera à construire une application web pour gérer une équipe de rugby, en suivant les meilleures pratiques de développement web. Nous allons décomposer le processus en étapes simples, en construisant une partie de l'application à la fois.

---

## Table des Matières

1. [Prérequis](#prérequis)
2. [Configuration de l'Environnement](#configuration-de-lenvironnement)
3. [Conception de la Base de Données](#conception-de-la-base-de-données)
4. [Création de la Structure du Projet](#création-de-la-structure-du-projet)
5. [Mise en Place de l'Authentification](#mise-en-place-de-lauthentification)
6. [Gestion des Joueurs](#gestion-des-joueurs)
7. [Gestion des Matchs](#gestion-des-matchs)
8. [Saisie des Feuilles de Match](#saisie-des-feuilles-de-match)
9. [Évaluation des Joueurs Après le Match](#évaluation-des-joueurs-après-le-match)
10. [Affichage des Statistiques](#affichage-des-statistiques)
11. [Sécurité et Prévention des Injections SQL](#sécurité-et-prévention-des-injections-sql)
12. [Mise en Forme et Ergonomie](#mise-en-forme-et-ergonomie)
13. [Tests et Validation](#tests-et-validation)
14. [Améliorations Futures](#améliorations-futures)
15. [Conclusion](#conclusion)

---

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- **Serveur Web** (Apache, Nginx, etc.)
- **PHP** (version compatible avec PDO)
- **Serveur de Base de Données MySQL**
- **Éditeur de Code** (Visual Studio Code, Sublime Text, etc.)
- **Connaissances de Base en HTML, CSS, PHP et SQL**

---

## Configuration de l'Environnement

1. **Installer un Serveur Web Local :**

   - Utilisez un environnement comme **XAMPP**, **WAMP** ou **MAMP** pour installer Apache, PHP et MySQL.
   - Vérifiez que le serveur fonctionne en créant un fichier `info.php` avec `<?php phpinfo(); ?>` et en l'exécutant.

2. **Configurer le Serveur :**

   - Créez un dossier pour votre projet dans le répertoire `htdocs` (Apache) ou `www` (Nginx).

3. **Créer la Base de Données :**

   - Accédez à **phpMyAdmin** ou utilisez la ligne de commande MySQL.
   - Créez une base de données nommée `gestion_equipe_rugby`.

---

## Conception de la Base de Données

1. **Modélisation des Données :**

   - Identifiez les entités principales :
     - **Joueurs**
     - **Matchs**
     - **Sélections**
     - **Évaluations**
     - **Utilisateurs**

2. **Définition des Tables :**

   - **Table `utilisateurs`** (pour l'authentification)
     - `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
     - `nom_utilisateur` (VARCHAR, UNIQUE)
     - `mot_de_passe` (VARCHAR)

   - **Table `joueurs`**
     - `id`
     - `nom`
     - `prenom`
     - `numero_licence` (UNIQUE)
     - `date_naissance`
     - `taille`
     - `poids`
     - `statut` (ENUM: 'Actif', 'Blessé', 'Suspendu', 'Absent')
     - `commentaires` (TEXT)
     - `poste_prefere`

   - **Table `matchs`**
     - `id`
     - `date`
     - `heure`
     - `equipe_adverse`
     - `lieu` (ENUM: 'Domicile', 'Extérieur')
     - `resultat` (ENUM: 'Victoire', 'Défaite', 'Nul', NULL par défaut)

   - **Table `selections`** (relation entre `joueurs` et `matchs`)
     - `id`
     - `match_id` (FOREIGN KEY vers `matchs`)
     - `joueur_id` (FOREIGN KEY vers `joueurs`)
     - `statut` (ENUM: 'Titulaire', 'Remplaçant')
     - `poste_occupe`

   - **Table `evaluations`**
     - `id`
     - `match_id`
     - `joueur_id`
     - `note` (INT, de 1 à 5)

3. **Création des Tables :**

   - Rédigez les scripts SQL pour créer les tables avec les contraintes appropriées.
   - Exécutez les scripts dans votre base de données.

---

## Création de la Structure du Projet

1. **Organisation des Dossiers :**

   - **Racine du Projet**
     - `index.php` (Page d'accueil ou de redirection)
     - `login.php` (Page de connexion)
     - `logout.php` (Script de déconnexion)
     - `config/`
       - `database.php` (Fichier de connexion PDO)
     - `lib/`
       - `functions.php` (Fonctions pour interagir avec la base de données)
     - `css/`
       - `style.css` (Feuille de style principale)
     - `templates/`
       - `header.php`
       - `footer.php`
     - `joueurs/` (Dossier pour la gestion des joueurs)
       - `liste.php`
       - `ajouter.php`
       - `modifier.php`
       - `supprimer.php`
     - `matchs/` (Dossier pour la gestion des matchs)
       - `liste.php`
       - `ajouter.php`
       - `modifier.php`
       - `supprimer.php`
     - `selections/`
       - `selectionner.php`
       - `modifier_selection.php`
     - `evaluations/`
       - `evaluer.php`
     - `statistiques/`
       - `index.php`

2. **Séparation du Code :**

   - **HTML/CSS** pour la structure et le style.
   - **PHP** pour la logique côté serveur.
   - **SQL** séparé dans des fonctions PHP dans `lib/functions.php`.

3. **Inclusion des Templates :**

   - Utilisez `include` ou `require` pour inclure `header.php` et `footer.php` dans vos pages.

---

## Mise en Place de l'Authentification

1. **Création de la Page de Connexion (`login.php`) :**

   - Créez un formulaire avec les champs `nom_utilisateur` et `mot_de_passe`.
   - Le formulaire envoie les données via `POST` à `login.php`.

2. **Traitement de la Connexion :**

   - Dans `login.php`, vérifiez si le formulaire a été soumis.
   - Récupérez l'utilisateur correspondant au `nom_utilisateur` depuis la base.
   - Utilisez `password_verify()` pour comparer le mot de passe saisi avec le hachage stocké.

3. **Gestion de la Session :**

   - Si les informations sont correctes, démarrez une session avec `session_start()` et stockez les informations nécessaires.
   - Redirigez l'utilisateur vers la page d'accueil ou le tableau de bord.

4. **Protection des Pages :**

   - Au début de chaque page restreinte, vérifiez si l'utilisateur est connecté :
     ```php
     session_start();
     if (!isset($_SESSION['utilisateur_id'])) {
         header('Location: /login.php');
         exit();
     }
     ```
5. **Déconnexion (`logout.php`) :**

   - Détruisez la session avec `session_destroy()` et redirigez vers `login.php`.

6. **Stockage Sécurisé des Mots de Passe :**

   - Lors de la création de l'utilisateur (administrateur), utilisez `password_hash()` pour stocker le mot de passe.

---

## Gestion des Joueurs

1. **Affichage de la Liste des Joueurs (`joueurs/liste.php`) :**

   - Récupérez tous les joueurs de la base de données via une fonction dans `lib/functions.php`.
   - Affichez-les dans un tableau HTML avec les options pour modifier ou supprimer.

2. **Ajout d'un Joueur (`joueurs/ajouter.php`) :**

   - Créez un formulaire pour saisir les informations du joueur.
   - Validez les données côté serveur.
   - Utilisez une requête préparée PDO pour insérer le joueur dans la base.

3. **Modification d'un Joueur (`joueurs/modifier.php?id=ID`) :**

   - Récupérez les informations du joueur via son `id`.
   - Pré-remplissez le formulaire avec les données existantes.
   - Mettez à jour les informations après validation.

4. **Suppression d'un Joueur (`joueurs/supprimer.php?id=ID`) :**

   - Demandez une confirmation avant la suppression.
   - Supprimez le joueur de la base de données.

5. **Gestion des Commentaires et du Statut :**

   - Intégrez un champ `commentaires` dans le formulaire.
   - Permettez la sélection du `statut` via un menu déroulant.

---

## Gestion des Matchs

1. **Affichage de la Liste des Matchs (`matchs/liste.php`) :**

   - Séparez les matchs à venir et les matchs passés.
   - Affichez les détails importants et des liens pour gérer les sélections et les résultats.

2. **Ajout d'un Match (`matchs/ajouter.php`) :**

   - Créez un formulaire pour saisir les informations du match.
   - Validez et enregistrez les données dans la base.

3. **Modification d'un Match (`matchs/modifier.php?id=ID`) :**

   - Permettez la modification des informations du match.

4. **Suppression d'un Match (`matchs/supprimer.php?id=ID`) :**

   - Demandez une confirmation avant la suppression.

---

## Saisie des Feuilles de Match

1. **Sélection des Joueurs pour un Match (`selections/selectionner.php?match_id=ID`) :**

   - Affichez la liste des joueurs actifs avec leurs informations (taille, poids, commentaires, évaluations).
   - Permettez de sélectionner les joueurs en tant que titulaires ou remplaçants.
   - Incluez un champ pour spécifier le poste occupé.

2. **Validation de la Sélection :**

   - Vérifiez que le nombre minimum de joueurs (15 titulaires pour le rugby) est atteint.
   - Affichez un message d'erreur si ce n'est pas le cas.

3. **Enregistrement de la Sélection :**

   - Enregistrez les données dans la table `selections`.
   - Utilisez des transactions PDO pour garantir l'intégrité des données.

4. **Modification de la Sélection (`selections/modifier_selection.php?match_id=ID`) :**

   - Permettez la modification des sélections existantes.

---

## Évaluation des Joueurs Après le Match

1. **Saisie du Résultat du Match (`matchs/modifier.php?id=ID`) :**

   - Ajoutez un champ pour le `resultat` (Victoire, Défaite, Nul).
   - Enregistrez le résultat dans la base de données.

2. **Évaluation des Joueurs (`evaluations/evaluer.php?match_id=ID`) :**

   - Affichez la liste des joueurs qui ont participé au match.
   - Permettez à l'entraîneur de saisir une note (de 1 à 5) pour chaque joueur.

3. **Enregistrement des Évaluations :**

   - Stockez les notes dans la table `evaluations`.
   - Utilisez des requêtes préparées pour l'insertion.

---

## Affichage des Statistiques

1. **Calcul des Statistiques Générales (`statistiques/index.php`) :**

   - Calculez le nombre total de matchs joués, gagnés, perdus, nuls.
   - Calculez les pourcentages correspondants.

2. **Statistiques par Joueur :**

   - Pour chaque joueur, affichez :
     - **Statut actuel**
     - **Poste préféré**
     - **Nombre de titularisations**
     - **Nombre de remplacements**
     - **Moyenne des évaluations**
     - **Nombre de matchs consécutifs joués**
     - **Pourcentage de matchs gagnés lorsqu'il a participé**

3. **Affichage des Données :**

   - Utilisez des tableaux HTML pour une présentation claire.
   - Ajoutez des graphiques si possible (optionnel).

4. **Optimisation des Requêtes :**

   - Utilisez des jointures et des sous-requêtes pour calculer efficacement les statistiques.

---

## Sécurité et Prévention des Injections SQL

1. **Utilisation de Requêtes Préparées PDO :**

   - Dans toutes les interactions avec la base de données, utilisez des requêtes préparées pour éviter les injections SQL.

2. **Validation et Nettoyage des Données :**

   - Validez toutes les entrées utilisateur côté serveur.
   - Utilisez des fonctions comme `filter_input()` ou `htmlspecialchars()`.

3. **Gestion des Sessions Sécurisées :**

   - Configurez les paramètres de session pour une sécurité optimale (utilisation de cookies sécurisés, etc.).

4. **Stockage des Mots de Passe :**

   - Ne stockez jamais les mots de passe en clair.
   - Utilisez `password_hash()` pour hacher les mots de passe et `password_verify()` pour les vérifier.

5. **Protection Contre les Attaques CSRF :**

   - Implémentez des tokens CSRF pour les formulaires sensibles (optionnel mais recommandé).

---

## Mise en Forme et Ergonomie

1. **Création d'un Menu de Navigation :**

   - Dans `templates/header.php`, ajoutez un menu pour naviguer entre les différentes sections de l'application.

2. **Utilisation de CSS pour le Design :**

   - Dans `css/style.css`, définissez les styles pour rendre l'application agréable visuellement.
   - Utilisez des frameworks CSS comme **Bootstrap** pour accélérer le développement (optionnel).

3. **Ergonomie et Accessibilité :**

   - Assurez-vous que l'application est facile à utiliser.
   - Utilisez des labels et des placeholders dans les formulaires.
   - Vérifiez la compatibilité avec différents navigateurs.

4. **Messages d'Erreur et de Confirmation :**

   - Affichez des messages clairs pour informer l'utilisateur du succès ou de l'échec des opérations.

---

## Tests et Validation

1. **Tests Unitaires :**

   - Rédigez des tests pour les fonctions critiques dans `lib/functions.php` (optionnel).

2. **Tests Fonctionnels :**

   - Parcourez toutes les fonctionnalités de l'application pour vous assurer qu'elles fonctionnent comme prévu.

3. **Validation des Données :**

   - Essayez d'entrer des données incorrectes pour vérifier que les validations fonctionnent.

4. **Tests de Sécurité :**

   - Tentez des injections SQL pour vérifier la robustesse des protections.
   - Vérifiez que les pages protégées ne sont pas accessibles sans authentification.

---

## Améliorations Futures

1. **Gestion des Blessures et Suspensions :**

   - Ajoutez des fonctionnalités pour gérer les périodes de blessure ou de suspension des joueurs.

2. **Notifications par Email :**

   - Envoyez des emails de rappel pour les matchs à venir (optionnel).

3. **Multilingue :**

   - Implémentez une interface multilingue si nécessaire.

4. **Mobile Friendly :**

   - Adaptez le design pour une utilisation sur mobile.

