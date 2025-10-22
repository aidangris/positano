
-- ------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- Positano implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `card_type` varchar(16) NOT NULL,
--   `card_type_arg` int(11) NOT NULL,
--   `card_location` varchar(16) NOT NULL,
--   `card_location_arg` int(11) NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Example 2: add a custom field to the standard "player" table
ALTER TABLE `player` ADD `blocks` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `player` ADD `block_supply` INT UNSIGNED NOT NULL DEFAULT '15';
ALTER TABLE `player` ADD `building_points` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `points` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `quality_pick` varchar(200) DEFAULT NULL;
ALTER TABLE `player` ADD `quality_id` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `lot_pick` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `real_color` varchar(20) DEFAULT null;
ALTER TABLE `player` ADD `bid_set` INT UNSIGNED NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `otto` (
  `player_name` varchar(200) NOT NULL DEFAULT 'Otto Amalfi',
  `blocks` INT UNSIGNED NOT NULL DEFAULT '1',
  `block_supply` INT UNSIGNED NOT NULL DEFAULT '15',
  `building_points` INT UNSIGNED NOT NULL DEFAULT '0',
  `points` INT UNSIGNED NOT NULL DEFAULT '0',
  `quality_pick` varchar(200) DEFAULT NULL,
  `quality_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `lot_pick` INT UNSIGNED NOT NULL DEFAULT '0',
  `color` varchar(20) DEFAULT null,
  `bid_set` INT UNSIGNED NOT NULL DEFAULT '0',
  `score` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`player_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `building` (
  `lot_id` smallint(5) unsigned NOT NULL,
  `player` int(10) unsigned DEFAULT NULL,
  `height` smallint(5) unsigned DEFAULT NULL,
  `max_height` smallint(5) unsigned DEFAULT NULL,
  `quality` smallint(5) unsigned DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `points` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`lot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notifications` (
  `player` int(10) unsigned NOT NULL,
  `notification` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`player`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bids` (
  `player` int(10) unsigned NOT NULL,
  `lot_bid` int(10) unsigned DEFAULT NULL,
  `block_bid` int(10) unsigned DEFAULT NULL,
  `quality_bid` int(10) unsigned DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `num_played` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bid_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`player`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) DEFAULT NULL,
  `card_type_arg` int(11) DEFAULT NULL,
  `card_location` varchar(16) DEFAULT NULL,
  `card_location_arg` int(11) DEFAULT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `removed` (
  `lot_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;