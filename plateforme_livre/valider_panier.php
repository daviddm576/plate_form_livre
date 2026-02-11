<?php
session_start();
require_once 'fonctions.php';

if (empty($_SESSION['panier'])) { die("Votre panier est vide"); }

try {
    $pdo->beginTransaction();
    
    // 1. Créer la commande (statut 'En attente de validation')
    $stmt = $pdo->prepare("INSERT INTO factures (client_id, total, statut, user_id) VALUES (?, 0, 'En attente de validation', NULL)");
    $stmt->execute([$_SESSION['user']['id']]);
    $facture_id = $pdo->lastInsertId();
    
    $total = 0;
    foreach ($_SESSION['panier'] as $id => $qte) {
        // ... (Même logique que pour le gestionnaire : calcul prix + insertion ligne_factures) ...
        // Note : On déduit le stock seulement quand le gestionnaire VALIDE la commande
    }

    $pdo->commit();
    unset($_SESSION['panier']); // On vide le panier
    echo "Commande réussie ! Un gestionnaire va la valider bientôt.";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur : " . $e->getMessage();
}