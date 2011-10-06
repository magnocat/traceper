-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema php
--


--
-- Definition of table `php`.`traceper_friends`
--

DROP TABLE IF EXISTS `traceper_friends`;
CREATE TABLE  `traceper_friends` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `friend1` int(11) DEFAULT NULL,
  `friend2` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `friend1` (`friend1`,`friend2`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;


--
-- Definition of table `php`.`traceper_groups`
--

DROP TABLE IF EXISTS `traceper_groups`;
CREATE TABLE  `traceper_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


--
-- Definition of table `php`.`traceper_invitedusers`
--

DROP TABLE IF EXISTS `traceper_invitedusers`;
CREATE TABLE  `traceper_invitedusers` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `dt` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;


--
-- Definition of table `php`.`traceper_status_messages`
--

DROP TABLE IF EXISTS `traceper_status_messages`;
CREATE TABLE  `traceper_status_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_message` varchar(128) DEFAULT NULL,
  `status_source` tinyint(4) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `locationId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;


--
-- Definition of table `php`.`traceper_upload`
--

DROP TABLE IF EXISTS `traceper_upload`;
CREATE TABLE  `traceper_upload` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `latitude` decimal(8,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `altitude` decimal(15,6) NOT NULL,
  `uploadTime` datetime NOT NULL,
  `publicData` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`) USING BTREE,
  KEY `new_index` (`userId`),
  KEY `index2` (`uploadTime`),
  KEY `publicData` (`publicData`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Definition of table `php`.`traceper_user_candidates`
--

DROP TABLE IF EXISTS `traceper_user_candidates`;
CREATE TABLE  `traceper_user_candidates` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `realname` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `index_name` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Definition of table `php`.`traceper_user_group_relation`
--

DROP TABLE IF EXISTS `traceper_user_group_relation`;
CREATE TABLE  `traceper_user_group_relation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `userIdIndex` (`userId`),
  KEY `groupIdIndex` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Definition of table `php`.`traceper_user_was_here`
--

DROP TABLE IF EXISTS `traceper_user_was_here`;
CREATE TABLE  `traceper_user_was_here` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `dataArrivedTime` datetime NOT NULL,
  `latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `altitude` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `deviceId` varchar(64) NOT NULL DEFAULT '0',
  `dataCalculatedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  KEY `new_index` (`userId`),
  KEY `time` (`dataArrivedTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- Definition of table `php`.`traceper_users`
--

DROP TABLE IF EXISTS `traceper_users`;
CREATE TABLE  `traceper_users` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `password` char(32) NOT NULL,
  `group` int(10) unsigned NOT NULL DEFAULT '0',
  `latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `altitude` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `realname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dataArrivedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deviceId` varchar(64) DEFAULT NULL,
  `facebook_id` int(15) NOT NULL,
  `status_message` varchar(128) DEFAULT NULL,
  `status_source` tinyint(4) DEFAULT NULL,
  `status_message_time` datetime DEFAULT NULL,
  `dataCalculatedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `email` (`email`),
  KEY `dataArrivedTime` (`dataArrivedTime`),
  KEY `realname` (`realname`) USING BTREE,
  KEY `facebook_id` (`facebook_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='This is for mobile app users';

DROP TABLE IF EXISTS `traceper_upload_rating`;
CREATE TABLE  `traceper_upload_rating` (
  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `voting_count` int(10) unsigned NOT NULL DEFAULT '0',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`upload_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traceper_upload_user_relation`;
CREATE TABLE`traceper_upload_user_relation` (
 `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `upload_id` INT UNSIGNED NOT NULL ,
 `user_id` INT UNSIGNED NOT NULL ,
 INDEX (`upload_id` ,`user_id`) 
) ENGINE=MYISAM ; 


--
-- Dumping data for table `php`.`traceper_users`
--

INSERT INTO `traceper_users` VALUES (1,'827ccb0eea8a706c4c34a16891f84e7b',0,'49.920925','22.868595','32.868595','Test','test@traceper.com','2011-06-22 23:46:33','351751049911319',0,'', 1,'2010-10-31 20:10:09', NOW());

--
-- Tablo için tablo yapısı `traceper_upload_comment`
--

CREATE TABLE IF NOT EXISTS `traceper_upload_comment` (
  `upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment_time` datetime NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`upload_id`)
) ENGINE=InnoDB  ;

--
-- Tablo döküm verisi `traceper_upload_comment`
--


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;