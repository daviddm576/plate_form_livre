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
    <title>Facturation RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            padding-top: 50px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Design Style Facture */
        .header-facture {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #2c3e50;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .total-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .total-box {
            background: #2c3e50;
            color: white;
            padding: 15px;
            font-size: 1.4rem;
            text-align: center;
            border-radius: 5px;
        }

        .btn-add {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-submit {
            background: #27ae60;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-remove {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
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
        <div class="header-facture">
            <div>
                <h2>🧾 FACTURATION</h2>
            </div>
            <div><strong>Date:</strong> <?= date('d/m/Y') ?></div>
        </div>

        <?= $message ?>

        <form method="POST" id="factureForm">
            <div style="margin-bottom: 30px;">
                <label><strong>Nom du Client / Bénéficiaire :</strong></label>
                <input type="text" name="client_nom" required placeholder="Ex: Jean Mukendi" style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>

            <table id="table-articles">
                <thead>
                    <tr>
                        <th style="width: 50%;">Livre</th>
                        <th>Prix (FC)</th>
                        <th>Qté</th>
                        <th>Total (FC)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="corps-facture">
                </tbody>
            </table>

            <button type="button" class="btn-add" onclick="ajouterLigne()">+ Ajouter un article</button>

            <div class="total-section">
                <div style="margin-bottom: 10px; font-weight: bold;">TOTAL GÉNÉRAL</div>
                <div class="total-box" id="grand-total">0 FC</div>
            </div>

            <div style="clear: both;"></div>
            <button type="submit" name="valider_facture" class="btn-submit">✅ VALIDER ET IMPRIMER LA VENTE</button>
        </form>
    </div>

    <script>
        // Liste des livres formatée pour JS
        const livresData = <?= json_encode($all_books) ?>;

        $(document).ready(function() {
            ajouterLigne(); // On ajoute une ligne vide au départ
        });

        function ajouterLigne() {
            const idUnique = Date.now();
            let options = '<option value="">Choisir un livre...</option>';
            livresData.forEach(l => {
                options += `<option value="${l.id}" data-prix="${l.prix}">${l.titre}</option>`;
            });

            const ligneHTML = `
            <tr class="ligne-livre">
                <td>
                    <select name="livre_id[]" class="select-livre" required style="width:100%" onchange="calculerLigne(this)">
                        ${options}
                    </select>
                </td>
                <td><input type="text" class="prix-unit" readonly style="width:80px; border:none; background:transparent;"></td>
                <td><input type="number" name="quantite[]" value="1" min="1" class="input-qte" style="width:60px;" oninput="calculerLigne(this)"></td>
                <td class="total-ligne">0</td>
                <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>
            </tr>
        `;

            $('#corps-facture').append(ligneHTML);
            $('.select-livre').select2();
        }

        function calculerLigne(element) {
            const ligne = $(element).closest('tr');
            const prix = ligne.find('.select-livre option:selected').data('prix') || 0;
            const qte = ligne.find('.input-qte').val() || 0;

            const total = prix * qte;

            ligne.find('.prix-unit').val(prix);
            ligne.find('.total-ligne').text(total.toLocaleString('fr-FR'));

            calculerGrandTotal();
        }

        function calculerGrandTotal() {
            let grandTotal = 0;
            $('.ligne-livre').each(function() {
                const prix = $(this).find('.select-livre option:selected').data('prix') || 0;
                const qte = $(this).find('.input-qte').val() || 0;
                grandTotal += (prix * qte);
            });

            $('#grand-total').text(grandTotal.toLocaleString('fr-FR') + ' FC');
        }

        function supprimerLigne(btn) {
            $(btn).closest('tr').remove();
            calculerGrandTotal();
        }
    </script>

</body>

</html>