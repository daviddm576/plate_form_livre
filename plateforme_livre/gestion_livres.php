<?php
session_start();
require_once 'config.php';
require_once 'fonctions.php';

// Sécurité
//if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Super Admin') {
// header("Location: connexion.php");
//  exit();
//}

// Fonction à ajouter dans fonction.php plus tard pour lister les livres
$sql = "SELECT livres.*, categories.nom_categorie 
        FROM livres 
        LEFT JOIN categories ON livres.categorie_id = categories.id 
        ORDER BY livres.id DESC";
$livres = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Livres </title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding-top: 100px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        /* Barre d'actions */
        .action-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-action {
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-add {
            background-color: #27ae60;
        }

        .btn-cat {
            background-color: #8e44ad;
        }

        .btn-action:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        /* Tableau des livres */
        .book-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .book-table th,
        .book-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .book-table th {
            background: #34495e;
            color: white;
        }

        .cover-img {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            background: #ddd;
        }

        .badge {
            background: #ecf0f1;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #7f8c8d;
        }
    </style>
</head>

<body>

    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <h1>Gestion de la Bibliothèque</h1>

        <div class="action-bar">
            <a href="ajouter_livre.php" class="btn-action btn-add">
                <span>➕</span> Ajouter un livre
            </a>
            <a href="gestion_categories.php" class="btn-action btn-cat">
                <span>📂</span> Gérer les catégories
            </a>
        </div>
        <div class="search-container" style="margin-bottom: 20px; position: relative;">
            <input type="text" id="searchInput" placeholder="🔍 Rechercher par titre, auteur ou ISBN..."
                style="width: 100%; padding: 15px; border-radius: 25px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 1rem;">
        </div>

        <table class="book-table">
            <thead>
                <tr>
                    <th>Couverture</th>
                    <th>Titre / Auteur</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td>
                            <img src="couvertures/<?= htmlspecialchars($livre['image_couverture']) ?>" class="cover-img" alt="couverture">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($livre['titre']) ?></strong><br>
                            <small style="color: #7f8c8d;"><?= htmlspecialchars($livre['auteur']) ?></small>
                        </td>
                        <td><span class="badge"><?= htmlspecialchars($livre['nom_categorie'] ?? 'Sans catégorie') ?></span></td>
                        <td><?= number_format($livre['prix'], 2) ?> €</td>
                        <td>
                            <span style="color: <?= $livre['stock'] > 0 ? '#27ae60' : '#e74c3c' ?>;">
                                <?= $livre['stock'] ?> en stock
                            </span>
                        </td>
                        <td>
                            <a href="#" style="text-decoration: none; color: #3498db;">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($livres)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">Aucun livre trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#tableLivres tbody tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</body>

</html>