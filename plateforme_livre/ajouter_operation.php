<?php
require_once 'fonctions.php';

$message = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_op'])) {
    $nom = htmlspecialchars($_POST['nom_op']);
    $desc = htmlspecialchars($_POST['desc_op']);

    if (addOperation($pdo, $nom, $desc)) {
        $message = "<p style='color:green;'>Opération ajoutée !</p>";
    } else {
        $message = "<p style='color:red;'>Erreur lors de l'ajout.</p>";
    }
}

// Récupération de la liste pour affichage
$toutes_ops = getOperations($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Opérations</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f8f9fa;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #6c757d;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background: #5a6268;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="index.php">← Retour aux utilisateurs</a>
        <h2>Ajouter une Opération</h2>

        <?php echo $message; ?>

        <form method="POST">
            <input type="text" name="nom_op" placeholder="Nom (ex: create_user, delete_article)" required>
            <textarea name="desc_op" placeholder="Description de l'action..."></textarea>
            <button type="submit" name="ajouter_op">Enregistrer l'opération</button>
        </form>

        <hr>

        <h3>Liste des opérations existantes</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
            </tr>
            <?php foreach ($toutes_ops as $op): ?>
                <tr>
                    <td><?= $op['id'] ?></td>
                    <td><strong><?= htmlspecialchars($op['nom']) ?></strong></td>
                    <td><?= htmlspecialchars($op['description']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>

</html>