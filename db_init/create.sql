-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Temps de generació: 21-04-2026 a les 07:13:17
-- Versió del servidor: 11.4.8-MariaDB-ubu2404
-- Versió de PHP: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Per assegurar-nes de que la codificació dels caràcters d'aquest script és la correcta
SET NAMES utf8mb4;


CREATE DATABASE IF NOT EXISTS persones
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Donem permisos a l'usuari 'usuari' per accedir a la base de dades 'persones'
-- sinó, aquest usuari no podrà veure la base de dades i no podrà accedir a les taules
GRANT ALL PRIVILEGES ON persones.* TO 'usuari'@'%';
FLUSH PRIVILEGES;

-- Després de crear la base de dades, cal seleccionar-la per treballar-hi
USE persones;

    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dades: `a25alarosure_grup3`
--

-- --------------------------------------------------------

--
-- Estructura de la taula `Actuacions`
--

CREATE TABLE `Actuacions` (
                              `ID_Incidencia` int(11) NOT NULL,
                              `ID_Actuacion` int(11) NOT NULL,
                              `Descripcio` varchar(500) DEFAULT NULL,
                              `Data_Actuacion` date DEFAULT NULL,
                              `FIN` tinyint(1) DEFAULT 0,
                              `Visible` tinyint(1) DEFAULT 1,
                              `Temps` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de la taula `DEPARTAMENT`
--

CREATE TABLE `DEPARTAMENT` (
                               `ID_Departament` int(11) NOT NULL,
                               `Nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de la taula `INCIDENCIA`
--

CREATE TABLE `INCIDENCIA` (
                              `ID_Incidencia` int(11) NOT NULL,
                              `ID_Departament` int(11) NOT NULL,
                              `Data_Inici` timestamp NOT NULL DEFAULT current_timestamp(),
                              `ID_Tipo` int(11) DEFAULT NULL,
                              `Data_FIN` date DEFAULT NULL,
                              `ID_Tecnic` int(11) DEFAULT NULL,
                              `Prioridad` enum('Baja','Media','Alta','Crítica') DEFAULT NULL,
                              `Descripcio` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de la taula `TECNIC`
--

CREATE TABLE `TECNIC` (
                          `ID_Tecnic` int(11) NOT NULL,
                          `Nom` varchar(100) NOT NULL,
                          `ID_Departament` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de la taula `TIPOLOGIA`
--

CREATE TABLE `TIPOLOGIA` (
                             `ID_Tipo` int(11) NOT NULL,
                             `Nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índexs per a les taules bolcades
--

--
-- Índexs per a la taula `Actuacions`
--
ALTER TABLE `Actuacions`
    ADD PRIMARY KEY (`ID_Incidencia`,`ID_Actuacion`);

--
-- Índexs per a la taula `DEPARTAMENT`
--
ALTER TABLE `DEPARTAMENT`
    ADD PRIMARY KEY (`ID_Departament`);

--
-- Índexs per a la taula `INCIDENCIA`
--
ALTER TABLE `INCIDENCIA`
    ADD PRIMARY KEY (`ID_Incidencia`),
  ADD KEY `fk_incidencia_dept` (`ID_Departament`),
  ADD KEY `fk_incidencia_tipo` (`ID_Tipo`),
  ADD KEY `fk_incidencia_tecnic` (`ID_Tecnic`);

--
-- Índexs per a la taula `TECNIC`
--
ALTER TABLE `TECNIC`
    ADD PRIMARY KEY (`ID_Tecnic`),
  ADD KEY `fk_tecnic_dept` (`ID_Departament`);

--
-- Índexs per a la taula `TIPOLOGIA`
--
ALTER TABLE `TIPOLOGIA`
    ADD PRIMARY KEY (`ID_Tipo`);

--
-- AUTO_INCREMENT per les taules bolcades
--

--
-- AUTO_INCREMENT per la taula `DEPARTAMENT`
--
ALTER TABLE `DEPARTAMENT`
    MODIFY `ID_Departament` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la taula `INCIDENCIA`
--
ALTER TABLE `INCIDENCIA`
    MODIFY `ID_Incidencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la taula `TECNIC`
--
ALTER TABLE `TECNIC`
    MODIFY `ID_Tecnic` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la taula `TIPOLOGIA`
--
ALTER TABLE `TIPOLOGIA`
    MODIFY `ID_Tipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restriccions per a les taules bolcades
--

--
-- Restriccions per a la taula `Actuacions`
--
ALTER TABLE `Actuacions`
    ADD CONSTRAINT `fk_actuacion_incidencia` FOREIGN KEY (`ID_Incidencia`) REFERENCES `INCIDENCIA` (`ID_Incidencia`) ON DELETE CASCADE;

--
-- Restriccions per a la taula `INCIDENCIA`
--
ALTER TABLE `INCIDENCIA`
    ADD CONSTRAINT `fk_incidencia_dept` FOREIGN KEY (`ID_Departament`) REFERENCES `DEPARTAMENT` (`ID_Departament`),
  ADD CONSTRAINT `fk_incidencia_tecnic` FOREIGN KEY (`ID_Tecnic`) REFERENCES `TECNIC` (`ID_Tecnic`),
  ADD CONSTRAINT `fk_incidencia_tipo` FOREIGN KEY (`ID_Tipo`) REFERENCES `TIPOLOGIA` (`ID_Tipo`);

--
-- Restriccions per a la taula `TECNIC`
--
ALTER TABLE `TECNIC`
    ADD CONSTRAINT `fk_tecnic_dept` FOREIGN KEY (`ID_Departament`) REFERENCES `DEPARTAMENT` (`ID_Departament`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
