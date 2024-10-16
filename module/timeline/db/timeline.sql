CREATE TABLE `timeline` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `datebegin` datetime NOT NULL,
  `dateend` datetime NOT NULL,
  `type` tinyint(1) unsigned NOT NULL default 0,
  `tags` text NOT NULL DEFAULT  '',
  `content` text NOT NULL,
  `contentimages` text NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
