-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 08 sep. 2023 à 13:46
-- Version du serveur : 5.7.36
-- Version de PHP : 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snowtricks`
--

-- --------------------------------------------------------

--
-- Structure de la table `tricks`
--

DROP TABLE IF EXISTS `tricks`;
CREATE TABLE IF NOT EXISTS `tricks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_tricks` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E1D902C167B3B43D` (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=338 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tricks`
--

INSERT INTO `tricks` (`id`, `users_id`, `name`, `description`, `group_tricks`, `slug`) VALUES
(328, 148, 'Stalefish', 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.', 'grab', 'stalefish'),
(329, 148, 'Back flip', 'Rotation arrière.', 'flip', 'back-flip'),
(330, 148, 'Haakon flip', 'Figure réalisée dans un half-pipe en se présentant en arrière puis en réalisant une rotation inversée de 720°.', 'flip', 'haakon-flip'),
(331, 148, 'indy', 'saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.', 'grab', 'indy'),
(332, 148, '360', 'Un tour complet', 'rotation', '360'),
(333, 148, 'Front flip', 'Rotation complète avant', 'flip', 'front-flip'),
(334, 148, 'Corkscrew', 'Rotation désaxée qui fait penser à un tirebouchon. Rotation effectuée la tête en bas et avec un angle, \r\nce qui fait que le rider ne se retrouve jamais totalement à l\'horizontale ou à la verticale.', 'rotation', 'corkscrew'),
(335, 148, 'Nose slide', 'Slider sur une barre avec l\'arrière de la planche sur la barre.', 'slide', 'nose-slide'),
(336, 148, 'One foot indy', 'Faire un grab de la planche avec une main et décrocher un de ses pieds de la fixation de la planche.', 'one foot', 'one-foot-indy'),
(337, 148, 'Japan air', 'Le rider doit avoir les genoux pliés et sa main avant passe derrière la jambe avant pour attraper la planche au niveau des pieds.', 'old school', 'japan-air');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `tricks`
--
ALTER TABLE `tricks`
  ADD CONSTRAINT `FK_E1D902C167B3B43D` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
