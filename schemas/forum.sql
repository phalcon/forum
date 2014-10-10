-- MySQL dump 10.14  Distrib 5.5.39-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: forum
-- ------------------------------------------------------
-- Server version	5.5.39-MariaDB

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (1,1,'U',NULL,1412742974),(2,1,'P',1,1412743026),(3,2,'U',NULL,1412756737),(4,2,'P',2,1412870272),(5,3,'U',NULL,1412873122);
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`,`was_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_notifications`
--

LOCK TABLES `activity_notifications` WRITE;
/*!40000 ALTER TABLE `activity_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_notifications` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'News/Announcements','news-announcements',1),(2,'General','general',0),(3,'Installation','installation',1),(4,'Development Tool','development-tool',NULL),(5,'Beginners','beginners',NULL),(6,'Paginator','paginator',NULL),(7,'Configuration','configuration',NULL),(8,'Security','security',NULL),(9,'Annotations','annotations',NULL),(10,'Jobs','jobs',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `irclog`
--

LOCK TABLES `irclog` WRITE;
/*!40000 ALTER TABLE `irclog` DISABLE KEYS */;
/*!40000 ALTER TABLE `irclog` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `notifications_bounces`
--

LOCK TABLES `notifications_bounces` WRITE;
/*!40000 ALTER TABLE `notifications_bounces` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications_bounces` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,1,'Chào mừng bạn đến diễn đàn Phalcon','ch-o-m-ng-b-n-n-di-n-n-phalcon','Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\n\r\nDo tôi dùng mã nguồn của Forum Phalcon nên hiện tại có một số xuất hiện tiếng anh và tiếng việt mong các bạn thông cảm, các bạn cũng có  thể giúp tôi nếu có thể bằng cách [fork](https://github.com/duythien/forum/), sau đó PR cho tôi, ví dụ trong tập tin [List-post](https://github.com/duythien/forum/blob/master/app/views/partials/top-menu.volt#L83)\r\n\r\n\r\n			 {{ link_to(\'login/oauth/authorize\', \'Log In with Github\' }}  //sữa thành \r\n			 {{ link_to(\'login/oauth/authorize\', t(\'Log In with Github\') }}\r\n\r\n\r\nsau đó vào tập tin ngôn ngữ [tiếng việt](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po) thêm vào như sau\r\n\r\n			msgid  \"Log In with Github\"\r\n			msgstr \"Đặng nhập với Github\"\r\n\r\nĐể có thể comment bạn cần phải có tài khoản [Github](http://github.com).  Ok như vậy là xong, các bạn có thắc mắc gì comment bên dưới\r\n\r\nXin cảm ơn!!!!',10,0,NULL,NULL,'Y',1412743026,1412743026,1412876293,'A',NULL,0,'N'),(2,2,3,'Hướng dẫn cài đặt Phalcon trên Heroku','huong-dan-cai-dat-phalcon-tren-heroku','Hi all\r\n\r\nCác bạn có thể hướng dẫn cài Phalcon trên Heroku được không, mình làm theo tut này mà không được  http://www.sitepoint.com/install-custom-php-extensions-heroku/\r\n\r\nThanks',7,0,NULL,NULL,'N',1412870272,1412870272,NULL,'A',NULL,0,'N');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `posts_bounties`
--

LOCK TABLES `posts_bounties` WRITE;
/*!40000 ALTER TABLE `posts_bounties` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_bounties` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_history`
--

LOCK TABLES `posts_history` WRITE;
/*!40000 ALTER TABLE `posts_history` DISABLE KEYS */;
INSERT INTO `posts_history` VALUES (1,1,1,1412743026,'nice'),(2,1,1,1412743026,'nice'),(3,1,1,1412847767,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\nXin cảm ơn!!!!'),(4,1,1,1412848491,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\ni- [Hỗ trợ giải đáp các câu hỏi](/)\r\n- [Đóng góp mã nguồn cho forum này](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po)\r\n\r\nXin cảm ơn!!!!'),(5,1,1,1412848510,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n- [Đóng góp mã nguồn cho forum này](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po)\r\n\r\nXin cảm ơn!!!!'),(6,2,2,1412870272,'Hi all\r\n\r\nCác bạn có thể hướng dẫn cài Phalcon trên Heroku được không, mình làm theo tut này mà không được  http://www.sitepoint.com/install-custom-php-extensions-heroku/\r\n\r\nThanks'),(7,2,2,1412870272,'Hi all\r\n\r\nCác bạn có thể hướng dẫn cài Phalcon trên Heroku được không, mình làm theo tut này mà không được  http://www.sitepoint.com/install-custom-php-extensions-heroku/\r\n\r\nThanks'),(8,1,1,1412871658,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\nDo tôi dùng mã nguồn của Forum Phalcon nên hiện tại có một số xuất hiện tiếng anh và tiếng việt mong các bạn thông cảm, các bạn củng cố thể giúp tôi nếu có thể bằng cách [fork](https://github.com/duythien/forum/), sau đó PR cho tôi, ví dụ trong tập tin [List-post](https://github.com/duythien/forum/blob/master/app/views/partials/top-menu.volt#L83)\r\n\r\n\r\n			{{ {{ link_to(\'login/oauth/authorize\', \'Log In with Github\' }} thành \r\n			{{ {{ link_to(\'login/oauth/authorize\', t(\'Log In with Github\') }}\r\n\r\n\r\nsau đó vào tập tin ngôn ngữ [tiếng việt](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po) thêm vào như sau\r\n\r\n			msgid  \"Log In with Github\"\r\n			msgstr \"Đặng nhập với Github\"\r\n\r\nOk như vậy là xong, các bạn có thắc mắc gì comment bên dưới\r\n\r\nXin cảm ơn!!!!'),(9,1,1,1412871702,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\nDo tôi dùng mã nguồn của Forum Phalcon nên hiện tại có một số xuất hiện tiếng anh và tiếng việt mong các bạn thông cảm, các bạn cũng có  thể giúp tôi nếu có thể bằng cách [fork](https://github.com/duythien/forum/), sau đó PR cho tôi, ví dụ trong tập tin [List-post](https://github.com/duythien/forum/blob/master/app/views/partials/top-menu.volt#L83)\r\n\r\n\r\n			{{ {{ link_to(\'login/oauth/authorize\', \'Log In with Github\' }} thành \r\n			{{ {{ link_to(\'login/oauth/authorize\', t(\'Log In with Github\') }}\r\n\r\n\r\nsau đó vào tập tin ngôn ngữ [tiếng việt](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po) thêm vào như sau\r\n\r\n			msgid  \"Log In with Github\"\r\n			msgstr \"Đặng nhập với Github\"\r\n\r\nOk như vậy là xong, các bạn có thắc mắc gì comment bên dưới\r\n\r\nXin cảm ơn!!!!'),(10,2,3,1412873128,'Hi all\r\n\r\nCác bạn có thể hướng dẫn cài Phalcon trên Heroku được không, mình làm theo tut này mà không được  http://www.sitepoint.com/install-custom-php-extensions-heroku/\r\n\r\nThanks'),(11,1,3,1412873168,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\nDo tôi dùng mã nguồn của Forum Phalcon nên hiện tại có một số xuất hiện tiếng anh và tiếng việt mong các bạn thông cảm, các bạn cũng có  thể giúp tôi nếu có thể bằng cách [fork](https://github.com/duythien/forum/), sau đó PR cho tôi, ví dụ trong tập tin [List-post](https://github.com/duythien/forum/blob/master/app/views/partials/top-menu.volt#L83)\r\n\r\n\r\n			{{ {{ link_to(\'login/oauth/authorize\', \'Log In with Github\' }} thành \r\n			{{ {{ link_to(\'login/oauth/authorize\', t(\'Log In with Github\') }}\r\n\r\n\r\nsau đó vào tập tin ngôn ngữ [tiếng việt](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po) thêm vào như sau\r\n\r\n			msgid  \"Log In with Github\"\r\n			msgstr \"Đặng nhập với Github\"\r\n\r\nOk như vậy là xong, các bạn có thắc mắc gì comment bên dưới\r\n\r\nXin cảm ơn!!!!'),(12,1,1,1412873273,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\n\r\nDo tôi dùng mã nguồn của Forum Phalcon nên hiện tại có một số xuất hiện tiếng anh và tiếng việt mong các bạn thông cảm, các bạn cũng có  thể giúp tôi nếu có thể bằng cách [fork](https://github.com/duythien/forum/), sau đó PR cho tôi, ví dụ trong tập tin [List-post](https://github.com/duythien/forum/blob/master/app/views/partials/top-menu.volt#L83)\r\n\r\n\r\n			 {{ link_to(\'login/oauth/authorize\', \'Log In with Github\' }}  //sữa thành \r\n			 {{ link_to(\'login/oauth/authorize\', t(\'Log In with Github\') }}\r\n\r\n\r\nsau đó vào tập tin ngôn ngữ [tiếng việt](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po) thêm vào như sau\r\n\r\n			msgid  \"Log In with Github\"\r\n			msgstr \"Đặng nhập với Github\"\r\n\r\nOk như vậy là xong, các bạn có thắc mắc gì comment bên dưới\r\n\r\nXin cảm ơn!!!!'),(13,1,1,1412876293,'Xin chào tất cả mọi người !!!\r\n\r\nTrước hết xin cảm ơn các bạn đã ghé thăm diền Forum này, mục đích của Forum này là dùng để chia sẽ những kinh nghiệp sử dụng Phalcon hay đặt những câu hỏi của các bạn khi gặp phải trong quá trình sử dụng Phalcon. Những gì mà bạn có thể đóng góp\r\n\r\n- [Dịch tài liệu Phalcon](https://www.transifex.com/projects/p/phalcon/)\r\n- [Chia sẽ các tut](/)\r\n- [Hỗ trợ giải đáp các câu hỏi](/)\r\n\r\n\r\nDo tôi dùng mã nguồn của Forum Phalcon nên hiện tại có một số xuất hiện tiếng anh và tiếng việt mong các bạn thông cảm, các bạn cũng có  thể giúp tôi nếu có thể bằng cách [fork](https://github.com/duythien/forum/), sau đó PR cho tôi, ví dụ trong tập tin [List-post](https://github.com/duythien/forum/blob/master/app/views/partials/top-menu.volt#L83)\r\n\r\n\r\n			 {{ link_to(\'login/oauth/authorize\', \'Log In with Github\' }}  //sữa thành \r\n			 {{ link_to(\'login/oauth/authorize\', t(\'Log In with Github\') }}\r\n\r\n\r\nsau đó vào tập tin ngôn ngữ [tiếng việt](https://github.com/duythien/forum/blob/master/app/lang/vi_VN/LC_MESSAGES/messages.po) thêm vào như sau\r\n\r\n			msgid  \"Log In with Github\"\r\n			msgstr \"Đặng nhập với Github\"\r\n\r\nĐể có thể comment bạn cần phải có tài khoản [Github](http://github.com).  Ok như vậy là xong, các bạn có thắc mắc gì comment bên dưới\r\n\r\nXin cảm ơn!!!!'),(14,2,1,1412909773,'Hi all\r\n\r\nCác bạn có thể hướng dẫn cài Phalcon trên Heroku được không, mình làm theo tut này mà không được  http://www.sitepoint.com/install-custom-php-extensions-heroku/\r\n\r\nThanks');
/*!40000 ALTER TABLE `posts_history` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_notifications`
--

LOCK TABLES `posts_notifications` WRITE;
/*!40000 ALTER TABLE `posts_notifications` DISABLE KEYS */;
INSERT INTO `posts_notifications` VALUES (1,1,1),(2,2,2);
/*!40000 ALTER TABLE `posts_notifications` ENABLE KEYS */;
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
-- Dumping data for table `posts_replies`
--

LOCK TABLES `posts_replies` WRITE;
/*!40000 ALTER TABLE `posts_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_replies` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `posts_replies_history`
--

LOCK TABLES `posts_replies_history` WRITE;
/*!40000 ALTER TABLE `posts_replies_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_replies_history` ENABLE KEYS */;
UNLOCK TABLES;

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
  `created_at` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_replies_votes`
--

LOCK TABLES `posts_replies_votes` WRITE;
/*!40000 ALTER TABLE `posts_replies_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_replies_votes` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_views`
--

LOCK TABLES `posts_views` WRITE;
/*!40000 ALTER TABLE `posts_views` DISABLE KEYS */;
INSERT INTO `posts_views` VALUES (4,1,'115.79.194.53'),(2,1,'115.79.34.167'),(12,1,'118.68.189.217'),(3,1,'118.69.52.129'),(1,1,'127.0.0.1'),(13,1,'14.169.15.146'),(5,1,'14.169.60.53'),(10,1,'183.81.10.99'),(14,1,'42.117.54.241'),(8,1,'42.118.50.0'),(17,2,'115.79.34.167'),(11,2,'118.68.189.217'),(16,2,'118.69.52.129'),(6,2,'14.169.60.53'),(9,2,'183.81.10.99'),(15,2,'42.117.54.241'),(7,2,'42.118.50.0');
/*!40000 ALTER TABLE `posts_views` ENABLE KEYS */;
UNLOCK TABLES;

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
  `created_at` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_votes`
--

LOCK TABLES `posts_votes` WRITE;
/*!40000 ALTER TABLE `posts_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_votes` ENABLE KEYS */;
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
  `login` varchar(72) CHARACTER SET ascii NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `access_token` (`access_token`),
  KEY `login` (`login`),
  KEY `email` (`email`),
  KEY `karma` (`karma`),
  KEY `login_2` (`login`),
  KEY `notifications` (`notifications`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Duy Thiện','duythien','fcduythien@gmail.com','97cfdb3586f82c3e1e4fe562bda688b8','bearer','36326fb099b942450487df821acdc2e830c49d5f',1412742974,1412909773,'P','Y','Europe/London','N',91,1,31),(2,'Nhi','stackphysics','fcopensuse@gmail.com','93bafae90377f79ffc801d91badf9142','bearer','6b58de5caa1924182a20679daf41d0505037c8f6',1412756736,1412909773,'P','Y','Europe/London','N',171,3,6),(3,'thanhvn-57','thanhvn-57','vungocthanh.2408@gmail.com','0ee42623631f5dfa80b2b71d274386e4','bearer','ec9c8b0c3cd9c249b261689cc129eaa6ba4b9042',1412873122,1412873168,'P','Y','Europe/London','N',54,1,2);
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

-- Dump completed on 2014-10-09 23:02:26
