<?php
session_start();
require_once 'config.php'; // Ton fichier de connexion PDO

// Vérification de sécurité
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    header("Location: connexion.php");
    exit();
}

// 1. Récupérer les catégories pour le menu déroulant
$query = $pdo->query("SELECT * FROM categories ORDER BY nom_categorie ASC");
$categories = $query->fetchAll();

// 2. Traitement du formulaire
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $cat_id = $_POST['categorie_id'];

    // Gestion de l'image
    $image_name = "default_cover.jpg";

    if (!empty($_FILES['couverture']['name'])) {
        $target_dir = "couvertures/";

        // Nettoyage du nom de fichier (on remplace les espaces par des underscores)
        $clean_name = str_replace(' ', '_', $_FILES['couverture']['name']);
        $image_name = time() . "_" . basename($clean_name);

        $target_file = $target_dir . $image_name;

        // CORRECTION ICI : 'couverture' au lieu de 'couvertures'
        if (move_uploaded_file($_FILES['couverture']['tmp_name'], $target_file)) {
            // Succès
        } else {
            // En cas d'échec, on garde l'image par défaut
            $image_name = "default_cover.jpg";
        }
    }
    $sql = "INSERT INTO livres (titre, auteur, prix, stock, categorie_id, image_couverture) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$titre, $auteur, $prix, $stock, $cat_id, $image_name])) {
        $message = "<p style='color:green;'>Livre ajouté avec succès !</p>";
    } else {
        $message = "<p style='color:red;'>Erreur lors de l'ajout.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un Livre - RACINE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding-top: 90px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn-submit {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>

    <?php include 'bar_de_navigation.php'; ?>

    <div class="form-container">
        <h2>📚 Ajouter un nouveau livre</h2>
        <?= $message ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Titre du livre</label>
                <input type="text" name="titre" required>
            </div>
            <div class="form-group">
                <label>Auteur</label>
                <input type="text" name="auteur" required>
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie_id">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Prix (Fc)</label>
                    <input type="number" step="0.01" name="prix" required>
                </div>
                <div style="flex: 1;">
                    <label>Stock</label>
                    <input type="number" name="stock" required>
                </div>
            </div>
            <div class="form-group">
                <label>Image de couverture</label>
                <input type="file" name="couverture" accept="image/*">
            </div>
            <button type="submit" class="btn-submit">Enregistrer le livre</button>
        </form>
    </div>

</body>

</html>