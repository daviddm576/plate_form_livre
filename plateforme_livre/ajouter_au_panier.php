<?php
session_start();
$id = $_POST['livre_id'];
$qte = $_POST['qte'];

// Si le panier n'existe pas, on le crée
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// On ajoute ou on met à jour la quantité
$_SESSION['panier'][$id] = ($_SESSION['panier'][$id] ?? 0) + $qte;

header("Location: catalogue.php?success=ajoute");
exit();