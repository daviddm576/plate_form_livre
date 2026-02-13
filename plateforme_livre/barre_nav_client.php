<?php
// On vérifie si une session est déjà lancée avant d'en ouvrir une
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupération sécurisée du prénom (dan) et du nom (Client)
// On va afficher "dan Client"
$prenom = $_SESSION['user']['prenom'] ?? '';
$nom = $_SESSION['user']['nom'] ?? 'Client';
$nom_affichage = ucwords($prenom . " " . $nom); // Met la première lettre en majuscule

// Calcul du panier
$total_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<style>
    :root {
        --primary: #2c3e50;
        --secondary: #3498db;
        --accent: #e74c3c;
        --success: #27ae60;
        --nav-hover: #34495e;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--primary);
        padding: 0 4%;
        /* Réduit un peu le padding vertical pour l'élégance */
        height: 70px;
        color: white;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    /* GAUCHE : LOGO + MENU */
    .nav-left {
        display: flex;
        align-items: center;
        gap: 30px;
        /* Espace entre le logo et le début du menu */
    }

    .nav-left img {
        height: 45px;
        width: auto;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-menu a {
        color: #bdc3c7;
        text-decoration: none;
        font-weight: 500;
        font-size: 1rem;
        padding: 23px 20px;
        /* Padding ajusté pour remplir la hauteur de la navbar */
        transition: all 0.3s ease;
        display: inline-block;
    }

    .nav-menu a:hover,
    .nav-menu a.active {
        color: white;
        background-color: var(--nav-hover);
    }

    .nav-menu a.active {
        border-bottom: 4px solid var(--secondary);
    }

    /* CENTRE : NOM */
    .nav-center {
        flex-grow: 1;
        text-align: center;
        font-size: 1.1rem;
    }

    .user-name {
        color: #f1c40f;
        font-weight: bold;
    }

    /* DROITE : ACTIONS */
    .nav-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .nav-btn {
        text-decoration: none;
        color: white;
        padding: 8px 18px;
        border-radius: 5px;
        font-weight: bold;
        transition: 0.3s;
        font-size: 0.9rem;
    }

    .btn-panier {
        background: var(--success);
        position: relative;
    }

    .btn-panier:hover {
        background: #219150;
    }

    .btn-quitter {
        background: transparent;
        border: 1px solid var(--accent);
        color: var(--accent);
    }

    .btn-quitter:hover {
        background: var(--accent);
        color: white;
    }

    .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--accent);
        color: white;
        padding: 2px 7px;
        border-radius: 50%;
        font-size: 0.75rem;
        border: 2px solid var(--primary);
    }
</style>

<nav class="navbar">
    <div class="nav-left">
        <a href="index.php">
            <img src="img/logo.png" alt="RACINE" class="logo-img">
        </a>

        <ul class="nav-menu">
            <li>
                <a href="catalogue.php" class="<?= ($current_page == 'catalogue.php') ? 'active' : '' ?>">
                    📖 Catalogue
                </a>
            </li>
        </ul>
    </div>

    <div class="nav-center">
        <span>Bienvenue, <span class="user-name"><?= htmlspecialchars($nom_affichage) ?></span></span>
    </div>

    <div class="nav-right">
        <a href="panier.php" class="nav-btn btn-panier">
            🛒 Panier
            <?php if ($total_articles > 0): ?>
                <span class="badge"><?= $total_articles ?></span>
            <?php endif; ?>
        </a>

        <a href="deconnexion.php" class="nav-btn btn-quitter">deconnexion</a>
    </div>
</nav>