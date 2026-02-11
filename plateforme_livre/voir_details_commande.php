<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Admin ou Gestionnaire
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    header("Location: connexion.php");
    exit();
}

$id_commande = $_GET['id'] ?? null;

if (!$id_commande) {
    header("Location: commandes_en_attente.php");
    exit();
}

// 1. Récupérer les infos de la commande et du client
$stmt = $pdo->prepare("SELECT f.*, u.nom, u.prenom, u.email 
                       FROM factures f 
                       JOIN users u ON f.client_id = u.id 
                       WHERE f.id = ?");
$stmt->execute([$id_commande]);
$commande = $stmt->fetch();

// 2. Récupérer les livres de cette commande
$stmtL = $pdo->prepare("SELECT lf.*, l.titre, l.isbn 
                        FROM ligne_factures lf 
                        JOIN livres l ON lf.livre_id = l.id 
                        WHERE lf.facture_id = ?");
$stmtL->execute([$id_commande]);
$lignes = $stmtL->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails Commande #<?= $id_commande ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            padding-top: 90px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .info-client {
            background: #eef2f7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .total-row {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
            padding-top: 20px;
        }

        .btn-retour {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <a href="commandes_en_attente.php" class="btn-retour">⬅ Retour aux commandes</a>

        <h2>Détails de la Commande #<?= $id_commande ?></h2>

        <div class="info-client">
            <strong>Client :</strong> <?= htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']) ?><br>
            <strong>Email :</strong> <?= htmlspecialchars($commande['email']) ?><br>
            <strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($commande['date_facture'])) ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>ISBN</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                    <tr>
                        <td><?= htmlspecialchars($ligne['titre']) ?></td>
                        <td><?= htmlspecialchars($ligne['isbn']) ?></td>
                        <td>x <?= $ligne['quantite'] ?></td>
                        <td><?= $ligne['prix_unitaire'] ?> €</td>
                        <td><?= $ligne['quantite'] * $ligne['prix_unitaire'] ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-row">
            Total à régler : <?= $commande['total'] ?> €
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <a href="commandes_en_attente.php?action=valider&id=<?= $id_commande ?>"
                style="background:#27ae60; color:white; padding:15px; border-radius:8px; text-decoration:none; flex:1; text-align:center; font-weight:bold;">
                ✅ Confirmer la préparation et valider
            </a>
        </div>
    </div>
</body>

</html>