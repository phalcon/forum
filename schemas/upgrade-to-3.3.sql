ALTER TABLE `users` ADD COLUMN(
    `admin` char(1) DEFAULT 'N', 
) AFTER `moderator`;