# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.17)
# Database: instatracks
# Generation Time: 2017-05-16 11:28:46 +0000
# ************************************************************



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS instatracks;

USE instatracks;

# Dump of table instances
# ------------------------------------------------------------

DROP TABLE IF EXISTS `instances`;

CREATE TABLE `instances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `oauthToken` varchar(128) DEFAULT NULL,
  `sessionId` varchar(128) DEFAULT NULL,
  `s3bucket` text,
  `lang` varchar(5) DEFAULT NULL,
  `status` enum('active','pending','aborted','rejected') DEFAULT NULL,
  `sessionMode` enum('manual','random') DEFAULT NULL,
  `stampCreate` datetime DEFAULT NULL,
  `videoFile` text,
  `viewCache` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table instanceSlides
# ------------------------------------------------------------

DROP TABLE IF EXISTS `instanceSlides`;

CREATE TABLE `instanceSlides` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `instanceID` int(11) DEFAULT NULL,
  `instagramID` varchar(128) DEFAULT NULL,
  `cdnURL` text,
  `s3URL` text,
  `status` enum('accepted','rejected') DEFAULT NULL,
  `rejectionReason` enum('safe-search','logo') DEFAULT NULL,
  `metadata` text,
  `lyrics` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table instanceViews
# ------------------------------------------------------------

DROP TABLE IF EXISTS `instanceViews`;

CREATE TABLE `instanceViews` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `instanceID` int(11) DEFAULT NULL,
  `stampView` datetime DEFAULT NULL,
  `ipAddress` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
