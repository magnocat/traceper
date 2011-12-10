-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 10, 2011 at 04:11 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

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
-- Table structure for table `traceper_call_logg`
--

CREATE TABLE IF NOT EXISTS `traceper_call_logg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) unsigned NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `number` int(20) NOT NULL,
  `latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `traceper_call_logg`
--

INSERT INTO `traceper_call_logg` (`id`, `userid`, `contact`, `number`, `latitude`, `longitude`, `begin`, `end`, `type`) VALUES
(2, 11, '0', 552255555, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(3, 11, '0', 2147483647, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(4, 11, '0', 2147483647, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(5, 10, NULL, 25639, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(6, 10, NULL, 25639, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(7, 10, NULL, 147, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(8, 10, NULL, 25639, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(9, 10, NULL, 147369, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(10, 10, NULL, 147369, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(11, 10, NULL, 7575, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(12, 10, NULL, 7575, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(13, 10, NULL, 123456, 0.000000, 0.000000, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(14, 10, NULL, 36987, 0.000000, 0.000000, '1991-05-20 13:14:14', '1991-05-20 14:31:29', 0),
(15, 10, NULL, 7895, 0.000000, 0.000000, '1991-05-24 02:17:29', '1991-05-24 03:38:00', 0),
(16, 10, NULL, 7895, 0.000000, 0.000000, '1991-05-22 20:55:40', '1991-05-22 23:02:20', 0),
(17, 10, NULL, 147, 0.000000, 0.000000, '1991-05-23 05:12:22', '1991-05-23 10:53:14', 0),
(18, 1, NULL, 147, 0.000000, 0.000000, '1991-06-02 21:07:17', '1991-06-02 22:32:27', 0),
(19, 1, NULL, 7895, 0.000000, 0.000000, '1991-06-03 06:31:33', '1991-06-03 08:32:01', 0);

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
  UNIQUE KEY `friend1_2` (`friend1`,`friend2`),
  KEY `friend1` (`friend1`),
  KEY `friend2` (`friend2`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=36 ;

--
-- Dumping data for table `traceper_friends`
--

INSERT INTO `traceper_friends` (`Id`, `friend1`, `friend2`, `status`) VALUES
(16, 18, 1, 1),
(19, 15, 1, 1),
(20, 21, 1, 1),
(22, 2, 1, 0),
(23, 3, 1, 1),
(24, 4, 1, 1),
(25, 5, 1, 0),
(26, 6, 1, 1),
(27, 7, 1, 0),
(28, 8, 1, 0),
(29, 9, 1, 1),
(30, 10, 1, 0),
(31, 11, 1, 1),
(32, 12, 1, 1),
(33, 13, 1, 1),
(34, 1, 14, 0),
(35, 1, 17, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `traceper_upload`
--

INSERT INTO `traceper_upload` (`Id`, `userId`, `latitude`, `longitude`, `altitude`, `uploadTime`, `publicData`) VALUES
(2, 1, 0.000000, 0.000000, 0.000000, '2011-11-27 10:26:35', 1),
(3, 1, 0.000000, 0.000000, 0.000000, '2011-11-27 10:32:07', 1),
(4, 1, 0.000000, 0.000000, 0.000000, '2011-12-03 18:28:40', 1),
(5, 1, 0.000000, 0.000000, 0.000000, '2011-12-03 18:29:43', 0),
(6, 1, 0.000000, 0.000000, 0.000000, '2011-12-03 18:30:53', 1),
(7, 1, 0.000000, 0.000000, 0.000000, '2011-12-03 18:35:01', 1),
(8, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 00:13:04', 1),
(9, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 00:15:54', 0),
(10, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 00:20:31', 1),
(11, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 00:22:09', 1),
(12, 1, 0.000000, 0.000000, 0.000000, '2011-12-04 16:23:40', 1);

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
(1, 'e10adc3949ba59abbe56e057f20f883e', 0, 73.422005, 47.084093, 47.084093, 'Test', 'test@traceper.com', '2011-12-04 17:22:50', '000000000000000', '', 1, '2010-10-31 20:10:09', '2011-12-03 22:00:06'),
(2, '', 0, 12.234567, 122.345677, 0.000000, 'test2', 'test2@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(3, '', 0, 1.000000, 0.000000, 0.000000, '1', '2', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(4, '', 0, 2.000000, 0.000000, 0.000000, '2', '3', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(5, '', 0, 3.000000, 0.000000, 0.000000, '4', '4', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(6, '', 0, 4.000000, 0.000000, 0.000000, '5', '5', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(7, '', 0, 5.000000, 0.000000, 0.000000, '6', '6', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(8, '', 0, 6.000000, 0.000000, 0.000000, '7', '7', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(9, '', 0, 7.000000, 0.000000, 0.000000, '8', '8', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(10, '', 0, 8.000000, 0.000000, 0.000000, '9', '9', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(11, '', 0, 0.000000, 0.000000, 0.000000, '10', '10', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(12, '', 0, 90.000000, 0.000000, 0.000000, '11', '11', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(13, '', 0, 10.000000, 0.000000, 0.000000, '12', '12', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(14, '', 0, 11.000000, 0.000000, 0.000000, '13', '13', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(15, '', 0, 13.000000, 0.000000, 0.000000, '14', '14', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(16, '', 0, 14.000000, 0.000000, 0.000000, '15', '15', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(17, '', 0, 16.000000, 0.000000, 0.000000, '16', '16', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(18, '', 0, 17.000000, 0.000000, 0.000000, '17', '17', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(19, '', 0, 18.000000, 0.000000, 0.000000, '18', '18', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(20, '', 0, 19.000000, 0.000000, 0.000000, '19', '19', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(21, '', 0, 20.000000, 0.000000, 0.000000, '20', '20', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(22, '', 0, 21.000000, 0.000000, 0.000000, '21', '21', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(23, '', 0, 22.000000, 0.000000, 0.000000, '22', '22', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(24, '', 0, 23.000000, 0.000000, 0.000000, '23', '23', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- Dumping data for table `traceper_user_was_here`
--

INSERT INTO `traceper_user_was_here` (`Id`, `userId`, `dataArrivedTime`, `latitude`, `altitude`, `longitude`, `deviceId`, `dataCalculatedTime`) VALUES
(1, 21, '2011-10-22 23:18:44', 37.422005, -122.084095, -122.084095, '000000000000000', '2011-10-21 21:00:00'),
(2, 21, '0000-00-00 00:00:00', 30.000000, 2.000000, 0.000000, '0', '0000-00-00 00:00:00'),
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
(65, 1, '2011-12-04 17:22:50', 73.422005, 47.084093, 47.084093, '000000000000000', '2011-12-03 22:00:06');

-- --------------------------------------------------------

--
-- Table structure for table `tree_elements`
--

CREATE TABLE IF NOT EXISTS `tree_elements` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `ownerEl` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'parent',
  `slave` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=75 ;

--
-- Dumping data for table `tree_elements`
--

INSERT INTO `tree_elements` (`Id`, `name`, `position`, `ownerEl`, `slave`) VALUES
(50, 'jhjhjhdd', 0, 51, '1'),
(51, 'uhuhuh', 0, 0, '0'),
(52, 'kskskskks', 4, 51, '1'),
(53, 'latifdfd', 2, 51, '1'),
(54, 'jnjnnj', 6, 51, '1'),
(55, 'salih', 2, 63, '1'),
(56, 'jjdjdjd', 7, 51, '1'),
(58, 'kdkkd', 3, 51, '1'),
(61, 'kdkd', 5, 51, '0'),
(62, 'jdjdjd', 0, 61, '0'),
(63, 'ueye', 8, 51, '0'),
(64, 'kdkd', 0, 63, '1'),
(65, 'nhb', 1, 63, '0'),
(66, 'btry', 1, 51, '0'),
(67, 'kdkd', 9, 51, '0'),
(68, 'njhy', 0, 67, '0'),
(69, 'bvc', 0, 68, '0'),
(70, 'nht', 0, 69, '1'),
(71, 'bgvf', 1, 69, '0'),
(72, 'mnjh', 10, 51, '0'),
(73, 'oooo', 11, 51, '1'),
(74, 'nnnn', 12, 51, '1');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `traceper_call_logg`
--
ALTER TABLE `traceper_call_logg`
  ADD CONSTRAINT `traceper_call_logg_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
