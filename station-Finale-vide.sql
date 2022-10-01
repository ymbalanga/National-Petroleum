-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 03 Janvier 2018 à 11:09
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `station`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateactivity` datetime NOT NULL,
  `etat` smallint(6) NOT NULL,
  `taux` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `approvisionnement`
--

CREATE TABLE IF NOT EXISTS `approvisionnement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qteappro` double NOT NULL,
  `produit` int(11) NOT NULL,
  `dateappro` date NOT NULL,
  `prixachat` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_approvisionnement_produit_id` (`produit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `credit`
--

CREATE TABLE IF NOT EXISTS `credit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quantite` double NOT NULL,
  `datecredit` date NOT NULL,
  `idcustomer` int(11) NOT NULL,
  `produit` int(11) NOT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '0',
  `taux` double DEFAULT NULL,
  `prix` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_credit_customer_id` (`idcustomer`),
  KEY `FK_credit_produit_id` (`produit`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `credit`
--


-- --------------------------------------------------------

--
-- Structure de la table `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `etat` smallint(6) NOT NULL,
  `partieresponsable` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `customer`
--



-- --------------------------------------------------------

--
-- Structure de la table `dailyreport`
--

CREATE TABLE IF NOT EXISTS `dailyreport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateactivity` date NOT NULL,
  `etat` smallint(6) NOT NULL DEFAULT '0',
  `taux` double NOT NULL,
  `station` int(11) NOT NULL,
  `totalcashsales` double NOT NULL,
  `expenses` double NOT NULL,
  `totalcreditsales` double NOT NULL,
  `totalcreditpayment` double NOT NULL,
  `actualcashbank` double NOT NULL,
  `coefficientmult` double NOT NULL,
  `dateenreg` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_daliyreport_station_id` (`station`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `dailyreport`
--


-- --------------------------------------------------------

--
-- Structure de la table `depenses`
--

CREATE TABLE IF NOT EXISTS `depenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `montant` double NOT NULL,
  `datedepense` datetime NOT NULL,
  `station` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_depenses_station_id` (`station`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `indexpompe`
--

CREATE TABLE IF NOT EXISTS `indexpompe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `indexinitial` double NOT NULL,
  `indexfinal` double DEFAULT NULL,
  `dateindex` date NOT NULL,
  `idpompe` int(11) NOT NULL,
  `taux` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_indexpompe_pompe_id` (`idpompe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `indextank`
--

CREATE TABLE IF NOT EXISTS `indextank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openstock` double NOT NULL,
  `tests` varchar(255) DEFAULT NULL,
  `purchase` varchar(255) DEFAULT NULL,
  `dip` varchar(255) DEFAULT NULL,
  `datetank` date NOT NULL,
  `tank` int(11) NOT NULL,
  `taux` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_indextank_tank_id` (`tank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `monthlyreport`
--

CREATE TABLE IF NOT EXISTS `monthlyreport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `totalcashsales` double NOT NULL,
  `expenses` double NOT NULL,
  `totalcreditsales` double NOT NULL,
  `totalcreditpayment` double NOT NULL,
  `actualcashbank` double NOT NULL,
  `dateEnreg` date DEFAULT NULL,
  `dateactivity` date NOT NULL,
  `station` int(11) NOT NULL,
  `taux` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `monthlyreport`
--


-- --------------------------------------------------------

--
-- Structure de la table `paiementcredit`
--

CREATE TABLE IF NOT EXISTS `paiementcredit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `montant` double NOT NULL,
  `datepay` date NOT NULL,
  `credit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_paeimentcredit_credit_id` (`credit`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `paiementcredit`
--

-- --------------------------------------------------------

--
-- Structure de la table `pompe`
--

CREATE TABLE IF NOT EXISTS `pompe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `tank` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pompe_tank_id` (`tank`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `pompe`
--

-- --------------------------------------------------------

--
-- Structure de la table `prixproduit`
--

CREATE TABLE IF NOT EXISTS `prixproduit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produit` int(11) NOT NULL,
  `prix` double NOT NULL,
  `dateinitiale` date NOT NULL,
  `datefinale` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_prixproduit_produit_id` (`produit`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `prixproduit`
--


-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE IF NOT EXISTS `produit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `typeproduit` int(11) NOT NULL,
  `station` int(11) NOT NULL,
  `qtestock` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_produit_typeproduit_id` (`typeproduit`),
  KEY `FK_produit_station_id` (`station`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `produit`
--


-- --------------------------------------------------------

--
-- Structure de la table `station`
--

CREATE TABLE IF NOT EXISTS `station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `station`
--

INSERT INTO `station` (`id`, `intitule`) VALUES
(1, 'Kasavubu');

-- --------------------------------------------------------

--
-- Structure de la table `tank`
--

CREATE TABLE IF NOT EXISTS `tank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `typetank` varchar(255) NOT NULL,
  `station` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_tank_station_id` (`station`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `tank`
--

INSERT INTO `tank` (`id`, `intitule`, `typetank`, `station`) VALUES
(1, 'AGO', 'AGO', 1),
(2, 'PMS', 'PMS', 1);

-- --------------------------------------------------------

--
-- Structure de la table `taux`
--

CREATE TABLE IF NOT EXISTS `taux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valeurtaux` varchar(255) NOT NULL,
  `dateinitiale` date NOT NULL,
  `datefinale` date DEFAULT NULL,
  `station` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_parametres_station_id` (`station`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `taux`
--

-- --------------------------------------------------------

--
-- Structure de la table `typeproduit`
--

CREATE TABLE IF NOT EXISTS `typeproduit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `datetype` date DEFAULT NULL,
  `unite` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `typeproduit`
--

INSERT INTO `typeproduit` (`id`, `intitule`, `datetype`, `unite`) VALUES
(1, 'LUBES', '2018-01-01', 'Pcs'),
(2, 'GAZ', '2018-01-01', 'Ltrs '),
(3, 'LPG', '2018-01-01', 'Ltrs');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `time` date NOT NULL,
  `station` int(11) NOT NULL,
  `etat` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `FK_users_station_id` (`station`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `login`, `pass`, `role`, `time`, `station`, `etat`) VALUES
(1, 'sabian', 'sabian', 'admin', '2017-12-11', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

CREATE TABLE IF NOT EXISTS `vente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produit` int(11) NOT NULL,
  `datevente` date NOT NULL,
  `qtevendu` double NOT NULL,
  `prixvente` double NOT NULL DEFAULT '0',
  `taux` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_vente_produit_id` (`produit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `approvisionnement`
--
ALTER TABLE `approvisionnement`
  ADD CONSTRAINT `FK_approvisionnement_produit_id` FOREIGN KEY (`produit`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `credit`
--
ALTER TABLE `credit`
  ADD CONSTRAINT `FK_credit_customer_id` FOREIGN KEY (`idcustomer`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `FK_credit_produit_id` FOREIGN KEY (`produit`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `dailyreport`
--
ALTER TABLE `dailyreport`
  ADD CONSTRAINT `FK_daliyreport_station_id` FOREIGN KEY (`station`) REFERENCES `station` (`id`);

--
-- Contraintes pour la table `depenses`
--
ALTER TABLE `depenses`
  ADD CONSTRAINT `FK_depenses_station_id` FOREIGN KEY (`station`) REFERENCES `station` (`id`);

--
-- Contraintes pour la table `indexpompe`
--
ALTER TABLE `indexpompe`
  ADD CONSTRAINT `FK_indexpompe_pompe_id` FOREIGN KEY (`idpompe`) REFERENCES `pompe` (`id`);

--
-- Contraintes pour la table `indextank`
--
ALTER TABLE `indextank`
  ADD CONSTRAINT `FK_indextank_tank_id` FOREIGN KEY (`tank`) REFERENCES `tank` (`id`);

--
-- Contraintes pour la table `paiementcredit`
--
ALTER TABLE `paiementcredit`
  ADD CONSTRAINT `FK_paeimentcredit_credit_id` FOREIGN KEY (`credit`) REFERENCES `credit` (`id`);

--
-- Contraintes pour la table `pompe`
--
ALTER TABLE `pompe`
  ADD CONSTRAINT `FK_pompe_tank_id` FOREIGN KEY (`tank`) REFERENCES `tank` (`id`);

--
-- Contraintes pour la table `prixproduit`
--
ALTER TABLE `prixproduit`
  ADD CONSTRAINT `FK_prixproduit_produit_id` FOREIGN KEY (`produit`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `FK_produit_station_id` FOREIGN KEY (`station`) REFERENCES `station` (`id`),
  ADD CONSTRAINT `FK_produit_typeproduit_id` FOREIGN KEY (`typeproduit`) REFERENCES `typeproduit` (`id`);

--
-- Contraintes pour la table `tank`
--
ALTER TABLE `tank`
  ADD CONSTRAINT `FK_tank_station_id` FOREIGN KEY (`station`) REFERENCES `station` (`id`);

--
-- Contraintes pour la table `taux`
--
ALTER TABLE `taux`
  ADD CONSTRAINT `FK_parametres_station_id` FOREIGN KEY (`station`) REFERENCES `station` (`id`);

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_users_station_id` FOREIGN KEY (`station`) REFERENCES `station` (`id`);

--
-- Contraintes pour la table `vente`
--
ALTER TABLE `vente`
  ADD CONSTRAINT `FK_vente_produit_id` FOREIGN KEY (`produit`) REFERENCES `produit` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
