-- MySQL dump 10.9
--
-- Host: localhost    Database: iodine
-- ------------------------------------------------------
-- Server version	4.1.15-Debian_1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calculators`
--

DROP TABLE IF EXISTS `calculators`;
CREATE TABLE `calculators` (
  `calcid` varchar(255) default NULL,
  `uid` mediumint(8) unsigned default NULL,
  `calcsn` varchar(20) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `calculators`
--


/*!40000 ALTER TABLE `calculators` DISABLE KEYS */;
LOCK TABLES `calculators` WRITE;
INSERT INTO `calculators` VALUES ('0A3BD5DE6E448A',928447,'2034050622'),('12345678912345',11,'1234567890'),('09238EAE6949DC',6,'2144025844'),('01234567890123',11,'123456789');
UNLOCK TABLES;
/*!40000 ALTER TABLE `calculators` ENABLE KEYS */;

--
-- Table structure for table `classdescriptions`
--

DROP TABLE IF EXISTS `classdescriptions`;
CREATE TABLE `classdescriptions` (
  `did` bigint(20) NOT NULL default '0',
  `name` varchar(255) default '',
  `description` text,
  PRIMARY KEY  (`did`),
  UNIQUE KEY `did` (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classdescriptions`
--


/*!40000 ALTER TABLE `classdescriptions` DISABLE KEYS */;
LOCK TABLES `classdescriptions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `classdescriptions` ENABLE KEYS */;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `cid` bigint(20) NOT NULL default '0',
  `teachers` text NOT NULL,
  `period` enum('0','1','2','3','4','5','6','7') default '0',
  `length` enum('0','1','2','4') default '0',
  `time` enum('0','1','2','3','4') default '0',
  `year` tinyint(4) default '0',
  `descriptionid` bigint(20) default '0',
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classes`
--


/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
LOCK TABLES `classes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;

--
-- Table structure for table `course_description`
--

DROP TABLE IF EXISTS `course_description`;
CREATE TABLE `course_description` (
  `courseid` mediumint(8) unsigned NOT NULL default '0',
  `classname` varchar(64) default NULL,
  `description` blob,
  PRIMARY KEY  (`courseid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course_description`
--


/*!40000 ALTER TABLE `course_description` DISABLE KEYS */;
LOCK TABLES `course_description` WRITE;
INSERT INTO `course_description` VALUES (422000,'Geosystems',NULL),(244561,'AP Government',NULL),(119662,'AP English Lan/Com',NULL),(299800,'World Hist/Geog 2',NULL),(317704,'AP Calculus BC',NULL),(319963,'Comp Systems Res',NULL),(841358,'Microproc Systems',NULL),(841258,'Audio Electronics',NULL),(11664,'Tchr Advisory 12th',NULL),(665027,'Network Admin 1',NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `course_description` ENABLE KEYS */;

--
-- Table structure for table `eighth_absentees`
--

DROP TABLE IF EXISTS `eighth_absentees`;
CREATE TABLE `eighth_absentees` (
  `bid` mediumint(8) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_absentees`
--


/*!40000 ALTER TABLE `eighth_absentees` DISABLE KEYS */;
LOCK TABLES `eighth_absentees` WRITE;
INSERT INTO `eighth_absentees` VALUES (87,12312312),(109,1234567),(109,12345678);
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_absentees` ENABLE KEYS */;

--
-- Table structure for table `eighth_activities`
--

DROP TABLE IF EXISTS `eighth_activities`;
CREATE TABLE `eighth_activities` (
  `aid` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(63) NOT NULL default '',
  `sponsors` varchar(127) NOT NULL default '',
  `rooms` varchar(127) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `restricted` tinyint(1) NOT NULL default '0',
  `presign` tinyint(1) NOT NULL default '0',
  `oneaday` tinyint(1) NOT NULL default '0',
  `bothblocks` tinyint(1) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`aid`),
  UNIQUE KEY `aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_activities`
--


/*!40000 ALTER TABLE `eighth_activities` DISABLE KEYS */;
LOCK TABLES `eighth_activities` WRITE;
INSERT INTO `eighth_activities` VALUES (2,'Intranet 2 Development','1','1','',0,0,0,0,0),(3,'HAS NOT SELECTED AN ACTIVITY','2','3','',0,1,0,0,0),(4,'Test','2','3','',0,0,0,0,0),(5,'Wheehappypants','2','2','They\'re pants, and they\'re happy!',0,0,0,0,0),(6,'Tiddlywinks club','3','','',0,0,0,0,0),(8,'1103: Previous number','5','','',0,0,0,0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_activities` ENABLE KEYS */;

--
-- Table structure for table `eighth_activity_map`
--

DROP TABLE IF EXISTS `eighth_activity_map`;
CREATE TABLE `eighth_activity_map` (
  `aid` mediumint(8) unsigned NOT NULL default '0',
  `bid` mediumint(8) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_activity_map`
--


/*!40000 ALTER TABLE `eighth_activity_map` DISABLE KEYS */;
LOCK TABLES `eighth_activity_map` WRITE;
INSERT INTO `eighth_activity_map` VALUES (2,40,9),(2,48,9),(2,53,9),(2,55,9),(2,61,9),(2,63,9),(2,103,9);
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_activity_map` ENABLE KEYS */;

--
-- Table structure for table `eighth_activity_permissions`
--

DROP TABLE IF EXISTS `eighth_activity_permissions`;
CREATE TABLE `eighth_activity_permissions` (
  `aid` mediumint(8) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`aid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_activity_permissions`
--


/*!40000 ALTER TABLE `eighth_activity_permissions` DISABLE KEYS */;
LOCK TABLES `eighth_activity_permissions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_activity_permissions` ENABLE KEYS */;

--
-- Table structure for table `eighth_block_map`
--

DROP TABLE IF EXISTS `eighth_block_map`;
CREATE TABLE `eighth_block_map` (
  `bid` mediumint(8) unsigned NOT NULL default '0',
  `activityid` mediumint(8) unsigned NOT NULL default '0',
  `sponsors` varchar(127) NOT NULL default '',
  `rooms` varchar(127) NOT NULL default '',
  `attendancetaken` tinyint(1) NOT NULL default '0',
  `cancelled` tinyint(1) NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `advertisement` text NOT NULL,
  PRIMARY KEY  (`bid`,`activityid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_block_map`
--


/*!40000 ALTER TABLE `eighth_block_map` DISABLE KEYS */;
LOCK TABLES `eighth_block_map` WRITE;
INSERT INTO `eighth_block_map` VALUES (1,1,'1,2','1,2',0,0,'',''),(2,1,'','',0,0,'',''),(3,1,'1','1',0,0,'',''),(4,1,'1','1',0,0,'',''),(6,1,'1','1',0,0,'',''),(1,3,'4','1,2',0,0,'',''),(2,3,'4','1,2',0,0,'',''),(1,7,'1','1',0,0,'',''),(7,1,'1','1',0,0,'',''),(2,7,'1','1',0,0,'',''),(3,7,'1','1',0,0,'',''),(4,7,'1','1',0,0,'',''),(6,7,'1','1',0,0,'',''),(7,7,'1','1',0,0,'',''),(14,7,'1','1',0,0,'',''),(13,7,'1','1',0,0,'',''),(13,1,'1','1',0,0,'',''),(14,1,'1','1',0,0,'',''),(2,8,'1','2',0,0,'',''),(17,7,'1','1',0,0,'',''),(15,7,'1','1',0,0,'',''),(16,7,'1','1',0,0,'',''),(19,1,'1','1',0,0,'',''),(20,1,'1','1',0,0,'',''),(19,7,'1','1',0,0,'',''),(20,7,'1','1',0,0,'',''),(15,1,'1','1',0,1,'',''),(16,1,'1','1',0,0,'',''),(15,8,'1','4',0,0,'',''),(16,8,'1','4',0,0,'',''),(19,8,'1','4',0,0,'',''),(20,8,'1','4',0,0,'',''),(15,6,'5','5',0,0,'',''),(16,6,'5','5',0,0,'',''),(19,6,'5','5',0,0,'',''),(20,6,'5','5',0,0,'',''),(15,3,'4','2',0,0,'',''),(16,3,'4','2',0,0,'',''),(19,3,'4','2',0,0,'',''),(20,3,'4','2',0,0,'',''),(15,9,'6','6',0,0,'',''),(16,9,'6','6',0,0,'',''),(19,9,'6','6',0,0,'',''),(20,9,'6','6',0,0,'',''),(15,10,'2','7',0,0,'',''),(16,10,'2','7',0,0,'',''),(19,10,'2','7',0,0,'',''),(20,10,'2','7',0,0,'',''),(19,14,'7','8',0,0,'Trip to Canada - free for all interested','There is a free hiking trip to Canada for all interested.'),(20,14,'7','8',0,0,'Trip to Canada - free for all interested','There is a free hiking trip to Canada for all interested.'),(29,1,'1','1',0,0,'',''),(31,1,'1','1',0,0,'',''),(29,9,'6','6',0,0,'',''),(31,9,'6','6',0,0,'',''),(29,6,'5','5',0,0,'',''),(31,6,'5','5',0,0,'',''),(29,3,'4','2',0,0,'',''),(31,3,'4','2',0,0,'',''),(29,14,'7','8',0,0,'',''),(31,14,'7','8',0,0,'',''),(40,2,'1','1',0,0,'',''),(41,2,'1','1',0,0,'',''),(42,2,'1','1',0,0,'',''),(43,2,'1','1',0,0,'',''),(44,2,'1','1',0,0,'',''),(45,2,'1','1',0,0,'',''),(46,2,'1','1',0,0,'',''),(47,2,'1','1',0,0,'',''),(48,2,'1','1',0,0,'',''),(49,2,'1','1',0,0,'',''),(50,2,'1','1',0,0,'',''),(53,2,'1','1',0,0,'',''),(54,2,'1','1',0,0,'',''),(40,3,'2','3',0,0,'',''),(41,3,'2','3',0,0,'',''),(42,3,'2','3',0,0,'',''),(43,3,'2','3',0,0,'',''),(44,3,'2','3',0,0,'',''),(45,3,'2','3',0,0,'',''),(46,3,'2','3',0,0,'',''),(47,3,'2','3',0,0,'',''),(48,3,'2','3',0,0,'',''),(49,3,'2','3',0,0,'',''),(50,3,'2','3',0,0,'',''),(53,3,'2','3',0,0,'',''),(54,3,'2','3',0,0,'',''),(49,5,'1','1',0,0,'',''),(51,2,'1','1',0,0,'',''),(52,2,'1','1',0,0,'',''),(55,2,'1','1',0,0,'',''),(56,2,'1','1',0,0,'',''),(61,2,'1','1',0,0,'',''),(62,2,'1','1',0,0,'',''),(63,2,'1','1',0,0,'',''),(64,2,'1','1',0,0,'',''),(65,2,'1','1',0,0,'',''),(66,2,'1','1',0,0,'',''),(71,4,'2','3',0,0,'',''),(72,4,'2','2',0,0,'I can\'t see what activity I am am making the comment for','I need to see the whole list of offerings for that day displayed'),(75,5,'2','2',0,0,'',''),(76,4,'1','3',0,0,'',''),(71,6,'3','6',0,0,'',''),(77,6,'3','6',0,0,'',''),(75,6,'3','6',0,0,'bring food',''),(76,6,'3','6',0,0,'',''),(78,6,'2','1',0,0,'',''),(97,6,'1','6',0,0,'',''),(77,2,'3','6',0,0,'',''),(78,2,'3','6',0,0,'',''),(85,6,'6','6',0,0,'',''),(85,4,'4','9',0,0,'',''),(86,2,'6','6',0,0,'',''),(99,2,'6','9',0,0,'',''),(85,3,'4','9',0,0,'',''),(103,2,'1','1',0,0,'',''),(100,2,'1','1',0,0,'',''),(102,2,'1','1',0,0,'',''),(101,2,'1','1',0,0,'',''),(102,6,'7','11',0,0,'',''),(108,6,'4','9',0,0,'',''),(110,6,'6','9',0,0,'officers only','');
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_block_map` ENABLE KEYS */;

--
-- Table structure for table `eighth_blocks`
--

DROP TABLE IF EXISTS `eighth_blocks`;
CREATE TABLE `eighth_blocks` (
  `bid` mediumint(8) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `block` enum('A','B') NOT NULL default 'A',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`bid`),
  UNIQUE KEY `pid` (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_blocks`
--


/*!40000 ALTER TABLE `eighth_blocks` DISABLE KEYS */;
LOCK TABLES `eighth_blocks` WRITE;
INSERT INTO `eighth_blocks` VALUES (1,'2005-08-13','A',0),(2,'2005-08-13','B',0),(3,'2005-08-14','A',0),(4,'2005-08-14','B',0),(6,'2005-09-21','A',0),(7,'2005-09-21','B',0),(14,'2005-09-30','B',0),(13,'2005-09-30','A',0),(16,'2005-10-10','B',0),(15,'2005-10-10','A',0),(17,'2005-10-03','A',0),(19,'2005-10-12','A',0),(20,'2005-10-12','B',0),(21,'2005-10-13','A',0),(22,'2005-10-14','A',0),(23,'2005-10-14','A',0),(24,'2005-10-14','A',0),(25,'2005-10-14','A',0),(26,'2005-10-14','A',0),(27,'2005-10-14','A',0),(28,'2005-10-15','A',0),(29,'2005-10-22','A',0),(31,'2005-10-22','B',0),(32,'2005-10-26','A',0),(33,'2005-11-30','A',0),(34,'2005-12-05','A',0),(40,'2005-12-10','A',0),(36,'2005-12-07','A',0),(37,'2005-12-07','B',0),(38,'2005-12-09','A',0),(39,'2005-12-09','B',0),(41,'2005-12-10','B',0),(42,'2005-12-12','A',0),(43,'2005-12-12','B',0),(44,'2005-12-14','A',0),(45,'2005-12-14','B',0),(46,'2005-12-16','A',0),(47,'2005-12-16','B',0),(48,'2005-12-19','A',0),(49,'2005-12-21','A',0),(50,'2005-12-21','B',0),(51,'2005-12-25','A',0),(52,'2005-12-25','B',0),(53,'2005-12-23','A',0),(54,'2005-12-23','B',0),(55,'2006-01-10','A',0),(56,'2006-01-10','B',0),(62,'2006-01-17','B',0),(61,'2006-01-17','A',0),(63,'2006-01-24','A',0),(64,'2006-01-24','B',0),(65,'2006-01-26','A',0),(66,'2006-01-26','B',0),(67,'2006-02-08','A',0),(68,'2006-02-08','B',0),(72,'2006-02-10','B',0),(71,'2006-02-10','A',0),(77,'2006-02-17','A',0),(78,'2006-02-17','B',0),(75,'2006-02-15','A',0),(81,'2006-02-20','A',0),(79,'2006-02-15','A',0),(89,'2006-02-16','A',0),(83,'2006-02-22','A',0),(84,'2006-02-22','B',0),(85,'2006-02-24','A',0),(107,'2006-03-10','A',0),(87,'2006-02-20','B',0),(88,'2006-02-27','B',0),(90,'2006-02-16','B',0),(91,'2006-02-16','A',0),(92,'2006-02-16','B',0),(93,'2006-02-16','A',0),(94,'2006-02-16','B',0),(95,'2006-02-16','A',0),(96,'2006-02-16','B',0),(97,'2006-02-16','A',0),(98,'2006-02-16','B',0),(106,'2006-02-24','B',0),(100,'2006-03-01','B',0),(101,'2006-03-08','A',1),(102,'2006-03-08','B',1),(103,'2006-03-01','A',0),(108,'2006-03-10','B',0),(109,'2006-03-13','B',0),(110,'2006-03-15','A',0),(111,'2006-03-15','B',0),(112,'2006-03-17','A',0),(113,'2006-03-17','B',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_blocks` ENABLE KEYS */;

--
-- Table structure for table `eighth_group_map`
--

DROP TABLE IF EXISTS `eighth_group_map`;
CREATE TABLE `eighth_group_map` (
  `gid` mediumint(8) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_group_map`
--


/*!40000 ALTER TABLE `eighth_group_map` DISABLE KEYS */;
LOCK TABLES `eighth_group_map` WRITE;
INSERT INTO `eighth_group_map` VALUES (1,9),(1,8),(1,6),(1,5),(1,4),(1,2),(1,1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_group_map` ENABLE KEYS */;

--
-- Table structure for table `eighth_groups`
--

DROP TABLE IF EXISTS `eighth_groups`;
CREATE TABLE `eighth_groups` (
  `gid` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(63) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`gid`),
  UNIQUE KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_groups`
--


/*!40000 ALTER TABLE `eighth_groups` DISABLE KEYS */;
LOCK TABLES `eighth_groups` WRITE;
INSERT INTO `eighth_groups` VALUES (1,'All Students','');
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_groups` ENABLE KEYS */;

--
-- Table structure for table `eighth_period_map`
--

DROP TABLE IF EXISTS `eighth_period_map`;
CREATE TABLE `eighth_period_map` (
  `bid` mediumint(8) unsigned NOT NULL default '0',
  `activityid` mediumint(8) unsigned NOT NULL default '0',
  `sponsors` varchar(127) NOT NULL default '',
  `rooms` varchar(127) NOT NULL default '',
  `attendancetaken` tinyint(1) NOT NULL default '0',
  `cancelled` tinyint(1) NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `advertisement` text NOT NULL,
  PRIMARY KEY  (`bid`,`activityid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_period_map`
--


/*!40000 ALTER TABLE `eighth_period_map` DISABLE KEYS */;
LOCK TABLES `eighth_period_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_period_map` ENABLE KEYS */;

--
-- Table structure for table `eighth_periods`
--

DROP TABLE IF EXISTS `eighth_periods`;
CREATE TABLE `eighth_periods` (
  `bid` mediumint(8) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `block` enum('A','B') NOT NULL default 'A',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`bid`),
  UNIQUE KEY `bid` (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_periods`
--


/*!40000 ALTER TABLE `eighth_periods` DISABLE KEYS */;
LOCK TABLES `eighth_periods` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_periods` ENABLE KEYS */;

--
-- Table structure for table `eighth_rooms`
--

DROP TABLE IF EXISTS `eighth_rooms`;
CREATE TABLE `eighth_rooms` (
  `rid` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(63) NOT NULL default '',
  `capacity` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_rooms`
--


/*!40000 ALTER TABLE `eighth_rooms` DISABLE KEYS */;
LOCK TABLES `eighth_rooms` WRITE;
INSERT INTO `eighth_rooms` VALUES (1,'Lab 151',666),(3,'Unassigned',9999),(5,'236wk',10),(12,'LC-7',24),(7,'Room 123 (50)',50),(11,'Aud Lob',100),(9,'243',60),(10,'Room 243',60);
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_rooms` ENABLE KEYS */;

--
-- Table structure for table `eighth_sponsors`
--

DROP TABLE IF EXISTS `eighth_sponsors`;
CREATE TABLE `eighth_sponsors` (
  `sid` mediumint(8) unsigned NOT NULL auto_increment,
  `fname` varchar(63) NOT NULL default '',
  `lname` varchar(127) NOT NULL default '',
  PRIMARY KEY  (`sid`),
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eighth_sponsors`
--


/*!40000 ALTER TABLE `eighth_sponsors` DISABLE KEYS */;
LOCK TABLES `eighth_sponsors` WRITE;
INSERT INTO `eighth_sponsors` VALUES (1,'Randy','Latimer'),(2,'Richard','Slivoskey'),(3,'Mickey','Mouse'),(4,'Bugs','Bunny'),(5,'Joe','Sponsor'),(6,'Meh','Bob'),(7,'Elmer','Fudd');
UNLOCK TABLES;
/*!40000 ALTER TABLE `eighth_sponsors` ENABLE KEYS */;

--
-- Table structure for table `group_user_map`
--

DROP TABLE IF EXISTS `group_user_map`;
CREATE TABLE `group_user_map` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `is_admin` tinyint(1) default NULL,
  `gid` mediumint(8) unsigned default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `group_user_map`
--


/*!40000 ALTER TABLE `group_user_map` DISABLE KEYS */;
LOCK TABLES `group_user_map` WRITE;
INSERT INTO `group_user_map` VALUES (11,NULL,9),(10,1,9),(12,NULL,9),(8,NULL,9),(7,0,6),(6,NULL,9),(5,NULL,9),(4,NULL,9),(3,NULL,9),(2,NULL,9),(1,NULL,9),(2,NULL,7),(6,NULL,12),(9,NULL,6),(1,1,8),(1,1,7),(1,0,6),(13,NULL,9),(14,NULL,9),(15,NULL,9),(1,1,11),(2,NULL,11),(3,NULL,11),(4,NULL,11),(5,NULL,11),(5,1,7),(6,NULL,11),(8,NULL,11),(10,0,6),(10,1,11),(11,NULL,11),(12,NULL,11),(13,NULL,11),(14,NULL,11),(15,NULL,11),(5,0,6),(5,1,12),(6,1,8),(6,NULL,7),(3,NULL,16),(3,NULL,6),(18,1,6);
UNLOCK TABLES;
/*!40000 ALTER TABLE `group_user_map` ENABLE KEYS */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `name` varchar(128) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `gid` mediumint(8) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--


/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
LOCK TABLES `groups` WRITE;
INSERT INTO `groups` VALUES ('special_people','Special People',13),('admin_ldap','LDAP Administrators',12),('i2_dev','Iodine Developers',11),('admin_mysql','MySQL Administrators',9),('admin_all','Master Administrators',6),('admin_news','News Administrators',7),('admin_groups','Group Administrators',8),('admin_eighth','Eigtht-Period Administrators',16);
UNLOCK TABLES;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;

--
-- Table structure for table `intrabox`
--

DROP TABLE IF EXISTS `intrabox`;
CREATE TABLE `intrabox` (
  `boxid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(63) NOT NULL default '',
  `display_name` varchar(127) default NULL,
  PRIMARY KEY  (`boxid`),
  UNIQUE KEY `boxid` (`boxid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `intrabox`
--


/*!40000 ALTER TABLE `intrabox` DISABLE KEYS */;
LOCK TABLES `intrabox` WRITE;
INSERT INTO `intrabox` VALUES (1,'birthdays','birthdays'),(2,'eighth','eighth'),(3,'mail','mail'),(4,'news','news'),(5,'studentdirectory','studentdirectory'),(6,'devlinks','devlinks'),(7,'links','links'),(8,'filecenter','filecenter');
UNLOCK TABLES;
/*!40000 ALTER TABLE `intrabox` ENABLE KEYS */;

--
-- Table structure for table `intrabox_group_map`
--

DROP TABLE IF EXISTS `intrabox_group_map`;
CREATE TABLE `intrabox_group_map` (
  `boxid` int(10) unsigned NOT NULL default '0',
  `gid` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `intrabox_group_map`
--


/*!40000 ALTER TABLE `intrabox_group_map` DISABLE KEYS */;
LOCK TABLES `intrabox_group_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `intrabox_group_map` ENABLE KEYS */;

--
-- Table structure for table `intrabox_map`
--

DROP TABLE IF EXISTS `intrabox_map`;
CREATE TABLE `intrabox_map` (
  `uid` int(10) unsigned NOT NULL default '0',
  `boxid` int(11) NOT NULL default '0',
  `box_order` smallint(6) default NULL,
  `closed` tinyint(4) default NULL,
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `intrabox_map`
--


/*!40000 ALTER TABLE `intrabox_map` DISABLE KEYS */;
LOCK TABLES `intrabox_map` WRITE;
INSERT INTO `intrabox_map` VALUES (1353404,1,1,NULL),(1,4,9,NULL),(1353404,4,2,NULL),(1,6,6,NULL),(1,2,7,NULL),(1353404,8,3,NULL),(1353404,9,4,NULL),(1,8,3,NULL),(1353404,12,5,NULL),(1353404,18,9,NULL),(1353404,18,8,NULL),(1353404,17,7,NULL),(1353404,16,6,NULL),(6,170,1,NULL),(6,173,2,NULL),(6,178,3,NULL),(6,181,4,NULL),(6,1,15,0),(6,17,5,NULL),(6,4,11,0),(6,18,6,NULL),(6,9,7,NULL),(6,12,8,NULL),(6,16,9,NULL),(7,1,8,NULL),(7,17,1,NULL),(7,4,9,NULL),(7,18,4,NULL),(11,2,4,NULL),(8,6,8,0),(8,5,7,NULL),(8,17,1,NULL),(6,2,16,0),(6,5,13,0),(6,6,17,0),(6,7,12,0),(11,6,1,NULL),(11,4,6,NULL),(11,5,5,1),(9,5,1,NULL),(9,1,5,NULL),(9,6,2,NULL),(9,2,7,NULL),(9,7,6,NULL),(9,4,0,NULL),(10,5,3,NULL),(10,8,5,NULL),(10,2,1,NULL),(10,7,6,NULL),(10,6,2,NULL),(10,1,4,NULL),(11,8,3,NULL),(11,7,2,NULL),(11,1,7,NULL),(8,4,5,0),(8,2,2,0),(8,1,6,NULL),(8,7,9,0),(7,2,2,NULL),(7,6,5,0),(7,7,7,NULL),(7,5,3,NULL),(1,7,8,NULL),(14,2,3,NULL),(14,5,2,NULL),(4,1,6,0),(4,2,1,0),(4,7,3,NULL),(19,2,1,NULL),(4,4,4,NULL),(4,5,5,NULL),(15,1,2,NULL),(15,6,3,NULL),(15,2,4,NULL),(15,5,1,NULL),(16,4,6,NULL),(6,8,14,0),(16,1,1,NULL),(16,5,7,NULL),(1,5,4,NULL),(9,8,3,NULL),(10,4,8,NULL),(4,8,2,NULL),(3,1,3,NULL),(3,2,1,NULL),(3,6,2,NULL),(3,4,4,NULL),(3,5,5,NULL),(3,8,6,NULL),(3,7,7,NULL),(5,8,3,NULL),(5,4,1,NULL),(5,2,2,NULL),(5,5,5,NULL),(1,1,5,NULL),(8,8,3,0),(7,8,6,NULL),(15,8,5,NULL),(15,4,7,NULL),(16,7,5,NULL),(16,8,4,NULL),(16,2,3,NULL),(16,6,2,NULL),(1,3,1,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `intrabox_map` ENABLE KEYS */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `authorID` mediumint(8) unsigned default NULL,
  `revised` timestamp NOT NULL default '0000-00-00 00:00:00',
  `posted` timestamp NOT NULL default '0000-00-00 00:00:00',
  `gid` mediumint(9) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `posted` (`posted`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news`
--


/*!40000 ALTER TABLE `news` DISABLE KEYS */;
LOCK TABLES `news` WRITE;
INSERT INTO `news` VALUES (14,'News-Groups','Group-specific news is now implemented. Sorry to those whose news posts I accidentally deleted in the process of working on this.<br /><br />\r\n\r\nEdit: I\'ve added a \'Posted to:\' section for news posted to groups, and you css people might want to give it some style, or something. It\'s in a div class=\"newsgroups\".',6,'2005-12-16 17:00:50','2005-12-15 18:39:44',NULL),(29,'Password cache now encrypted','The password cache that stores the user\'s password is now encrypted by Auth. Use Auth::get_user_password() to get it, instead of the former $_SESSION[\'i2_password\']. It uses a psuedorandom number stored in the session, an initialization vector stored in a cookie, and the user\'s accessing IP to decrypt the password.',1,'0000-00-00 00:00:00','2006-01-22 02:29:30',11),(25,'Filecenter broken','The filecenter module is now broken.  This may be because of a Heimdal upgrade, or the complete rebuild of PHP5 I\'ve just completed.  I\'ll investigate; at first glance it appears to be a few questionable sections of code in there which don\'t work with the now-stricter PHP version, in combination with changes to unserialize() and whatnot.\r\n\r\nOn an unrelated note, slapd is not running.  So just ignore the LDAP-related errors you get.',5,'0000-00-00 00:00:00','2006-01-19 09:53:50',11),(26,'Filecenter unbroken','The problem was that /usr/local/bin/php is the CGI version, not the CLI version. I\'m not sure where the CLI version is located so bin/cslhelper.php5 is currently using the version from my home directory.',9,'2006-01-19 21:54:05','2006-01-19 21:27:34',NULL),(30,'New mail IMAP header caching','I\'ve implemented the initial caching of IMAP headers. Please rerun setup to generate the options for the cache directories.',1,'0000-00-00 00:00:00','2006-02-02 10:29:45',11),(33,'Downtime','Hi, this is BRJ, fiddling with Heimdal 0.7.  You may experience some downtime.  Of course, if you can see this at all you must have authenticated successfully (or used the admin password), so it probably isn\'t TOO bad.',5,'0000-00-00 00:00:00','2006-03-01 14:27:33',11);
UNLOCK TABLES;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;

--
-- Table structure for table `news_group_map`
--

DROP TABLE IF EXISTS `news_group_map`;
CREATE TABLE `news_group_map` (
  `nid` bigint(20) unsigned NOT NULL default '0',
  `gid` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news_group_map`
--


/*!40000 ALTER TABLE `news_group_map` DISABLE KEYS */;
LOCK TABLES `news_group_map` WRITE;
INSERT INTO `news_group_map` VALUES (14,11);
UNLOCK TABLES;
/*!40000 ALTER TABLE `news_group_map` ENABLE KEYS */;

--
-- Table structure for table `news_read_map`
--

DROP TABLE IF EXISTS `news_read_map`;
CREATE TABLE `news_read_map` (
  `uid` mediumint(9) NOT NULL default '0',
  `nid` bigint(20) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news_read_map`
--


/*!40000 ALTER TABLE `news_read_map` DISABLE KEYS */;
LOCK TABLES `news_read_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `news_read_map` ENABLE KEYS */;

--
-- Table structure for table `section_course_map`
--

DROP TABLE IF EXISTS `section_course_map`;
CREATE TABLE `section_course_map` (
  `sectionid` int(10) unsigned NOT NULL default '0',
  `courseid` mediumint(8) unsigned default NULL,
  `teacherid` mediumint(8) unsigned default NULL,
  `period` tinyint(3) unsigned default NULL,
  `term` tinyint(4) default NULL,
  `room` varchar(32) default NULL,
  PRIMARY KEY  (`sectionid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `section_course_map`
--


/*!40000 ALTER TABLE `section_course_map` DISABLE KEYS */;
LOCK TABLES `section_course_map` WRITE;
INSERT INTO `section_course_map` VALUES (42200012,422000,133,6,0,'143'),(24456101,244561,134,3,0,'210'),(11966201,119662,316,2,0,'209'),(29980004,299800,97,7,0,'215'),(31770411,317704,144,5,0,'242A'),(31996302,319963,88,1,0,'115'),(84135801,841358,14,4,1,'118'),(84125801,841258,14,4,2,'118'),(1166420,11664,88,8,0,'115'),(66502701,665027,112,8,0,'115');
UNLOCK TABLES;
/*!40000 ALTER TABLE `section_course_map` ENABLE KEYS */;

--
-- Table structure for table `student_section_map`
--

DROP TABLE IF EXISTS `student_section_map`;
CREATE TABLE `student_section_map` (
  `studentid` mediumint(8) unsigned NOT NULL default '0',
  `sectionid` int(10) unsigned NOT NULL default '0',
  KEY `studentid` (`studentid`),
  KEY `sectionid` (`sectionid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student_section_map`
--


/*!40000 ALTER TABLE `student_section_map` DISABLE KEYS */;
LOCK TABLES `student_section_map` WRITE;
INSERT INTO `student_section_map` VALUES (847309,1166420),(847309,11966201),(847309,24456101),(847309,29980004),(847309,31770411),(847309,31996302),(847309,42200012),(847309,66502701),(847309,84125801),(847309,84135801);
UNLOCK TABLES;
/*!40000 ALTER TABLE `student_section_map` ENABLE KEYS */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` varchar(15) NOT NULL default '',
  `sex` enum('M','F') default NULL,
  `grade` enum('9','10','11','12','staff') default NULL,
  `fname` varchar(63) NOT NULL default '',
  `mname` varchar(63) default '',
  `lname` varchar(127) NOT NULL default '',
  `suffix` varchar(15) default '',
  `nickname` varchar(63) default '',
  `startpage` varchar(127) NOT NULL default 'news',
  `style` varchar(255) NOT NULL default 'default',
  `header` tinyint(1) NOT NULL default '0',
  `chrome` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `uid` (`uid`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--


/*!40000 ALTER TABLE `user` DISABLE KEYS */;
LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'adeason','M','12','Andrew','Pierce','Deason','','Deason','news','default',1,1),(2,'dtran','M','12','Dan','Nhat','Tran','','The Intranet Man','news','default',1,1),(3,'asmith','M','12','Andrew','Evans','Smith','','asmith','eighth','default',1,1),(4,'eharmon','M','12','Eric','Stephen','Harmon','','','news','default',1,1),(5,'brau-jac','M','12','Bryan','','Rau-Jacobs','','braujac','news','l33t',0,1),(6,'jboning','M','10','Josiah','Charles','Boning','','jobin','news','default',1,1),(7,'jbreese','M','10','Jack','F','Breese','','jbreese','news','LCARS',1,1),(8,'lkearsle','M','11','Logan','Richard','Kearsley','','','news','Ub321337',1,1),(9,'sgross','M','12','Samuel','Alan','Gross','','','news','default',1,1),(10,'nwatson','M','10','Nathan','Eric','Watson','','','news','default',1,1),(11,'astebbin','M','10','Drew','','Stebbins','','Stebby','news','modern',1,1),(12,'wyang','M','10','William','Edward','Yang','','','eighth','modern',0,1),(13,'lburton','','11','Lee','Reed','Burton','','','news','default',1,1),(14,'akusuma','M','10','Asa','Benjamin','Kusuma','','','news','default',1,1),(15,'aparthum','M','12','Alfred','Song','Parthum','','Alfie','news','ocean',1,1),(16,'guest','F','','Guestia','','Guest','','','news','default',1,1),(18,'admin','M','staff','Admin','\nR','Smith','','','eighth','default',0,0),(19,'ycho','M','','Y','Middlename','Cho','','','news','default',1,1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

--
-- Table structure for table `userinfo`
--

DROP TABLE IF EXISTS `userinfo`;
CREATE TABLE `userinfo` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `bdate` date default NULL,
  `phone_home` varchar(20) default NULL,
  `phone_cell` varchar(20) default NULL,
  `phone_other` varchar(20) default NULL,
  `address1_city` varchar(63) default NULL,
  `address1_state` char(2) default NULL,
  `address1_zip` varchar(12) default NULL,
  `address1_street` varchar(255) default NULL,
  `address2_city` varchar(63) default NULL,
  `address2_state` char(2) default NULL,
  `address2_zip` varchar(12) default NULL,
  `address2_street` varchar(255) default NULL,
  `address3_city` varchar(63) default NULL,
  `address3_state` char(2) default NULL,
  `address3_zip` varchar(12) default NULL,
  `address3_street` varchar(255) default NULL,
  `sn0` varchar(127) default NULL,
  `sn1` varchar(127) default NULL,
  `sn2` varchar(127) default NULL,
  `sn3` varchar(127) default NULL,
  `sn4` varchar(127) default NULL,
  `sn5` varchar(127) default NULL,
  `sn6` varchar(127) default NULL,
  `sn7` varchar(127) default NULL,
  `email0` varchar(127) default NULL,
  `email1` varchar(127) default NULL,
  `email2` varchar(127) default NULL,
  `email3` varchar(127) default NULL,
  `webpage` varchar(255) default NULL,
  `locker` smallint(5) unsigned default NULL,
  `counselor` varchar(63) default NULL,
  `picture0` smallint(6) default NULL,
  `picture1` smallint(6) default NULL,
  `picture2` smallint(6) default NULL,
  `picture3` smallint(6) default NULL,
  `studentid` mediumint(8) unsigned default NULL,
  `comments` text NOT NULL,
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userinfo`
--


/*!40000 ALTER TABLE `userinfo` DISABLE KEYS */;
LOCK TABLES `userinfo` WRITE;
INSERT INTO `userinfo` VALUES (6,'1987-12-17',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'jboning@gmail.com',NULL,NULL,NULL,'',123,NULL,NULL,NULL,NULL,NULL,NULL,''),(12,'2005-02-14','703-123-4566','',NULL,'Alexandria','VA','12345','6560 rd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'',NULL,NULL,NULL,'',0,'DOFF',NULL,NULL,NULL,NULL,NULL,''),(1,'1987-09-18',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'adeason@gmail.com',NULL,NULL,NULL,'',0,'DOFF',NULL,NULL,NULL,NULL,847309,''),(15,'1987-12-17',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'',NULL,NULL,NULL,'',1337,NULL,NULL,NULL,NULL,NULL,NULL,''),(16,'1987-12-17',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'',NULL,NULL,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,''),(5,'1987-12-17',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'',NULL,NULL,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,'If you search for Bryan by last name it brings up a double image (Mrs. lauducci)'),(10,'1987-12-17',NULL,'7037325000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'antarcticphoenix','antarcticphoenix1','antarcticphoenix@hotmail.com','antarcticphoenix@jabber.org','263169258','newatson@gmail.com',NULL,NULL,'nwatson@nwatson.org','',NULL,NULL,'http://www.tjhsst.edu/~nwatson',0,NULL,NULL,NULL,NULL,NULL,NULL,''),(9,'1988-03-21','703-280-9063','',NULL,'Fairfax','VA','22031','8930 Colesbury Place',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'colesbury','','','','','',NULL,NULL,'colesbury@gmail.com',NULL,NULL,NULL,'',0,'SMITH',NULL,NULL,NULL,NULL,837486,'Sam is an amazing!'),(7,'1990-10-29','703-724-7628','',NULL,'Ashburn','VA','20148','42863 Spring Morning Court',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'jbreese@jbreese.org',NULL,NULL,NULL,'http://www.tjhsst.edu/~jbreese/',0,NULL,NULL,NULL,NULL,NULL,NULL,'comments?'),(18,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'',NULL,NULL,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,'This pulls up the image twice also'),(19,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','',NULL,NULL,'',NULL,NULL,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,'');
UNLOCK TABLES;
/*!40000 ALTER TABLE `userinfo` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

