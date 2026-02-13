<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'fonctions.php';

// Gestion de la suppression (Logique inchangée)
if (isset($_GET['delete'])) {
    deleteUser($pdo, $_GET['delete']);
    header("Location: voir_utilisateurs.php");
    exit();
}

// Récupération des filtres (Logique inchangée)
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role_id'] ?? '';

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
    <title>Membres RACINE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --accent: #3498db;
            --danger: #ef4444;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            padding-top: 70px; /* Pour la navbar fixe */
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Barre de filtres */
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 12px;
            align-items: center;
        }

        input, select {
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            flex: 1;
        }

        .btn-search {
            background: var(--text-main);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-search:hover { opacity: 0.9; }

        /* Actions au-dessus du tableau */
        .action-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back { background: #e2e8f0; color: #475569; }
        .btn-back:hover { background: #cbd5e1; }

        .btn-add { background: var(--accent); color: white; }
        .btn-add:hover { filter: brightness(1.1); }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
        }

        th {
            text-align: left;
            padding: 16px;
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            border-bottom: 2px solid var(--border);
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
        }

        tr:last-child td { border-bottom: none; }

        tr:hover td { background-color: #f8fafc; }

        /* Badges & Actions */
        .role-tag {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-edit { color: var(--accent); text-decoration: none; font-weight: 600; }
        .btn-delete { color: var(--danger); text-decoration: none; font-weight: 600; margin-left: 15px; }

        .btn-edit:hover, .btn-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <?php include 'bar_de_navigation.php'; ?>

    <div class="container">
        <h2><i class="fas fa-users-cog"></i> Gestion des Membres</h2>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Rechercher un membre..." value="<?= htmlspecialchars($search) ?>">
            <select name="role_id">
                <option value="">Tous les rôles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $role_filter == $r['id'] ? 'selected' : '' ?>><?= $r['nom'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-search">Filtrer</button>
            <a href="voir_utilisateurs.php" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none; margin-left: 10px;">Réinitialiser</a>
        </form>

        <div class="action-bar">
            <a href="admin.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Panneau de control</a>
            <a href="index.php" class="btn btn-add"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></strong></td>
                        <td><span class="role-tag"><?= htmlspecialchars($u['role_nom']) ?></span></td>
                        <td style="color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <a href="modifier_utilisateur.php?id=<?= $u['id'] ?>" class="btn-edit">Modifier</a>
                            <a href="voir_utilisateurs.php?delete=<?= $u['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>