CREATE TABLE `gamebooks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bookName` varchar(30) NOT NULL DEFAULT '',
  `registerDate` datetime NOT NULL,
  `type` MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT 0,
  `desc` text NOT NULL DEFAULT '',
  `price` mediumint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `gamebooksborrowlog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bookid` mediumint(8) unsigned NOT NULL,
  `reader` char(30) NOT NULL DEFAULT '',
  `borrowDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `borrowDays` mediumint(4) NOT NULL DEFAULT '0',
  `returned` tinyint(1) NOT NULL DEFAULT '0',
  `returnDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;