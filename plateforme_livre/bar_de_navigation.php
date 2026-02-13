<?php
// On récupère le nom du fichier actuel pour gérer l'état "active" des liens
$page = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['user']['role'] ?? '';
?>

<style>
    /* --- Styles de la Barre de Navigation --- */
    .navbar {
        background-color: #2c3e50;
        /* Bleu nuit professionnel */
        height: 70px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 40px;
        position: fixed;
        /* Reste en haut lors du scroll */
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- Partie Gauche : Logo + Liens --- */
    .nav-left {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .nav-logo-link {
        display: flex;
        align-items: center;
    }

    .logo-img {
        height: 50px;
        /* Ajuste selon la forme de ton logo */
        width: auto;
        transition: transform 0.3s ease;
    }

    .logo-img:hover {
        transform: scale(1.08);
    }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 10px;
    }

    .nav-menu a {
        color: #bdc3c7;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 8px 15px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .nav-menu a:hover,
    .nav-menu a.active {
        color: white;
        background-color: #34495e;
    }

    .nav-menu a.active {
        border-bottom: 3px solid #3498db;
        border-radius: 6px 6px 0 0;
    }

    /* --- Partie Droite : Utilisateur --- */
    .nav-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-profile {
        color: white;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.1);
        padding: 6px 15px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .user-profile strong {
        color: #3498db;
    }

    .btn-deconnexion {
        background-color: #e74c3c;
        color: white;
        text-decoration: none;
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: bold;
        transition: background 0.3s ease, transform 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-deconnexion:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }

    /* --- Ajustement pour le reste de la page --- */
    /* Ajoutez "padding-top: 90px;" au body de vos fichiers pour ne pas que la barre cache le contenu */
</style>

<nav class="navbar">
    <div class="nav-left">
        <a href="admin.php" class="nav-logo-link">
            <img src="img/logo.png" alt="RACINE" class="logo-img">
        </a>
        <ul class="nav-menu">
            <?php if ($userRole === 'Super Admin') : ?>
                <li><a href="admin.php" class="<?= $page == 'admin.php' ? 'active' : '' ?>">Panneau de control</a></li>
                <li><a href="tableau_de_bord.php" class="<?= $page == 'tableau_de_bord.php' ? 'active' : '' ?>">tableau de bord</a></li>
                <li><a href="voir_utilisateurs.php" class="<?= $page == 'voir_utilisateurs.php' ? 'active' : '' ?>">Utilisateurs</a></li>
                <li><a href="creer_facture.php" class="<?= $page == 'creer_facture.php' ? 'active' : '' ?>">Créer Facture</a></li>
                <li><a href="commande_en_attente.php" class="<?= $page == 'commande_en_attente.php' ? 'active' : '' ?>">Commandes en attente</a></li>
            <?php endif; ?>

            <?php if ($userRole === 'Super Admin' || $userRole === 'Gestionnaire de stock') : ?>
                <li><a href="gestion_livres.php" class="<?= $page == 'gestion_livres.php' ? 'active' : '' ?>">Gestion Livres</a></li>


            <?php endif; ?>

            <?php if ($userRole === 'Gestionnaire de stock') : ?>
                <li><a href="creer_facture.php" class="<?= $page == 'creer_facture.php' ? 'active' : '' ?>">Créer Facture</a></li>
                <li><a href="commande_en_attente.php" class="<?= $page == 'commande_en_attente.php' ? 'active' : '' ?>">Commandes en attente</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="nav-right">
        <div class="user-profile">
            👤 <strong><?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></strong>
        </div>
        <a href="deconnexion.php" class="btn-deconnexion">
            <span>🚪</span> Deconnexion
        </a>
    </div>
</nav>