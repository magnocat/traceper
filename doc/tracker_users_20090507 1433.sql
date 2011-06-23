-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.22-community-nt


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema test
--

CREATE DATABASE IF NOT EXISTS test;
USE test;

--
-- Definition of table `tracker_users`
--

DROP TABLE IF EXISTS `tracker_users`;
CREATE TABLE `tracker_users` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(12) default NULL,
  `password` char(32) NOT NULL,
  `group` int(10) unsigned NOT NULL default '0',
  `latitude` decimal(8,6) NOT NULL default '0.000000',
  `longitude` decimal(9,6) NOT NULL default '0.000000',
  `altitude` decimal(15,6) NOT NULL default '0.000000',
  `realname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `im` varchar(100) default NULL,
  `dataArrivedTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `deviceId` varchar(64) default NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `username` USING BTREE (`username`),
  KEY `realname_email` USING BTREE (`realname`,`email`),
  KEY `dataArrivedTime` (`dataArrivedTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tracker_users`
--

/*!40000 ALTER TABLE `tracker_users` DISABLE KEYS */;
INSERT INTO `tracker_users` (`Id`,`username`,`password`,`group`,`latitude`,`longitude`,`altitude`,`realname`,`email`,`im`,`dataArrivedTime`,`deviceId`) VALUES 
 (1,'mekya','123456',0,'5.878322','132.726547','981.808969','oğuz','test@test.com','mekyaim@mekya.com','2009-05-04 09:07:03','12.23.34.55'),
 (2,'faraklit','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'21.817200','133.083586','1939.052608','ahmet','test@test.com','???','0000-00-00 00:00:00','0'),
 (3,'jeffer','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'56.661460','43.070405','615.369663','Zafer','test@test.com','???','0000-00-00 00:00:00','0'),
 (4,'a_celebi','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'73.852569','32.375887','875.155843','Hüseyin','test@test.com','???','0000-00-00 00:00:00','0'),
 (5,'yasarcanun','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'58.346237','167.170298','1397.489709','Yaşar','test@test.com','???','0000-00-00 00:00:00','0'),
 (6,'bbalci','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'63.679740','79.475186','169.972180','Burak','test@test.com','???','0000-00-00 00:00:00','0'),
 (7,'belenlihun','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'9.030964','44.417153','1865.555417','Ali','test@test.com','???','0000-00-00 00:00:00','0'),
 (8,'tolga','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'83.124243','147.542468','655.187680','Tolga','test@test.com','???','0000-00-00 00:00:00','0'),
 (9,'fsoj','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'16.103529','164.134617','45.021351','Halil','test@test.com','???','0000-00-00 00:00:00','0'),
 (10,'satellite','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'33.927881','147.123047','1911.643952','Selim','test@test.com','???','0000-00-00 00:00:00','0'),
 (11,'fatihydz','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'29.435322','138.209357','1715.942871','fatih','test@test.com','???','0000-00-00 00:00:00','0'),
 (12,'derya','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'88.773114','64.426566','1661.046136','derya','test@test.com','???','0000-00-00 00:00:00','0'),
 (13,'Pamuk','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'7.095535','162.472899','553.236560','Gökhan','test@test.com','???','0000-00-00 00:00:00','0'),
 (14,'btcaglar','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'60.768880','98.314936','1410.682292','Bülent','test@test.com','???','0000-00-00 00:00:00','0'),
 (15,'tileyli','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'79.931114','58.426926','1917.199457','emre','test@test.com','???','0000-00-00 00:00:00','0'),
 (16,'ekilickaya','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'73.729492','39.651071','1287.537868','Emre','test@test.com','???','0000-00-00 00:00:00','0'),
 (17,'myildirim','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'50.219417','154.558951','1238.647330','meltem','test@test.com','???','0000-00-00 00:00:00','0'),
 (18,'eacelik','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'46.857225','134.137477','328.267137','EREN','test@test.com','???','0000-00-00 00:00:00','0'),
 (19,'beravci','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'52.653892','77.906802','817.896242','Bahattin','test@test.com','???','0000-00-00 00:00:00','0'),
 (20,'serdar','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'67.166454','90.832561','568.489867','Serdar ','test@test.com','???','0000-00-00 00:00:00','0'),
 (21,'Noreturn','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'81.661381','123.121548','1395.994009','Tolga ','test@test.com','???','0000-00-00 00:00:00','0'),
 (22,'skarabulut','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'39.416333','17.244954','330.297502','Serbay ','test@test.com','???','0000-00-00 00:00:00','0'),
 (23,'tuygunol','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'48.449510','35.314784','731.965122','Tolga ','test@test.com','???','0000-00-00 00:00:00','0'),
 (24,'ferbay','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'21.719980','19.569221','1639.180366','Fulya ','test@test.com','???','0000-00-00 00:00:00','0'),
 (25,'Max_Well','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'69.461753','72.038838','1371.374163','EMİN ','test@test.com','???','0000-00-00 00:00:00','0'),
 (26,'hsynpyrz','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'20.500933','14.738314','1452.066380','Hüseyin ','test@test.com','???','0000-00-00 00:00:00','0'),
 (27,'MELih','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'34.607468','134.016762','1138.211192','Mustafa ','test@test.com','???','0000-00-00 00:00:00','0'),
 (28,'tesla','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'55.072378','63.406766','1851.106277','Alper ','test@test.com','???','0000-00-00 00:00:00','0'),
 (29,'Kıvırcık','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'51.388766','14.088975','1356.803147','Bahar ','test@test.com','???','0000-00-00 00:00:00','0'),
 (30,'koroglu','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'14.147248','135.135634','564.385450','ozan ','test@test.com','???','0000-00-00 00:00:00','0'),
 (31,'cemocan','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'14.283277','170.448708','517.154243','cem ','test@test.com','???','0000-00-00 00:00:00','0'),
 (32,'nazli','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'40.686645','87.234812','133.935511','nazlı ','test@test.com','???','0000-00-00 00:00:00','0'),
 (33,'dervishan','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'79.283274','36.670159','751.679525','DERVİŞ ','test@test.com','???','0000-00-00 00:00:00','0'),
 (34,'Yunus','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'24.122657','38.273103','518.106431','Yunus','test@test.com','???','0000-00-00 00:00:00','0'),
 (35,'seaft','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'59.164295','91.754222','1153.170508','Hüseyin','test@test.com','???','0000-00-00 00:00:00','0'),
 (36,'carcun','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'31.832033','6.964303','264.770231','Serkan','test@test.com','???','0000-00-00 00:00:00','0'),
 (37,'PANC','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'49.126850','59.780541','46.017909','Alpaslan','test@test.com','???','0000-00-00 00:00:00','0'),
 (38,'erdknt','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'10.683221','94.407379','532.639729','erdem','test@test.com','???','0000-00-00 00:00:00','0'),
 (39,'arda45','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'68.232873','178.516011','1368.697989','Arda','test@test.com','???','0000-00-00 00:00:00','0'),
 (40,'murathan','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'40.183035','32.281894','1114.569635','murat','test@test.com','???','0000-00-00 00:00:00','0'),
 (41,'miray','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'22.355330','102.619505','210.728308','miray','test@test.com','???','0000-00-00 00:00:00','0'),
 (42,'cem','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'73.484615','137.949516','764.887715','cem','test@test.com','???','0000-00-00 00:00:00','0'),
 (43,'Taskin','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'55.175464','165.234966','1501.356108','Gökhan ','test@test.com','???','0000-00-00 00:00:00','0'),
 (44,'Bahadır','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'89.952678','134.160635','1456.523491','Bahadır ','test@test.com','???','0000-00-00 00:00:00','0'),
 (45,'egunaydin','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'36.476835','151.507017','1985.267535','Emre ','test@test.com','???','0000-00-00 00:00:00','0'),
 (46,'mfaktan','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'39.424673','38.224497','1495.271738','Mehmet','test@test.com','???','0000-00-00 00:00:00','0'),
 (47,'sarkozsky','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'9.099398','47.270615','19.518808','ahmet ','test@test.com','???','0000-00-00 00:00:00','0'),
 (48,'conqueror','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'23.485812','49.588049','1189.170870','mehmet','test@test.com','???','0000-00-00 00:00:00','0'),
 (49,'ecalikci','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'13.181375','170.737619','606.665006','rgn','test@test.com','???','0000-00-00 00:00:00','0'),
 (50,'karaagac','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'60.393201','80.132464','425.590219','Hakan ','test@test.com','???','0000-00-00 00:00:00','0'),
 (51,'Ecan','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'65.559106','0.681706','1667.265571','Egemen ','test@test.com','???','0000-00-00 00:00:00','0'),
 (52,'nutcracker','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'14.112197','50.960269','1890.312981','Özer ','test@test.com','???','0000-00-00 00:00:00','0'),
 (53,'onurtan','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'78.880020','98.415706','208.871231','Onur ','test@test.com','???','0000-00-00 00:00:00','0'),
 (54,'mebahmet','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'79.372471','17.329485','1671.251170','Ahmet ','test@test.com','???','0000-00-00 00:00:00','0'),
 (55,'aözdamar','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'80.037288','169.135069','60.573570','ahmet ','test@test.com','???','0000-00-00 00:00:00','0'),
 (56,'i_yildiz','5ee1fc0d6cc02a7ae3c29d89ce1dd6ab',0,'29.926453','102.909673','1722.107244','ismail','test@test.com','???','0000-00-00 00:00:00','0'),
 (57,'demo','6c5ac7b4d3bd3311f033f971196cfa75',0,'53.109623','66.127281','133.096345','demo','test@test.com','???','0000-00-00 00:00:00','0');
/*!40000 ALTER TABLE `tracker_users` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
