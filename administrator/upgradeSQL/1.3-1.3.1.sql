ALTER TABLE `pages` CHANGE `visible` `visible` VARCHAR(1024) NOT NULL DEFAULT 'a:1:{i:0;s:3:"any";}';
UPDATE `pages` SET `visible`='a:1:{i:0;s:3:"any";}';
CREATE TABLE IF NOT EXISTS `blog_comments` (
  `bc_id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `poster_id` int(11) NOT NULL,
  `date_posted` datetime NOT NULL,
  `editor_id` int(11) NOT NULL,
  `date_edited` datetime NOT NULL,
  PRIMARY KEY (`bc_id`),
  UNIQUE KEY `ic_id` (`bc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE `blog` ADD `comments_allowed` BOOLEAN NOT NULL DEFAULT TRUE ;
ALTER TABLE `users` ADD `old_pass` BOOLEAN NOT NULL DEFAULT TRUE ;
ALTER TABLE `users` CHANGE `hashed_pass` `hashed_pass` VARCHAR(255) NOT NULL;
ALTER TABLE `users` ADD `chng_pass_authcode` VARCHAR(255) NOT NULL , ADD `chng_pass_authcode_date` DATETIME NOT NULL ;
UPDATE `site_info` SET `version` = '1.3.1' WHERE `site_info`.`id` = 1;