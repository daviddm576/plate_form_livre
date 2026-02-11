<?php
session_start();
require_once 'fonctions.php';  // Tes fonctions

// Sécurité
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Super Admin') {
    header("Location: connexion.php");
    exit();
}

$message = "";

// --- LOGIQUE D'ACTION ---

// Si on ajoute
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_cat'])) {
    $nom = trim($_POST['nom_categorie']);
    $desc = trim($_POST['description']);
    
    if (addCategory($pdo, $nom, $desc)) {
        $message = "<p style='color:green;'>Catégorie ajoutée avec succès !</p>";
    } else {
        $message = "<p style='color:red;'>Erreur : le nom existe déjà ou une erreur est survenue.</p>";
    }
}

// Si on supprime
if (isset($_GET['delete'])) {
    deleteCategory($pdo, $_GET['delete']);
    header("Location: gestion_categories.php");
    exit();
}

// Récupération pour l'affichage
$categories = getAllCategories($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Catégories - RACINE</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; padding-top: 90px; }
        .container { max-width: 1000px; margin: auto; display: flex; gap: 30px; padding: 20px; }
        
        .sidebar { flex: 1; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); height: fit-content; }
        .main-content { flex: 2; }

        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn { background: #27ae60; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; cursor: pointer; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #34495e; color: white; }
        .delete-link { color: #e74c3c; text-decoration: none; font-size: 0.9rem; border: 1px solid #e74c3c; padding: 5px 10px; border-radius: 4px; }
        .delete-link:hover { background: #e74c3c; color: white; }
    </style>
</head>
<body>

    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <div class="sidebar">
            <h3>Nouvelle Catégorie</h3>
            <?= $message ?>
            <form method="POST">
                <input type="text" name="nom_categorie" placeholder="Nom de la catégorie" required>
                <textarea name="description" placeholder="Courte description..." rows="4"></textarea>
                <button type="submit" name="ajouter_cat" class="btn">Enregistrer</button>
            </form>
        </div>

        <div class="main-content">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cat['nom_categorie']) ?></strong></td>
                        <td><?= htmlspecialchars($cat['description']) ?></td>
                        <td>
                            <a href="?delete=<?= $cat['id'] ?>" class="delete-link" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>