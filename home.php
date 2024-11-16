<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Rugby Manager</title>
    <link rel="stylesheet" type="text/css" href="./css/home.css">
</head>
<body>
    <nav class="main-nav">
        <div class="logo">Rugby Manager</div>
        <ul>
            <li><a href="joueurs.php">Joueurs</a></li>
            <li><a href="matchs.php">Matchs</a></li>
            <li><a href="stats.php">Statistiques</a></li>
            <li><a href="evaluations.php">Évaluations</a></li>
        </ul>
        <div class="user-menu">
            <span>Bienvenue, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?></span>
            <a href="logout.php">Déconnexion</a>
        </div>
    </nav>

    <div class="dashboard">
        <h1>Tableau de bord</h1>
        
        <div class="quick-actions">
            <div class="card">
                <h2>Gestion des Joueurs</h2>
                <p>Gérez votre effectif et les informations des joueurs</p>
                <a href="joueurs.php" class="btn">Accéder</a>
            </div>
            
            <div class="card">
                <h2>Matchs à venir</h2>
                <p>Organisez et planifiez vos prochains matchs</p>
                <a href="matchs.php" class="btn">Accéder</a>
            </div>
            
            <div class="card">
                <h2>Statistiques</h2>
                <p>Consultez les performances de l'équipe</p>
                <a href="stats.php" class="btn">Accéder</a>
            </div>
            
            <div class="card">
                <h2>Évaluations</h2>
                <p>Évaluez les performances individuelles</p>
                <a href="evaluations.php" class="btn">Accéder</a>
            </div>
        </div>
    </div>
</body>
</html>