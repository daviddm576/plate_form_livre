<?php
require_once 'fonctions.php';

$message = "";
$selected_role = isset($_POST['role_id']) ? $_POST['role_id'] : null;

// Sauvegarde des permissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_permissions'])) {
    $ops = isset($_POST['ops']) ? $_POST['ops'] : [];
    if (updateRolePermissions($pdo, $selected_role, $ops)) {
        $message = "<p style='color:green;'>Permissions mises à jour !</p>";
    }
}

$roles = getRoles($pdo);
$all_operations = getOperations($pdo);
$current_permissions = $selected_role ? getRolePermissions($pdo, $selected_role) : [];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Permissions</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f4f9;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .role-selector {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .perm-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .perm-item:hover {
            background: #f8f9fa;
        }

        .perm-item input {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .btn-save {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            font-size: 16px;
        }

        .nav {
            margin-bottom: 15px;
        }

        .nav a {
            margin-right: 15px;
            text-decoration: none;
            color: blue;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="nav">
            <a href="index.php">Utilisateurs</a> | <a href="ajouter_role.php">Rôles</a> | <a href="ajouter_operation.php">Opérations</a>
        </div>

        <h2>Matrice des Permissions</h2>
        <?php echo $message; ?>

        <form method="POST" id="roleForm">
            <div class="role-selector">
                <label><strong>1. Choisissez un Rôle :</strong></label>
                <select name="role_id" onchange="document.getElementById('roleForm').submit()" style="width: 100%; padding: 10px; margin-top: 10px;">
                    <option value="">-- Sélectionner un rôle --</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= ($selected_role == $role['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($selected_role): ?>
                <label><strong>2. Attribuez les opérations :</strong></label>
                <div style="margin-top: 10px;">
                    <?php foreach ($all_operations as $op): ?>
                        <div class="perm-item">
                            <input type="checkbox" name="ops[]" value="<?= $op['id'] ?>"
                                <?= in_array($op['id'], $current_permissions) ? 'checked' : '' ?>>
                            <div>
                                <strong><?= htmlspecialchars($op['nom']) ?></strong><br>
                                <small style="color: #666;"><?= htmlspecialchars($op['description']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="save_permissions" class="btn-save">Enregistrer les droits pour ce rôle</button>
            <?php endif; ?>
        </form>
    </div>

</body>

</html>