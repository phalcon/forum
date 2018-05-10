-- MySQL dump 10.13  Distrib 5.5.36, for osx10.9 (i386)
--
-- Host: localhost    Database: forum
-- ------------------------------------------------------
-- Server version	5.5.36

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
  KEY `created_at` (`created_at`),
  KEY `users_id` (`users_id`),
  KEY `posts_id` (`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_notifications`
--

DROP TABLE IF EXISTS `activity_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned DEFAULT NULL,
  `type` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `users_origin_id` int(10) unsigned DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `was_read` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `extra` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`,`was_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(72) NOT NULL,
  `description` text NOT NULL,
  `slug` varchar(32) DEFAULT NULL,
  `number_posts` int(10) unsigned DEFAULT NULL,
  `no_bounty` char(1) NOT NULL DEFAULT 'N',
  `no_digest` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `number_posts` (`number_posts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `categories` (`id`, `name`, `description`, `slug`, `number_posts`, `no_bounty`, `no_digest`) VALUES (1, 'First Category', 'Description Category', 'first', 1, 'N', 'N');

--
-- Table structure for table `irclog`
--

DROP TABLE IF EXISTS `irclog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irclog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `who` varchar(64) NOT NULL,
  `content` text,
  `datelog` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datelog` (`datelog`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned DEFAULT NULL,
  `type` char(1) NOT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  `message_id` char(60) DEFAULT NULL,
  `sent` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `posts_id` (`posts_id`),
  KEY `sent` (`sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications_bounces`
--

DROP TABLE IF EXISTS `notifications_bounces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications_bounces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(120) NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `diagnostic` varchar(120) DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `reported` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`,`reported`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `votes_up` int(10) unsigned DEFAULT NULL,
  `votes_down` int(10) unsigned DEFAULT NULL,
  `sticked` char(1) DEFAULT 'N',
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  `edited_at` int(18) unsigned DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  `locked` char(1) DEFAULT 'N',
  `deleted` int(3) DEFAULT '0',
  `accepted_answer` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `categories_id` (`categories_id`),
  KEY `title` (`title`),
  KEY `number_replies` (`number_replies`),
  KEY `modified_at` (`modified_at`),
  KEY `created_at` (`created_at`),
  KEY `sticked` (`sticked`,`created_at`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `posts_poll_options`
--

CREATE TABLE `posts_poll_options` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `posts_id` INT(10) UNSIGNED NOT NULL,
  `title` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_poll_options_post_id` (`posts_id`),
  FOREIGN KEY (`posts_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_poll_votes`
--

CREATE TABLE `posts_poll_votes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` INT(10) UNSIGNED NOT NULL,
  `options_id` BIGINT UNSIGNED NOT NULL,
  `posts_id` INT(10) UNSIGNED NOT NULL,
  `created_at` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_poll_votes_user_id` (`users_id`),
  UNIQUE `posts_poll_votes_post_id_user_id` (`posts_id`, `users_id`),
  FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`options_id`) REFERENCES `posts_poll_options` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`posts_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_bounties`
--

DROP TABLE IF EXISTS `posts_bounties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_bounties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `created_at` int(18) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`,`posts_replies_id`),
  KEY `posts_id` (`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_history`
--

DROP TABLE IF EXISTS `posts_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `posts_id` (`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_notifications`
--

DROP TABLE IF EXISTS `posts_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`,`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `in_reply_to_id` int(10) unsigned DEFAULT '0',
  `content` text,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  `edited_at` int(18) unsigned DEFAULT NULL,
  `votes_up` int(10) unsigned DEFAULT NULL,
  `votes_down` int(10) unsigned DEFAULT NULL,
  `accepted` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `posts_id` (`posts_id`),
  KEY `users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_replies_history`
--

DROP TABLE IF EXISTS `posts_replies_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_replies_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `posts_replies_id` (`posts_replies_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_replies_votes`
--

DROP TABLE IF EXISTS `posts_replies_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_replies_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `vote` int(3) NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_subscribers`
--

DROP TABLE IF EXISTS `posts_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_id` (`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `posts_votes`
--

DROP TABLE IF EXISTS `posts_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `vote` int(3) NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `email` varchar(70) DEFAULT NULL,
  `gravatar_id` char(32) DEFAULT NULL,
  `token_type` varchar(16) DEFAULT NULL,
  `access_token` char(40) DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  `notifications` char(1) DEFAULT 'N',
  `digest` char(1) DEFAULT 'Y',
  `timezone` varchar(48) DEFAULT NULL,
  `moderator` char(1) DEFAULT 'N',
  `karma` int(11) DEFAULT NULL,
  `votes` int(10) unsigned DEFAULT NULL,
  `votes_points` int(11) DEFAULT NULL,
  `banned` char(1) DEFAULT 'N',
  `theme` char(1) DEFAULT 'D',
  PRIMARY KEY (`id`),
  KEY `access_token` (`access_token`),
  KEY `login` (`login`),
  KEY `email` (`email`),
  KEY `karma` (`karma`),
  KEY `login_2` (`login`),
  KEY `notifications` (`notifications`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `topic_tracking`
--

DROP TABLE IF EXISTS `topic_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topic_tracking` (
  `user_id` int(11) NOT NULL,
  `topic_id` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_badges`
--

DROP TABLE IF EXISTS `users_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `badge` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  `type` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code1` int(10) unsigned DEFAULT NULL,
  `code2` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`,`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-10-12  8:14:37
