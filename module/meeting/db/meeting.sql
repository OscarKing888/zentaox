CREATE TABLE `gamemeeting` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `pri` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `assignedTo` varchar(30) NOT NULL,
  `createDate` date NOT NULL,
  `deadline` date NOT NULL,
  `status` enum('wait','doing','done') NOT NULL DEFAULT 'wait',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;