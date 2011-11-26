-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 18, 2011 at 01:55 AM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test2`
--

-- --------------------------------------------------------

--
-- Table structure for table `traceper_friends`
--

CREATE TABLE IF NOT EXISTS `traceper_friends` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `friend1` int(11) unsigned NOT NULL,
  `friend2` int(11) unsigned NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `friend1` (`friend1`),
  KEY `friend2` (`friend2`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=17 ;

--
-- Dumping data for table `traceper_friends`
--

INSERT INTO `traceper_friends` (`Id`, `friend1`, `friend2`, `status`) VALUES
(1, 1, 2, 1),
(2, 1, 3, 1),
(3, 1, 4, 1),
(4, 1, 5, 1),
(5, 1, 6, 1),
(6, 1, 7, 1),
(7, 1, 8, 1),
(8, 1, 9, 1),
(9, 1, 10, 1),
(10, 1, 11, 1),
(11, 1, 12, 1),
(12, 1, 13, 1),
(13, 1, 14, 1),
(14, 1, 15, 1),
(15, 1, 16, 1),
(16, 1, 17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `traceper_geofence`
--

CREATE TABLE IF NOT EXISTS `traceper_geofence` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `point1Latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `point1Longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point2Latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `point2Longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `point3Latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `point3Longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `traceper_groups`
--

CREATE TABLE IF NOT EXISTS `traceper_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

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
  `userId` int(11) unsigned NOT NULL,
  `latitude` decimal(8,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `altitude` decimal(15,6) NOT NULL,
  `uploadTime` datetime NOT NULL,
  `publicData` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`) USING BTREE,
  KEY `index2` (`uploadTime`),
  KEY `publicData` (`publicData`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `realname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dataArrivedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deviceId` varchar(64) DEFAULT NULL,
  `status_message` varchar(128) DEFAULT NULL,
  `status_source` tinyint(4) DEFAULT NULL,
  `status_message_time` datetime DEFAULT NULL,
  `dataCalculatedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `email` (`email`),
  KEY `dataArrivedTime` (`dataArrivedTime`),
  KEY `realname` (`realname`) USING BTREE,
  KEY `password` (`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='This is for mobile app users' AUTO_INCREMENT=25 ;

--
-- Dumping data for table `traceper_users`
--

INSERT INTO `traceper_users` (`Id`, `password`, `group`, `latitude`, `longitude`, `altitude`, `realname`, `email`, `dataArrivedTime`, `deviceId`, `status_message`, `status_source`, `status_message_time`, `dataCalculatedTime`) VALUES
(1, 'e10adc3949ba59abbe56e057f20f883e', 0, 37.422005, -122.084095, -122.084095, 'Test', 'test@traceper.com', '2011-10-22 23:18:44', '000000000000000', '', 1, '2010-10-31 20:10:09', '2011-10-21 21:00:00'),
(2, '', 0, 0.000000, 0.000000, 0.000000, 'test2', 'test2@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(3, '', 0, 0.000000, 0.000000, 0.000000, '1', '2', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(4, '', 0, 0.000000, 0.000000, 0.000000, '2', '3', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(5, '', 0, 0.000000, 0.000000, 0.000000, '4', '4', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(6, '', 0, 0.000000, 0.000000, 0.000000, '5', '5', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(7, '', 0, 0.000000, 0.000000, 0.000000, '6', '6', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(8, '', 0, 0.000000, 0.000000, 0.000000, '7', '7', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(9, '', 0, 0.000000, 0.000000, 0.000000, '8', '8', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(10, '', 0, 0.000000, 0.000000, 0.000000, '9', '9', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(11, '', 0, 0.000000, 0.000000, 0.000000, '10', '10', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(12, '', 0, 0.000000, 0.000000, 0.000000, '11', '11', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(13, '', 0, 0.000000, 0.000000, 0.000000, '12', '12', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(14, '', 0, 0.000000, 0.000000, 0.000000, '13', '13', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(15, '', 0, 0.000000, 0.000000, 0.000000, '14', '14', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(16, '', 0, 0.000000, 0.000000, 0.000000, '15', '15', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(17, '', 0, 0.000000, 0.000000, 0.000000, '16', '16', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(18, '', 0, 0.000000, 0.000000, 0.000000, '17', '17', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(19, '', 0, 0.000000, 0.000000, 0.000000, '18', '18', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(20, '', 0, 0.000000, 0.000000, 0.000000, '19', '19', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(21, '', 0, 0.000000, 0.000000, 0.000000, '20', '20', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(22, '', 0, 0.000000, 0.000000, 0.000000, '21', '21', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(23, '', 0, 0.000000, 0.000000, 0.000000, '22', '22', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(24, '', 0, 0.000000, 0.000000, 0.000000, '23', '23', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dumping data for table `traceper_user_candidates`
--

INSERT INTO `traceper_user_candidates` (`Id`, `email`, `realname`, `password`, `time`) VALUES
(1, 'test3@traceper.com', 'test3', '8ad8757baa8564dc136c1e07507f4a98', '2011-10-04 10:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `traceper_user_group_relation`
--

CREATE TABLE IF NOT EXISTS `traceper_user_group_relation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `userIdIndex` (`userId`),
  KEY `groupIdIndex` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `traceper_user_was_here`
--

INSERT INTO `traceper_user_was_here` (`Id`, `userId`, `dataArrivedTime`, `latitude`, `altitude`, `longitude`, `deviceId`, `dataCalculatedTime`) VALUES
(1, 1, '2011-10-22 23:18:44', 37.422005, -122.084095, -122.084095, '000000000000000', '2011-10-21 21:00:00');

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
-- Constraints for table `traceper_user_was_here`
--
ALTER TABLE `traceper_user_was_here`
  ADD CONSTRAINT `traceper_user_was_here_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
