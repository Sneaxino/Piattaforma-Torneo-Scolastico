-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 31, 2026 alle 20:51
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `torneo_scolastico`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `atleta`
--

CREATE TABLE `atleta` (
  `id_atleta` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `id_squadra` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `atleta`
--

INSERT INTO `atleta` (`id_atleta`, `nome`, `cognome`, `id_squadra`) VALUES
(1, 'Luca', 'Rossi', 1),
(2, 'Marco', 'Bianchi', 1),
(3, 'Andrea', 'Verdi', 2),
(4, 'Paolo', 'Neri', 2),
(5, 'Davide', 'Ferrari', 3),
(6, 'Mario ', 'Verdi', 3),
(7, 'Carmine', 'Ciccarelli', 5),
(8, 'Filippo', 'Cuccurullo', 5),
(9, 'Emanuele', 'Magno', 5),
(10, 'Kevin ', 'De Bruyne', 6),
(11, 'Romelu', 'Lukaku', 6),
(12, 'Antonio', 'Conte', 4),
(13, 'Dries', 'Mertens', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome`) VALUES
(1, 'Prime'),
(2, 'Seconde'),
(3, 'Terze'),
(4, 'Quarte'),
(5, 'Quinte');

-- --------------------------------------------------------

--
-- Struttura della tabella `edizione`
--

CREATE TABLE `edizione` (
  `id_edizione` int(11) NOT NULL,
  `anno_scolastico` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `edizione`
--

INSERT INTO `edizione` (`id_edizione`, `anno_scolastico`) VALUES
(1, '2024/2025'),
(2, '2025/2026');

-- --------------------------------------------------------

--
-- Struttura della tabella `partita`
--

CREATE TABLE `partita` (
  `id_partita` int(11) NOT NULL,
  `data` date DEFAULT NULL,
  `ora` time DEFAULT NULL,
  `campo` varchar(50) DEFAULT NULL,
  `id_torneo` int(11) DEFAULT NULL,
  `id_squadra1` int(11) DEFAULT NULL,
  `id_squadra2` int(11) DEFAULT NULL,
  `punteggio1` int(11) DEFAULT NULL,
  `punteggio2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `partita`
--

INSERT INTO `partita` (`id_partita`, `data`, `ora`, `campo`, `id_torneo`, `id_squadra1`, `id_squadra2`, `punteggio1`, `punteggio2`) VALUES
(1, '2026-01-14', '10:00:00', 'Campo 1', 1, 1, 2, 3, 1),
(2, '2025-10-17', '11:00:00', 'Campo 2', 2, 3, 4, 2, 2),
(4, '2026-03-10', '09:30:00', 'Campo 2', 3, 5, 6, 3, 1),
(6, '2026-03-16', '16:00:00', 'Campo 5', 2, 4, 3, 3, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `premio_individuale`
--

CREATE TABLE `premio_individuale` (
  `id_premio` int(11) NOT NULL,
  `tipo_premio` varchar(100) DEFAULT NULL,
  `id_atleta` int(11) DEFAULT NULL,
  `id_torneo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `premio_individuale`
--

INSERT INTO `premio_individuale` (`id_premio`, `tipo_premio`, `id_atleta`, `id_torneo`) VALUES
(1, 'Miglior giocatore', 1, 1),
(2, 'Capocannoniere', 2, 1),
(3, 'MVP', 5, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `sport`
--

CREATE TABLE `sport` (
  `id_sport` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `sport`
--

INSERT INTO `sport` (`id_sport`, `nome`) VALUES
(1, 'Calcio'),
(2, 'Pallavolo');

-- --------------------------------------------------------

--
-- Struttura della tabella `squadra`
--

CREATE TABLE `squadra` (
  `id_squadra` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `id_torneo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `squadra`
--

INSERT INTO `squadra` (`id_squadra`, `nome`, `logo`, `id_torneo`) VALUES
(1, '1A', 'images\\liverpool.png', 1),
(2, '1E', 'images\\como.png', 1),
(3, '4C', 'images\\city.png', 2),
(4, '4B', 'images\\psg.png', 2),
(5, '5E', 'images\\napoli.png', 3),
(6, '5A', 'images\\united.png', 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `torneo`
--

CREATE TABLE `torneo` (
  `id_torneo` int(11) NOT NULL,
  `id_edizione` int(11) DEFAULT NULL,
  `id_sport` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `torneo`
--

INSERT INTO `torneo` (`id_torneo`, `id_edizione`, `id_sport`, `id_categoria`) VALUES
(1, 2, 1, 1),
(2, 2, 2, 4),
(3, 2, 1, 5),
(4, 1, 1, 4);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `atleta`
--
ALTER TABLE `atleta`
  ADD PRIMARY KEY (`id_atleta`),
  ADD KEY `id_squadra` (`id_squadra`);

--
-- Indici per le tabelle `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indici per le tabelle `edizione`
--
ALTER TABLE `edizione`
  ADD PRIMARY KEY (`id_edizione`);

--
-- Indici per le tabelle `partita`
--
ALTER TABLE `partita`
  ADD PRIMARY KEY (`id_partita`),
  ADD KEY `id_torneo` (`id_torneo`),
  ADD KEY `id_squadra1` (`id_squadra1`),
  ADD KEY `id_squadra2` (`id_squadra2`);

--
-- Indici per le tabelle `premio_individuale`
--
ALTER TABLE `premio_individuale`
  ADD PRIMARY KEY (`id_premio`),
  ADD KEY `id_atleta` (`id_atleta`),
  ADD KEY `id_torneo` (`id_torneo`);

--
-- Indici per le tabelle `sport`
--
ALTER TABLE `sport`
  ADD PRIMARY KEY (`id_sport`);

--
-- Indici per le tabelle `squadra`
--
ALTER TABLE `squadra`
  ADD PRIMARY KEY (`id_squadra`),
  ADD KEY `id_torneo` (`id_torneo`);

--
-- Indici per le tabelle `torneo`
--
ALTER TABLE `torneo`
  ADD PRIMARY KEY (`id_torneo`),
  ADD KEY `id_edizione` (`id_edizione`),
  ADD KEY `id_sport` (`id_sport`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `atleta`
--
ALTER TABLE `atleta`
  MODIFY `id_atleta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT per la tabella `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `edizione`
--
ALTER TABLE `edizione`
  MODIFY `id_edizione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `partita`
--
ALTER TABLE `partita`
  MODIFY `id_partita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `premio_individuale`
--
ALTER TABLE `premio_individuale`
  MODIFY `id_premio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `sport`
--
ALTER TABLE `sport`
  MODIFY `id_sport` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `squadra`
--
ALTER TABLE `squadra`
  MODIFY `id_squadra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `torneo`
--
ALTER TABLE `torneo`
  MODIFY `id_torneo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `atleta`
--
ALTER TABLE `atleta`
  ADD CONSTRAINT `atleta_ibfk_1` FOREIGN KEY (`id_squadra`) REFERENCES `squadra` (`id_squadra`);

--
-- Limiti per la tabella `partita`
--
ALTER TABLE `partita`
  ADD CONSTRAINT `partita_ibfk_1` FOREIGN KEY (`id_torneo`) REFERENCES `torneo` (`id_torneo`),
  ADD CONSTRAINT `partita_ibfk_2` FOREIGN KEY (`id_squadra1`) REFERENCES `squadra` (`id_squadra`),
  ADD CONSTRAINT `partita_ibfk_3` FOREIGN KEY (`id_squadra2`) REFERENCES `squadra` (`id_squadra`);

--
-- Limiti per la tabella `premio_individuale`
--
ALTER TABLE `premio_individuale`
  ADD CONSTRAINT `premio_individuale_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atleta` (`id_atleta`),
  ADD CONSTRAINT `premio_individuale_ibfk_2` FOREIGN KEY (`id_torneo`) REFERENCES `torneo` (`id_torneo`);

--
-- Limiti per la tabella `squadra`
--
ALTER TABLE `squadra`
  ADD CONSTRAINT `squadra_ibfk_1` FOREIGN KEY (`id_torneo`) REFERENCES `torneo` (`id_torneo`);

--
-- Limiti per la tabella `torneo`
--
ALTER TABLE `torneo`
  ADD CONSTRAINT `torneo_ibfk_1` FOREIGN KEY (`id_edizione`) REFERENCES `edizione` (`id_edizione`),
  ADD CONSTRAINT `torneo_ibfk_2` FOREIGN KEY (`id_sport`) REFERENCES `sport` (`id_sport`),
  ADD CONSTRAINT `torneo_ibfk_3` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
