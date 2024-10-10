CREATE TABLE IF NOT EXISTS `gameblog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `owner` char(30) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `product` mediumint(8) unsigned NOT NULL,
  `content` text NOT NULL,
  `contentimages` text NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `gameblog` ADD COLUMN IF NOT EXISTS `milestone` tinyint(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS `gameuserinfo` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `owner` char(30) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `absent` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `artstation` (
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

ALTER TABLE `artstation` ADD COLUMN IF NOT EXISTS `confirmdesign` mediumint(8) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `artstation` ADD COLUMN IF NOT EXISTS `confirmdate` datetime NOT NULL DEFAULT '0000-00-00';


CREATE TABLE IF NOT EXISTS `artstationlike` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `imageId` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `user` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `artstationcomment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `imageid` mediumint(8) unsigned NOT NULL default 0,
  `owner` char(30) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `content` text NOT NULL DEFAULT '',
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `gamebooks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bookName` varchar(30) NOT NULL DEFAULT '',
  `registerDate` datetime NOT NULL,
  `type` MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT 0,
  `desc` text NOT NULL DEFAULT '',
  `price` mediumint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gamebooksborrowlog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bookid` mediumint(8) unsigned NOT NULL,
  `reader` char(30) NOT NULL DEFAULT '',
  `borrowDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `borrowDays` mediumint(4) NOT NULL DEFAULT '0',
  `returned` tinyint(1) NOT NULL DEFAULT '0',
  `returnDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;





CREATE TABLE IF NOT EXISTS `gameblog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `owner` char(30) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `product` mediumint(8) unsigned NOT NULL,
  `content` text NOT NULL,
  `contentimages` text NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `gametaskinternal` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,

  `version` mediumint(8) NOT NULL DEFAULT '0',
  `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `owner` char(30) NOT NULL DEFAULT '',
  `product` MEDIUMINT(8) unsigned NOT NULL DEFAULT 0,

  `title` VARCHAR(20) NOT NULL DEFAULT '',
  `count` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 1,


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

ALTER TABLE `gametaskinternal` ADD COLUMN IF NOT EXISTS `createDate` datetime NOT NULL DEFAULT '2018-01-26';
ALTER TABLE `gametaskinternal` ADD COLUMN IF NOT EXISTS `lastUpdateDate` datetime NOT NULL DEFAULT '2018-01-26';
ALTER TABLE `gametaskinternal` ADD COLUMN IF NOT EXISTS `lastUpdateBy` char(30) NOT NULL DEFAULT '';

ALTER TABLE `gametaskinternal` ADD COLUMN IF NOT EXISTS `completeDate` datetime NOT NULL DEFAULT '2018-01-26';


CREATE TABLE IF NOT EXISTS `gametaskinternalversion` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `deadline` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gamegroupleaders` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `username`char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `gamemeeting` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `pri` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `assignedTo` varchar(30) NOT NULL,
  `createDate` date NOT NULL,
  `deadline` date NOT NULL,
  `status` enum('wait','doing','done') NOT NULL DEFAULT 'wait',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;









CREATE TABLE IF NOT EXISTS `gamepipeline` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pipename` char(30) NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `gamepipelinestages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `gamepipeline` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dept` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `parent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'step',
  `desc` text NOT NULL,
  `estimate` mediumint(8) unsigned NOT NULL DEFAULT '4',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gamegroupleaders` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `username`char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `autostory` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `project` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `gamepipelinestages` ADD COLUMN IF NOT EXISTS `stepname` char(30) NOT NULL DEFAULT '';


CREATE TABLE IF NOT EXISTS `taskmilestone` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `deadline` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;






ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `pipeline` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `createtype` varchar(40) NOT NULL DEFAULT "";

ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `milestone` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0;








CREATE TABLE IF NOT EXISTS `timeline` (
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


CREATE TABLE IF NOT EXISTS `productmilestone` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `deadline` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `productmilestonestory` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project` mediumint(8) unsigned NOT NULL default '0',
  `productMilestone` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `story` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `story` (`productMilestone`, `story`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `checkBy` varchar(30) NOT NULL DEFAULT "";
ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `checkedBy` varchar(30) NOT NULL DEFAULT "";
ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `checked` enum('0','1') NOT NULL default '0';
ALTER TABLE `zt_task` CHANGE COLUMN `checked` `checkedStatus` enum('0','1') NOT NULL default '0';
ALTER TABLE `zt_task` ADD COLUMN IF NOT EXISTS `checkDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `zt_task` MODIFY COLUMN `status` enum('wait','doing','done','pause','cancel','closed','checked') NOT NULL default 'wait';





UPDATE `zt_task` SET checkBy=openedBy;

SELECT * from `zt_task` where name  like '%-卡%';

UPDATE `zt_task` SET name=replace(name, '-卡', '月卡') where name like '%-卡%';

CREATE TABLE IF NOT EXISTS `test_tasks` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `product` mediumint(8) unsigned NOT NULL default '0',
  `status` enum('wait','done', 'closed') NOT NULL DEFAULT 'wait',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `test_tasks` ADD COLUMN IF NOT EXISTS `testComments` text NOT NULL DEFAULT "";
ALTER TABLE `test_tasks` ADD COLUMN IF NOT EXISTS `deleted` enum('0','1') NOT NULL default '0';
ALTER TABLE `test_tasks` ADD COLUMN IF NOT EXISTS `createBy`char(30) NOT NULL DEFAULT '';
ALTER TABLE `test_tasks` MODIFY COLUMN `status` enum('wait','doing','done','pause','cancel','closed','checked') NOT NULL default 'wait';


CREATE TABLE IF NOT EXISTS `test_task_stories` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `testtask` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT  0,
  `story` mediumint(8) unsigned NOT NULL default '0',
  `assignedTo` varchar(30) NOT NULL,
  `status` enum('wait', 'fail', 'done', 'cancel') NOT NULL DEFAULT 'wait',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_bug` ADD COLUMN IF NOT EXISTS `dept` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0;
UPDATE zt_bug LEFT JOIN zt_user ON zt_bug.assignedTo = zt_user.account SET zt_bug.dept = zt_user.dept;

ALTER TABLE `zt_team` ADD COLUMN IF NOT EXISTS `project` mediumint(8) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `zt_team` ADD COLUMN IF NOT EXISTS `task` mediumint(8) NOT NULL DEFAULT '0';