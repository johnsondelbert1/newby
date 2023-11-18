ALTER TABLE `site_info` ADD `user_creation` VARCHAR(255) NOT NULL DEFAULT 'admin' AFTER `logo_url`, ADD `require_email_activation` BOOLEAN NOT NULL DEFAULT TRUE AFTER `user_creation`;
ALTER TABLE `users` ADD `created_via` VARCHAR(255) NOT NULL DEFAULT 'admin' AFTER `rank`,  ADD `data_public_not_visible` TEXT NOT NULL AFTER `last_logged_in`;
CREATE TABLE IF NOT EXISTS `users_custom_fields` (
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE(`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
INSERT INTO `users_custom_fields`
(`uid`)
SELECT `id`
FROM `users`;
CREATE TABLE IF NOT EXISTS `custom_field_users_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `desc` text NOT NULL,
  `placeholder` varchar(255) NOT NULL,
  `maxchar` int(11) NOT NULL,
  `validate` varchar(255) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;
ALTER TABLE `users` ADD `activation_code` VARCHAR(255) NOT NULL AFTER `old_pass`, ADD `activation_code_date` DATETIME NOT NULL AFTER `activation_code`, ADD `activated_email` BOOLEAN NOT NULL DEFAULT TRUE AFTER `activation_code_date`, ADD `approved_admin` BOOLEAN NOT NULL DEFAULT TRUE AFTER `activated_email`, ADD `banned` BOOLEAN NOT NULL DEFAULT FALSE AFTER `approved_admin`;
UPDATE `site_info` SET `version` = '1.4' WHERE `site_info`.`id` = 1;