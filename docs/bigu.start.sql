-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: bigu
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1-log

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
-- Table structure for table `tbl_category`
--

DROP TABLE IF EXISTS `tbl_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_category` (
  `category` varchar(60) NOT NULL,
  `parent_category` varchar(60) DEFAULT NULL,
  `post_count` int(11) DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_category`
--

LOCK TABLES `tbl_category` WRITE;
/*!40000 ALTER TABLE `tbl_category` DISABLE KEYS */;
INSERT INTO `tbl_category` VALUES ('比咕视角','',0,'2013-10-31 05:47:55'),('业内观点','比咕视角',0,'2013-10-31 05:48:09'),('宏观视角','比咕视角',0,'2013-10-31 05:48:19'),('创造力','比咕视角',0,'2013-10-31 05:48:42'),('每日一文','比咕视角',0,'2013-10-31 05:48:50'),('自然语言处理','',0,'2013-10-31 05:48:55'),('中文分词','自然语言处理',0,'2013-10-31 05:49:07'),('词性标注','自然语言处理',0,'2013-10-31 05:49:15'),('语法语义','自然语言处理',0,'2013-10-31 05:49:23'),('机器学习','自然语言处理',0,'2013-10-31 05:49:32'),('NLP其它','自然语言处理',0,'2013-10-31 05:49:44'),('搜索引擎','',0,'2013-10-31 05:49:50'),('存储技术','搜索引擎',0,'2013-10-31 05:50:02'),('爬虫技术','搜索引擎',0,'2013-10-31 05:50:14'),('搜索功能','搜索引擎',0,'2013-10-31 05:50:23'),('数据抽取','搜索引擎',0,'2013-10-31 05:50:33'),('程序技巧','',0,'2013-10-31 05:51:32'),('编程代码','程序技巧',0,'2013-10-31 05:51:43'),('辅助工具','程序技巧',0,'2013-10-31 05:51:53'),('系统相关','程序技巧',0,'2013-10-31 05:52:07'),('比咕实验室','',0,'2013-10-31 05:52:13'),('互联网项目','比咕实验室',0,'2013-10-31 05:52:22'),('硬件产品','比咕实验室',0,'2013-10-31 05:52:29'),('创业点子','比咕实验室',0,'2013-10-31 05:52:37');
/*!40000 ALTER TABLE `tbl_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_comment`
--

DROP TABLE IF EXISTS `tbl_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `content` text NOT NULL,
  `pub_datetime` datetime NOT NULL,
  `author` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_comment`
--

LOCK TABLES `tbl_comment` WRITE;
/*!40000 ALTER TABLE `tbl_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_permissions`
--

DROP TABLE IF EXISTS `tbl_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_role` int(11) NOT NULL,
  `id_resource` int(11) NOT NULL,
  `permission` enum('allow','deny') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_permissions`
--

LOCK TABLES `tbl_permissions` WRITE;
/*!40000 ALTER TABLE `tbl_permissions` DISABLE KEYS */;
INSERT INTO `tbl_permissions` VALUES (1,3,1,'allow'),(2,1,2,'allow'),(3,2,3,'allow'),(4,3,3,'allow'),(5,2,1,'allow'),(6,1,4,'allow'),(7,1,5,'allow'),(8,1,6,'allow'),(9,1,7,'allow'),(10,1,8,'allow'),(11,1,9,'allow'),(12,1,10,'allow'),(13,1,11,'deny'),(19,1,12,'allow'),(21,1,15,'allow');
/*!40000 ALTER TABLE `tbl_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_post`
--

DROP TABLE IF EXISTS `tbl_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `abstract` varchar(1280) NOT NULL,
  `pub_datetime` datetime NOT NULL,
  `author` varchar(40) NOT NULL,
  `category` varchar(60) DEFAULT NULL,
  `sub_category` varchar(60) DEFAULT NULL,
  `source` varchar(256) DEFAULT NULL,
  `view_count` int(11) DEFAULT '0',
  `comment_count` int(11) DEFAULT '0',
  `image_path` varchar(128) DEFAULT NULL,
  `index_thumb` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_post`
--

LOCK TABLES `tbl_post` WRITE;
/*!40000 ALTER TABLE `tbl_post` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_profile`
--

DROP TABLE IF EXISTS `tbl_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `pname` varchar(40) NOT NULL,
  `pvalue` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_profile`
--

LOCK TABLES `tbl_profile` WRITE;
/*!40000 ALTER TABLE `tbl_profile` DISABLE KEYS */;
INSERT INTO `tbl_profile` VALUES (35,'admin','gender','未知'),(36,'admin','address',''),(37,'admin','intro',''),(38,'admin','city',''),(39,'admin','weibo',''),(40,'admin','qq',''),(41,'admin','phone',''),(42,'admin','company',''),(43,'admin','title',''),(44,'admin','industry',''),(45,'admin','homepage','');
/*!40000 ALTER TABLE `tbl_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_resources`
--

DROP TABLE IF EXISTS `tbl_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_resources`
--

LOCK TABLES `tbl_resources` WRITE;
/*!40000 ALTER TABLE `tbl_resources` DISABLE KEYS */;
INSERT INTO `tbl_resources` VALUES (1,'*/*/*'),(3,'auth/index/index'),(2,'auth/*/*'),(4,'post/post/*'),(5,'post/comment/*'),(6,'post/category/*'),(7,'default/index/index'),(8,'post/post/view'),(9,'default/error/error'),(10,'profile/comment/*'),(11,'post/comment/delete'),(12,'info/*/*'),(15,'search/*/*');
/*!40000 ALTER TABLE `tbl_resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_roles`
--

DROP TABLE IF EXISTS `tbl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(40) NOT NULL,
  `id_parent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_roles`
--

LOCK TABLES `tbl_roles` WRITE;
/*!40000 ALTER TABLE `tbl_roles` DISABLE KEYS */;
INSERT INTO `tbl_roles` VALUES (3,'admin',0),(2,'user',1),(1,'guest',0);
/*!40000 ALTER TABLE `tbl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_users` (
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `password_salt` varchar(32) NOT NULL,
  `real_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `avatar` varchar(150) DEFAULT NULL,
  `status` enum('unapproved','approved') DEFAULT 'unapproved',
  `id_role` int(11) NOT NULL,
  PRIMARY KEY (`username`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES ('admin','5d3cee8a7100c3aea1576b2cce3bb04cb2415054','fbe8b98d0bdbf62c4a05efbf151ae0a0','管理员','guohua_zhuang@163.com',NULL,'approved',3);
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-31 13:54:22
