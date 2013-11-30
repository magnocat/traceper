-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Anamakine: localhost
-- Üretim Zamanı: 30 Kas 2013, 20:39:07
-- Sunucu sürümü: 5.5.20
-- PHP Sürümü: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Veritabanı: `php`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_deleted_uploads`
--

CREATE TABLE IF NOT EXISTS `traceper_deleted_uploads` (
  `uploadId` int(11) NOT NULL,
  `publicData` tinyint(4) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL,
  `deletionTime` datetime NOT NULL,
  PRIMARY KEY (`uploadId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tablo döküm verisi `traceper_deleted_uploads`
--

INSERT INTO `traceper_deleted_uploads` (`uploadId`, `publicData`, `userId`, `deletionTime`) VALUES
(31, 1, 6, '2013-11-23 20:00:00'),
(39, 0, 1, '0000-00-00 00:00:00'),
(40, 0, 1, '2013-11-22 03:30:34'),
(43, 1, 1, '2013-11-22 03:53:43');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_friends`
--

CREATE TABLE IF NOT EXISTS `traceper_friends` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `friend1` int(11) unsigned NOT NULL,
  `friend1Visibility` tinyint(1) NOT NULL,
  `friend2` int(11) unsigned NOT NULL,
  `friend2Visibility` tinyint(1) NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `isNew` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `friend1_2` (`friend1`,`friend2`),
  KEY `friend1` (`friend1`),
  KEY `friend2` (`friend2`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=129 ;

--
-- Tablo döküm verisi `traceper_friends`
--

INSERT INTO `traceper_friends` (`Id`, `friend1`, `friend1Visibility`, `friend2`, `friend2Visibility`, `status`, `isNew`) VALUES
(44, 1, 0, 9, 1, 1, 0),
(45, 10, 0, 1, 0, 1, 0),
(46, 1, 0, 11, 0, 1, 0),
(57, 1, 1, 18, 1, 0, 0),
(60, 13, 0, 1, 0, 0, 0),
(61, 16, 0, 1, 0, 0, 0),
(65, 30, 1, 1, 1, 1, 0),
(67, 1, 0, 8, 1, 1, 0),
(68, 1, 1, 12, 1, 1, 0),
(69, 1, 1, 14, 1, 1, 0),
(70, 1, 1, 15, 1, 1, 0),
(72, 1, 1, 19, 1, 1, 0),
(73, 1, 1, 20, 1, 1, 0),
(74, 1, 1, 32, 1, 1, 0),
(75, 31, 1, 1, 1, 0, 0),
(77, 1, 1, 34, 1, 1, 0),
(78, 1, 1, 35, 1, 1, 0),
(86, 1, 1, 43, 1, 1, 0),
(95, 1, 1, 52, 1, 1, 0),
(99, 1, 1, 56, 1, 1, 0),
(100, 1, 1, 57, 1, 1, 0),
(102, 1, 1, 59, 1, 1, 0),
(103, 1, 1, 60, 1, 1, 0),
(105, 1, 1, 62, 1, 1, 0),
(106, 1, 1, 63, 1, 1, 0),
(107, 1, 1, 64, 1, 1, 0),
(108, 1, 1, 65, 1, 1, 0),
(109, 1, 1, 66, 1, 1, 0),
(110, 1, 1, 67, 1, 1, 0),
(111, 1, 1, 68, 1, 1, 0),
(112, 24, 1, 1, 1, 0, 0),
(123, 30, 1, 2, 1, 1, 0),
(127, 1, 0, 2, 1, 1, 0),
(128, 69, 1, 1, 1, 0, 0);

--
-- Tetikleyiciler `traceper_friends`
--
DROP TRIGGER IF EXISTS `friend_bi`;
DELIMITER //
CREATE TRIGGER `friend_bi` BEFORE INSERT ON `traceper_friends`
 FOR EACH ROW BEGIN 
DECLARE found_count,newcol1,newcol2,dummy INT;
SET newcol1 = NEW.friend1;
SET newcol2 = NEW.friend2;
SELECT COUNT(1) INTO found_count FROM traceper_friends
WHERE friend1 = newcol2 AND friend2 = newcol1;
IF found_count = 1 THEN
SELECT 1 INTO dummy FROM information_schema.tables;
END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_geofence`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Tablo döküm verisi `traceper_geofence`
--

INSERT INTO `traceper_geofence` (`Id`, `userId`, `name`, `description`, `point1Latitude`, `point1Longitude`, `point2Latitude`, `point2Longitude`, `point3Latitude`, `point3Longitude`) VALUES
(18, 1, 'DenemeGeo', 'geoooo', '40.044438', '28.740234', '34.741613', '33.486328', '39.707187', '37.001953'),
(19, 1, 'Deneme2', '', '43.004647', '31.508789', '48.545706', '39.550781', '49.239121', '25.312500');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_geofence_user_relation`
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
-- Tablo için tablo yapısı `traceper_invitedusers`
--

CREATE TABLE IF NOT EXISTS `traceper_invitedusers` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `dt` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=71 ;

--
-- Tablo döküm verisi `traceper_invitedusers`
--

INSERT INTO `traceper_invitedusers` (`Id`, `email`, `dt`) VALUES
(1, 'qwerrq@werq.com', '2011-10-04 22:10:35'),
(6, 'edwardkalay@hotmail.com', '2013-02-07 21:02:03'),
(14, 'aliveli@yahoo.com', '2013-02-12 21:02:02'),
(43, 'jason@hotmail.com', '2013-02-12 22:02:51'),
(44, 'tuba@yahoo.com', '2013-02-12 22:02:51'),
(45, 'josh@mynet.com', '2013-02-12 22:02:51'),
(70, 'adnankalay@yahoo.com', '2013-02-13 21:02:09');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_privacy_groups`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113 ;

--
-- Tablo döküm verisi `traceper_privacy_groups`
--

INSERT INTO `traceper_privacy_groups` (`id`, `name`, `type`, `owner`, `description`, `allowedToSeeOwnersPosition`) VALUES
(9, 'Deneme', 0, 1, 'Bu bir deneme grubudur.', 0),
(11, 'dolap', 0, 30, 'Dolap grubu', 0),
(31, 'Televizyon', 0, 1, '', 1),
(46, 'Mouse2', 0, 1, '', 1),
(47, 'Kingdom', 1, 1, '', 1),
(54, 'Door', 1, 1, '', 1),
(58, 'Olay', 0, 1, '', 0),
(59, 'Okul', 0, 1, '', 0),
(63, 'Table', 1, 1, '', 1),
(64, 'Table', 0, 1, '', 1),
(89, 'Yakın', 0, 1, '', 1),
(90, 'Uzak', 1, 1, '', 1),
(92, 'Deneme', 1, 1, '', 1),
(93, 'Class', 1, 1, '', 1),
(100, 'Grup 1', 0, 1, '', 0),
(102, 'Grup 2', 0, 1, '', 1),
(103, 'Grup 3', 0, 1, '', 1),
(104, 'Grup 4', 0, 1, '', 1),
(105, 'Grup 5', 0, 1, '', 1),
(106, 'Grup 6', 0, 1, '', 1),
(112, 'Grup 7', 0, 1, '777777777', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_reset_password`
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

INSERT INTO `traceper_reset_password` (`email`, `token`, `requestTime`) VALUES
('test@traceper.com', 'aac394883337cbcc9161f4b196b539278f9ddae4', '2013-07-22 19:48:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_status_messages`
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
-- Tablo için tablo yapısı `traceper_upload`
--

CREATE TABLE IF NOT EXISTS `traceper_upload` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueId` int(11) NOT NULL DEFAULT '0',
  `fileType` tinyint(4) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(11,6) NOT NULL,
  `altitude` decimal(15,6) NOT NULL,
  `uploadTime` datetime NOT NULL,
  `publicData` tinyint(4) NOT NULL DEFAULT '0',
  `live` tinyint(4) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE,
  KEY `index2` (`uploadTime`),
  KEY `publicData` (`publicData`),
  KEY `userId` (`userId`),
  KEY `descriptioon` (`description`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Tablo döküm verisi `traceper_upload`
--

INSERT INTO `traceper_upload` (`Id`, `uniqueId`, `fileType`, `userId`, `latitude`, `longitude`, `altitude`, `uploadTime`, `publicData`, `live`, `description`) VALUES
(7, 0, 0, 1, '0.000000', '0.000000', '0.000000', '2011-12-03 18:35:01', 1, 0, 'Kriko'),
(10, 0, 0, 1, '0.000000', '0.000000', '0.000000', '2011-12-04 00:20:31', 1, 0, 'Tekerlek'),
(14, 0, 1, 1, '0.000000', '0.000000', '0.000000', '2012-01-01 07:34:35', 0, 0, 'ghj'),
(15, 0, 0, 3, '10.000000', '23.000000', '2222.000000', '0000-00-00 00:00:00', 0, 0, 'photo1'),
(16, 0, 0, 17, '25.000000', '53.000000', '333.000000', '0000-00-00 00:00:00', 0, 0, 'photo_17'),
(17, 0, 0, 30, '45.000000', '32.000000', '444.000000', '2012-01-11 10:37:38', 1, 0, 'res'),
(18, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'manzara'),
(19, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'çanta'),
(20, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'tren'),
(21, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'araba'),
(22, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'ev'),
(24, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'dağ'),
(25, 0, 0, 1, '0.000000', '0.000000', '0.000000', '0000-00-00 00:00:00', 0, 0, 'mera'),
(27, 0, 0, 8, '12.000000', '45.000000', '-34.000000', '2013-11-01 00:00:00', 1, 0, 'Deneme'),
(28, 0, 0, 4, '23.000000', '52.000000', '71.000000', '2013-02-05 00:00:00', 1, 0, 'Masa'),
(29, 0, 0, 12, '-24.000000', '-10.000000', '58.000000', '2013-03-05 00:00:00', 1, 0, 'Bulut'),
(30, 0, 0, 15, '67.000000', '34.000000', '1000.000000', '2013-06-12 00:00:00', 1, 0, 'Göl'),
(32, 0, 0, 5, '37.000000', '27.000000', '100.000000', '2013-08-14 00:00:00', 1, 0, 'Dağ'),
(33, 0, 0, 16, '45.000000', '-18.000000', '1000.000000', '2013-07-10 00:00:00', 1, 0, 'Gökyüzü'),
(34, 0, 0, 18, '39.000000', '58.000000', '1000.000000', '2013-11-03 00:00:00', 1, 0, 'Deniz'),
(35, 0, 0, 70, '27.000000', '-12.000000', '1000.000000', '2013-04-16 00:00:00', 0, 0, 'Orman'),
(36, 0, 0, 2, '-5.000000', '15.000000', '1000.000000', '2013-03-05 00:00:00', 0, 0, 'Resim'),
(44, 0, 0, 1, '10.000000', '10.000000', '10.000000', '2013-11-22 00:00:00', 1, 0, 'Çarşaf'),
(45, 0, 0, 1, '3.000000', '3.000000', '3.000000', '2013-11-13 00:00:00', 0, 0, 'Boncuk'),
(46, 0, 0, 1, '5.000000', '5.000000', '5.000000', '2013-11-12 00:00:00', 0, 0, 'Mavi Boncuk'),
(47, 0, 0, 2, '12.000000', '12.000000', '12.000000', '2013-11-13 00:00:00', 0, 0, 'Defter'),
(48, 0, 0, 2, '21.000000', '21.000000', '21.000000', '2013-11-19 00:00:00', 0, 0, 'Kitaplık'),
(49, 0, 0, 11, '67.000000', '67.000000', '67.000000', '2013-11-12 00:00:00', 1, 0, 'Zırva'),
(50, 0, 0, 14, '-12.000000', '-12.000000', '-12.000000', '2013-11-04 00:00:00', 1, 0, 'Matbaa');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_upload_comment`
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
-- Tablo için tablo yapısı `traceper_upload_rating`
--

CREATE TABLE IF NOT EXISTS `traceper_upload_rating` (
  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `voting_count` int(10) unsigned NOT NULL DEFAULT '0',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`upload_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Tablo döküm verisi `traceper_upload_rating`
--

INSERT INTO `traceper_upload_rating` (`upload_id`, `voting_count`, `points`) VALUES
(1, 17, 51);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_upload_user_relation`
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
-- Tablo için tablo yapısı `traceper_users`
--

CREATE TABLE IF NOT EXISTS `traceper_users` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `password` char(32) NOT NULL,
  `group` int(10) unsigned NOT NULL DEFAULT '0',
  `latitude` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(11,6) NOT NULL DEFAULT '0.000000',
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
  `lastLocationAddress` text,
  `minDataSentInterval` int(11) NOT NULL DEFAULT '300000',
  `minDistanceInterval` int(11) NOT NULL DEFAULT '500',
  `autoSend` tinyint(1) NOT NULL DEFAULT '0',
  `androidVer` varchar(20) DEFAULT NULL,
  `appVer` varchar(10) DEFAULT NULL,
  `registrationMedium` varchar(10) DEFAULT NULL,
  `preferredLanguage` varchar(20) DEFAULT NULL,
  `termsAccepted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `email` (`email`),
  KEY `dataArrivedTime` (`dataArrivedTime`),
  KEY `realname` (`realname`) USING BTREE,
  KEY `password` (`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='This is for mobile app users' AUTO_INCREMENT=73 ;

--
-- Tablo döküm verisi `traceper_users`
--

INSERT INTO `traceper_users` (`Id`, `password`, `group`, `latitude`, `longitude`, `altitude`, `publicPosition`, `authorityLevel`, `realname`, `email`, `dataArrivedTime`, `deviceId`, `status_message`, `status_source`, `status_message_time`, `dataCalculatedTime`, `fb_id`, `g_id`, `gender`, `userType`, `account_type`, `gp_image`, `lastLocationAddress`, `minDataSentInterval`, `minDistanceInterval`, `autoSend`, `androidVer`, `appVer`, `registrationMedium`, `preferredLanguage`, `termsAccepted`) VALUES
(1, '827ccb0eea8a706c4c34a16891f84e7b', 0, '39.300000', '32.300000', '1000.000000', 0, 1, 'Test', 'test@traceper.com', '2013-10-18 16:08:46', '1234', '', 1, '2010-10-31 20:10:09', '1970-01-01 00:16:40', '0', '0', 0, 0, 0, NULL, 'Haymana, Ankara Province, Turkey', 200000, 300, 0, 'Ice Cream', '1.0.9', NULL, 'tr', 1),
(2, '827ccb0eea8a706c4c34a16891f84e7b', 0, '40.000000', '35.000000', '115.000000', 0, 1, 'test2', 'test2@traceper.com', '2013-02-28 07:37:28', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, 'tr', 1),
(3, '', 0, '1.000000', '0.000000', '0.000000', 0, 1, '1', '2', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(4, '', 0, '2.000000', '0.000000', '0.000000', 0, 1, '2', '3', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(5, '', 0, '3.000000', '0.000000', '0.000000', 0, 1, '4', '4', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(6, '', 0, '4.000000', '0.000000', '0.000000', 0, 1, '5', '5', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(7, '', 0, '5.000000', '0.000000', '0.000000', 0, 1, '6', '6', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(8, '', 0, '6.000000', '0.000000', '0.000000', 0, 1, '7', '7', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(9, '', 0, '7.000000', '0.000000', '0.000000', 0, 1, '8', '8', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(10, '', 0, '8.000000', '0.000000', '0.000000', 0, 1, '9', '9', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(11, '', 0, '0.000000', '0.000000', '0.000000', 0, 1, '10', '10', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(12, '', 0, '88.000000', '0.000000', '0.000000', 0, 1, '11', '11', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(13, '', 0, '10.000000', '0.000000', '0.000000', 0, 1, '12', '12', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(14, '', 0, '11.000000', '0.000000', '0.000000', 0, 1, '13', '13', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(15, '', 0, '13.000000', '0.000000', '0.000000', 0, 1, '14', '14', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(16, '', 0, '14.000000', '0.000000', '0.000000', 0, 1, '15', '15', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(17, '', 0, '16.000000', '0.000000', '0.000000', 0, 1, '16', '16', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(18, '', 0, '17.000000', '0.000000', '0.000000', 0, 1, '17', '17', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(19, '', 0, '18.000000', '0.000000', '0.000000', 0, 1, '18', '18', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(20, '', 0, '19.000000', '0.000000', '0.000000', 0, 1, '19', '19', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(21, '', 0, '20.000000', '0.000000', '0.000000', 0, 1, '20', '20', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(22, '', 0, '21.000000', '0.000000', '0.000000', 0, 1, '21', '21', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(23, '', 0, '22.000000', '0.000000', '0.000000', 0, 1, '22', '22', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(24, '', 0, '23.000000', '0.000000', '0.000000', 0, 1, '23', '23', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(30, 'e0a6c20903266d8457eb62504efc889c', 0, '10.000000', '20.000000', '1000.000000', 0, 1, 'Adnan', 'adnan3@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(31, '395063ccee7ebd4022bf1f5f3ca8c2ce', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Adnan', 'adnan@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(32, '06b9281e396db002010bde1de57262eb', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Auto', '12345', '0000-00-00 00:00:00', '12345', NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(34, '0ec3aaa8e5284ed11959e6bab6a2831e', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Cihazzzz', '1256', '0000-00-00 00:00:00', '1256', NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(35, '9355ea41e08806bbb6bb9862a4e7a197', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Cihaz2', '222', '0000-00-00 00:00:00', '222', NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(43, '934b535800b1cba8f96a5d72f72f1611', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Dylan', 'dylan@yahoo.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(52, '202cb962ac59075b964b07152d234b70', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'kevin', 'kevin@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(56, '83f97f4825290be4cb794ec6a234595f', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'dave', 'dave@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(57, 'ea789a573d35f290c51d2b29eb94532a', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Michael', 'michael@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(59, 'b33538179f5661a86cbe327a1793e199', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Motor', '11223344', '0000-00-00 00:00:00', '11223344', NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 1, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(60, 'e68f503bbc4439a84411feba5935eacc', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Mark', 'mark@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(62, 'c4ba944ae255386e8323a6b0a2a30dc5', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Danny', 'danny@yahoo.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(63, 'b59c67bf196a4758191e42f76670ceba', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Mary', 'mary@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(64, 'b59c67bf196a4758191e42f76670ceba', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Lily', 'lily@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(65, 'b59c67bf196a4758191e42f76670ceba', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Amanda', 'amanda@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(66, 'b59c67bf196a4758191e42f76670ceba', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Lisa', 'lisa@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(67, 'b59c67bf196a4758191e42f76670ceba', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Kelly', 'kelly@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(68, 'b59c67bf196a4758191e42f76670ceba', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Emma', 'emma@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 2, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(69, '', 0, '0.000000', '0.000000', '0.000000', 0, 1, '33', '', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, NULL, 0),
(70, '827ccb0eea8a706c4c34a16891f84e7b', 0, '34.000000', '45.000000', '1000.000000', 0, 1, 'Test 3', 'test3@traceper.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, NULL, 'tr', 0),
(72, '827ccb0eea8a706c4c34a16891f84e7b', 0, '0.000000', '0.000000', '0.000000', 0, 1, 'Adnan Kalay', 'adnankalay@yahoo.com', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0', '0', 0, 0, 0, NULL, NULL, 300000, 500, 0, NULL, NULL, 'Web', 'tr', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_user_candidates`
--

CREATE TABLE IF NOT EXISTS `traceper_user_candidates` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `realname` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  `time` datetime NOT NULL,
  `registrationMedium` varchar(10) DEFAULT NULL,
  `preferredLanguage` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `index_name` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=25 ;

--
-- Tablo döküm verisi `traceper_user_candidates`
--

INSERT INTO `traceper_user_candidates` (`Id`, `email`, `realname`, `password`, `time`, `registrationMedium`, `preferredLanguage`) VALUES
(2, 'qwe@qwe.com', 'kdfkjdlf', '827ccb0eea8a706c4c34a16891f84e7b', '2011-11-26 02:36:46', NULL, NULL),
(3, 'qwer@qer.com', 'dlfkdf', '827ccb0eea8a706c4c34a16891f84e7b', '2011-11-26 02:38:56', NULL, NULL),
(4, 'deneme@deneme.com', 'deneme', '827ccb0eea8a706c4c34a16891f84e7b', '2011-11-26 03:02:02', NULL, NULL),
(5, 'contact@mekya.com', 'mekya', 'e10adc3949ba59abbe56e057f20f883e', '2011-12-03 06:06:58', NULL, NULL),
(6, 'ahmetmermerkaya@hotmail.com', 'Ouz', 'e10adc3949ba59abbe56e057f20f883e', '2011-12-12 05:06:51', NULL, NULL),
(9, 'dene@deneme.com', 'Deneme', '202cb962ac59075b964b07152d234b70', '2012-01-14 09:33:06', NULL, NULL),
(10, 'pelin@mynet.com', 'Deneme', '202cb962ac59075b964b07152d234b70', '2012-01-14 09:33:33', NULL, NULL),
(11, 'pelin456789@mynet.com', 'Deneme', '202cb962ac59075b964b07152d234b70', '2012-01-14 09:33:43', NULL, NULL),
(12, 'pelin245@mynet.com', 'Deneme', '202cb962ac59075b964b07152d234b70', '2012-01-14 09:34:44', NULL, NULL),
(13, 'adnan@traceper.com', 'Adnan', '395063ccee7ebd4022bf1f5f3ca8c2ce', '2012-01-15 07:17:46', NULL, NULL),
(14, 'edi@traceper.com', 'Edi', '202cb962ac59075b964b07152d234b70', '2012-01-17 10:50:45', NULL, NULL),
(15, 'bud@traceper.com', 'Buddy', '202cb962ac59075b964b07152d234b70', '2012-01-17 10:51:25', NULL, NULL),
(20, 'test4@traceper.com', 'Test 4', '827ccb0eea8a706c4c34a16891f84e7b', '2013-02-24 09:45:04', NULL, NULL),
(21, 'test5@traceper.com', 'Test 5', '827ccb0eea8a706c4c34a16891f84e7b', '2013-02-24 09:45:58', NULL, NULL),
(22, 'test6@traceper.com', 'Test 6', '202cb962ac59075b964b07152d234b70', '2013-02-24 09:47:02', NULL, NULL),
(24, 'deneme@yahoo.com', 'Deneme', '827ccb0eea8a706c4c34a16891f84e7b', '2013-06-06 07:26:15', NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_user_privacy_group_relation`
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
  KEY `groupId` (`groupId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

--
-- Tablo döküm verisi `traceper_user_privacy_group_relation`
--

INSERT INTO `traceper_user_privacy_group_relation` (`Id`, `groupOwner`, `userId`, `groupId`) VALUES
(4, 30, 2, 11),
(67, 1, 14, 31),
(71, 1, 11, 31),
(72, 1, 9, 9),
(73, 1, 10, 9),
(74, 1, 19, 46),
(75, 1, 20, 46),
(76, 1, 2, 100);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `traceper_user_was_here`
--

CREATE TABLE IF NOT EXISTS `traceper_user_was_here` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `dataArrivedTime` datetime NOT NULL,
  `latitude` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `altitude` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(11,6) NOT NULL DEFAULT '0.000000',
  `deviceId` varchar(64) NOT NULL DEFAULT '0',
  `dataCalculatedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  KEY `time` (`dataArrivedTime`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=142 ;

--
-- Tablo döküm verisi `traceper_user_was_here`
--

INSERT INTO `traceper_user_was_here` (`Id`, `userId`, `dataArrivedTime`, `latitude`, `altitude`, `longitude`, `deviceId`, `dataCalculatedTime`) VALUES
(1, 21, '2011-10-22 23:18:44', '37.422005', '-122.084095', '-122.084095', '000000000000000', '2011-10-21 21:00:00'),
(2, 21, '0000-00-00 00:00:00', '30.000000', '2.000000', '0.000000', '0', '0000-00-00 00:00:00'),
(3, 1, '2011-11-24 21:37:18', '37.422005', '22.084095', '22.084095', '000000000000000', '2011-11-23 22:00:00'),
(4, 1, '2011-11-24 21:43:00', '7.422005', '2.084095', '2.084095', '000000000000000', '2011-11-23 22:00:02'),
(5, 1, '2011-11-24 21:44:11', '7.422005', '2.084095', '2.084095', '000000000000000', '2011-11-23 22:00:02'),
(9, 1, '2011-11-25 02:56:39', '78.422005', '2.084095', '2.084095', '000000000000000', '2011-11-23 22:00:04'),
(10, 1, '2011-11-25 02:56:53', '78.422005', '23.084095', '23.084095', '000000000000000', '2011-11-23 22:00:05'),
(11, 1, '2011-11-25 02:57:03', '78.422005', '3.084095', '3.084095', '000000000000000', '2011-11-23 22:00:06'),
(12, 1, '2011-11-25 02:57:24', '8.422005', '3.084095', '3.084095', '000000000000000', '2011-11-23 22:00:07'),
(13, 1, '2011-11-25 03:32:21', '8.422005', '38.084093', '38.084093', '000000000000000', '2011-11-23 22:00:08'),
(14, 1, '2011-12-03 18:25:47', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:25:46'),
(15, 1, '2011-12-03 18:26:02', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:25:46'),
(16, 1, '2011-12-03 18:26:02', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:01'),
(17, 1, '2011-12-03 18:26:12', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:25:46'),
(18, 1, '2011-12-03 18:26:13', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:01'),
(19, 1, '2011-12-03 18:26:13', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:11'),
(20, 1, '2011-12-03 18:30:09', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:25:46'),
(21, 1, '2011-12-03 18:30:09', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:01'),
(22, 1, '2011-12-03 18:30:10', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:11'),
(23, 1, '2011-12-03 18:30:10', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:30:08'),
(24, 1, '2011-12-03 18:32:07', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:25:46'),
(25, 1, '2011-12-03 18:32:07', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:01'),
(26, 1, '2011-12-03 18:32:07', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:26:11'),
(27, 1, '2011-12-03 18:32:07', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:30:08'),
(28, 1, '2011-12-03 18:32:07', '39.921466', '32.858031', '32.858031', '351751049911319', '2011-12-03 18:32:06'),
(29, 1, '2011-12-03 18:35:41', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(30, 1, '2011-12-03 18:36:31', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(31, 1, '2011-12-03 18:36:31', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(32, 1, '2011-12-03 18:38:40', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(33, 1, '2011-12-03 18:38:40', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(34, 1, '2011-12-03 18:38:43', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(35, 1, '2011-12-03 18:38:43', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(36, 1, '2011-12-03 18:38:43', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:38:42'),
(37, 1, '2011-12-03 18:39:37', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(38, 1, '2011-12-03 18:39:37', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(39, 1, '2011-12-03 18:39:37', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:38:42'),
(40, 1, '2011-12-03 18:39:37', '39.920730', '32.869167', '32.869167', '351751049911319', '2011-12-03 18:39:37'),
(41, 1, '2011-12-03 19:03:53', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(42, 1, '2011-12-03 19:03:54', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(43, 1, '2011-12-03 19:03:54', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:38:42'),
(44, 1, '2011-12-03 19:03:54', '39.920730', '32.869167', '32.869167', '351751049911319', '2011-12-03 18:39:37'),
(45, 1, '2011-12-03 19:04:10', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(46, 1, '2011-12-03 19:04:11', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(47, 1, '2011-12-03 19:04:11', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:38:42'),
(48, 1, '2011-12-03 19:04:11', '39.920730', '32.869167', '32.869167', '351751049911319', '2011-12-03 18:39:37'),
(49, 1, '2011-12-03 19:04:11', '39.917822', '32.859920', '32.859920', '351751049911319', '2011-12-03 19:04:09'),
(50, 1, '2011-12-03 19:04:12', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:35:39'),
(51, 1, '2011-12-03 19:04:12', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:36:30'),
(52, 1, '2011-12-03 19:04:12', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 18:38:42'),
(53, 1, '2011-12-03 19:04:12', '39.920730', '32.869167', '32.869167', '351751049911319', '2011-12-03 18:39:37'),
(54, 1, '2011-12-03 19:04:12', '39.917822', '32.859920', '32.859920', '351751049911319', '2011-12-03 19:04:09'),
(55, 1, '2011-12-03 19:04:13', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-03 19:04:10'),
(56, 1, '2011-12-04 14:59:33', '37.422005', '22.084095', '22.084095', '000000000000000', '2011-12-03 22:00:00'),
(57, 1, '2011-12-04 15:07:10', '3.422005', '22.084095', '22.084095', '000000000000000', '2011-12-03 22:00:01'),
(58, 1, '2011-12-04 15:09:40', '3.422005', '22.084095', '22.084095', '000000000000000', '2011-12-03 22:00:01'),
(59, 1, '2011-12-04 15:10:00', '3.422005', '2.084095', '2.084095', '000000000000000', '2011-12-03 22:00:02'),
(60, 1, '2011-12-04 15:13:47', '3.422005', '28.084095', '28.084095', '000000000000000', '2011-12-03 22:00:03'),
(61, 1, '2011-12-04 15:33:59', '73.422005', '28.084095', '28.084095', '000000000000000', '2011-12-03 22:00:04'),
(62, 1, '2011-12-04 16:16:23', '73.422005', '28.084095', '28.084095', '000000000000000', '2011-12-03 22:00:00'),
(63, 1, '2011-12-04 17:21:12', '73.422005', '84.084095', '84.084095', '000000000000000', '2011-12-03 22:00:04'),
(64, 1, '2011-12-04 17:21:59', '73.422005', '4.084093', '4.084093', '000000000000000', '2011-12-03 22:00:05'),
(65, 1, '2011-12-04 17:22:50', '73.422005', '47.084093', '47.084093', '000000000000000', '2011-12-03 22:00:06'),
(66, 1, '2011-12-12 04:22:33', '37.422005', '-122.084095', '-122.084095', '000000000000000', '2011-12-11 22:00:00'),
(67, 1, '2011-12-12 05:08:21', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-12 05:08:13'),
(68, 1, '2011-12-21 21:34:49', '37.422005', '-122.084095', '-122.084095', '000000000000000', '2011-12-20 22:00:00'),
(69, 1, '2011-12-22 00:05:14', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 00:04:56'),
(70, 1, '2011-12-22 00:35:40', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 00:35:34'),
(71, 1, '2011-12-22 00:39:16', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 00:39:06'),
(72, 1, '2011-12-22 00:43:02', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 00:42:47'),
(73, 1, '2011-12-22 01:16:04', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:16:01'),
(74, 1, '2011-12-22 01:16:30', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:16:17'),
(75, 1, '2011-12-22 01:17:00', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:16:47'),
(76, 1, '2011-12-22 01:17:30', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:17:17'),
(77, 1, '2011-12-22 01:19:25', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:19:22'),
(78, 1, '2011-12-22 01:19:50', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:19:47'),
(79, 1, '2011-12-22 01:20:20', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:20:17'),
(80, 1, '2011-12-22 01:20:36', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:20:33'),
(81, 1, '2011-12-22 01:20:50', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:20:47'),
(82, 1, '2011-12-22 01:21:02', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:21:00'),
(83, 1, '2011-12-22 01:21:21', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:21:17'),
(84, 1, '2011-12-22 01:23:25', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:23:22'),
(85, 1, '2011-12-22 01:23:50', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:23:47'),
(86, 1, '2011-12-22 01:24:23', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:24:17'),
(87, 1, '2011-12-22 01:24:50', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:24:47'),
(88, 1, '2011-12-22 01:25:20', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:25:17'),
(89, 1, '2011-12-22 01:25:50', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:25:47'),
(90, 1, '2011-12-22 01:26:20', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:26:17'),
(91, 1, '2011-12-22 01:26:53', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:26:47'),
(92, 1, '2011-12-22 01:27:15', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:27:12'),
(93, 1, '2011-12-22 01:27:26', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:27:23'),
(94, 1, '2011-12-22 01:28:20', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:27:47'),
(95, 1, '2011-12-22 01:28:50', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:28:25'),
(96, 1, '2011-12-22 01:36:20', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:35:37'),
(97, 1, '2011-12-22 01:36:27', '39.920755', '32.868721', '32.868721', '351751049911319', '2011-12-22 01:36:20'),
(98, 1, '2011-12-22 01:56:16', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:55:43'),
(99, 1, '2011-12-22 01:56:59', '39.920865', '32.868708', '32.868708', '351751049911319', '2011-12-22 01:56:35'),
(100, 1, '2011-12-22 02:06:23', '39.920526', '32.868991', '32.868991', '351751049911319', '2011-12-22 02:05:56'),
(101, 1, '2011-12-22 02:10:00', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 02:09:58'),
(102, 1, '2011-12-22 02:14:35', '39.920703', '32.868969', '32.868969', '351751049911319', '2011-12-22 02:14:36'),
(103, 1, '2011-12-22 02:20:13', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 02:20:03'),
(104, 1, '2011-12-22 02:24:49', '39.920528', '32.868930', '32.868930', '351751049911319', '2011-12-22 02:24:18'),
(105, 1, '2011-12-22 02:25:41', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 02:25:10'),
(106, 1, '2011-12-22 02:29:41', '39.920802', '32.868919', '32.868919', '351751049911319', '2011-12-22 02:29:42'),
(107, 1, '2011-12-22 02:34:47', '39.921189', '32.869280', '32.869280', '351751049911319', '2011-12-22 02:34:47'),
(108, 1, '2011-12-22 02:39:44', '39.921332', '32.868721', '32.868721', '351751049911319', '2011-12-22 02:39:45'),
(109, 1, '2011-12-22 02:46:07', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 02:45:48'),
(110, 1, '2011-12-22 02:49:49', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 02:49:18'),
(111, 1, '2011-12-22 02:54:35', '39.920659', '32.869207', '32.869207', '351751049911319', '2011-12-22 02:54:36'),
(112, 1, '2011-12-22 02:59:49', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 02:59:18'),
(113, 1, '2011-12-22 03:01:41', '39.920761', '32.868674', '32.868674', '351751049911319', '2011-12-22 03:00:32'),
(114, 1, '2011-12-22 03:05:19', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:04:54'),
(115, 1, '2011-12-22 03:10:19', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:10:03'),
(116, 1, '2011-12-22 03:14:14', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:14:11'),
(117, 1, '2011-12-22 03:14:20', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:14:17'),
(118, 1, '2011-12-22 03:15:29', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:14:17'),
(119, 1, '2011-12-22 03:17:34', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:17:21'),
(120, 1, '2011-12-22 03:18:10', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-22 03:18:07'),
(121, 1, '2011-12-22 03:19:09', '39.920905', '32.868862', '32.868862', '351751049911319', '2011-12-22 03:18:07'),
(122, 1, '2011-12-22 03:19:20', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:19:17'),
(123, 1, '2011-12-22 03:20:19', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:19:17'),
(124, 1, '2011-12-22 03:21:15', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:21:12'),
(125, 1, '2011-12-22 03:22:24', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:21:12'),
(126, 1, '2011-12-22 03:24:25', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:24:17'),
(127, 1, '2011-12-22 03:25:19', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:24:17'),
(128, 1, '2011-12-22 03:29:20', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:29:17'),
(129, 1, '2011-12-22 03:30:19', '39.920875', '32.868639', '32.868639', '351751049911319', '2011-12-22 03:29:17'),
(130, 1, '2013-07-14 00:03:57', '39.800000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(131, 1, '2013-07-14 00:05:25', '39.700000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(132, 1, '2013-07-14 00:06:10', '39.800000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(133, 1, '2013-07-14 00:08:05', '39.900000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(134, 1, '2013-07-14 00:09:43', '39.800000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(135, 1, '2013-07-14 00:11:14', '39.700000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(136, 1, '2013-07-14 00:12:29', '39.800000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(137, 1, '2013-07-14 00:20:07', '39.700000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(138, 1, '2013-07-14 00:21:10', '39.800000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(139, 1, '2013-07-14 00:39:17', '39.700000', '1000.000000', '32.900000', '1', '1970-01-01 00:16:40'),
(140, 1, '2013-10-18 16:05:33', '39.300000', '2000.000000', '32.200000', '1', '1970-01-01 00:16:40'),
(141, 1, '2013-10-18 16:08:46', '39.300000', '1000.000000', '32.300000', '1', '1970-01-01 00:16:40');

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `traceper_friends`
--
ALTER TABLE `traceper_friends`
  ADD CONSTRAINT `traceper_friends_ibfk_1` FOREIGN KEY (`friend1`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traceper_friends_ibfk_2` FOREIGN KEY (`friend2`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `traceper_upload`
--
ALTER TABLE `traceper_upload`
  ADD CONSTRAINT `traceper_upload_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `traceper_user_privacy_group_relation`
--
ALTER TABLE `traceper_user_privacy_group_relation`
  ADD CONSTRAINT `traceper_user_privacy_group_relation_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traceper_user_privacy_group_relation_ibfk_2` FOREIGN KEY (`groupId`) REFERENCES `traceper_privacy_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `traceper_user_was_here`
--
ALTER TABLE `traceper_user_was_here`
  ADD CONSTRAINT `traceper_user_was_here_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `traceper_users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
