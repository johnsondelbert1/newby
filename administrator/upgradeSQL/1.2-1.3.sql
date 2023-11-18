ALTER TABLE `pages` ADD `doc_folder` VARCHAR(128) NOT NULL AFTER `galleries`;
UPDATE `site_info` SET `version` = '1.3' WHERE `site_info`.`id` = 1;
CREATE TABLE IF NOT EXISTS `style_colors` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `c_name` varchar(64) NOT NULL,
  `color_hex` varchar(7) NOT NULL,
  `date_created` datetime NOT NULL,
  `creator` int(11) NOT NULL,
  `date_edited` datetime NOT NULL,
  `editor` int(11) NOT NULL,
  `deletable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cid`),
  UNIQUE KEY `id` (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;
INSERT INTO `style_colors` (`cid`, `c_name`, `color_hex`, `date_created`, `creator`, `date_edited`, `editor`, `deletable`) VALUES
(1, 'Usable Colors', '#FFFFFF', '0000-00-00 00:00:00', 1, '2015-07-11 01:20:47', 1, 0),
(10, 'Site BG', '#FFCB21', '2015-07-10 13:27:46', 1, '2015-07-11 01:20:47', 1, 1),
(11, 'Content BG', '#C7C7C7', '2015-07-10 13:28:28', 1, '2015-07-11 01:20:47', 1, 1),
(12, 'Menu', '#065475', '2015-07-10 13:29:02', 1, '2015-07-11 01:20:47', 1, 1),
(13, 'Menu Hover', '#032E40', '2015-07-10 13:29:37', 1, '2015-07-11 01:20:47', 1, 1),
(19, '', '#000000', '2015-07-10 14:27:12', 1, '2015-07-11 01:20:47', 1, 1);
CREATE TABLE IF NOT EXISTS `css_selectors` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `s_name` varchar(128) NOT NULL,
  `style_color_id` int(11) NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `id` (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;