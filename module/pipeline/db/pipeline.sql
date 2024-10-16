
CREATE TABLE `gamepipeline` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pipename` char(30) NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `gamepipelinestages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `gamepipeline` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dept` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `parent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'step',
  `desc` text NOT NULL,
  `estimate` mediumint(8) unsigned NOT NULL DEFAULT '4',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `gamegroupleaders` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `username`char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `autostory` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `project` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `gamepipelinestages` ADD `stepname` char(30) NOT NULL DEFAULT ''


CREATE TABLE `taskmilestone` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `deadline` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;