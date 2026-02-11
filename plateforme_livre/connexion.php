<?php
require_once 'fonctions.php';
session_start();

// Si l'utilisateur est déjà connecté, on le redirige selon son rôle
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'Super Admin') {
        header("Location: admin.php");
    } else {
        header("Location: gestion_livres.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

    // Tentative de connexion
    if (login($pdo, $identifier, $password)) {
        // Une fois connecté, on vérifie le rôle pour rediriger au bon endroit
        $userRole = $_SESSION['user']['role'];

        if ($userRole === 'Super Admin') {
            header("Location: admin.php");
        } elseif ($userRole === 'Gestionnaire de stock') {
            header("Location: gestion_livres.php");
        } else {
            // Rôle par défaut si tu as des clients/visiteurs
            header("Location: catalogue_livres.php");
        }
        exit();
    } else {
        $error = "Identifiants incorrects. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - RACINE</title>
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --bg: #f0f2f5; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 380px; 
            text-align: center; /* Centre tout le contenu */
        }

        /* Style pour le conteneur du logo */
        .logo-container {
            margin-bottom: 20px;
        }

        .logo-img {
            max-width: 150px; /* Ajustez la taille selon votre logo */
            height: auto;
            display: block;
            margin: 0 auto;
        }

        h2 { color: var(--primary); margin-bottom: 25px; font-size: 1.5rem; }
        
        .form-group { text-align: left; margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.9rem; }
        
        input { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #e1e8ed; 
            border-radius: 10px; 
            box-sizing: border-box; 
            transition: 0.3s; 
        }
        
        input:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 8px rgba(52, 152, 219, 0.2); }
        
        .btn-login { 
            width: 100%; 
            padding: 14px; 
            background: var(--accent); 
            color: white; 
            border: none; 
            border-radius: 10px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 1rem; 
            margin-top: 10px;
        }
        
        .btn-login:hover { background: #2980b9; transform: translateY(-1px); }
        
        .error-msg { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-size: 0.85rem; 
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-container">
        <img src="img/logo.png" alt="Logo RACINE" class="logo-img">
    </div>

    <h2>Accès Plateforme</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email ou Pseudo</label>
            <input type="text" name="identifier" required placeholder="Ex: admin@racine.com">
        </div>

        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-login">Se connecter</button>
    </form>
</div>

</body>
</html>