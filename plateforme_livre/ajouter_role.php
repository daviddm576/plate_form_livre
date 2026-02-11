<?php
require_once 'fonctions.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_role'])) {
    $nom_role = htmlspecialchars($_POST['nom_role']);
    $desc_role = htmlspecialchars($_POST['desc_role']);

    if (!empty($nom_role)) {
        if (addRole($pdo, $nom_role, $desc_role)) {
            $message = "<div class='alert success'>✅ Rôle '$nom_role' créé !</div>";
        } else {
            $message = "<div class='alert error'>❌ Erreur de création.</div>";
        }
    }
}

$roles_existants = getRoles($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Rôles - RACINE</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --bg: #f0f2f5;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            border-bottom: 1px solid #eee;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            color: var(--primary);
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 style="color: var(--primary);">🛡️ Nouveau Rôle Librairie</h2>

        <?php echo $message; ?>

        <form method="POST">
            <label>Nom du Rôle</label>
            <input type="text" name="nom_role" placeholder="Ex: Gestionnaire de Stock" required>

            <label>Description des responsabilités</label>
            <textarea name="desc_role" rows="3" placeholder="Ex: Peut ajouter des livres et modifier les prix..."></textarea>

            <button type="submit" name="ajouter_role">Enregistrer le rôle</button>
        </form>

        <h3>Rôles en place</h3>
        <table>
            <thead>
                <tr>
                    <th>Rôle</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles_existants as $role): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($role['nom']) ?></strong></td>
                        <td><small><?= htmlspecialchars($role['description'] ?? 'Aucune description') ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="text-align:center; margin-top:20px;">
            <a href="admin.php" style="text-decoration:none; color:#7f8c8d;">← Dashboard</a>
        </p>
    </div>

</body>

</html>