-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 28, 2016 at 07:56 PM
-- Server version: 5.6.33-cll-lve
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `curiousc_brawlmance`
--

-- --------------------------------------------------------

--
-- Table structure for table `legends`
--

CREATE TABLE IF NOT EXISTS `legends` (
  `legend_id` int(10) unsigned NOT NULL,
  `legend_name_key` varchar(20) NOT NULL,
  `role` enum('1','2','3','4','5') DEFAULT NULL,
  `bio_name` varchar(25) NOT NULL,
  `weapon_one` varchar(20) NOT NULL,
  `weapon_two` varchar(20) NOT NULL,
  `strength` tinyint(1) unsigned NOT NULL,
  `dexterity` tinyint(1) unsigned NOT NULL,
  `defense` tinyint(1) unsigned NOT NULL,
  `speed` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`legend_id`),
  UNIQUE KEY `legend_name_key` (`legend_name_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patches`
--

CREATE TABLE IF NOT EXISTS `patches` (
  `id` varchar(10) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Dumping data for table `patches`
--

INSERT INTO `patches` (`id`, `timestamp`) VALUES
('2.38', 1479925853);

-- --------------------------------------------------------

--
-- Table structure for table `playerlegends`
--

CREATE TABLE IF NOT EXISTS `playerlegends` (
  `brawlhalla_id` int(10) unsigned NOT NULL,
  `legend_id` tinyint(2) unsigned NOT NULL,
  `day` int(10) unsigned NOT NULL,
  `damagedealt` int(10) unsigned NOT NULL,
  `damagetaken` int(10) unsigned NOT NULL,
  `kos` int(10) unsigned NOT NULL,
  `falls` int(10) unsigned NOT NULL,
  `suicides` int(10) unsigned NOT NULL,
  `teamkos` int(10) unsigned NOT NULL,
  `matchtime` int(10) unsigned NOT NULL,
  `games` int(10) unsigned NOT NULL,
  `wins` int(10) unsigned NOT NULL,
  `damageunarmed` int(10) unsigned NOT NULL,
  `damagethrownitem` int(10) unsigned NOT NULL,
  `damageweaponone` int(10) unsigned NOT NULL,
  `damageweapontwo` int(10) unsigned NOT NULL,
  `damagegadgets` int(10) unsigned NOT NULL,
  `kounarmed` int(10) unsigned NOT NULL,
  `kothrownitem` int(10) unsigned NOT NULL,
  `koweaponone` int(10) unsigned NOT NULL,
  `koweapontwo` int(10) unsigned NOT NULL,
  `kogadgets` int(10) unsigned NOT NULL,
  `timeheldweaponone` int(10) unsigned NOT NULL,
  `timeheldweapontwo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`brawlhalla_id`,`legend_id`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE IF NOT EXISTS `stats` (
  `legend_id` tinyint(2) unsigned NOT NULL,
  `day` int(10) unsigned NOT NULL,
  `damagedealt` int(10) unsigned NOT NULL,
  `damagetaken` int(10) unsigned NOT NULL,
  `kos` int(10) unsigned NOT NULL,
  `falls` int(10) unsigned NOT NULL,
  `suicides` int(10) unsigned NOT NULL,
  `teamkos` int(10) unsigned NOT NULL,
  `matchtime` int(10) unsigned NOT NULL,
  `games` int(10) unsigned NOT NULL,
  `wins` int(10) unsigned NOT NULL,
  `damageunarmed` int(10) unsigned NOT NULL,
  `damagethrownitem` int(10) unsigned NOT NULL,
  `damageweaponone` int(10) unsigned NOT NULL,
  `damageweapontwo` int(10) unsigned NOT NULL,
  `damagegadgets` int(10) unsigned NOT NULL,
  `kounarmed` int(10) unsigned NOT NULL,
  `kothrownitem` int(10) unsigned NOT NULL,
  `koweaponone` int(10) unsigned NOT NULL,
  `koweapontwo` int(10) unsigned NOT NULL,
  `kogadgets` int(10) unsigned NOT NULL,
  `timeheldweaponone` int(10) unsigned NOT NULL,
  `timeheldweapontwo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`legend_id`,`day`),
  KEY `day` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
