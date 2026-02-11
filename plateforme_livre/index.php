<?php
require_once 'fonctions.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_user'])) {
    $success = addUser(
        $pdo,
        $_POST['username'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        $_POST['telephone'],
        $_POST['sexe'],
        $_POST['adresse'],
        $_POST['password'],
        $_POST['role_id']
    );

    if ($success) {
        $message = "<div class='alert success'>✅ Utilisateur complet ajouté !</div>";
    } else {
        $message = "<div class='alert error'>❌ Erreur lors de l'ajout.</div>";
    }
}
$roles = getRoles($pdo);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription Complète - RACINE</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg: #f0f2f5;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
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

        button {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
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
        <h2 style="text-align:center; color: var(--primary);">Nouveau Profil Utilisateur</h2>

        <?php echo $message; ?>

        <form method="POST">
            <div class="row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" placeholder="Ex: Musas" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" placeholder="Ex: David" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="username" placeholder="D.Musas" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" placeholder="+243 855244574..." required>
                </div>
            </div>

            <div class="row">
                <div class="form-group" style="flex: 2;">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Sexe</label>
                    <select name="sexe" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse Résidentielle</label>
                <textarea name="adresse" rows="2" placeholder="N°, Avenue, Quartier, Ville..."></textarea>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <button type="submit" name="ajouter_user">Enregistrer le membre</button>
        </form>
        <p style="text-align:center;"><a href="voir_utilisateurs.php" style="color: #95a5a6; text-decoration:none;">← voir la liste des utiisateurs</a></p>
        <p style="text-align:center;"><a href="admin.php" style="color: #95a5a6; text-decoration:none;">← Annuler et retourner</a></p>
    </div>

</body>

</html>