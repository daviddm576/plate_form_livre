<?php
session_start();
require_once 'fonctions.php'; // Assure-toi que ce fichier contient ta connexion PDO

// Sécurité
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Client') {
    header("Location: connexion.php");
    exit();
}

$livres = $pdo->query("SELECT * FROM livres WHERE stock > 0")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Catalogue - Librairie Racine</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 40px;
        }

        .catalogue-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
        }

        .book-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-bottom: 15px;
            border: 1px solid #eee;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .book-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }

        .book-info {
            padding: 15px;
            text-align: center;
        }

        .book-info h3 {
            margin: 10px 0;
            font-size: 1.1rem;
            color: #2c3e50;
            min-height: 50px;
        }

        .book-info .price {
            color: #27ae60;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .add-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 15px;
        }

        .qte-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .input-qte {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .btn-add {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-add:hover {
            background: #2980b9;
        }

        .stock-tag {
            font-size: 0.8rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <?php include 'barre_nav_client.php'; ?>

    <div class="container">
        <h1 class="page-title">📚 Explorez nos ouvrages </h1>

        <div class="catalogue-grid">
            <?php foreach ($livres as $l): ?>
                <div class="book-card">
                    <img src="couvertures/<?= $l['image_couverture'] ?? 'default.jpg' ?>" alt="<?= htmlspecialchars($l['titre']) ?>">

                    <div class="book-info">
                        <h3><?= htmlspecialchars($l['titre']) ?></h3>
                        <p class="price"><?= number_format($l['prix'], 0, '.', ' ') ?> Fc</p>
                        <p class="stock-tag">En stock: <?= $l['stock'] ?></p>
                    </div>

                    <form action="ajouter_au_panier.php" method="POST" class="add-form">
                        <input type="hidden" name="livre_id" value="<?= $l['id'] ?>">
                        <div class="qte-wrapper">
                            <label>Qté:</label>
                            <input type="number" name="qte" value="1" min="1" max="<?= $l['stock'] ?>" class="input-qte">
                        </div>
                        <button type="submit" class="btn-add">🛒 Ajouter au panier</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>