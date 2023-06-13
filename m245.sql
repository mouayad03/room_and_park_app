-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Mrz 2023 um 23:34
-- Server-Version: 10.4.25-MariaDB
-- PHP-Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `m245`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `from_date` datetime NOT NULL DEFAULT current_timestamp(),
  `to_date` datetime DEFAULT NULL,
  `place_name` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `description` varchar(2048) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `places`
--

CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `position` varchar(2048) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `places`
--

INSERT INTO `places` (`id`, `position`, `name`, `type`) VALUES
(41, '{\"x\":\"500\",\"y\":\"500\",\"width\":\"194\",\"height\":\"239\",\"etage\":0}', 'Rubin', 'R'),
(42, '{\"x\":\"500\",\"y\":\"242\",\"width\":\"194\",\"height\":\"258\",\"etage\":0}', 'Smaragd', 'R'),
(43, '{\"x\":\"693\",\"y\":\"242\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK1', 'P'),
(44, '{\"x\":\"693\",\"y\":\"297\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK2', 'P'),
(45, '{\"x\":\"693\",\"y\":\"352\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK3', 'P'),
(46, '{\"x\":\"693\",\"y\":\"407\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK4', 'P'),
(47, '{\"x\":\"693\",\"y\":\"462\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK5', 'P'),
(48, '{\"x\":\"693\",\"y\":\"517\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK6', 'P'),
(49, '{\"x\":\"693\",\"y\":\"572\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK7', 'P'),
(50, '{\"x\":\"693\",\"y\":\"627\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK8', 'P'),
(51, '{\"x\":\"693\",\"y\":\"682\",\"width\":\"100\",\"height\":\"55\",\"etage\":0}', 'PK9', 'P'),
(52, '{\"x\":\"500\",\"y\":\"100\",\"width\":\"55\",\"height\":\"100\",\"etage\":0}', 'PK10', 'P'),
(54, '{\"x\":\"555\",\"y\":\"80\",\"width\":\"55\",\"height\":\"100\",\"etage\":0}', 'PK11', 'P'),
(55, '{\"x\":\"610\",\"y\":\"60\",\"width\":\"55\",\"height\":\"100\",\"etage\":0}', 'PK12', 'P'),
(56, '{\"x\":\"448\",\"y\":\"242\",\"width\":\"246\",\"height\":\"162\",\"etage\":1}', 'Harvard', 'R'),
(57, '{\"x\":\"283\",\"y\":\"242\",\"width\":\"165\",\"height\":\"162\",\"etage\":1}', 'Boston', 'R'),
(58, '{\"x\":\"120\",\"y\":\"192\",\"width\":\"163\",\"height\":\"162\",\"etage\":1}', 'Oxford', 'R'),
(59, '{\"x\":\"567\",\"y\":\"403\",\"width\":\"127\",\"height\":\"197\",\"etage\":1}', 'Sorbonne', 'R'),
(60, '{\"x\":\"522\",\"y\":\"600\",\"width\":\"172\",\"height\":\"197\",\"etage\":1}', 'Cambridge', 'R'),
(61, '{\"x\":\"396\",\"y\":\"404\",\"width\":\"171\",\"height\":\"125\",\"etage\":2}', 'Eiger', 'R'),
(62, '{\"x\":\"396\",\"y\":\"529\",\"width\":\"171\",\"height\":\"125\",\"etage\":2}', 'Blüemlisalp', 'R'),
(64, '{\"x\":\"25\",\"y\":\"25\",\"width\":\"25\",\"height\":\"25\",\"etage\":0}', 'P13', 'P');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `type` varchar(1) NOT NULL,
  `add_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `type`, `add_date`) VALUES
(1, 'admin', 'mouayad.alnhlawe@ict.csbe.ch', '11bcd7d7b9bc2794316b3c5c40c646f747b108c3691ffa9ffc204c00719bd1e6', 'A', '0000-00-00'),
(2, 'sekretariat', 'dominic.streit@ict.csbe.ch', '11bcd7d7b9bc2794316b3c5c40c646f747b108c3691ffa9ffc204c00719bd1e6', 'D', '2023-02-27'),
(3, 'dozent', 'mouayad.alnhlawe@ict.csbe.ch', '11bcd7d7b9bc2794316b3c5c40c646f747b108c3691ffa9ffc204c00719bd1e6', 'S', '2023-02-27'),
(4, 'james', 'mouayad.alnhlawe@ict.csbe.ch', '11bcd7d7b9bc2794316b3c5c40c646f747b108c3691ffa9ffc204c00719bd1e6', 'D', '2023-03-09');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name_host` (`host`),
  ADD KEY `name_place_name` (`place_name`);

--
-- Indizes für die Tabelle `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `position` (`position`) USING HASH;

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT für Tabelle `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `name_host` FOREIGN KEY (`host`) REFERENCES `users` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `name_place_name` FOREIGN KEY (`place_name`) REFERENCES `places` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
