<?php
session_start();
require_once 'fonctions.php';

// Sécurité : Seul l'Admin ou le Gestionnaire
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Super Admin', 'Gestionnaire de stock'])) {
    exit("Accès refusé");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // On récupère d'abord le nom de l'image pour la supprimer du dossier
    $stmt = $pdo->prepare("SELECT couverture FROM livres WHERE id = ?");
    $stmt->execute([$id]);
    $livre = $stmt->fetch();

    if ($livre && $livre['couverture'] != 'default_cover.jpg') {
        unlink("couvertures/" . $livre['couverture']); // Supprime la photo du serveur
    }

    // Suppression en base de données
    $stmt = $pdo->prepare("DELETE FROM livres WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: gestion_livres.php?msg=supprime");
exit();