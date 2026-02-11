-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 11 fév. 2026 à 10:13
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `livre_v`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom_categorie`, `description`) VALUES
(1, 'Roman', 'littérature générale et fiction contemporaines'),
(2, 'policier', ',enquête et romans noirs'),
(3, 'Science-fiction', 'Aventure dans le future et technologies avancées'),
(4, 'Informatique', 'Livres sur la programmation et les systèmes numériques'),
(5, 'Bande dessinée', 'Albums illustrés et romans graphique'),
(6, 'Histoire', 'Récit et analyses des événements passés');

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

CREATE TABLE `factures` (
  `id` int(11) NOT NULL,
  `client_nom` varchar(100) NOT NULL,
  `date_facture` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT 0.00,
  `statut` enum('Panier','En attente de validation','Payé','Annulé') DEFAULT 'En attente de validation',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ligne_factures`
--

CREATE TABLE `ligne_factures` (
  `id` int(11) NOT NULL,
  `facture_id` int(11) DEFAULT NULL,
  `livre_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

CREATE TABLE `livres` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image_couverture` varchar(255) DEFAULT 'default_cover.jpg',
  `categorie_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `operations`
--

CREATE TABLE `operations` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `operations`
--

INSERT INTO `operations` (`id`, `nom`, `description`) VALUES
(1, 'gestion des utilisateurs', 'permet d ajouter, supprimer et modifier un utilisateur '),
(2, 'Gestions du catalogue', 'permet de contrôler qui peut modifier les produits '),
(3, 'Gestion des ventes et commandes', 'c est le cœur de la activité commerciale'),
(4, 'Achat', 'acheter un livre'),
(5, 'Modifier profil', 'modifier un profil');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`) VALUES
(1, 'Super Admin', 'Les développeur ou le propriétaire '),
(2, 'Gestionnaire de stock', 'S&#039;occupe uniquement des livres'),
(3, 'Agent de commande', 'Gère les ventes et les livraisons'),
(4, 'Client', 'l&#039;utilisateur final qui achète ');

-- --------------------------------------------------------

--
-- Structure de la table `role_operation`
--

CREATE TABLE `role_operation` (
  `role_id` int(11) NOT NULL,
  `operation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role_operation`
--

INSERT INTO `role_operation` (`role_id`, `operation_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 3),
(4, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `nom`, `prenom`, `sexe`, `email`, `telephone`, `adresse`, `password`, `role_id`) VALUES
(2, 'tmbdmnq@gmail.com', 'tumba', 'dominique', 'M', 'DMNQ@gmail.com', '0993180297', '12 bhdhjjkdk ', '$2y$10$gTfUJlKR266PllmXI57FauFOwvS7dSD4roi/2cAwIiy09bGV/e3AO', 1),
(5, 'daviddm@gmail.com', 'Musas', 'David', 'M', 'davimusas2@gmail.com', '+243855244574', '19,av: lac kipopo, Q: Gambela2, C: Lubumbashi', '$2y$10$RFuEKyv0eXetymjU0bUOkuvmPb6Tit71t6X2T30XsbCYchb.yWiWq', 1),
(6, 'gogo', 'bashiya', 'Gothank', 'F', 'gogo@gmail.com', '+243855244534', '20, Av: , Q: bel-air', '$2y$10$DE.QjHtcd1Xq2G7q/BSaWeeoMafcg7GAzbyLXUpLy2HVWDj8TkHDa', 2),
(7, 'dmd', 'diyomb', 'dev', 'M', 'ddm@gmail.com', '+243855244578', 'vnkcksifopzkvikvbijdklusbcj', '$2y$10$.PwLlJNYMMkQuV0n8Z.Hx.5..ynxkOThhmjZPOHhcj5M0bcLjYDa2', 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_categorie` (`nom_categorie`);

--
-- Index pour la table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_facture` (`user_id`);

--
-- Index pour la table `ligne_factures`
--
ALTER TABLE `ligne_factures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facture_id` (`facture_id`),
  ADD KEY `livre_id` (`livre_id`);

--
-- Index pour la table `livres`
--
ALTER TABLE `livres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `fk_categorie` (`categorie_id`);

--
-- Index pour la table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `role_operation`
--
ALTER TABLE `role_operation`
  ADD PRIMARY KEY (`role_id`,`operation_id`),
  ADD KEY `operation_id` (`operation_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `factures`
--
ALTER TABLE `factures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ligne_factures`
--
ALTER TABLE `ligne_factures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `livres`
--
ALTER TABLE `livres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `operations`
--
ALTER TABLE `operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `factures`
--
ALTER TABLE `factures`
  ADD CONSTRAINT `fk_user_facture` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `ligne_factures`
--
ALTER TABLE `ligne_factures`
  ADD CONSTRAINT `ligne_factures_ibfk_1` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ligne_factures_ibfk_2` FOREIGN KEY (`livre_id`) REFERENCES `livres` (`id`);

--
-- Contraintes pour la table `livres`
--
ALTER TABLE `livres`
  ADD CONSTRAINT `fk_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `role_operation`
--
ALTER TABLE `role_operation`
  ADD CONSTRAINT `role_operation_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_operation_ibfk_2` FOREIGN KEY (`operation_id`) REFERENCES `operations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
