CREATE TABLE `gameblog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `owner` char(30) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `product` mediumint(8) unsigned NOT NULL,
  `content` text NOT NULL,
  `contentimages` text NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `gameblog` ADD `milestone` tinyint(1) NOT NULL DEFAULT 0;