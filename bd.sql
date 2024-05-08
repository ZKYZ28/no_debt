-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  localhost
-- Généré le :  Sam 21 Mai 2022 à 14:41
-- Version du serveur :  5.7.29
-- Version de PHP :  5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `in21b10208`
--

-- --------------------------------------------------------

--
-- Structure de la table `caracteriser`
--

CREATE TABLE `caracteriser` (
  `idDepense` int(11) NOT NULL,
  `idTag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `caracteriser`
--

INSERT INTO `caracteriser` (`idDepense`, `idTag`) VALUES
(203, 40),
(204, 40);

-- --------------------------------------------------------

--
-- Structure de la table `depense`
--

CREATE TABLE `depense` (
  `idDepense` int(11) NOT NULL,
  `dateHeure` datetime NOT NULL,
  `montant` int(11) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `idGroupe` int(11) NOT NULL,
  `idUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `depense`
--

INSERT INTO `depense` (`idDepense`, `dateHeure`, `montant`, `libelle`, `idGroupe`, `idUser`) VALUES
(203, '2022-05-21 12:37:44', 50, '', 45, 39),
(204, '2022-05-21 12:39:53', 235, '', 45, 38);

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE `facture` (
  `idFacture` int(11) NOT NULL,
  `scan` varchar(255) NOT NULL,
  `idDepense` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `facture`
--

INSERT INTO `facture` (`idFacture`, `scan`, `idDepense`) VALUES
(96, 'Ma facture', 203);

-- --------------------------------------------------------

--
-- Structure de la table `groupe`
--

CREATE TABLE `groupe` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `devise` varchar(10) NOT NULL,
  `idFounder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `groupe`
--

INSERT INTO `groupe` (`id`, `nom`, `devise`, `idFounder`) VALUES
(45, 'TEST', '&euro;', 39);

-- --------------------------------------------------------

--
-- Structure de la table `participer`
--

CREATE TABLE `participer` (
  `idUser` int(11) NOT NULL,
  `idGroupe` int(11) NOT NULL,
  `estConfirme` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `participer`
--

INSERT INTO `participer` (`idUser`, `idGroupe`, `estConfirme`) VALUES
(39, 45, 1),
(37, 45, 1),
(38, 45, 1);

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE `tag` (
  `idTag` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `idGroupe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tag`
--

INSERT INTO `tag` (`idTag`, `tag`, `idGroupe`) VALUES
(40, '', 45);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(25) NOT NULL,
  `firstName` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `firstName`) VALUES
(37, 'francois.mahy28@gmail.com', '$2y$10$rWX6zCTWnOQbiY2iC/p9MOy0ofJjK99WQ8zbKI0FXZwNr4h.pz2.2', 'Mahy', 'Francois'),
(38, 'francis.discord28@gmail.com', '$2y$10$LLowBhnmRWaVJCFrlY5v6u0aqQUERh0nDSeu5RLgfOJAQ0fBojiOy', 'Francis', 'Discord'),
(39, 'francois.mahy1@gmail.com', '$2y$10$n7R0pAcfBvTFPbbD/NoHX.2e3UXA5K05fmLWLgo6F0D8F52JsLX8C', 'Nicolas', 'Dubois');

-- --------------------------------------------------------

--
-- Structure de la table `versement`
--

CREATE TABLE `versement` (
  `idVersement` int(11) NOT NULL,
  `dateHeure` datetime NOT NULL,
  `montant` double NOT NULL,
  `estConfirme` tinyint(1) NOT NULL,
  `idCrediteur` int(11) NOT NULL,
  `idDebiteur` int(11) NOT NULL,
  `idGroupe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `caracteriser`
--
ALTER TABLE `caracteriser`
  ADD KEY `idDepenseCara` (`idDepense`),
  ADD KEY `idTagRef` (`idTag`);

--
-- Index pour la table `depense`
--
ALTER TABLE `depense`
  ADD PRIMARY KEY (`idDepense`),
  ADD KEY `idUserDepense` (`idUser`),
  ADD KEY `idGroupeDepense` (`idGroupe`);

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`idFacture`),
  ADD KEY `idDepenseRef` (`idDepense`);

--
-- Index pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idFounder` (`idFounder`);

--
-- Index pour la table `participer`
--
ALTER TABLE `participer`
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idGroupe` (`idGroupe`);

--
-- Index pour la table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`idTag`),
  ADD KEY `idGroupeRef` (`idGroupe`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `versement`
--
ALTER TABLE `versement`
  ADD PRIMARY KEY (`idVersement`),
  ADD KEY `refIdCrediteur` (`idCrediteur`),
  ADD KEY `refIdDebiteur` (`idDebiteur`),
  ADD KEY `refIdGroupe` (`idGroupe`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `depense`
--
ALTER TABLE `depense`
  MODIFY `idDepense` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;
--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
  MODIFY `idFacture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT pour la table `groupe`
--
ALTER TABLE `groupe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT pour la table `tag`
--
ALTER TABLE `tag`
  MODIFY `idTag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT pour la table `versement`
--
ALTER TABLE `versement`
  MODIFY `idVersement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5605;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `caracteriser`
--
ALTER TABLE `caracteriser`
  ADD CONSTRAINT `idDepenseCara` FOREIGN KEY (`idDepense`) REFERENCES `depense` (`idDepense`),
  ADD CONSTRAINT `idTagRef` FOREIGN KEY (`idTag`) REFERENCES `tag` (`idTag`);

--
-- Contraintes pour la table `depense`
--
ALTER TABLE `depense`
  ADD CONSTRAINT `idGroupeDepense` FOREIGN KEY (`idGroupe`) REFERENCES `groupe` (`id`),
  ADD CONSTRAINT `idUserDepense` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `idDepenseRef` FOREIGN KEY (`idDepense`) REFERENCES `depense` (`idDepense`);

--
-- Contraintes pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD CONSTRAINT `idFounder` FOREIGN KEY (`idFounder`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `participer`
--
ALTER TABLE `participer`
  ADD CONSTRAINT `idGroupe` FOREIGN KEY (`idGroupe`) REFERENCES `groupe` (`id`),
  ADD CONSTRAINT `idUser` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `idGroupeRef` FOREIGN KEY (`idGroupe`) REFERENCES `groupe` (`id`);

--
-- Contraintes pour la table `versement`
--
ALTER TABLE `versement`
  ADD CONSTRAINT `refIdCrediteur` FOREIGN KEY (`idCrediteur`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `refIdDebiteur` FOREIGN KEY (`idDebiteur`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `refIdGroupe` FOREIGN KEY (`idGroupe`) REFERENCES `groupe` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
