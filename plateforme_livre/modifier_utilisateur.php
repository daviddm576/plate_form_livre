<?php
require_once 'fonctions.php';

$message = "";

// 1. Vérifier si l'ID de l'utilisateur est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: voir_utilisateurs.php");
    exit();
}

$id = $_GET['id'];
$user = getUserById($pdo, $id);

// Si l'utilisateur n'existe pas en base
if (!$user) {
    die("Utilisateur non trouvé.");
}

// 2. Traitement de la modification (lorsqu'on clique sur le bouton)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifier_user'])) {
    // On prépare les données (on garde l'ID caché du formulaire)
    $data = [
        'id'        => $_POST['id'],
        'username'  => htmlspecialchars($_POST['username']),
        'nom'       => htmlspecialchars($_POST['nom']),
        'prenom'    => htmlspecialchars($_POST['prenom']),
        'email'     => htmlspecialchars($_POST['email']),
        'telephone' => htmlspecialchars($_POST['telephone']),
        'sexe'      => $_POST['sexe'],
        'adresse'   => htmlspecialchars($_POST['adresse']),
        'role_id'   => $_POST['role_id']
    ];

    if (updateUser($pdo, $data)) {
        // Redirection avec un petit message de succès dans l'URL
        header("Location: voir_utilisateurs.php?msg=updated");
        exit();
    } else {
        $message = "<div class='alert error'>❌ Erreur lors de la mise à jour.</div>";
    }
}

$roles = getRoles($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Profil - RACINE</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --bg: #f0f2f5;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            display: flex;
            justify-content: center;
            padding: 40px;
        }

        .container {
            background: white;
            width: 100%;
            max-width: 600px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.85rem;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input:focus {
            border-color: var(--accent);
            outline: none;
        }

        .btn-save {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
        }

        .btn-save:hover {
            background: #219150;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 style="text-align:center; color: var(--primary);">✏️ Modifier l'Utilisateur</h2>
        <p style="text-align:center; color: #7f8c8d; margin-bottom: 25px;">ID membre : #<?= $user['id'] ?></p>

        <?php echo $message; ?>

        <form action="modifier_utilisateur.php?id=<?= $user['id'] ?>" method="POST">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="form-group" style="flex: 2;">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Sexe</label>
                    <select name="sexe">
                        <option value="M" <?= $user['sexe'] == 'M' ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= $user['sexe'] == 'F' ? 'selected' : '' ?>>Féminin</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse</label>
                <textarea name="adresse" rows="2"><?= htmlspecialchars($user['adresse']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Rôle du membre</label>
                <select name="role_id" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= ($user['role_id'] == $role['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="modifier_user" class="btn-save">Mettre à jour le profil</button>
            <a href="voir_utilisateurs.php" style="display:block; text-align:center; margin-top:15px; color:#95a5a6; text-decoration:none;">Annuler les modifications</a>
        </form>
    </div>

</body>

</html>