<?php
session_start(); // Toujours démarrer la session avant de la détruire
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit physiquement la session sur le serveur

// Redirection vers la page de login avec un petit message (optionnel)
header("Location: connexion.php?msg=deconnecte");
exit();
?>