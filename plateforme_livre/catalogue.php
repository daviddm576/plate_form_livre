<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Seul un client peut commander ici
if ($_SESSION['user']['role'] !== 'Client') {
    header("Location: connexion.php");
    exit();
}

$livres = $pdo->query("SELECT * FROM livres WHERE stock > 0")->fetchAll();
?>

<div class="catalogue-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; padding: 20px;">
    <?php foreach ($livres as $l): ?>
        <div class="book-card" style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <img src="img/<?= $l['image'] ?? 'default.jpg' ?>" style="width: 100px; height: 150px; object-fit: cover;">
            <h3><?= htmlspecialchars($l['titre']) ?></h3>
            <p style="color: #27ae60; font-weight: bold;"><?= $l['prix'] ?> €</p>

            <form action="ajouter_au_panier.php" method="POST">
                <input type="hidden" name="livre_id" value="<?= $l['id'] ?>">
                <input type="number" name="qte" value="1" min="1" max="<?= $l['stock'] ?>" style="width: 50px;">
                <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer;">
                    🛒 Ajouter
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</div>