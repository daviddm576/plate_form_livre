<?php
require_once 'fonctions.php';
$id = $_GET['id'];

// Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livre = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ici, tu fais un UPDATE au lieu d'un INSERT
    $titre = $_POST['titre'];
    // ... récupère les autres champs ...

    $stmt = $pdo->prepare("UPDATE livres SET couverture = ?, titre = ?, auteur = ?, prix = ?, categorie= ?, stock = ? WHERE id = ?");
    $stmt->execute([$titre, $auteur, $prix, $stock, $id]);
    
    header("Location: gestion_livres.php?msg=modifie");
    exit();
}
?>

<input type="text" name="titre" value="<?= htmlspecialchars($livre['titre']) ?>">