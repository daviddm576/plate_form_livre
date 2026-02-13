<?php
session_start();
$id = $_GET['id'] ?? null;

if ($id && isset($_SESSION['panier'][$id])) {
    unset($_SESSION['panier'][$id]);
}

header("Location: panier.php");
exit();