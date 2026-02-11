<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Admin ou Gestionnaire
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    header("Location: connexion.php");
    exit();
}

// Requête pour obtenir le comparatif Vendu vs Restant
$sql = "SELECT 
            l.titre, 
            l.stock AS restant, 
            IFNULL(SUM(lf.quantite), 0) AS vendu,
            (l.stock + IFNULL(SUM(lf.quantite), 0)) AS stock_initial_estime
        FROM livres l
        LEFT JOIN ligne_factures lf ON l.id = lf.livre_id
        GROUP BY l.id
        ORDER BY vendu DESC";

$stats_stocks = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>État des Stocks - RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            padding-top: 80px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        /* Style des barres de stock */
        .progress-bar-bg {
            background: #e0e0e0;
            border-radius: 10px;
            width: 100%;
            height: 10px;
            margin-top: 5px;
        }

        .progress-vendu {
            background: #3498db;
            height: 10px;
            border-radius: 10px;
        }

        .progress-restant {
            background: #27ae60;
            height: 10px;
            border-radius: 10px;
        }

        .alerte-stock {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <h2>📊 Analyse des Stocks et Ventes</h2>
        <p>Ce tableau compare le succès de vos livres face à l'inventaire actuel.</p>

        <table>
            <thead>
                <tr>
                    <th>Titre du Livre</th>
                    <th>Vendus (Sorties)</th>
                    <th>En Stock (Restant)</th>
                    <th>Popularité visuelle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats_stocks as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['titre']) ?></strong></td>
                        <td style="color: #3498db; font-weight: bold;"><?= $s['vendu'] ?> ex.</td>
                        <td class="<?= ($s['restant'] <= 5) ? 'alerte-stock' : '' ?>">
                            <?= $s['restant'] ?> ex.
                            <?= ($s['restant'] <= 5) ? ' ⚠️' : '' ?>
                        </td>
                        <td style="width: 250px;">
                            <?php
                            // Calcul du ratio pour l'affichage graphique
                            $total = ($s['vendu'] + $s['restant']) ?: 1;
                            $perc_vendu = ($s['vendu'] / $total) * 100;
                            ?>
                            <div style="font-size: 0.7rem; display: flex; justify-content: space-between;">
                                <span>Vendu (<?= round($perc_vendu) ?>%)</span>
                                <span>Stock</span>
                            </div>
                            <div class="progress-bar-bg" style="display: flex;">
                                <div class="progress-vendu" style="width: <?= $perc_vendu ?>%;"></div>
                                <div class="progress-restant" style="width: <?= 100 - $perc_vendu ?>%; background: #2ecc71;"></div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>