-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 03, 2017 at 04:55 PM
-- Server version: 5.6.35-cll-lve
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brawlmance`
--

-- --------------------------------------------------------

--
-- Table structure for table `legends`
--

CREATE TABLE `legends` (
  `legend_id` int(10) UNSIGNED NOT NULL,
  `legend_name_key` varchar(20) NOT NULL,
  `role` enum('0','1','2','3','4','5') NOT NULL DEFAULT '0',
  `bio_name` varchar(25) NOT NULL,
  `weapon_one` varchar(20) NOT NULL,
  `weapon_two` varchar(20) NOT NULL,
  `strength` tinyint(1) UNSIGNED NOT NULL,
  `dexterity` tinyint(1) UNSIGNED NOT NULL,
  `defense` tinyint(1) UNSIGNED NOT NULL,
  `speed` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patches`
--

CREATE TABLE `patches` (
  `id` varchar(10) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `changes` enum('1','0') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `playerlegends`
--

CREATE TABLE `playerlegends` (
  `brawlhalla_id` int(10) UNSIGNED NOT NULL,
  `legend_id` tinyint(2) UNSIGNED NOT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `damagedealt` int(10) UNSIGNED NOT NULL,
  `damagetaken` int(10) UNSIGNED NOT NULL,
  `kos` int(10) UNSIGNED NOT NULL,
  `falls` int(10) UNSIGNED NOT NULL,
  `suicides` int(10) UNSIGNED NOT NULL,
  `teamkos` int(10) UNSIGNED NOT NULL,
  `matchtime` int(10) UNSIGNED NOT NULL,
  `games` int(10) UNSIGNED NOT NULL,
  `wins` int(10) UNSIGNED NOT NULL,
  `damageunarmed` int(10) UNSIGNED NOT NULL,
  `damagethrownitem` int(10) UNSIGNED NOT NULL,
  `damageweaponone` int(10) UNSIGNED NOT NULL,
  `damageweapontwo` int(10) UNSIGNED NOT NULL,
  `damagegadgets` int(10) UNSIGNED NOT NULL,
  `kounarmed` int(10) UNSIGNED NOT NULL,
  `kothrownitem` int(10) UNSIGNED NOT NULL,
  `koweaponone` int(10) UNSIGNED NOT NULL,
  `koweapontwo` int(10) UNSIGNED NOT NULL,
  `kogadgets` int(10) UNSIGNED NOT NULL,
  `timeheldweaponone` int(10) UNSIGNED NOT NULL,
  `timeheldweapontwo` int(10) UNSIGNED NOT NULL,
  `xp` int(10) UNSIGNED NOT NULL,
  `level` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `brawlhalla_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `rank` int(10) UNSIGNED NOT NULL,
  `tier` varchar(50) NOT NULL,
  `games` int(10) UNSIGNED NOT NULL,
  `wins` int(10) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL,
  `region` varchar(5) NOT NULL,
  `legend1` int(10) UNSIGNED NOT NULL,
  `legend2` int(10) UNSIGNED NOT NULL,
  `legend3` int(10) UNSIGNED NOT NULL,
  `lastupdated` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `legend_id` tinyint(2) UNSIGNED NOT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `damagedealt` int(10) UNSIGNED NOT NULL,
  `damagetaken` int(10) UNSIGNED NOT NULL,
  `kos` int(10) UNSIGNED NOT NULL,
  `falls` int(10) UNSIGNED NOT NULL,
  `suicides` int(10) UNSIGNED NOT NULL,
  `teamkos` int(10) UNSIGNED NOT NULL,
  `matchtime` int(10) UNSIGNED NOT NULL,
  `games` int(10) UNSIGNED NOT NULL,
  `wins` int(10) UNSIGNED NOT NULL,
  `elo` int(10) UNSIGNED NOT NULL,
  `damageunarmed` int(10) UNSIGNED NOT NULL,
  `damagethrownitem` int(10) UNSIGNED NOT NULL,
  `damageweaponone` int(10) UNSIGNED NOT NULL,
  `damageweapontwo` int(10) UNSIGNED NOT NULL,
  `damagegadgets` int(10) UNSIGNED NOT NULL,
  `kounarmed` int(10) UNSIGNED NOT NULL,
  `kothrownitem` int(10) UNSIGNED NOT NULL,
  `koweaponone` int(10) UNSIGNED NOT NULL,
  `koweapontwo` int(10) UNSIGNED NOT NULL,
  `kogadgets` int(10) UNSIGNED NOT NULL,
  `timeheldweaponone` int(10) UNSIGNED NOT NULL,
  `timeheldweapontwo` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `legends`
--
ALTER TABLE `legends`
  ADD PRIMARY KEY (`legend_id`),
  ADD UNIQUE KEY `legend_name_key` (`legend_name_key`);

--
-- Indexes for table `patches`
--
ALTER TABLE `patches`
  ADD PRIMARY KEY (`timestamp`);

--
-- Indexes for table `playerlegends`
--
ALTER TABLE `playerlegends`
  ADD PRIMARY KEY (`brawlhalla_id`,`legend_id`,`day`),
  ADD KEY `xpindex` (`legend_id`,`brawlhalla_id`,`xp`) USING BTREE;

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`brawlhalla_id`) USING BTREE,
  ADD UNIQUE KEY `rank` (`rank`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`legend_id`,`day`),
  ADD KEY `day` (`day`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
