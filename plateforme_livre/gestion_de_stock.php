<?php
session_start();

require_once 'fonctions.php';

// Vérification : Il faut être soit Super Admin, soit Gestionnaire de stock
$userRole = $_SESSION['user']['role'] ?? '';

if (!isset($_SESSION['user']) || !($userRole === 'Super Admin' || $userRole === 'Gestionnaire de stock')) {
    header("Location: connexion.php");
    exit();
}


// Traitement des actions rapides (+1 / -1)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $valeur = ($_GET['action'] === 'plus') ? 1 : -1;
    updateStockQuantity($pdo, $_GET['id'], $valeur);
    header("Location: gestion_de_stock.php");
    exit();
}

$inventaire = getStockStatus($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion de Stock - RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding-top: 90px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        .stock-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            color: #7f8c8d;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        /* Badges de stock */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .bg-danger {
            background: #fadbd8;
            color: #e74c3c;
        }

        /* Rupture */
        .bg-warning {
            background: #fef5e7;
            color: #f39c12;
        }

        /* Bas */
        .bg-success {
            background: #d4efdf;
            color: #27ae60;
        }

        /* OK */

        /* Boutons rapides */
        .btn-qty {
            text-decoration: none;
            padding: 5px 10px;
            background: #eee;
            color: #333;
            border-radius: 4px;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-qty:hover {
            background: #ddd;
        }

        .btn-plus {
            color: #27ae60;
        }

        .btn-moins {
            color: #e74c3c;
        }
    </style>
</head>

<body>

    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <div class="stock-card">
            <h2>📦 État de l'Inventaire</h2>
            <p>Gérez vos quantités en temps réel.</p>

            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Statut</th>
                        <th>Actions Rapides</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventaire as $item):
                        // Logique des couleurs
                        $statusClass = 'bg-success';
                        $statusText = 'En stock';
                        if ($item['stock'] <= 0) {
                            $statusClass = 'bg-danger';
                            $statusText = 'Rupture';
                        } elseif ($item['stock'] <= 5) {
                            $statusClass = 'bg-warning';
                            $statusText = 'Stock Faible';
                        }
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($item['titre']) ?></strong></td>
                            <td><?= number_format($item['prix'], 2) ?> €</td>
                            <td><strong><?= $item['stock'] ?></strong></td>
                            <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                            <td>
                                <a href="?action=moins&id=<?= $item['id'] ?>" class="btn-qty btn-moins">- 1</a>
                                <a href="?action=plus&id=<?= $item['id'] ?>" class="btn-qty btn-plus">+ 1</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>