<?php
// functions.php
require_once 'config.php';

// Fonction pour récupérer tous les rôles
function getRoles($pdo)
{
    $stmt = $pdo->query("SELECT * FROM roles");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour ajouter un utilisateur
function addUser($pdo, $username, $nom, $prenom, $email, $telephone, $sexe, $adresse, $password, $role_id)
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (username, nom, prenom, email, telephone, sexe, adresse, password, role_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $nom, $prenom, $email, $telephone, $sexe, $adresse, $hashedPassword, $role_id]);
}
?>
<?php

// Fonction pour ajouter un nouveau rôle
function addRole($pdo, $nom, $description) {
    $sql = "INSERT INTO roles (nom, description) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
   
    return $stmt->execute([$nom, $description]);
}


?>

<?php
// Fonction pour ajouter une opération
function addOperation($pdo, $nom, $description)
{
    $sql = "INSERT INTO operations (nom, description) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nom, $description]);
}

// Fonction pour lister toutes les opérations 
function getOperations($pdo)
{
    $stmt = $pdo->query("SELECT * FROM operations ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php

// Supprime les anciennes permissions et ajoute les nouvelles pour un rôle
function updateRolePermissions($pdo, $role_id, $operation_ids)
{
    // 1. On nettoie les anciennes permissions pour ce rôle
    $stmt = $pdo->prepare("DELETE FROM role_operation WHERE role_id = ?");
    $stmt->execute([$role_id]);

    // 2. On ajoute les nouvelles
    if (!empty($operation_ids)) {
        $sql = "INSERT INTO role_operation (role_id, operation_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        foreach ($operation_ids as $op_id) {
            $stmt->execute([$role_id, $op_id]);
        }
    }
    return true;
}

// Récupère les IDs des opérations déjà attribuées à un rôle
function getRolePermissions($pdo, $role_id)
{
    $stmt = $pdo->prepare("SELECT operation_id FROM role_operation WHERE role_id = ?");
    $stmt->execute([$role_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Retourne un tableau simple d'IDs [1, 4, 5]
}
?>

<?php
// Supprimer un utilisateur
function deleteUser($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

// Récupérer un utilisateur par son ID (pour le formulaire de modification)
function getUserById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Modifier un utilisateur
function updateUser($pdo, $data) {
    $sql = "UPDATE users SET username=?, nom=?, prenom=?, email=?, telephone=?, sexe=?, adresse=?, role_id=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['username'], $data['nom'], $data['prenom'], 
        $data['email'], $data['telephone'], $data['sexe'], 
        $data['adresse'], $data['role_id'], $data['id']
    ]);
}
?>

<?php


function login($pdo, $identifier, $password) {
    // 1. Requête avec Jointure pour récupérer le nom du rôle via l'ID
    $sql = "SELECT u.*, r.nom
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? OR u.username = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // On démarre la session si ce n'est pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 3. Stockage de TOUTES les informations nécessaires
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'nom'      => $user['nom'],
            'prenom'   => $user['prenom'],
            'role'     => $user['nom'] // Le nom récupéré grâce à la jointure
        ];
        
        return true;
    }
    
    return false;
}

// --- FONCTIONS POUR LES CATÉGORIES ---

// Récupérer toutes les catégories
function getAllCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY nom_categorie ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter une catégorie
function addCategory($pdo, $nom, $description) {
    try {
        $stmt = $pdo->prepare("INSERT INTO categories (nom_categorie, description) VALUES (?, ?)");
        return $stmt->execute([$nom, $description]);
    } catch (PDOException $e) {
        return false;
    }
}

// Supprimer une catégorie
function deleteCategory($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

// Dans fonction.php
function addBook($pdo, $titre, $auteur, $prix, $stock, $cat_id, $image) {
    $sql = "INSERT INTO livres (titre, auteur, prix, stock, categorie_id, image_couverture) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$titre, $auteur, $prix, $stock, $cat_id, $image]);
}

// Récupérer l'état du stock (avec calcul d'alerte)
function getStockStatus($pdo) {
    $sql = "SELECT id, titre, stock, prix FROM livres ORDER BY stock ASC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Modifier la quantité (Ajouter ou Retirer)
function updateStockQuantity($pdo, $id, $quantite) {
    // On utilise une addition pour pouvoir envoyer des nombres négatifs (ex: -1 pour une vente)
    $sql = "UPDATE livres SET stock = stock + ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$quantite, $id]);
}
?>
