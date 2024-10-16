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


CREATE TABLE `gametaskinternal` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,

  `version` mediumint(8) NOT NULL DEFAULT '0',
  `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `owner` char(30) NOT NULL DEFAULT '',
  `product` MEDIUMINT(8) unsigned NOT NULL DEFAULT 0,

  `title` VARCHAR(20) NOT NULL DEFAULT '',
  `count` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 1,

  --size
  `sizeType` TINYINT(1) NOT NULL DEFAULT 0,
  `sizeWidth` MEDIUMINT(4) NOT NULL DEFAULT 0,
  `sizeHeight` MEDIUMINT(4) NOT NULL DEFAULT 0,

  `desc`  text NOT NULL DEFAULT '',
  `srcResPath`  text NOT NULL DEFAULT '',
  `gameResPath`  text NOT NULL DEFAULT '',
  `pri` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 3,

  `workhour` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 4,
  `assignedTo` char(30) NOT NULL DEFAULT '',

  `completed` TINYINT(1) NOT NULL DEFAULT 0,
  `closed` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `gametaskinternal` ADD `createDate` datetime NOT NULL DEFAULT '2018-01-26';
ALTER TABLE `gametaskinternal` ADD `lastUpdateDate` datetime NOT NULL DEFAULT '2018-01-26';
ALTER TABLE `gametaskinternal` ADD `lastUpdateBy` char(30) NOT NULL DEFAULT '';

ALTER TABLE `gametaskinternal` ADD `completeDate` datetime NOT NULL DEFAULT '2018-01-26';


CREATE TABLE `gametaskinternalversion` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `deadline` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `gamegroupleaders` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `username`char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;