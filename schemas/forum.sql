-- MySQL dump 10.13  Distrib 5.5.27, for osx10.7 (i386)
--
-- Host: localhost    Database: forum
-- ------------------------------------------------------
-- Server version	5.5.27

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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `type` char(1) NOT NULL,
  `posts_id` int(10) unsigned DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(72) NOT NULL,
  `slug` varchar(32) DEFAULT NULL,
  `number_posts` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `number_posts` (`number_posts`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'MVC','mvc',0),(2,'ORM','orm',0),(3,'Volt','volt',0),(4,'Offtopic','offtopic',0),(5,'General','general',0),(6,'Installation','installation',0),(7,'Jobs','jobs',0),(8,'Cache','cache',0),(9,'Paginator','paginator',0),(10,'DI/IoC','di-ioc',0),(11,'Events','events-manager',0),(12,'Developer Tools','developer-tools',0),(13,'Routing','routing',0),(14,'Annotations','annotations',0),(15,'News/Announcements','news-announcements',0),(16,'Security','security',0),(17,'Acl','acl',0),(18,'Escaping/Filtering','escaping-filtering',0),(19,'Incubator','incubator',0),(20,'Internals','internals',0);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `categories_id` int(10) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `content` text,
  `number_views` int(3) unsigned NOT NULL,
  `number_replies` int(3) unsigned NOT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts_replies`
--

DROP TABLE IF EXISTS `posts_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_replies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `content` text,
  `created_at` int(18) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_replies`
--

LOCK TABLES `posts_replies` WRITE;
/*!40000 ALTER TABLE `posts_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts_views`
--

DROP TABLE IF EXISTS `posts_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `ipaddress` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_id` (`posts_id`,`ipaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_views`
--

LOCK TABLES `posts_views` WRITE;
/*!40000 ALTER TABLE `posts_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(72) DEFAULT NULL,
  `login` varchar(32) DEFAULT NULL,
  `gravatar_id` char(32) DEFAULT NULL,
  `token_type` varchar(16) DEFAULT NULL,
  `access_token` char(40) DEFAULT NULL,
  `number_posts` int(10) unsigned DEFAULT NULL,
  `number_replies` int(10) unsigned DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `access_token` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-22 22:58:46
