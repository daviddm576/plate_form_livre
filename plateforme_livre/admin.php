<?php
session_start();

// 1. Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}

// 2. Vérifier si le rôle est Super Admin
if ($_SESSION['user']['role'] !== 'Super Admin') {
    // Si ce n'est pas un admin, on le renvoie vers une page d'accueil simple ou on affiche une erreur
    die("Accès refusé : Vous n'avez pas les permissions nécessaires pour accéder à la RACINE.");
}

$user = $_SESSION['user'];
if ($_SESSION['user']['role'] !== 'Super Admin') {
    // Si ce n'est pas un admin, on le redirige vers sa page autorisée
    header("Location: gestion_livres.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - RACINE</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* --- AJOUT : Style pour la barre de déconnexion --- */
        .top-nav {
            position: absolute;
            top: 0;
            width: 100%;
            background: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .user-tag {
            font-size: 0.9rem;
            color: var(--primary);
        }

        .btn-logout {
            background-color: var(--danger);
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.8rem;
            transition: opacity 0.3s;
        }

        .btn-logout:hover {
            opacity: 0.8;
        }

        /* ----------------------------------------------- */

        h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 4px solid var(--accent);
            padding-bottom: 10px;
            margin-top: 80px;
            /* Ajouté pour ne pas être collé au top-nav */
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            width: 90%;
            max-width: 800px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .card:hover {
            transform: translateY(-10px);
            border-color: var(--accent);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.2);
        }

        .card h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .card p {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: 10px;
        }

        .btn-users {
            border-left: 5px solid var(--accent);
        }

        .btn-roles {
            border-left: 5px solid var(--success);
        }

        .btn-ops {
            border-left: 5px solid var(--warning);
        }

        .btn-perms {
            border-left: 5px solid var(--danger);
        }

        .btn-view {
            border-left: 5px solid var(--primary);
        }

        .footer {
            margin-top: 50px;
            color: #95a5a6;
            font-size: 0.8rem;
            padding-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include 'bar_de_navigation.php'; ?>

    <div style="margin-top: 100px; text-align: center;">
        <h1>BIENVENUE DEV DE LA RACINE</h1>

    <div class="grid-container">
        <a href="index.php" class="card btn-users">
            <h3>👥 Gestion Utilisateurs</h3>
            <p>Ajouter et gérer les comptes membres</p>
        </a>

        <a href="ajouter_role.php" class="card btn-roles">
            <h3>🛡️ Gestion des Rôles</h3>
            <p>Définir les niveaux d'accès (Admin, User...)</p>
        </a>

        <a href="ajouter_operation.php" class="card btn-ops">
            <h3>⚙️ Opérations</h3>
            <p>Créer des actions système spécifiques</p>
        </a>

        <a href="role_operation.php" class="card btn-perms">
            <h3>🔑 Permissions</h3>
            <p>Lier les opérations aux rôles</p>
        </a>

        <a href="voir_utilisateurs.php" class="card btn-view">
            <h3>👥 Utilisateurs</h3>
            <p>Voir la liste des utilisateurs</p>
        </a>
        <a href="tableau_de_bord.php" class="card btn-view">
            <h3>📊 Analyse des Stocks et Ventes</h3>
            <p>Ce tableau compare le succès de vos livres face à l'inventaire actuel</p>
        </a>
    </div>

    <div class="footer">
        Système de gestion de base de données v1.0 - Projet Racine
    </div>

</body>

</html>