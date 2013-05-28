-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 28, 2012 at 08:12 PM
-- Server version: 5.5.22
-- PHP Version: 5.3.10-1ubuntu3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `php`
--

-- --------------------------------------------------------

--
-- Table structure for table `traceper_facebookUser`
--

CREATE TABLE IF NOT EXISTS `traceper_facebookUser` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `facebookId` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `facebookId` (`facebookId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_friends`
--

CREATE TABLE IF NOT EXISTS `traceper_friends` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `friend1` int(11) unsigned NOT NULL,
  `friend1Visibility` tinyint(1) NOT NULL,
  `friend2` int(11) unsigned NOT NULL,
  `friend2Visibility` tinyint(1) NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `friend1_2` (`friend1`,`friend2`),
  KEY `friend1` (`friend1`),
  KEY `friend2` (`friend2`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_geofence`
--

CREATE TABLE IF NOT EXISTS `traceper_geofence` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` varchar(500) NOT NULL DEFAULT '',
  `point1Latitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point1Longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point2Latitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point2Longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point3Latitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point3Longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `ownerHasUniqueGeofenceName` (`name`,`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_geofence_user_relation`
--

CREATE TABLE IF NOT EXISTS `traceper_geofence_user_relation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `geofenceId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `geofenceUserId` (`geofenceId`,`userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_invitedusers`
--

CREATE TABLE IF NOT EXISTS `traceper_invitedusers` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `dt` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dumping data for table `traceper_invitedusers`
--

INSERT INTO `traceper_invitedusers` (`Id`, `email`, `dt`) VALUES
(1, 'qwerrq@werq.com', '2011-10-04 22:10:35');

-- --------------------------------------------------------

--
-- Table structure for table `traceper_privacy_groups`
--

CREATE TABLE IF NOT EXISTS `traceper_privacy_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `allowedToSeeOwnersPosition` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ownerHasUniqueGroupName` (`name`,`owner`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_status_messages`
--

CREATE TABLE IF NOT EXISTS `traceper_status_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_message` varchar(128) DEFAULT NULL,
  `status_source` tinyint(4) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `locationId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_upload`
--

CREATE TABLE IF NOT EXISTS `traceper_upload` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fileType` tinyint(4) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `latitude` decimal(8,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `altitude` decimal(15,6) NOT NULL,
  `uploadTime` datetime NOT NULL,
  `publicData` tinyint(4) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL,
  `isLive` tinyint(4) NOT NULL DEFAULT '0',
  `liveKey` varchar(128) NOT NULL,
  PRIMARY KEY (`Id`) USING BTREE,
  KEY `index2` (`uploadTime`),
  KEY `publicData` (`publicData`),
  KEY `userId` (`userId`),
  KEY `descriptioon` (`description`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `traceper_upload`
--

INSERT INTO `traceper_upload` (`Id`, `fileType`, `userId`, `latitude`, `longitude`, `altitude`, `uploadTime`, `publicData`, `description`, `isLive`, `liveKey`) VALUES
(7, 0, 1, 0.000000, 0.000000, 0.000000, '2011-12-03 18:35:01', 1, '', 0, ''),
(10, 0, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 00:20:31', 1, '', 0, ''),
(11, 0, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 00:22:09', 1, '', 0, ''),
(12, 0, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 16:23:40', 1, '', 0, ''),
(13, 0, 1, 0.000000, 0.000000, 0.000000, '2012-01-01 05:56:24', 1, '', 0, ''),
(14, 0, 1, 0.000000, 0.000000, 0.000000, '2012-01-01 07:34:35', 0, 'ghj', 0, ''),
(15, 0, 1, 39.759800, 39.467087, 0.000000, '2012-08-26 09:11:55', 0, 'jfjf', 0, '0'),
(16, 0, 1, 39.759800, 39.467087, 0.000000, '2012-08-26 09:13:41', 0, 'hdhd', 0, '0'),
(17, 0, 1, 39.737456, 39.412809, 0.000000, '2012-08-26 09:16:07', 0, 'hdjd', 0, '0'),
(18, 0, 1, 39.759800, 39.467087, 0.000000, '2012-08-26 09:25:28', 0, 'hhhh', 0, '0'),
(19, 0, 1, 39.759800, 39.467087, 0.000000, '2012-08-26 09:32:28', 0, 'tÃ¼r', 0, '0'),
(20, 0, 1, 39.922642, 32.867466, 1330.000000, '2012-08-28 20:10:39', 0, 'bi', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `traceper_upload_comment`
--

CREATE TABLE IF NOT EXISTS `traceper_upload_comment` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment_time` datetime NOT NULL,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_upload_rating`
--

CREATE TABLE IF NOT EXISTS `traceper_upload_rating` (
  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `voting_count` int(10) unsigned NOT NULL DEFAULT '0',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`upload_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `traceper_upload_rating`
--

INSERT INTO `traceper_upload_rating` (`upload_id`, `voting_count`, `points`) VALUES
(1, 17, 51);

-- --------------------------------------------------------

--
-- Table structure for table `traceper_upload_user_relation`
--

CREATE TABLE IF NOT EXISTS `traceper_upload_user_relation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `upload_id` (`upload_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_users`
--

CREATE TABLE IF NOT EXISTS `traceper_users` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `password` char(32) NOT NULL,
  `group` int(10) unsigned NOT NULL DEFAULT '0',
  `latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `altitude` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `publicPosition` tinyint(4) NOT NULL DEFAULT '0',
  `authorityLevel` tinyint(4) NOT NULL DEFAULT '1',
  `realname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dataArrivedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deviceId` varchar(64) DEFAULT NULL,
  `status_message` varchar(128) DEFAULT NULL,
  `status_source` tinyint(4) DEFAULT NULL,
  `status_message_time` datetime DEFAULT NULL,
  `dataCalculatedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fb_id` varchar(50) NOT NULL DEFAULT '0',
  `g_id` varchar(50) NOT NULL DEFAULT '0',
  `gender` tinyint(4) DEFAULT '0',
  `userType` tinyint(4) NOT NULL DEFAULT '0',
  `account_type` tinyint(4) NOT NULL,
  `gp_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `email` (`email`),
  KEY `dataArrivedTime` (`dataArrivedTime`),
  KEY `realname` (`realname`) USING BTREE,
  KEY `password` (`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='This is for mobile app users' AUTO_INCREMENT=87 ;

--
-- Dumping data for table `traceper_users`
--

INSERT INTO `traceper_users` (`Id`, `password`, `group`, `latitude`, `longitude`, `altitude`, `publicPosition`, `authorityLevel`, `realname`, `email`, `dataArrivedTime`, `deviceId`, `status_message`, `status_source`, `status_message_time`, `dataCalculatedTime`, `fb_id`, `g_id`, `gender`, `userType`, `account_type`, `gp_image`) VALUES
(1, '827ccb0eea8a706c4c34a16891f84e7b', 0, 39.922642, 32.867466, 1330.000000, 0, 1, 'Test', 'test@traceper.com', '2012-08-28 20:10:03', '353043053846625', '', 1, '2010-10-31 20:10:09', '2012-08-28 20:10:04', '0', '0', 0, 0, 0, NULL),
(86, 'a384b6463fc216a5f8ecb6670f86456a', 0, 0.000000, 0.000000, 0.000000, 0, 1, 'Ahmet OÄŸuz Mermerkaya', 'ahmetmermerkaya@hotmail.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '711388294', '0', 0, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `traceper_user_candidates`
--

CREATE TABLE IF NOT EXISTS `traceper_user_candidates` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `realname` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `index_name` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6 ;

--
-- Dumping data for table `traceper_user_candidates`
--

INSERT INTO `traceper_user_candidates` (`Id`, `email`, `realname`, `password`, `time`) VALUES
(1, 'test3@traceper.com', 'test3', '8ad8757baa8564dc136c1e07507f4a98', '2011-10-04 10:27:00'),
(2, 'qwe@qwe.com', 'kdfkjdlf', '827ccb0eea8a706c4c34a16891f84e7b', '2011-11-26 02:36:46'),
(3, 'qwer@qer.com', 'dlfkdf', '827ccb0eea8a706c4c34a16891f84e7b', '2011-11-26 02:38:56'),
(4, 'deneme@deneme.com', 'deneme', '827ccb0eea8a706c4c34a16891f84e7b', '2011-11-26 03:02:02'),
(5, 'contact@mekya.com', 'mekya', 'e10adc3949ba59abbe56e057f20f883e', '2011-12-03 06:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `traceper_user_privacy_group_relation`
--

CREATE TABLE IF NOT EXISTS `traceper_user_privacy_group_relation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupOwner` int(11) NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `userIdGroupId` (`userId`,`groupId`),
  UNIQUE KEY `groupOwnerUserId` (`groupOwner`,`userId`),
  KEY `userId` (`userId`),
  KEY `traceper_user_privacy_group_relation_ibfk_2` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapýsý `traceper_reset_password`
--

CREATE TABLE IF NOT EXISTS `traceper_reset_password` (
  `email` varchar(100) NOT NULL,
  `token` varchar(50) NOT NULL,
  `requestTime` datetime NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tablo döküm verisi `traceper_reset_password`
--

-- --------------------------------------------------------

--
-- Table structure for table `traceper_user_was_here`
--

CREATE TABLE IF NOT EXISTS `traceper_user_was_here` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `dataArrivedTime` datetime NOT NULL,
  `latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `altitude` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `deviceId` varchar(64) NOT NULL DEFAULT '0',
  `dataCalculatedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  KEY `time` (`dataArrivedTime`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=153 ;

--
-- Dumping data for table `traceper_user_was_here`
--

INSERT INTO `traceper_user_was_here` (`Id`, `userId`, `dataArrivedTime`, `latitude`, `altitude`, `longitude`, `deviceId`, `dataCalculatedTime`) VALUES
(3, 1, '2011-11-24 21:37:18', 37.422005, 22.084095, 22.084095, '000000000000000', '2011-11-23 22:00:00'),
(4, 1, '2011-11-24 21:43:00', 7.422005, 2.084095, 2.084095, '000000000000000', '2011-11-23 22:00:02'),
(5, 1, '2011-11-24 21:44:11', 7.422005, 2.084095, 2.084095, '000000000000000', '2011-11-23 22:00:02'),
(9, 1, '2011-11-25 02:56:39', 78.422005, 2.084095, 2.084095, '000000000000000', '2011-11-23 22:00:04'),
(10, 1, '2011-11-25 02:56:53', 78.422005, 23.084095, 23.084095, '000000000000000', '2011-11-23 22:00:05'),
(11, 1, '2011-11-25 02:57:03', 78.422005, 3.084095, 3.084095, '000000000000000', '2011-11-23 22:00:06'),
(12, 1, '2011-11-25 02:57:24', 8.422005, 3.084095, 3.084095, '000000000000000', '2011-11-23 22:00:07'),
(13, 1, '2011-11-25 03:32:21', 8.422005, 38.084093, 38.084093, '000000000000000', '2011-11-23 22:00:08'),
(14, 1, '2011-12-03 18:25:47', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:25:46'),
(15, 1, '2011-12-03 18:26:02', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:25:46'),
(16, 1, '2011-12-03 18:26:02', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:01'),
(17, 1, '2011-12-03 18:26:12', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:25:46'),
(18, 1, '2011-12-03 18:26:13', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:01'),
(19, 1, '2011-12-03 18:26:13', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:11'),
(20, 1, '2011-12-03 18:30:09', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:25:46'),
(21, 1, '2011-12-03 18:30:09', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:01'),
(22, 1, '2011-12-03 18:30:10', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:11'),
(23, 1, '2011-12-03 18:30:10', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:30:08'),
(24, 1, '2011-12-03 18:32:07', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:25:46'),
(25, 1, '2011-12-03 18:32:07', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:01'),
(26, 1, '2011-12-03 18:32:07', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:26:11'),
(27, 1, '2011-12-03 18:32:07', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:30:08'),
(28, 1, '2011-12-03 18:32:07', 39.921466, 32.858031, 32.858031, '351751049911319', '2011-12-03 18:32:06'),
(29, 1, '2011-12-03 18:35:41', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(30, 1, '2011-12-03 18:36:31', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(31, 1, '2011-12-03 18:36:31', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(32, 1, '2011-12-03 18:38:40', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(33, 1, '2011-12-03 18:38:40', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(34, 1, '2011-12-03 18:38:43', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(35, 1, '2011-12-03 18:38:43', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(36, 1, '2011-12-03 18:38:43', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:38:42'),
(37, 1, '2011-12-03 18:39:37', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(38, 1, '2011-12-03 18:39:37', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(39, 1, '2011-12-03 18:39:37', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:38:42'),
(40, 1, '2011-12-03 18:39:37', 39.920730, 32.869167, 32.869167, '351751049911319', '2011-12-03 18:39:37'),
(41, 1, '2011-12-03 19:03:53', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(42, 1, '2011-12-03 19:03:54', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(43, 1, '2011-12-03 19:03:54', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:38:42'),
(44, 1, '2011-12-03 19:03:54', 39.920730, 32.869167, 32.869167, '351751049911319', '2011-12-03 18:39:37'),
(45, 1, '2011-12-03 19:04:10', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(46, 1, '2011-12-03 19:04:11', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(47, 1, '2011-12-03 19:04:11', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:38:42'),
(48, 1, '2011-12-03 19:04:11', 39.920730, 32.869167, 32.869167, '351751049911319', '2011-12-03 18:39:37'),
(49, 1, '2011-12-03 19:04:11', 39.917822, 32.859920, 32.859920, '351751049911319', '2011-12-03 19:04:09'),
(50, 1, '2011-12-03 19:04:12', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:35:39'),
(51, 1, '2011-12-03 19:04:12', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:36:30'),
(52, 1, '2011-12-03 19:04:12', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 18:38:42'),
(53, 1, '2011-12-03 19:04:12', 39.920730, 32.869167, 32.869167, '351751049911319', '2011-12-03 18:39:37'),
(54, 1, '2011-12-03 19:04:12', 39.917822, 32.859920, 32.859920, '351751049911319', '2011-12-03 19:04:09'),
(55, 1, '2011-12-03 19:04:13', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-03 19:04:10'),
(56, 1, '2011-12-04 14:59:33', 37.422005, 22.084095, 22.084095, '000000000000000', '2011-12-03 22:00:00'),
(57, 1, '2011-12-04 15:07:10', 3.422005, 22.084095, 22.084095, '000000000000000', '2011-12-03 22:00:01'),
(58, 1, '2011-12-04 15:09:40', 3.422005, 22.084095, 22.084095, '000000000000000', '2011-12-03 22:00:01'),
(59, 1, '2011-12-04 15:10:00', 3.422005, 2.084095, 2.084095, '000000000000000', '2011-12-03 22:00:02'),
(60, 1, '2011-12-04 15:13:47', 3.422005, 28.084095, 28.084095, '000000000000000', '2011-12-03 22:00:03'),
(61, 1, '2011-12-04 15:33:59', 73.422005, 28.084095, 28.084095, '000000000000000', '2011-12-03 22:00:04'),
(62, 1, '2011-12-04 16:16:23', 73.422005, 28.084095, 28.084095, '000000000000000', '2011-12-03 22:00:00'),
(63, 1, '2011-12-04 17:21:12', 73.422005, 84.084095, 84.084095, '000000000000000', '2011-12-03 22:00:04'),
(64, 1, '2011-12-04 17:21:59', 73.422005, 4.084093, 4.084093, '000000000000000', '2011-12-03 22:00:05'),
(65, 1, '2011-12-04 17:22:50', 73.422005, 47.084093, 47.084093, '000000000000000', '2011-12-03 22:00:06'),
(66, 1, '2011-12-12 04:22:33', 37.422005, -122.084095, -122.084095, '000000000000000', '2011-12-11 22:00:00'),
(67, 1, '2011-12-12 05:08:21', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-12 05:08:13'),
(68, 1, '2011-12-21 21:34:49', 37.422005, -122.084095, -122.084095, '000000000000000', '2011-12-20 22:00:00'),
(69, 1, '2011-12-22 00:05:14', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 00:04:56'),
(70, 1, '2011-12-22 00:35:40', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 00:35:34'),
(71, 1, '2011-12-22 00:39:16', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 00:39:06'),
(72, 1, '2011-12-22 00:43:02', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 00:42:47'),
(73, 1, '2011-12-22 01:16:04', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:16:01'),
(74, 1, '2011-12-22 01:16:30', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:16:17'),
(75, 1, '2011-12-22 01:17:00', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:16:47'),
(76, 1, '2011-12-22 01:17:30', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:17:17'),
(77, 1, '2011-12-22 01:19:25', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:19:22'),
(78, 1, '2011-12-22 01:19:50', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:19:47'),
(79, 1, '2011-12-22 01:20:20', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:20:17'),
(80, 1, '2011-12-22 01:20:36', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:20:33'),
(81, 1, '2011-12-22 01:20:50', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:20:47'),
(82, 1, '2011-12-22 01:21:02', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:21:00'),
(83, 1, '2011-12-22 01:21:21', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:21:17'),
(84, 1, '2011-12-22 01:23:25', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:23:22'),
(85, 1, '2011-12-22 01:23:50', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:23:47'),
(86, 1, '2011-12-22 01:24:23', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:24:17'),
(87, 1, '2011-12-22 01:24:50', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:24:47'),
(88, 1, '2011-12-22 01:25:20', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:25:17'),
(89, 1, '2011-12-22 01:25:50', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:25:47'),
(90, 1, '2011-12-22 01:26:20', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:26:17'),
(91, 1, '2011-12-22 01:26:53', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:26:47'),
(92, 1, '2011-12-22 01:27:15', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:27:12'),
(93, 1, '2011-12-22 01:27:26', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:27:23'),
(94, 1, '2011-12-22 01:28:20', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:27:47'),
(95, 1, '2011-12-22 01:28:50', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:28:25'),
(96, 1, '2011-12-22 01:36:20', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:35:37'),
(97, 1, '2011-12-22 01:36:27', 39.920755, 32.868721, 32.868721, '351751049911319', '2011-12-22 01:36:20'),
(98, 1, '2011-12-22 01:56:16', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:55:43'),
(99, 1, '2011-12-22 01:56:59', 39.920865, 32.868708, 32.868708, '351751049911319', '2011-12-22 01:56:35'),
(100, 1, '2011-12-22 02:06:23', 39.920526, 32.868991, 32.868991, '351751049911319', '2011-12-22 02:05:56'),
(101, 1, '2011-12-22 02:10:00', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 02:09:58'),
(102, 1, '2011-12-22 02:14:35', 39.920703, 32.868969, 32.868969, '351751049911319', '2011-12-22 02:14:36'),
(103, 1, '2011-12-22 02:20:13', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 02:20:03'),
(104, 1, '2011-12-22 02:24:49', 39.920528, 32.868930, 32.868930, '351751049911319', '2011-12-22 02:24:18'),
(105, 1, '2011-12-22 02:25:41', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 02:25:10'),
(106, 1, '2011-12-22 02:29:41', 39.920802, 32.868919, 32.868919, '351751049911319', '2011-12-22 02:29:42'),
(107, 1, '2011-12-22 02:34:47', 39.921189, 32.869280, 32.869280, '351751049911319', '2011-12-22 02:34:47'),
(108, 1, '2011-12-22 02:39:44', 39.921332, 32.868721, 32.868721, '351751049911319', '2011-12-22 02:39:45'),
(109, 1, '2011-12-22 02:46:07', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 02:45:48'),
(110, 1, '2011-12-22 02:49:49', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 02:49:18'),
(111, 1, '2011-12-22 02:54:35', 39.920659, 32.869207, 32.869207, '351751049911319', '2011-12-22 02:54:36'),
(112, 1, '2011-12-22 02:59:49', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 02:59:18'),
(113, 1, '2011-12-22 03:01:41', 39.920761, 32.868674, 32.868674, '351751049911319', '2011-12-22 03:00:32'),
(114, 1, '2011-12-22 03:05:19', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:04:54'),
(115, 1, '2011-12-22 03:10:19', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:10:03'),
(116, 1, '2011-12-22 03:14:14', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:14:11'),
(117, 1, '2011-12-22 03:14:20', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:14:17'),
(118, 1, '2011-12-22 03:15:29', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:14:17'),
(119, 1, '2011-12-22 03:17:34', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:17:21'),
(120, 1, '2011-12-22 03:18:10', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-22 03:18:07'),
(121, 1, '2011-12-22 03:19:09', 39.920905, 32.868862, 32.868862, '351751049911319', '2011-12-22 03:18:07'),
(122, 1, '2011-12-22 03:19:20', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:19:17'),
(123, 1, '2011-12-22 03:20:19', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:19:17'),
(124, 1, '2011-12-22 03:21:15', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:21:12'),
(125, 1, '2011-12-22 03:22:24', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:21:12'),
(126, 1, '2011-12-22 03:24:25', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:24:17'),
(127, 1, '2011-12-22 03:25:19', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:24:17'),
(128, 1, '2011-12-22 03:29:20', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:29:17'),
(129, 1, '2011-12-22 03:30:19', 39.920875, 32.868639, 32.868639, '351751049911319', '2011-12-22 03:29:17'),
(130, 1, '2012-08-25 22:30:01', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-25 22:30:23'),
(131, 1, '2012-08-25 22:30:33', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-25 22:31:03'),
(132, 1, '2012-08-25 22:32:13', 39.788101, 1316.000000, 39.468555, '353043053846625', '2012-08-25 22:32:02'),
(133, 1, '2012-08-26 08:44:40', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 08:45:11'),
(134, 1, '2012-08-26 08:51:04', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 08:51:38'),
(135, 1, '2012-08-26 08:54:56', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 08:55:29'),
(136, 1, '2012-08-26 08:54:56', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 08:55:29'),
(137, 1, '2012-08-26 08:59:52', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:00:26'),
(138, 1, '2012-08-26 09:12:31', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:13:05'),
(139, 1, '2012-08-26 09:15:55', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:13:05'),
(140, 1, '2012-08-26 09:15:56', 39.737456, 0.000000, 39.412809, '353043053846625', '2012-08-26 09:16:30'),
(141, 1, '2012-08-26 09:25:18', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:25:52'),
(142, 1, '2012-08-26 09:27:14', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:27:48'),
(143, 1, '2012-08-26 09:28:25', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:28:59'),
(144, 1, '2012-08-26 09:31:13', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:31:47'),
(145, 1, '2012-08-26 09:33:00', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:33:34'),
(146, 1, '2012-08-26 09:33:37', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:34:11'),
(147, 1, '2012-08-26 09:34:20', 39.759800, 0.000000, 39.467087, '353043053846625', '2012-08-26 09:34:54'),
(148, 1, '2012-08-26 09:35:28', 39.737456, 0.000000, 39.412809, '353043053846625', '2012-08-26 09:36:02'),
(149, 1, '2012-08-28 20:04:26', 39.921074, 960.000000, 32.868755, '353043053846625', '2012-08-28 20:04:28'),
(150, 1, '2012-08-28 20:06:02', 39.922298, 904.000000, 32.868797, '353043053846625', '2012-08-28 20:06:03'),
(151, 1, '2012-08-28 20:07:52', 39.919422, 1125.000000, 32.871437, '353043053846625', '2012-08-28 20:07:53'),
(152, 1, '2012-08-28 20:10:03', 39.922642, 1330.000000, 32.867466, '353043053846625', '2012-08-28 20:10:04');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `traceper_friends`
--
ALTER TABLE `traceper_friends`
  ADD CONSTRAINT `traceper_friends_ibfk_1` FOREIGN KEY (`friend1`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traceper_friends_ibfk_2` FOREIGN KEY (`friend2`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `traceper_upload`
--
ALTER TABLE `traceper_upload`
  ADD CONSTRAINT `traceper_upload_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `traceper_user_privacy_group_relation`
--
ALTER TABLE `traceper_user_privacy_group_relation`
  ADD CONSTRAINT `traceper_user_privacy_group_relation_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traceper_user_privacy_group_relation_ibfk_2` FOREIGN KEY (`groupId`) REFERENCES `traceper_privacy_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `traceper_user_was_here`
--
ALTER TABLE `traceper_user_was_here`
  ADD CONSTRAINT `traceper_user_was_here_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
