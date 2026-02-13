<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Seul le Super Admin peut voir ce rapport
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Super Admin') {
    header("Location: connexion.php");
    exit();
}

// REQUÊTE CORRIGÉE : Utilisation de date_facture
$query = "SELECT f.*, u.username as gestionnaire 
          FROM factures f 
          LEFT JOIN users u ON f.user_id = u.id 
          WHERE DATE(f.date_facture) = CURDATE() 
          ORDER BY f.date_facture DESC";

try {
    $stmt = $pdo->query($query);
    $factures_du_jour = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}

// Calcul du total cumulé de la journée
$total_journalier = 0;
foreach ($factures_du_jour as $f) {
    $total_journalier += $f['total'];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rapport des Ventes - RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding-top: 80px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            padding: 20px;
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .card.sales {
            background: #3498db;
        }

        .card.money {
            background: #27ae60;
        }

        .card h3 {
            margin: 0;
            font-size: 1rem;
            opacity: 0.9;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 2rem;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #ecf0f1;
            color: #2c3e50;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #bdc3c7;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-paye {
            background: #d4efdf;
            color: #1e8449;
        }

        .btn-detail {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        @media print {

            .navbar,
            .btn-print {
                display: none;
            }

            body {
                padding-top: 0;
            }
        }
    </style>
</head>

<body>

    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>📊 Rapport des Ventes du Jour</h2>
            <button onclick="window.print()" class="btn-print" style="padding: 10px 20px; cursor: pointer; background: #2c3e50; color: white; border: none; border-radius: 5px;">
                🖨️ Imprimer le rapport
            </button>
        </div>

        <div class="stats-cards">
            <div class="card sales">
                <h3>Nombre de Ventes</h3>
                <p><?= count($factures_du_jour) ?></p>
            </div>
            <div class="card money">
                <h3>Recette Totale (FC)</h3>
                <p><?= number_format($total_journalier, 0, '.', ' ') ?> FC</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>N° Facture</th>
                    <th>Heure</th>
                    <th>Client</th>
                    <th>Gestionnaire</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($factures_du_jour)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">Aucune vente enregistrée aujourd'hui.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($factures_du_jour as $f): ?>
                        <tr>
                            <td><strong>#<?= $f['id'] ?></strong></td>
                            <td><?= date('H:i', strtotime($f['date_facture'])) ?></td>
                            <td><?= htmlspecialchars($f['client_nom'] ?? 'Client') ?></td>
                            <td><?= htmlspecialchars($f['gestionnaire'] ?? 'Système') ?></td>
                            <td style="font-weight: bold;"><?= number_format($f['total'], 0, '.', ' ') ?> FC</td>
                            <td><span class="status status-paye"><?= $f['statut'] ?></span></td>
                            <td><a href="voir_facture.php?id=<?= $f['id'] ?>" class="btn-detail">👁️ Détails</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>