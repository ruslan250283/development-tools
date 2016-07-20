-- MySQL dump 10.13  Distrib 5.6.26, for Win64 (x86_64)
--
-- Host: localhost    Database: fota
-- ------------------------------------------------------
-- Server version	5.6.26-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `is_authen` tinyint(1) NOT NULL DEFAULT '1',
  `encyrpt1` varchar(100) DEFAULT NULL,
  `encyrpt2` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`is_authen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='存储验证的相关信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` VALUES (0,'','');
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delta`
--

DROP TABLE IF EXISTS `delta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delta` (
  `delta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version_id` int(10) unsigned NOT NULL,
  `delta_name` varchar(200) DEFAULT NULL,
  `delta_path` longtext,
  `delta_size` mediumtext,
  `delta_compress` float DEFAULT NULL,
  `download_num` int(10) unsigned NOT NULL DEFAULT '0',
  `download_ratio` float DEFAULT NULL,
  `sucess_num` int(10) unsigned DEFAULT NULL,
  `sucess_ratio` float DEFAULT NULL,
  `old_version` varchar(200) NOT NULL,
  `delta_version` varchar(200) NOT NULL,
  `delta_notes` longtext,
  PRIMARY KEY (`delta_id`),
  UNIQUE KEY `unique` (`version_id`,`delta_id`) USING BTREE,
  CONSTRAINT `FK_delta_version` FOREIGN KEY (`version_id`) REFERENCES `version` (`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9077 DEFAULT CHARSET=latin1 COMMENT='存储差分包相关信息，便于断点序传和版本回退';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delta`
--

LOCK TABLES `delta` WRITE;
/*!40000 ALTER TABLE `delta` DISABLE KEYS */;
/*!40000 ALTER TABLE `delta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device` (
  `imei` varchar(100) NOT NULL,
  `sn` varchar(100) DEFAULT NULL,
  `oem` varchar(100) DEFAULT NULL,
  `product` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `operator` varchar(100) DEFAULT NULL,
  `sim` varchar(100) DEFAULT NULL,
  `push_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_push` tinyint(1) DEFAULT '0',
  `istest_device` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`imei`),
  UNIQUE KEY `unique` (`operator`,`region`,`product`,`oem`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='存储device的信息，用户device注册和wap push';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device`
--

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;
/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sn`
--

DROP TABLE IF EXISTS `sn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sn` (
  `sn` varchar(100) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='存储sn库信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sn`
--

LOCK TABLES `sn` WRITE;
/*!40000 ALTER TABLE `sn` DISABLE KEYS */;
INSERT INTO `sn` VALUES ('15811375356');
/*!40000 ALTER TABLE `sn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `scan` tinyint(1) NOT NULL DEFAULT '0',
  `upload` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='存储管理员基本信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('mtkadmin','12345',1,0,0,0,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `version` (
  `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oem` varchar(100) DEFAULT NULL,
  `product` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `operator` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`version_id`),
  UNIQUE KEY `unique` (`oem`,`product`,`region`,`operator`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=latin1 COMMENT='存储版本的相关信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `version`
--

LOCK TABLES `version` WRITE;
/*!40000 ALTER TABLE `version` DISABLE KEYS */;
/*!40000 ALTER TABLE `version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `version_detail`
--

DROP TABLE IF EXISTS `version_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `version_detail` (
  `version_id` int(10) unsigned NOT NULL,
  `version` varchar(200) NOT NULL,
  `version_name` varchar(200) DEFAULT NULL,
  `version_path` text,
  `version_size` mediumtext,
  `version_compress` float DEFAULT NULL,
  `release_notes` longtext,
  `version_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delta_id` int(10) unsigned DEFAULT NULL,
  `delta_notes` longtext,
  `publish_time` int(20) unsigned NOT NULL DEFAULT '0',
  `is_publish` smallint(5) unsigned NOT NULL DEFAULT '0',
  `delta_version` varchar(200) DEFAULT NULL,
  `scattermd5` varchar(100) DEFAULT NULL,
  `fingerprint` varchar(255) DEFAULT NULL,
  `android_version` varchar(100) DEFAULT NULL,
  UNIQUE KEY `version` (`version`,`version_id`),
  KEY `search` (`version_id`,`version`),
  KEY `FK_version_detail_deltaid` (`delta_id`),
  CONSTRAINT `FK_version_detail_deltaid` FOREIGN KEY (`delta_id`) REFERENCES `delta` (`delta_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_version_detail_id` FOREIGN KEY (`version_id`) REFERENCES `version` (`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='存储每个版本的详细信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `version_detail`
--

LOCK TABLES `version_detail` WRITE;
/*!40000 ALTER TABLE `version_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `version_detail` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-26 18:02:08
