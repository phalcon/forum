CREATE TABLE IF NOT EXISTS `topic_tracking` (
  `user_id` INT(11) NOT NULL,
  `topic_id` TEXT NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `posts_poll_options` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `posts_id` INT(10) UNSIGNED NOT NULL,
  `title` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_poll_options_post_id` (`posts_id`),
  FOREIGN KEY (`posts_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
