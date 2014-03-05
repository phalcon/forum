/* Run this script to update your Phosphorum 1.0 to version 2.0 */

alter table posts add locked char(1) default 'N';
alter table users add moderator char(1) default 'N';
alter table posts add edited_at int(18) unsigned after modified_at;
alter table posts_replies add edited_at int(18) unsigned;
alter table users add karma int;
alter table posts_replies add votes_up int unsigned;
alter table posts_replies add votes_down int unsigned;
alter table posts add votes_up int unsigned after number_replies;
alter table posts add votes_down int unsigned after votes_up;
alter table users add votes int unsigned;
alter table users add votes_points int;
alter table posts add deleted int(3) default 0;
alter table posts add index `deleted`(deleted);
update posts set votes_up = (number_views / 50) - 1 where votes_up is null;
update posts set votes_up = null where votes_up = 0;
update users set votes_points = 0 where votes_points is null;

CREATE TABLE `posts_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `posts_id` (`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `posts_replies_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `posts_replies_id` (`posts_replies_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `posts_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `posts_replies_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_replies_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `created_at` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table posts_replies add accepted char(1) default 'N';
alter table posts add accepted_answer char(1) default 'N';

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

alter table posts_replies add in_reply_to_id int unsigned default 0 after users_id;

alter table users add index(email);
alter table users add index(karma);
alter table users add index(login);
alter table users add index(notifications);

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
