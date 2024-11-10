<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Équipe Rugby</title>
    <link rel="stylesheet" href="/Projet%20PHP/css/style.css">
</head>
<body>
    <div class="container">
    <header>
        <nav class="main-nav">
            <div class="logo">
                <h1>Gestion Rugby</h1>
            </div>
            <ul class="nav-links">
                <li><a href="/index.php">Accueil</a></li>
                <li><a href="/joueurs/liste.php">Joueurs</a></li>
                <li><a href="/matchs/liste.php">Matchs</a></li>
                <li><a href="/statistiques/index.php">Statistiques</a></li>
                <?php if (isset($_SESSION['utilisateur_id'])): ?>
                    <li class="logout"><a href="/logout.php">Déconnexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
    </header>
    <main>