<?php
session_start();
require_once 'fonctions.php'; // Ta connexion PDO

// 1. Initialisation des variables
$panier_details = [];
$total_general = 0;

// 2. Récupérer les détails des livres si le panier n'est pas vide
if (!empty($_SESSION['panier'])) {
    $ids = array_keys($_SESSION['panier']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';

    $stmt = $pdo->prepare("SELECT * FROM livres WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $livres_bdd = $stmt->fetchAll();

    foreach ($livres_bdd as $l) {
        $id = $l['id'];
        $quantite = $_SESSION['panier'][$id];
        $sous_total = $l['prix'] * $quantite;
        $total_general += $sous_total;

        $panier_details[] = [
            'id' => $id,
            'titre' => $l['titre'],
            'prix' => $l['prix'],
            'quantite' => $quantite,
            'sous_total' => $sous_total,
            'image' => $l['image_couverture'] ?? 'default.jpg'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon Panier - Racine</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: left;
            color: #7f8c8d;
            border-bottom: 1px solid #eee;
            padding: 10px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .img-panier {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .prix-unitaire {
            color: #27ae60;
            font-weight: bold;
        }

        .total-ligne {
            font-weight: bold;
            color: #2c3e50;
        }

        .total-panier {
            text-align: right;
            margin-top: 30px;
            font-size: 1.5rem;
            color: #2c3e50;
        }

        .total-panier span {
            color: #27ae60;
            font-weight: 900;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-retour {
            background: #95a5a6;
            color: white;
        }

        .btn-commander {
            background: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .btn-commander:hover {
            background: #219150;
            transform: scale(1.05);
        }

        .btn-supprimer {
            color: #e74c3c;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <?php include 'barre_nav_client.php'; ?>

    <div class="container">
        <h1>🛒 Votre Panier</h1>

        <?php if (empty($panier_details)): ?>
            <p style="text-align:center; padding: 50px;">Votre panier est vide... <br><br>
                <a href="catalogue.php" class="btn btn-retour">Aller au catalogue</a>
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($panier_details as $item): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <img src="couvertures/<?= $item['image'] ?>" class="img-panier">
                                    <strong><?= htmlspecialchars($item['titre']) ?></strong>
                                </div>
                            </td>
                            <td class="prix-unitaire"><?= number_format($item['prix'], 0, '.', ' ') ?> FC</td>
                            <td><?= $item['quantite'] ?></td>
                            <td class="total-ligne"><?= number_format($item['sous_total'], 0, '.', ' ') ?> FC</td>
                            <td>
                                <a href="supprimer_item.php?id=<?= $item['id'] ?>" class="btn-supprimer">❌ Retirer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-panier">
                Total à payer : <span><?= number_format($total_general, 0, '.', ' ') ?> FC</span>
            </div>

            <div class="actions">
                <a href="catalogue.php" class="btn btn-retour">⬅ Continuer mes achats</a>
                <form action="valider_commande.php" method="POST">
                    <button type="submit" class="btn btn-commander">✅ Confirmer la commande</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>