SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `clans` (
  `clan_id` int(10) UNSIGNED NOT NULL,
  `clan_name` varchar(100) NOT NULL,
  `clan_xp` int(10) UNSIGNED NOT NULL,
  `personal_xp` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `clan_members` (
  `clan_id` int(10) UNSIGNED NOT NULL,
  `brawlhalla_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `legends` (
  `legend_id` int(10) UNSIGNED NOT NULL,
  `legend_name_key` varchar(20) NOT NULL,
  `bio_name` varchar(25) NOT NULL,
  `weapon_one` varchar(20) NOT NULL,
  `weapon_two` varchar(20) NOT NULL,
  `strength` tinyint(1) UNSIGNED NOT NULL,
  `dexterity` tinyint(1) UNSIGNED NOT NULL,
  `defense` tinyint(1) UNSIGNED NOT NULL,
  `speed` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `patches` (
  `id` varchar(10) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `changes` enum('1','0') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `playerlegends` (
  `brawlhalla_id` int(10) UNSIGNED NOT NULL,
  `legend_id` tinyint(2) UNSIGNED NOT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `damagedealt` bigint(20) UNSIGNED NOT NULL,
  `damagetaken` bigint(10) UNSIGNED NOT NULL,
  `kos` int(10) UNSIGNED NOT NULL,
  `falls` int(10) UNSIGNED NOT NULL,
  `suicides` int(10) UNSIGNED NOT NULL,
  `teamkos` int(10) UNSIGNED NOT NULL,
  `matchtime` bigint(10) UNSIGNED NOT NULL,
  `games` int(10) UNSIGNED NOT NULL,
  `wins` int(10) UNSIGNED NOT NULL,
  `damageunarmed` bigint(10) UNSIGNED NOT NULL,
  `damagethrownitem` bigint(10) UNSIGNED NOT NULL,
  `damageweaponone` bigint(10) UNSIGNED NOT NULL,
  `damageweapontwo` bigint(10) UNSIGNED NOT NULL,
  `damagegadgets` bigint(10) UNSIGNED NOT NULL,
  `kounarmed` int(10) UNSIGNED NOT NULL,
  `kothrownitem` int(10) UNSIGNED NOT NULL,
  `koweaponone` int(10) UNSIGNED NOT NULL,
  `koweapontwo` int(10) UNSIGNED NOT NULL,
  `kogadgets` int(10) UNSIGNED NOT NULL,
  `timeheldweaponone` bigint(10) UNSIGNED NOT NULL,
  `timeheldweapontwo` bigint(10) UNSIGNED NOT NULL,
  `xp` int(10) UNSIGNED NOT NULL,
  `level` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `players` (
  `brawlhalla_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `xp` int(10) UNSIGNED NOT NULL,
  `level` int(10) UNSIGNED NOT NULL,
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

CREATE TABLE `stats` (
  `legend_id` tinyint(2) UNSIGNED NOT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `tier` varchar(50) NOT NULL,
  `damagedealt` bigint(20) UNSIGNED NOT NULL,
  `damagetaken` bigint(20) UNSIGNED NOT NULL,
  `kos` bigint(20) UNSIGNED NOT NULL,
  `falls` bigint(20) UNSIGNED NOT NULL,
  `suicides` bigint(20) UNSIGNED NOT NULL,
  `teamkos` bigint(20) UNSIGNED NOT NULL,
  `matchtime` bigint(20) UNSIGNED NOT NULL,
  `games` bigint(20) UNSIGNED NOT NULL,
  `wins` bigint(20) UNSIGNED NOT NULL,
  `elo` bigint(20) UNSIGNED NOT NULL,
  `damageunarmed` bigint(20) UNSIGNED NOT NULL,
  `damagethrownitem` bigint(20) UNSIGNED NOT NULL,
  `damageweaponone` bigint(20) UNSIGNED NOT NULL,
  `damageweapontwo` bigint(20) UNSIGNED NOT NULL,
  `damagegadgets` bigint(20) UNSIGNED NOT NULL,
  `kounarmed` bigint(20) UNSIGNED NOT NULL,
  `kothrownitem` bigint(20) UNSIGNED NOT NULL,
  `koweaponone` bigint(20) UNSIGNED NOT NULL,
  `koweapontwo` bigint(20) UNSIGNED NOT NULL,
  `kogadgets` bigint(20) UNSIGNED NOT NULL,
  `timeheldweaponone` bigint(20) UNSIGNED NOT NULL,
  `timeheldweapontwo` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `clans`
  ADD PRIMARY KEY (`clan_id`);

ALTER TABLE `clan_members`
  ADD PRIMARY KEY (`brawlhalla_id`),
  ADD KEY `clan_id` (`clan_id`);

ALTER TABLE `legends`
  ADD PRIMARY KEY (`legend_id`),
  ADD UNIQUE KEY `legend_name_key` (`legend_name_key`);

ALTER TABLE `patches`
  ADD PRIMARY KEY (`timestamp`),
  ADD KEY `changesID` (`changes`,`id`);

ALTER TABLE `playerlegends`
  ADD PRIMARY KEY (`brawlhalla_id`,`legend_id`,`day`),
  ADD KEY `xpindex` (`legend_id`,`xp`) USING BTREE;

ALTER TABLE `players`
  ADD PRIMARY KEY (`brawlhalla_id`) USING BTREE,
  ADD KEY `rank` (`rank`);

ALTER TABLE `stats`
  ADD PRIMARY KEY (`legend_id`,`day`,`tier`) USING BTREE,
  ADD KEY `day` (`day`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
