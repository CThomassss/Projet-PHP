# Gestion d'une Équipe de Sport

Ce projet est une application web dédiée à la gestion des joueurs, matchs, et performances pour une équipe de sport. Il vise à aider un entraîneur dans la sélection des joueurs, le suivi des performances, et l'analyse des statistiques de son équipe.

## 🎯 Objectifs du Projet

- **Gestion des Joueurs** : Ajouter, modifier, et supprimer les joueurs avec leurs informations détaillées.
- **Planification des Matchs** : Enregistrer les matchs, leurs informations (adversaire, date, lieu, résultats, etc.), et créer des feuilles de match.
- **Évaluation des Performances** : Permettre à l'entraîneur d'évaluer les joueurs après chaque match.
- **Analyse Statistique** : Fournir des statistiques utiles sur les performances globales de l'équipe et de chaque joueur.
- **Sécurité** : Application accessible uniquement après authentification sécurisée.

---

## ⚙️ Fonctionnalités Principales

### 1. **Gestion des Joueurs**
- Ajout de joueurs avec :
  - Nom, prénom, numéro de licence, date de naissance, taille, poids.
  - Statut : Actif, Blessé, Suspendu, Absent.
  - Commentaires personnalisés de l’entraîneur.
- Modification et suppression des joueurs.

### 2. **Gestion des Matchs**
- Ajout des matchs avec :
  - Date, heure, équipe adverse, lieu (domicile/extérieur).
  - Résultat (saisi après le match).
- Visualisation et modification des matchs planifiés ou terminés.

### 3. **Feuilles de Match**
- Sélection des joueurs pour un match à venir :
  - Affichage des joueurs actifs uniquement.
  - Précision des rôles : Titulaire ou Remplaçant.
  - Poste occupé par chaque joueur.
- Validation conditionnelle basée sur le nombre minimum de joueurs requis.

### 4. **Évaluations et Statistiques**
- Évaluation des joueurs après chaque match :
  - Notation (par exemple : de 1 à 5).
  - Commentaires facultatifs.
- Statistiques détaillées :
  - Nombre et pourcentage de victoires, défaites, et matchs nuls.
  - Statistiques par joueur : statut actuel, poste préféré, titularisations, remplacements, moyenne des évaluations, sélections consécutives, etc.

### 5. **Authentification Sécurisée**
- Accès protégé par un nom d'utilisateur et un mot de passe.
- Gestion sécurisée des mots de passe (hachage).
- Protection contre les injections SQL.

---

## 🛠️ Technologies Utilisées

- **Frontend** :
  - HTML CSS, JS
- **Backend** :
  - PHP, JS (PDO pour la gestion sécurisée des bases de données).
- **Base de Données** :
  - MySQL (modèle validé avant l'implémentation).
- **Gestion de Version** :
  - Git 

---

== Connexion au site

Pour vous connecter, rien de plus simple ! Accédez au site [https://gestionnaireequipeginerceolin.alwaysdata.net/home.php](https://gestionnaireequipeginerceolin.alwaysdata.net/home.php), puis utilisez les identifiants suivants :

* **Identifiant** : `clem`
* **Mot de passe** : `clem14`

Une fois connecté, vous aurez accès à toutes les fonctionnalités disponibles sur le site.


