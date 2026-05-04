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

-----------------------------------------------------

FROM php:apache

# Realment només necessitem això per mysql
#La gent de docker i php han preparat uns scripts que ens faciliten la feina
# i ens permeten instal·lar extensions de php de manera senzilla
RUN docker-php-ext-install pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Exemple de com instal·lar altres paquets a la imatge
RUN apt-get update && apt-get install -y \
    unzip \
    zip \


----------------------------------------------------------------

# Hi ha 3 contenidors
# 1. php amb apache (la nostra aplicació web) que la trobaràs a http://localhost:8080
# 2. mysql (la base de dades), que no és accessible des de l'exterior (el primer cop pot trigar diversos minuts, mireu els logs)
# 3. adminer (un client web per gestionar el mysql), que la trobaràs a http://localhost:8081


services:
  web:
    #La imatge php:apache no conté els drivers de mysql, hem de fer una imatge a mida
    #image: php:apache
    build:
      context: ./images
      dockerfile: Dockerfile_php
    ports:
      - "8080:80"
    volumes:
      - ./php:/var/www/html
    environment:
      VAR1: "sóc una pera i estic al docker-compose"
      VAR2: ${VAR2}
    depends_on:
      - db

  db:
    # Tota la informació d'aquesta imatge està https://dockerhub.com/_/mysql
    image: mysql:9.3
    environment:
      MYSQL_ROOT_PASSWORD: passwordDeRootQueNoShaDeFerServirMai
      # És millor no crear la BBDD aquí, ja que no pots control·lar la codificació
      # de caràcters i aleshores donarà problemes amb accents i caràcters especials
      # La BBDD es crearà a l'inici del contenidor amb els script d'inicialització
      # MYSQL_DATABASE: persones
      MYSQL_USER: usuari
      MYSQL_PASSWORD: paraula_de_pas
      LANG: C.UTF-8
    #    El mysql no s'exposa a l'exterior
    #    L'aplicació web hi accedirà per la xarxa interna de docker
    #    ports:
    #      - "3306:3306"

    # La carpeta de mysql ha d'estar al .gitignore per no pujar-la al repositori
    volumes:
      - ./db_data:/var/lib/mysql
      - ./db_init:/docker-entrypoint-initdb.d


  # Aquesta imatge és un client web per gestionar el mysql via web
  # està disponible a http://localhost:8081
  # i les credencials són les del mysql (per que "simplement" es connecta al mysql)
  adminer:
    image: adminer
    ports:
      - "8081:8080"
    depends_on:
      - db
