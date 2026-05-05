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

-- Adminer 5.4.2 MySQL 9.3.0 dump

SET foreign_key_checks = 0;


DROP DATABASE IF EXISTS `persones`;
CREATE DATABASE `persones` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;


GRANT ALL PRIVILEGES ON persones.* TO 'usuari'@'%';
FLUSH PRIVILEGES;

USE `persones`;

DROP TABLE IF EXISTS `Actuacions`;
CREATE TABLE `Actuacions` (
                              `ID_Incidencia` int NOT NULL,
                              `ID_Actuacion` int NOT NULL,
                              `Descripcio` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `Data_Actuacion` date DEFAULT NULL,
                              `FIN` tinyint(1) DEFAULT '0',
                              `Visible` tinyint(1) DEFAULT '1',
                              `Temps` decimal(5,2) DEFAULT NULL,
                              PRIMARY KEY (`ID_Incidencia`,`ID_Actuacion`),
                              CONSTRAINT `fk_actuacion_incidencia` FOREIGN KEY (`ID_Incidencia`) REFERENCES `INCIDENCIA` (`ID_Incidencia`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `DEPARTAMENT`;
CREATE TABLE `DEPARTAMENT` (
                               `ID_Departament` int NOT NULL AUTO_INCREMENT,
                               `Nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                               PRIMARY KEY (`ID_Departament`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `DEPARTAMENT` (`ID_Departament`, `Nom`) VALUES
                                                        (1,	'Sistemes'),
                                                        (2,	'Català'),
                                                        (3,	'Castellà');

DROP TABLE IF EXISTS `INCIDENCIA`;
CREATE TABLE `INCIDENCIA` (
                              `ID_Incidencia` int NOT NULL AUTO_INCREMENT,
                              `ID_Departament` int NOT NULL,
                              `Data_Inici` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `ID_Tipo` int DEFAULT NULL,
                              `Data_FIN` date DEFAULT NULL,
                              `ID_Tecnic` int DEFAULT NULL,
                              `Prioridad` enum('Baja','Media','Alta','Crítica') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `Descripcio` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
                              PRIMARY KEY (`ID_Incidencia`),
                              KEY `fk_incidencia_dept` (`ID_Departament`),
                              KEY `fk_incidencia_tipo` (`ID_Tipo`),
                              KEY `fk_incidencia_tecnic` (`ID_Tecnic`),
                              CONSTRAINT `fk_incidencia_dept` FOREIGN KEY (`ID_Departament`) REFERENCES `DEPARTAMENT` (`ID_Departament`),
                              CONSTRAINT `fk_incidencia_tecnic` FOREIGN KEY (`ID_Tecnic`) REFERENCES `TECNIC` (`ID_Tecnic`),
                              CONSTRAINT `fk_incidencia_tipo` FOREIGN KEY (`ID_Tipo`) REFERENCES `TIPOLOGIA` (`ID_Tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `INCIDENCIA` (`ID_Incidencia`, `ID_Departament`, `Data_Inici`, `ID_Tipo`, `Data_FIN`, `ID_Tecnic`, `Prioridad`, `Descripcio`) VALUES
                                                                                                                                              (1,	1,	'2026-05-05 07:32:37',	2,	NULL,	1,	'Baja',	'Tot trencat'),
                                                                                                                                              (2,	3,	'2026-05-05 07:33:30',	3,	NULL,	3,	'Alta',	'No funciona internet');

DROP TABLE IF EXISTS `TECNIC`;
CREATE TABLE `TECNIC` (
                          `ID_Tecnic` int NOT NULL AUTO_INCREMENT,
                          `Nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                          PRIMARY KEY (`ID_Tecnic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `TECNIC` (`ID_Tecnic`, `Nom`) VALUES
                                              (1,	'Marco'),
                                              (2,	'Alan'),
                                              (3,	'Joan');

DROP TABLE IF EXISTS `TIPOLOGIA`;
CREATE TABLE `TIPOLOGIA` (
                             `ID_Tipo` int NOT NULL AUTO_INCREMENT,
                             `Nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                             PRIMARY KEY (`ID_Tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `TIPOLOGIA` (`ID_Tipo`, `Nom`) VALUES
                                               (1,	'Teclat'),
                                               (2,	'Ratoli'),
                                               (3,	'Xarxa');

-- 2026-05-05 07:39:24 UTC
