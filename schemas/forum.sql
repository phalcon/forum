-- phpMyAdmin SQL Dump
-- version 4.2.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 09, 2014 at 12:52 PM
-- Server version: 5.5.38-MariaDB
-- PHP Version: 5.5.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
`id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `type` char(1) NOT NULL,
  `posts_id` int(10) unsigned DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `users_id`, `type`, `posts_id`, `created_at`) VALUES
(1, 1, 'U', NULL, 1412742974),
(2, 1, 'P', 1, 1412743026),
(3, 2, 'U', NULL, 1412756737);

-- --------------------------------------------------------

--
-- Table structure for table `activity_notifications`
--

CREATE TABLE IF NOT EXISTS `activity_notifications` (
`id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned DEFAULT NULL,
  `type` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `users_origin_id` int(10) unsigned DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `was_read` char(1) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(72) NOT NULL,
  `slug` varchar(32) DEFAULT NULL,
  `number_posts` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `number_posts`) VALUES
(1, 'News Announcements', 'news-announcements', 1),
(2, 'General', 'general', 0);

-- --------------------------------------------------------

--
-- Table structure for table `irclog`
--

CREATE TABLE IF NOT EXISTS `irclog` (
`id` int(10) unsigned NOT NULL,
  `who` varchar(64) NOT NULL,
  `content` text,
  `datelog` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
`id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned DEFAULT NULL,
  `type` char(1) NOT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  `message_id` char(60) DEFAULT NULL,
  `sent` char(1) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications_bounces`
--

CREATE TABLE IF NOT EXISTS `notifications_bounces` (
`id` int(10) unsigned NOT NULL,
  `email` varchar(120) NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `diagnostic` varchar(120) DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `reported` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
`id` int(10) unsigned NOT NULL,
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
  `accepted_answer` char(1) DEFAULT 'N'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `users_id`, `categories_id`, `title`, `slug`, `content`, `number_views`, `number_replies`, `votes_up`, `votes_down`, `sticked`, `created_at`, `modified_at`, `edited_at`, `status`, `locked`, `deleted`, `accepted_answer`) VALUES
(1, 1, 1, 'Chào mừng bạn đến diễn đàn Phalcon', 'chao-mung-ban-den-dien-dan-phalcon', 'nice', 1, 0, NULL, NULL, 'Y', 1412743026, 1412743026, NULL, 'A', NULL, 0, 'N');

-- --------------------------------------------------------

--
-- Table structure for table `posts_bounties`
--

CREATE TABLE IF NOT EXISTS `posts_bounties` (
`id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `created_at` int(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts_history`
--

CREATE TABLE IF NOT EXISTS `posts_history` (
`id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `posts_history`
--

INSERT INTO `posts_history` (`id`, `posts_id`, `users_id`, `created_at`, `content`) VALUES
(1, 1, 1, 1412743026, 'nice'),
(2, 1, 1, 1412743026, 'nice');

-- --------------------------------------------------------

--
-- Table structure for table `posts_notifications`
--

CREATE TABLE IF NOT EXISTS `posts_notifications` (
`id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `posts_notifications`
--

INSERT INTO `posts_notifications` (`id`, `users_id`, `posts_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts_replies`
--

CREATE TABLE IF NOT EXISTS `posts_replies` (
`id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `in_reply_to_id` int(10) unsigned DEFAULT '0',
  `content` text,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  `edited_at` int(18) unsigned DEFAULT NULL,
  `votes_up` int(10) unsigned DEFAULT NULL,
  `votes_down` int(10) unsigned DEFAULT NULL,
  `accepted` char(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts_replies_history`
--

CREATE TABLE IF NOT EXISTS `posts_replies_history` (
`id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts_replies_votes`
--

CREATE TABLE IF NOT EXISTS `posts_replies_votes` (
`id` int(10) unsigned NOT NULL,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts_views`
--

CREATE TABLE IF NOT EXISTS `posts_views` (
`id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `ipaddress` varchar(20) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `posts_views`
--

INSERT INTO `posts_views` (`id`, `posts_id`, `ipaddress`) VALUES
(1, 1, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `posts_votes`
--

CREATE TABLE IF NOT EXISTS `posts_votes` (
`id` int(10) unsigned NOT NULL,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(10) unsigned NOT NULL,
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
  `votes_points` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `login`, `email`, `gravatar_id`, `token_type`, `access_token`, `created_at`, `modified_at`, `notifications`, `digest`, `timezone`, `moderator`, `karma`, `votes`, `votes_points`) VALUES
(1, 'Duy Thiện', 'duythien', 'fcduythien@gmail.com', '97cfdb3586f82c3e1e4fe562bda688b8', 'bearer', '36326fb099b942450487df821acdc2e830c49d5f', 1412742974, 1412743026, 'P', 'Y', 'Europe/London', 'N', 60, 1, 0),
(2, 'Nhi', 'stackphysics', 'fcopensuse@gmail.com', '93bafae90377f79ffc801d91badf9142', 'bearer', '6b58de5caa1924182a20679daf41d0505037c8f6', 1412756736, NULL, 'P', 'Y', 'Europe/London', 'N', 50, 0, 50);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
 ADD PRIMARY KEY (`id`), ADD KEY `created_at` (`created_at`), ADD KEY `users_id` (`users_id`), ADD KEY `posts_id` (`posts_id`);

--
-- Indexes for table `activity_notifications`
--
ALTER TABLE `activity_notifications`
 ADD PRIMARY KEY (`id`), ADD KEY `users_id` (`users_id`,`was_read`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
 ADD PRIMARY KEY (`id`), ADD KEY `number_posts` (`number_posts`);

--
-- Indexes for table `irclog`
--
ALTER TABLE `irclog`
 ADD PRIMARY KEY (`id`), ADD KEY `datelog` (`datelog`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
 ADD PRIMARY KEY (`id`), ADD KEY `users_id` (`users_id`), ADD KEY `posts_id` (`posts_id`), ADD KEY `sent` (`sent`);

--
-- Indexes for table `notifications_bounces`
--
ALTER TABLE `notifications_bounces`
 ADD PRIMARY KEY (`id`), ADD KEY `email` (`email`,`reported`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
 ADD PRIMARY KEY (`id`), ADD KEY `users_id` (`users_id`), ADD KEY `categories_id` (`categories_id`), ADD KEY `title` (`title`), ADD KEY `number_replies` (`number_replies`), ADD KEY `modified_at` (`modified_at`), ADD KEY `created_at` (`created_at`), ADD KEY `sticked` (`sticked`,`created_at`), ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `posts_bounties`
--
ALTER TABLE `posts_bounties`
 ADD PRIMARY KEY (`id`), ADD KEY `users_id` (`users_id`,`posts_replies_id`), ADD KEY `posts_id` (`posts_id`);

--
-- Indexes for table `posts_history`
--
ALTER TABLE `posts_history`
 ADD PRIMARY KEY (`id`), ADD KEY `posts_id` (`posts_id`);

--
-- Indexes for table `posts_notifications`
--
ALTER TABLE `posts_notifications`
 ADD PRIMARY KEY (`id`), ADD KEY `users_id` (`users_id`,`posts_id`);

--
-- Indexes for table `posts_replies`
--
ALTER TABLE `posts_replies`
 ADD PRIMARY KEY (`id`), ADD KEY `posts_id` (`posts_id`), ADD KEY `users_id` (`users_id`);

--
-- Indexes for table `posts_replies_history`
--
ALTER TABLE `posts_replies_history`
 ADD PRIMARY KEY (`id`), ADD KEY `posts_replies_id` (`posts_replies_id`);

--
-- Indexes for table `posts_replies_votes`
--
ALTER TABLE `posts_replies_votes`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts_views`
--
ALTER TABLE `posts_views`
 ADD PRIMARY KEY (`id`), ADD KEY `posts_id` (`posts_id`,`ipaddress`);

--
-- Indexes for table `posts_votes`
--
ALTER TABLE `posts_votes`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD KEY `access_token` (`access_token`), ADD KEY `login` (`login`), ADD KEY `email` (`email`), ADD KEY `karma` (`karma`), ADD KEY `login_2` (`login`), ADD KEY `notifications` (`notifications`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `activity_notifications`
--
ALTER TABLE `activity_notifications`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `irclog`
--
ALTER TABLE `irclog`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications_bounces`
--
ALTER TABLE `notifications_bounces`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `posts_bounties`
--
ALTER TABLE `posts_bounties`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts_history`
--
ALTER TABLE `posts_history`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `posts_notifications`
--
ALTER TABLE `posts_notifications`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `posts_replies`
--
ALTER TABLE `posts_replies`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts_replies_history`
--
ALTER TABLE `posts_replies_history`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts_replies_votes`
--
ALTER TABLE `posts_replies_votes`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts_views`
--
ALTER TABLE `posts_views`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `posts_votes`
--
ALTER TABLE `posts_votes`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
