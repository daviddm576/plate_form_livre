<?php
session_start();
require_once 'fonctions.php';

// Sécurité
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    header("Location: connexion.php");
    exit();
}

$message = "";
$all_books = $pdo->query("SELECT id, titre, prix, stock FROM livres WHERE stock > 0 ORDER BY titre ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_facture'])) {
    $client = trim($_POST['client_nom']);
    $livres_ids = $_POST['livre_id']; // Tableau (grâce au name="livre_id[]")
    $quantites = $_POST['quantite'];  // Tableau (grâce au name="quantite[]")
    $user_id = $_SESSION['user']['id'];
    $total_facture = 0;

    try {
        $pdo->beginTransaction();

        // Créer la facture initiale avec un total de 0
        $stmt = $pdo->prepare("INSERT INTO factures (client_nom, total, statut, user_id) VALUES (?, 0, 'Payé', ?)");
        $stmt->execute([$client, $user_id]);
        $facture_id = $pdo->lastInsertId();

        // Parcourir chaque ligne de livre ajoutée
        foreach ($livres_ids as $index => $l_id) {
            $qte = (int)$quantites[$index];

            $st = $pdo->prepare("SELECT prix, stock FROM livres WHERE id = ?");
            $st->execute([$l_id]);
            $b = $st->fetch();

            if ($b && $b['stock'] >= $qte) {
                $sous_total = $b['prix'] * $qte;
                $total_facture += $sous_total;

                // Enregistrer le détail
                $stmtL = $pdo->prepare("INSERT INTO ligne_factures (facture_id, livre_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
                $stmtL->execute([$facture_id, $l_id, $qte, $b['prix']]);

                // Déduire du stock
                updateStockQuantity($pdo, $l_id, -$qte);
            } else {
                throw new Exception("Stock insuffisant pour l'un des livres.");
            }
        }

        // Mettre à jour le total réel
        $stmtU = $pdo->prepare("UPDATE factures SET total = ? WHERE id = ?");
        $stmtU->execute([$total_facture, $facture_id]);

        $pdo->commit();
        $message = "<div class='alert success'>✅ Facture #$facture_id générée ($total_facture €) !</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert error'>❌ Erreur : " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <meta charset="UTF-8">
    <title>Créer Facture - RACINE</title>
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
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .ligne-livre {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .btn-add {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .btn-remove {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit {
            background: #27ae60;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        input,
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background: #d4efdf;
            color: #1e8449;
        }

        .error {
            background: #fadbd8;
            color: #c0392b;
        }
    </style>
</head>

<body>
    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <h2>🧾 Nouvelle Vente Multi-articles</h2>
        <?= $message ?>

        <form method="POST" id="factureForm">
            <div style="margin-bottom: 20px;">
                <label><strong>Nom du Client :</strong></label><br>
                <input type="text" name="client_nom" required style="width: 100%; margin-top: 5px;">
            </div>

            <div id="liste-livres">
                <label><strong>Sélection des livres :</strong></label>
                <div class="ligne-livre">
                    <select name="livre_id[]" required style="flex: 3;">
                        <option value="">Choisir un livre...</option>
                        <?php foreach ($all_books as $lb): ?>
                            <option value="<?= $lb['id'] ?>"><?= htmlspecialchars($lb['titre']) ?> (<?= $lb['prix'] ?>€)</option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantite[]" value="1" min="1" style="flex: 1;" placeholder="Qté">
                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">✕</button>
                </div>
            </div>

            <button type="button" class="btn-add" onclick="ajouterLigne()">+ Ajouter un autre livre</button>

            <div style="margin-top: 20px; border-top: 2px solid #eee; pt: 20px;">
                <button type="submit" name="valider_facture" class="btn-submit">Valider et enregistrer la vente</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Initialise le premier select
            $('.select-livre').select2();
        });

        function ajouterLigne() {
            const conteneur = document.getElementById('liste-livres');
            const nouvelleLigne = document.createElement('div');
            nouvelleLigne.className = 'ligne-livre';

            // Ajoute la classe 'select-livre' au nouveau select
            nouvelleLigne.innerHTML = `
        <select name="livre_id[]" class="select-livre" required style="flex: 3;">
            <option value="">Choisir un livre...</option>
            <?php foreach ($all_books as $lb): ?>
                <option value="<?= $lb['id'] ?>"><?= htmlspecialchars($lb['titre']) ?> (<?= $lb['prix'] ?>€)</option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantite[]" value="1" min="1" style="flex: 1;">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">✕</button>
    `;
            conteneur.appendChild(nouvelleLigne);

            // ACTIVE LA RECHERCHE SUR LE NOUVEAU SELECT
            $(nouvelleLigne).find('.select-livre').select2();
        }
    </script>
</body>

</html>