SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `clans` (
  `clan_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `clan_name` varchar(255) DEFAULT NULL,
  `clan_xp` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clan_members` (
  `clan_id` int(10) UNSIGNED DEFAULT NULL,
  `brawlhalla_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `personal_xp` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `legends` (
  `legend_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `legend_name_key` varchar(255) DEFAULT NULL,
  `bio_name` varchar(255) DEFAULT NULL,
  `weapon_one` varchar(255) DEFAULT NULL,
  `weapon_two` varchar(255) DEFAULT NULL,
  `strength` tinyint(3) UNSIGNED DEFAULT NULL,
  `dexterity` tinyint(3) UNSIGNED DEFAULT NULL,
  `defense` tinyint(3) UNSIGNED DEFAULT NULL,
  `speed` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `patches` (
  `id` varchar(255) DEFAULT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `changes` enum('1','0') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `players` (
  `brawlhalla_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `xp` int(10) UNSIGNED DEFAULT NULL,
  `level` int(10) UNSIGNED DEFAULT NULL,
  `rank` int(10) UNSIGNED DEFAULT NULL,
  `tier` varchar(255) DEFAULT NULL,
  `games` int(10) UNSIGNED DEFAULT NULL,
  `wins` int(10) UNSIGNED DEFAULT NULL,
  `rating` int(10) UNSIGNED DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `legend1` int(10) UNSIGNED DEFAULT NULL,
  `legend2` int(10) UNSIGNED DEFAULT NULL,
  `legend3` int(10) UNSIGNED DEFAULT NULL,
  `lastupdated` int(10) UNSIGNED DEFAULT NULL,
  `peak_rating` smallint(5) UNSIGNED DEFAULT NULL,
  `ranked_games` smallint(5) UNSIGNED DEFAULT NULL,
  `ranked_wins` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `player_legends` (
  `brawlhalla_id` int(10) UNSIGNED DEFAULT NULL,
  `legend_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `day` int(10) UNSIGNED DEFAULT NULL,
  `damagedealt` bigint(20) UNSIGNED DEFAULT NULL,
  `damagetaken` bigint(20) UNSIGNED DEFAULT NULL,
  `kos` int(10) UNSIGNED DEFAULT NULL,
  `falls` int(10) UNSIGNED DEFAULT NULL,
  `suicides` int(10) UNSIGNED DEFAULT NULL,
  `teamkos` int(10) UNSIGNED DEFAULT NULL,
  `matchtime` bigint(20) UNSIGNED DEFAULT NULL,
  `games` int(10) UNSIGNED DEFAULT NULL,
  `wins` int(10) UNSIGNED DEFAULT NULL,
  `damageunarmed` bigint(20) UNSIGNED DEFAULT NULL,
  `damagethrownitem` bigint(20) UNSIGNED DEFAULT NULL,
  `damageweaponone` bigint(20) UNSIGNED DEFAULT NULL,
  `damageweapontwo` bigint(20) UNSIGNED DEFAULT NULL,
  `damagegadgets` bigint(20) UNSIGNED DEFAULT NULL,
  `kounarmed` int(10) UNSIGNED DEFAULT NULL,
  `kothrownitem` int(10) UNSIGNED DEFAULT NULL,
  `koweaponone` int(10) UNSIGNED DEFAULT NULL,
  `koweapontwo` int(10) UNSIGNED DEFAULT NULL,
  `kogadgets` int(10) UNSIGNED DEFAULT NULL,
  `timeheldweaponone` bigint(20) UNSIGNED DEFAULT NULL,
  `timeheldweapontwo` bigint(20) UNSIGNED DEFAULT NULL,
  `xp` int(10) UNSIGNED DEFAULT NULL,
  `level` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `player_ranked_legends` (
  `brawlhalla_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `legend_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `day` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating` smallint(5) UNSIGNED DEFAULT NULL,
  `peak_rating` smallint(5) UNSIGNED DEFAULT NULL,
  `tier` varchar(255) DEFAULT NULL,
  `wins` smallint(5) UNSIGNED DEFAULT NULL,
  `games` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ranking_progresses` (
  `type` varchar(50) NOT NULL DEFAULT '',
  `first_page_crawl_ts` int(10) UNSIGNED DEFAULT NULL,
  `page` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `stats` (
  `legend_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `day` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tier` varchar(50) NOT NULL DEFAULT '',
  `damagedealt` bigint(20) UNSIGNED DEFAULT NULL,
  `damagetaken` bigint(20) UNSIGNED DEFAULT NULL,
  `kos` bigint(20) UNSIGNED DEFAULT NULL,
  `falls` bigint(20) UNSIGNED DEFAULT NULL,
  `suicides` bigint(20) UNSIGNED DEFAULT NULL,
  `teamkos` bigint(20) UNSIGNED DEFAULT NULL,
  `matchtime` bigint(20) UNSIGNED DEFAULT NULL,
  `games` bigint(20) UNSIGNED DEFAULT NULL,
  `wins` bigint(20) UNSIGNED DEFAULT NULL,
  `damageunarmed` bigint(20) UNSIGNED DEFAULT NULL,
  `damagethrownitem` bigint(20) UNSIGNED DEFAULT NULL,
  `damageweaponone` bigint(20) UNSIGNED DEFAULT NULL,
  `damageweapontwo` bigint(20) UNSIGNED DEFAULT NULL,
  `damagegadgets` bigint(20) UNSIGNED DEFAULT NULL,
  `kounarmed` bigint(20) UNSIGNED DEFAULT NULL,
  `kothrownitem` bigint(20) UNSIGNED DEFAULT NULL,
  `koweaponone` bigint(20) UNSIGNED DEFAULT NULL,
  `koweapontwo` bigint(20) UNSIGNED DEFAULT NULL,
  `kogadgets` bigint(20) UNSIGNED DEFAULT NULL,
  `timeheldweaponone` bigint(20) UNSIGNED DEFAULT NULL,
  `timeheldweapontwo` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `clans`
  ADD PRIMARY KEY (`clan_id`);

ALTER TABLE `clan_members`
  ADD PRIMARY KEY (`brawlhalla_id`),
  ADD KEY `clan_members_clan_id` (`clan_id`);

ALTER TABLE `legends`
  ADD PRIMARY KEY (`legend_id`),
  ADD KEY `legends_legend_name_key` (`legend_name_key`(191));

ALTER TABLE `patches`
  ADD PRIMARY KEY (`timestamp`),
  ADD KEY `patches_changes_id` (`changes`,`id`(191));

ALTER TABLE `players`
  ADD PRIMARY KEY (`brawlhalla_id`),
  ADD KEY `players_rank` (`rank`);

ALTER TABLE `player_legends`
  ADD UNIQUE KEY `player_legends_brawlhalla_id_legend_id` (`brawlhalla_id`,`legend_id`),
  ADD KEY `player_legends_day_legend_id` (`day`,`legend_id`),
  ADD KEY `player_legends_legend_id_xp` (`legend_id`,`xp`);

ALTER TABLE `player_ranked_legends`
  ADD PRIMARY KEY (`brawlhalla_id`,`legend_id`,`day`),
  ADD UNIQUE KEY `player_ranked_legends_brawlhalla_id_legend_id` (`brawlhalla_id`,`legend_id`),
  ADD KEY `player_ranked_legends_day_legend_id` (`day`,`legend_id`),
  ADD KEY `player_ranked_legends_legend_id_rating` (`legend_id`,`rating`);

ALTER TABLE `ranking_progresses`
  ADD PRIMARY KEY (`type`);

ALTER TABLE `stats`
  ADD UNIQUE KEY `stats_legend_id_day_tier` (`legend_id`,`day`,`tier`),
  ADD KEY `stats_day_legend_id` (`day`,`legend_id`),
  ADD KEY `day` (`day`);
COMMIT;
