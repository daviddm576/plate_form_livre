<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Admin ou Gestionnaire uniquement
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    header("Location: connexion.php");
    exit();
}

// 1. Chiffre d'affaires total et ventes du jour
$stats_generales = $pdo->query("
    SELECT 
        SUM(total) as CA_total, 
        COUNT(id) as nb_ventes,
        SUM(CASE WHEN DATE(date_facture) = CURDATE() THEN total ELSE 0 END) as CA_aujourdhui
    FROM factures 
    WHERE statut = 'Payé'
")->fetch();

// 2. Top 5 des livres les plus vendus
$top_livres = $pdo->query("
    SELECT l.titre, SUM(lf.quantite) as total_vendu
    FROM ligne_factures lf
    JOIN livres l ON lf.livre_id = l.id
    GROUP BY l.id
    ORDER BY total_vendu DESC
    LIMIT 5
")->fetchAll();

// 3. Ventes par gestionnaire (pour le Super Admin)
$ventes_admin = $pdo->query("
    SELECT u.nom, COUNT(f.id) as nb_factures, SUM(f.total) as total_genere
    FROM factures f
    JOIN users u ON f.user_id = u.id
    WHERE f.statut = 'Payé'
    GROUP BY u.id
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rapports de Vente - RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            padding-top: 80px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            margin: 0;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
    </style>
</head>

<body>
    <?php include 'bar_de_navigation.php'; ?>

    <div style="max-width: 1100px; margin: auto; padding: 20px;">
        <h2>📊 Tableau de Bord des Ventes</h2>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Ventes Aujourd'hui</h3>
                <p><?= number_format($stats_generales['CA_aujourdhui'], 2) ?> €</p>
            </div>
            <div class="card">
                <h3>Chiffre d'Affaires Global</h3>
                <p><?= number_format($stats_generales['CA_total'], 2) ?> €</p>
            </div>
            <div class="card">
                <h3>Nombre de Ventes</h3>
                <p><?= $stats_generales['nb_ventes'] ?></p>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="section" style="flex: 1;">
                <h3>🏆 Top 5 des ventes</h3>
                <table>
                    <?php foreach ($top_livres as $livre): ?>
                        <tr>
                            <td><?= htmlspecialchars($livre['titre']) ?></td>
                            <td><strong><?= $livre['total_vendu'] ?></strong> vendus</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <?php if ($_SESSION['user']['role'] === 'Super Admin'): ?>
                <div class="section" style="flex: 1;">
                    <h3>👨‍💼 Performance Équipe</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Ventes</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <?php foreach ($ventes_admin as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['nom']) ?></td>
                                <td><?= $v['nb_factures'] ?></td>
                                <td><?= number_format($v['total_genere'], 2) ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>