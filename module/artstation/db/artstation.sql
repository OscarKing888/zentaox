CREATE TABLE `artstation` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `owner` char(30) NOT NULL DEFAULT '',
  `createDate` datetime NOT NULL,

  `product` mediumint(8) unsigned NOT NULL,
  `story` mediumint(8) unsigned NOT NULL DEFAULT 0,

  `type` tinyint(1) unsigned NOT NULL default 0,
  `tags` text NOT NULL DEFAULT  '',

  `title` VARCHAR(40) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',

  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `artstation` ADD `confirmdesign` mediumint(8) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `artstation` ADD `confirmdate` datetime NOT NULL DEFAULT '0000-00-00';


CREATE TABLE `artstationlike` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `imageId` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `user` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `artstationcomment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `imageid` mediumint(8) unsigned NOT NULL default 0,
  `owner` char(30) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `content` text NOT NULL DEFAULT '',
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
