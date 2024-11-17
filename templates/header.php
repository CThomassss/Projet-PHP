<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Rugby Manager'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <?php echo isset($additionalCss) ? $additionalCss : ''; ?>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <nav class="main-nav">
                <div class="logo">
                    <a href="/index.php">
                        <h1>Gestion Rugby</h1>
                    </a>
                </div>
                <button class="nav-toggle" aria-label="Toggle navigation">
                    <span class="sr-only">Menu</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
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
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert success" role="alert">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Main content will go here -->