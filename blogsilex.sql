-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  lun. 15 jan. 2018 à 20:20
-- Version du serveur :  5.7.19
-- Version de PHP :  5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `blogsilex`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `idArticle` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `contenuArticle` varchar(65000) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `lienPhoto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idArticle`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`idArticle`, `titre`, `contenuArticle`, `tag`, `lienPhoto`) VALUES
(1, 'Les nouvelles technologies', 'Bonjour à tous nous sommes ici.', 'technologies', NULL),
(2, 'Mickael Danjoux', 'Je suis un etudiant en droit, un etudiant en droit', 'droit', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `citation`
--

DROP TABLE IF EXISTS `citation`;
CREATE TABLE IF NOT EXISTS `citation` (
  `idCitation` int(11) NOT NULL AUTO_INCREMENT,
  `contenuCitation` varchar(500) NOT NULL,
  `lienVideo` varchar(500) NOT NULL,
  `nombreAime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idCitation`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `citation`
--

INSERT INTO `citation` (`idCitation`, `contenuCitation`, `lienVideo`, `nombreAime`) VALUES
(1, 'L\'egalite des sexes', 'https://www.youtube.com/embed/64QjwYDaSEw', 0);

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `idCommentaire` int(11) NOT NULL AUTO_INCREMENT,
  `idArticle` int(11) NOT NULL,
  `nomEditeur` varchar(50) NOT NULL,
  `contenuCommentaire` varchar(10000) NOT NULL,
  PRIMARY KEY (`idCommentaire`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commentaire`
--

INSERT INTO `commentaire` (`idCommentaire`, `idArticle`, `nomEditeur`, `contenuCommentaire`) VALUES
(1, 1, 'Mael', 'Bonjour, je trouve ce sujet super interessant !!\r\nMerci'),
(2, 1, 'Jean-Mi', 'Bonjour, ce commentaire est nul .. très déçu .. \r\nJean-Mi'),
(3, 1, 'Mick', 'Salute'),
(4, 1, 'Brevet', 'Je suis beau'),
(5, 1, 'Brevet', 'Mael'),
(6, 1, 'fg', 'fg'),
(7, 1, 'kj', 'kj');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
