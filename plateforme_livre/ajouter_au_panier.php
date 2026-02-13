<?php
session_start();

// On s'assure que les données sont bien des nombres entiers
$id = (int)$_POST['livre_id'];
$qte = (int)$_POST['qte'];

if ($id > 0 && $qte > 0) {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    $_SESSION['panier'][$id] = ($_SESSION['panier'][$id] ?? 0) + $qte;
}

header("Location: catalogue.php?success=ajoute");
exit();