<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Seul le gestionnaire ou l'admin peut valider
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    header("Location: login.php");
    exit();
}

// 1. Action de Validation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_commande = $_GET['id'];

    if ($_GET['action'] === 'valider') {
        // On passe le statut à 'Payé' (ou 'Validé')
        $stmt = $pdo->prepare("UPDATE factures SET statut = 'Payé', user_id = ? WHERE id = ?");
        $stmt->execute([$_SESSION['user']['id'], $id_commande]);

        // Note : On pourrait aussi déduire le stock ici si ce n'est pas fait à la commande
        $msg = "✅ Commande #$id_commande validée avec succès !";
    } elseif ($_GET['action'] === 'annuler') {
        $stmt = $pdo->prepare("UPDATE factures SET statut = 'Annulé' WHERE id = ?");
        $stmt->execute([$id_commande]);
        $msg = "❌ Commande #$id_commande annulée.";
    }
}

// 2. Récupération des commandes en attente
$sql = "SELECT f.*, u.nom, u.prenom 
        FROM factures f 
        JOIN users u ON f.client_id = u.id 
        WHERE f.statut = 'En attente de validation' 
        ORDER BY f.date_facture DESC";
$commandes = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commandes en attente - RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            padding-top: 80px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-attente {
            background: #f1c40f;
            color: #7f8c8d;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-right: 5px;
        }

        .btn-valider {
            background: #27ae60;
            color: white;
        }

        .btn-annuler {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>

<body>
    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <h2>⏳ Commandes à valider</h2>
        <?php if (isset($msg)) echo "<p style='color:green; font-weight:bold;'>$msg</p>"; ?>

        <?php if (empty($commandes)): ?>
            <p>Aucune commande en attente pour le moment. ☕</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td>#<?= $cmd['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['date_facture'])) ?></td>
                            <td><strong><?= htmlspecialchars($cmd['nom'] . ' ' . $cmd['prenom']) ?></strong></td>
                            <td><?= $cmd['total'] ?> €</td>
                            <td>
                                <a href="voir_details_commande.php?id=<?= $cmd['id'] ?>" class="btn" style="background:#3498db; color:white;">👁️ Voir</a>
                                <a href="?action=valider&id=<?= $cmd['id'] ?>" class="btn btn-valider" onclick="return confirm('Valider cette commande ?')">✅ Valider</a>
                                <a href="?action=annuler&id=<?= $cmd['id'] ?>" class="btn btn-annuler" onclick="return confirm('Annuler cette commande ?')">❌ Refuser</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>