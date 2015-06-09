SET NAMES utf8;
SET time_zone = SYSTEM;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `users` (`id`, `name`, `login`, `email`, `gravatar_id`, `token_type`, `access_token`, `created_at`, `modified_at`, `notifications`, `digest`, `timezone`, `moderator`, `karma`, `votes`, `votes_points`, `banned`, `theme`) VALUES
  ('1', 'John Doe', NULL, 'john@doe.com', NULL, NULL, NULL, '1433875400', '1433875643', 'P', 'Y', 'Europe/London', 'N', '45', '0', '45', 'N', 'D');

INSERT INTO `categories` (`id`, `name`, `description`, `slug`, `number_posts`, `no_bounty`, `no_digest`) VALUES
  ('2', 'Awesome Category', 'This is description for awesome category', 'awesome-category', 1, 'N', 'N');

INSERT INTO `posts` (`id`, `users_id`, `categories_id`, `title`, `slug`, `content`, `number_views`, `number_replies`, `votes_up`, `votes_down`, `sticked`, `created_at`, `modified_at`, `edited_at`, `status`, `locked`, `deleted`, `accepted_answer`)  VALUES
  ('1', '1', '1', 'Repellendus qui voluptatem', 'repellendus-qui-voluptatem', 'Repellendus qui voluptatem consectetur sint sit est sunt. Voluptas odio sequi eum ut natus quaerat. Non omnis enim qui et et.', '50', '', NULL, NULL, 'N', '1433876366', '1433876366', NULL, 'A', 'N', '0', 'N');

SET foreign_key_checks = 1;
