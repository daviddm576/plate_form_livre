<?php
require_once 'fonctions.php';

// Gestion de la suppression
if (isset($_GET['delete'])) {
    deleteUser($pdo, $_GET['delete']);
    header("Location: voir_utilisateurs.php");
    exit();
}

// Récupération des filtres
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role_id'] ?? '';

// Construction de la requête avec filtres
$query = "SELECT u.*, r.nom as role_nom FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (u.nom LIKE ? OR u.prenom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role_filter)) {
    $query .= " AND u.role_id = ?";
    $params[] = $role_filter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

$roles = getRoles($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: #eee;
            padding: 15px;
            border-radius: 8px;
        }

        input,
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .btn-edit {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
            font-weight: bold;
        }

        .btn-delete {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-search {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-add {
            background: #3498db;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>📋 Gestion des Membres RACINE</h2>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Rechercher un nom..." value="<?= htmlspecialchars($search) ?>">
            <select name="role_id">
                <option value="">Tous les rôles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $role_filter == $r['id'] ? 'selected' : '' ?>><?= $r['nom'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-search">Filtrer</button>
            <a href="voir_utilisateurs.php" style="text-decoration:none; font-size:0.8rem; align-self:center;">Réinitialiser</a>
        </form>

        <div class="action-bar">
            <a href="admin.php" class="btn btn-back">← Dashboard</a>
            <a href="index.php" class="btn btn-add">+ Ajouter un utilisateur</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></strong></td>
                        <td><?= htmlspecialchars($u['role_nom']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <a href="modifier_utilisateur.php?id=<?= $u['id'] ?>" class="btn-edit">Modifier</a>
                            <a href="voir_utilisateurs.php?delete=<?= $u['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>

</body>

</html>