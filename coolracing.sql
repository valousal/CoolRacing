-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 25 Mars 2015 à 17:11
-- Version du serveur :  5.6.17-log
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `coolracing`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

CREATE TABLE IF NOT EXISTS `adresse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `num` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `rue` varchar(250) NOT NULL,
  `cp` varchar(250) NOT NULL,
  `ville` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `adresse`
--

INSERT INTO `adresse` (`id`, `num`, `type`, `rue`, `cp`, `ville`) VALUES
(14, '12', 'Rue', 'grand rue', '87000', 'Paris'),
(15, '13', 'Boulevard', 'grand rue', '65000', 'Toulouse'),
(16, '1', 'Rue', 'grand rue', '56000', 'Lorient');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`) VALUES
(1, 'VTT'),
(2, 'Marathon');

-- --------------------------------------------------------

--
-- Structure de la table `classement`
--

CREATE TABLE IF NOT EXISTS `classement` (
  `idParticipants` int(10) unsigned NOT NULL,
  `idEvent` int(10) unsigned NOT NULL,
  `positionFinale` int(11) NOT NULL,
  `statut` varchar(250) NOT NULL,
  `tempsTotal` varchar(250) NOT NULL,
  `tempsIntermediaire` varchar(250) NOT NULL,
  PRIMARY KEY (`idParticipants`,`idEvent`),
  UNIQUE KEY `idParticipants` (`idParticipants`,`idEvent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `classement`
--

INSERT INTO `classement` (`idParticipants`, `idEvent`, `positionFinale`, `statut`, `tempsTotal`, `tempsIntermediaire`) VALUES
(6, 20, 6, 'fini', '13:54:56', '');

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(250) NOT NULL,
  `dateCreation` date NOT NULL,
  `description` varchar(250) NOT NULL,
  `lieu` varchar(250) NOT NULL,
  `dateCloture` date NOT NULL,
  `dateOuverture` date NOT NULL,
  `dateEvenement` date NOT NULL,
  `sessions` varchar(250) NOT NULL,
  `nombrePlace` int(11) NOT NULL,
  `prix` int(11) NOT NULL,
  `distance` varchar(250) NOT NULL,
  `idOrganisateur` int(10) unsigned NOT NULL,
  `idStatut` int(10) unsigned NOT NULL,
  `idCategorie` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idOrganisateur` (`idOrganisateur`),
  KEY `idStatut` (`idStatut`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Contenu de la table `event`
--

INSERT INTO `event` (`id`, `titre`, `dateCreation`, `description`, `lieu`, `dateCloture`, `dateOuverture`, `dateEvenement`, `sessions`, `nombrePlace`, `prix`, `distance`, `idOrganisateur`, `idStatut`, `idCategorie`) VALUES
(17, 'Course du quinté +', '2015-03-19', 'Course du quinté + à poney et en VTT\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ultricies iaculis nibh lobortis vestibulum. Maecenas et porttitor purus. Maecenas vestibulum lorem ac volutpat feugiat. Sed quis magna scelerisque, sod', 'Vittel', '2015-11-05', '2015-06-01', '2016-07-08', '', 347, 30, '42', 31, 1, 1),
(18, 'Color Run', '2015-03-19', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ultricies iaculis nibh lobortis vestibulum. Maecenas et porttitor purus. Maecenas vestibulum lorem ac volutpat feugiat. Sed quis magna scelerisque, sodales purus nec, lacinia ante. Duis sap', 'Nancy', '2015-05-03', '2015-01-05', '2015-06-04', '', 2400, 10, '100', 31, 1, 2),
(19, 'VTT Run', '2015-03-19', 'VTT RUN IN LYON', 'Lyon', '2015-08-01', '2015-01-01', '2017-11-01', '', 456, 249, '69', 32, 1, 1),
(20, 'VTT de descente', '2015-03-19', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vitae eros molestie, iaculis quam non, laoreet ligula. Donec ornare semper nibh, eget dignissim eros facilisis quis. Vivamus finibus, nibh non porta hendrerit, dolor turpis vulputate ma', 'La Bresse', '2014-10-01', '2014-08-02', '2015-02-05', '', 139, 13, '121', 32, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `oauth_client`
--

CREATE TABLE IF NOT EXISTS `oauth_client` (
  `id` varchar(250) NOT NULL,
  `nom` varchar(250) NOT NULL,
  `secret` varchar(250) NOT NULL,
  `redirect_auth_code` varchar(250) NOT NULL,
  `redirect_access_token` varchar(250) NOT NULL,
  `client_access_token` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `oauth_client`
--

INSERT INTO `oauth_client` (`id`, `nom`, `secret`, `redirect_auth_code`, `redirect_access_token`, `client_access_token`) VALUES
('coolracing', 'coolracing', 'azerty', 'http://coolracing/application/oauth/jeton', 'http://coolracing/application/oauth/access_token', '209592932550acf353b2528.70564490');

-- --------------------------------------------------------

--
-- Structure de la table `oauth_code`
--

CREATE TABLE IF NOT EXISTS `oauth_code` (
  `auth_code` varchar(250) NOT NULL,
  `id_client` varchar(250) NOT NULL,
  `redirect_uri` varchar(250) NOT NULL,
  `owner_id` varchar(250) NOT NULL,
  PRIMARY KEY (`auth_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `oauth_scope`
--

CREATE TABLE IF NOT EXISTS `oauth_scope` (
  `id` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `oauth_scope`
--

INSERT INTO `oauth_scope` (`id`, `description`) VALUES
('admin', ''),
('organisateur', ''),
('participant', '');

-- --------------------------------------------------------

--
-- Structure de la table `oauth_token`
--

CREATE TABLE IF NOT EXISTS `oauth_token` (
  `access_token` varchar(250) NOT NULL,
  `id_client` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `owner_id` varchar(250) NOT NULL,
  `scope` varchar(250) NOT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `organisateurs`
--

CREATE TABLE IF NOT EXISTS `organisateurs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom_structure` varchar(250) NOT NULL,
  `forme_juridique` varchar(250) NOT NULL,
  `mailPub` varchar(250) NOT NULL,
  `mailPrivate` varchar(250) NOT NULL,
  `login` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `tel` varchar(250) NOT NULL,
  `dateInscription` varchar(250) NOT NULL,
  `nomResponsable` varchar(250) NOT NULL,
  `prenomResponsable` varchar(250) NOT NULL,
  `mailResponsable` varchar(250) NOT NULL,
  `idAdress` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Contenu de la table `organisateurs`
--

INSERT INTO `organisateurs` (`id`, `nom_structure`, `forme_juridique`, `mailPub`, `mailPrivate`, `login`, `password`, `tel`, `dateInscription`, `nomResponsable`, `prenomResponsable`, `mailResponsable`, `idAdress`) VALUES
(31, 'CoursPoney', 'Association', 'poney@poney.fr', 'poney@poney.fr', 'CoursPoney', '$2y$10$mwSG.b12m.FyctXTtlkFNOu/tZVRbxcy77eaGpBZqzNvi9DN9jXA.', '0606060606', '2015-03-19', 'Dupont', 'Derrick', 'poney@poney.fr', 15),
(32, 'Foyer des jeunes', 'Association', 'jeunes@fai.com', 'jeunes@fai.com', 'FoyerDesJeunes', '$2y$10$8u8LFF8TTkZ23xAZ/T.0yu3f0E7ahzjj/27j.8z3Qg.ahI/crUiiO', '0606060606', '2015-03-19', 'Dupont', 'Polo', 'jeunes@fai.com', 16);

-- --------------------------------------------------------

--
-- Structure de la table `parcours`
--

CREATE TABLE IF NOT EXISTS `parcours` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lieu` varchar(250) NOT NULL,
  `distance` varchar(250) NOT NULL,
  `relais` varchar(250) NOT NULL,
  `nombreRelais` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `participants`
--

CREATE TABLE IF NOT EXISTS `participants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prenom` varchar(250) NOT NULL,
  `nom` varchar(250) NOT NULL,
  `dateNaissance` date NOT NULL,
  `dateInscription` date NOT NULL,
  `sexe` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `login` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `mailPub` varchar(250) NOT NULL,
  `mailPrivate` varchar(250) NOT NULL,
  `tel` varchar(250) NOT NULL,
  `licence` varchar(250) NOT NULL,
  `descriptionLicence` varchar(250) NOT NULL,
  `idAdress` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idAdress` (`idAdress`),
  KEY `idAdress_2` (`idAdress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `participants`
--

INSERT INTO `participants` (`id`, `prenom`, `nom`, `dateNaissance`, `dateInscription`, `sexe`, `description`, `login`, `password`, `mailPub`, `mailPrivate`, `tel`, `licence`, `descriptionLicence`, `idAdress`) VALUES
(6, 'Gerard', 'Dupont', '1980-03-23', '2015-03-19', 'M', '', 'Gerard', '$2y$10$p9asM1bqZ8IGkIPFwPGdL.MkMhNcE8OEPjSTfzheFWJnc..Voxtn6', 'gerard@fai.com', 'gerard@fai.com', '0606060606', '', '', 14);

-- --------------------------------------------------------

--
-- Structure de la table `participe`
--

CREATE TABLE IF NOT EXISTS `participe` (
  `idParticipants` int(10) unsigned NOT NULL,
  `idEvent` int(10) unsigned NOT NULL,
  `dossard` varchar(250) NOT NULL,
  `sessionDepart` varchar(250) NOT NULL,
  `sasDepart` varchar(250) NOT NULL,
  `certificatMedical` varchar(250) NOT NULL,
  `club` varchar(250) NOT NULL,
  PRIMARY KEY (`idParticipants`,`idEvent`),
  KEY `idParticipants` (`idParticipants`),
  KEY `idEvent` (`idEvent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `participe`
--

INSERT INTO `participe` (`idParticipants`, `idEvent`, `dossard`, `sessionDepart`, `sasDepart`, `certificatMedical`, `club`) VALUES
(6, 18, '212', 'Prem&#39;s', 'Prem&#39;s', '', 'PasDeClub'),
(6, 19, '105', '', '', '', 'Assoc du Village'),
(6, 20, '55', 'One', 'One', '', 'FJEP');

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

CREATE TABLE IF NOT EXISTS `statut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `statut`
--

INSERT INTO `statut` (`id`, `label`) VALUES
(1, 'venir'),
(2, 'cours'),
(3, 'termine');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `classement`
--
ALTER TABLE `classement`
  ADD CONSTRAINT `classement_ibfk_1` FOREIGN KEY (`idParticipants`) REFERENCES `participants` (`id`);

--
-- Contraintes pour la table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`idOrganisateur`) REFERENCES `organisateurs` (`id`),
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`idStatut`) REFERENCES `statut` (`id`);

--
-- Contraintes pour la table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`idAdress`) REFERENCES `adresse` (`id`);

--
-- Contraintes pour la table `participe`
--
ALTER TABLE `participe`
  ADD CONSTRAINT `participe_ibfk_1` FOREIGN KEY (`idParticipants`) REFERENCES `participants` (`id`),
  ADD CONSTRAINT `participe_ibfk_2` FOREIGN KEY (`idEvent`) REFERENCES `event` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
