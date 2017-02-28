/*
SQLyog Community
MySQL - 5.6.35 : Database - webuser
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cloud_dirs` */

DROP TABLE IF EXISTS `cloud_dirs`;

CREATE TABLE `cloud_dirs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `cloud_files` */

DROP TABLE IF EXISTS `cloud_files`;

CREATE TABLE `cloud_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cloud_dirs_id` int(10) unsigned NOT NULL,
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `dt` datetime NOT NULL,
  `sz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `config` */

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ad` tinyint(1) NOT NULL,
  `domain1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `domain2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ldap` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `theme` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sitename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `emailadmin` varchar(100) CHARACTER SET utf8 NOT NULL,
  `smtphost` varchar(20) CHARACTER SET utf8 NOT NULL,
  `smtpauth` tinyint(1) NOT NULL,
  `smtpport` varchar(20) CHARACTER SET utf8 NOT NULL,
  `smtpusername` varchar(40) CHARACTER SET utf8 NOT NULL,
  `smtppass` varchar(20) CHARACTER SET utf8 NOT NULL,
  `emailreplyto` varchar(40) CHARACTER SET utf8 NOT NULL,
  `sendemail` tinyint(1) NOT NULL,
  `version` varchar(10) CHARACTER SET utf8 NOT NULL,
  `urlsite` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `config_common` */

DROP TABLE IF EXISTS `config_common`;

CREATE TABLE `config_common` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nameparam` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `valueparam` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `contract` */

DROP TABLE IF EXISTS `contract`;

CREATE TABLE `contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kntid` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datestart` date NOT NULL,
  `dateend` date NOT NULL,
  `work` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `num` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `eq_param` */

DROP TABLE IF EXISTS `eq_param`;

CREATE TABLE `eq_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grpid` int(10) unsigned NOT NULL,
  `paramid` int(10) unsigned NOT NULL,
  `eqid` int(10) unsigned NOT NULL,
  `param` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `equipment` */

DROP TABLE IF EXISTS `equipment`;

CREATE TABLE `equipment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orgid` int(10) unsigned NOT NULL,
  `placesid` int(10) unsigned NOT NULL,
  `usersid` int(10) unsigned NOT NULL,
  `nomeid` int(10) unsigned NOT NULL,
  `buhname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datepost` datetime NOT NULL,
  `cost` int(11) NOT NULL,
  `currentcost` int(11) NOT NULL,
  `sernum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `invnum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `shtrihkod` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `os` tinyint(1) NOT NULL,
  `mode` tinyint(1) NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `repair` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `mapx` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `mapy` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `mapmoved` int(2) NOT NULL,
  `mapyet` tinyint(4) NOT NULL DEFAULT '0',
  `kntid` int(10) unsigned NOT NULL,
  `dtendgar` date NOT NULL,
  `tmcgo` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `files_contract` */

DROP TABLE IF EXISTS `files_contract`;

CREATE TABLE `files_contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcontract` int(10) unsigned NOT NULL,
  `filename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `userfreandlyfilename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `group_nome` */

DROP TABLE IF EXISTS `group_nome`;

CREATE TABLE `group_nome` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `group_param` */

DROP TABLE IF EXISTS `group_param`;

CREATE TABLE `group_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(10) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `knt` */

DROP TABLE IF EXISTS `knt`;

CREATE TABLE `knt` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `fullname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `ERPCode` int(11) NOT NULL,
  `INN` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `KPP` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `bayer` int(11) NOT NULL,
  `supplier` int(11) NOT NULL,
  `dog` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `mailq` */

DROP TABLE IF EXISTS `mailq`;

CREATE TABLE `mailq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `btxt` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `move` */

DROP TABLE IF EXISTS `move`;

CREATE TABLE `move` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eqid` int(10) unsigned NOT NULL,
  `dt` datetime NOT NULL,
  `orgidfrom` int(10) unsigned NOT NULL,
  `orgidto` int(10) unsigned NOT NULL,
  `placesidfrom` int(10) unsigned NOT NULL,
  `placesidto` int(10) unsigned NOT NULL,
  `useridfrom` int(10) unsigned NOT NULL,
  `useridto` int(10) unsigned NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `news` */

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dt` datetime NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `stiker` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `nome` */

DROP TABLE IF EXISTS `nome`;

CREATE TABLE `nome` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(10) unsigned NOT NULL,
  `vendorid` int(10) unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `org` */

DROP TABLE IF EXISTS `org`;

CREATE TABLE `org` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `picmap` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `places` */

DROP TABLE IF EXISTS `places`;

CREATE TABLE `places` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orgid` int(10) unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `opgroup` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `places_users` */

DROP TABLE IF EXISTS `places_users`;

CREATE TABLE `places_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `placesid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `repair` */

DROP TABLE IF EXISTS `repair`;

CREATE TABLE `repair` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dt` date NOT NULL,
  `kntid` int(10) unsigned NOT NULL,
  `eqid` int(10) unsigned NOT NULL,
  `cost` float NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `dtend` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `userfrom` int(10) unsigned NOT NULL,
  `userto` int(10) unsigned NOT NULL,
  `doc` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `randomid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `orgid` int(10) unsigned NOT NULL,
  `login` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `salt` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `mode` int(11) NOT NULL,
  `lastdt` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users_profile` */

DROP TABLE IF EXISTS `users_profile`;

CREATE TABLE `users_profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersid` int(10) unsigned NOT NULL,
  `fio` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `post` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telephonenumber` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `homephone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `jpegphoto` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usersid` (`usersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `usersroles` */

DROP TABLE IF EXISTS `usersroles`;

CREATE TABLE `usersroles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `role` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `vendor` */

DROP TABLE IF EXISTS `vendor`;

CREATE TABLE `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
