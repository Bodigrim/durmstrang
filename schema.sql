CREATE TABLE `hogwarts_config` (
  `key` tinytext NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `key` (`key`(32))
);
CREATE TABLE `hogwarts_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `authorid` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text,
  PRIMARY KEY (`id`)
);
CREATE TABLE `hogwarts_sessions` (
  `name` tinytext NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `pwhash` tinytext NOT NULL,
  `entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valid` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page` text NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  UNIQUE KEY `userid` (`userid`,`name`(32)),
  KEY `valid` (`valid`),
  KEY `name` (`name`(32))
);
CREATE TABLE `hogwarts_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` tinytext NOT NULL,
  `pwhash` tinytext NOT NULL,
  `pw` tinytext NOT NULL,
  `name` text NOT NULL,
  `city` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `activecode` tinytext NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(3) unsigned DEFAULT '0',
  `no_admin_mail` tinyint(3) unsigned DEFAULT '0',
  `status` enum('query','participant','participant_no_room','special_guest','committee','rejected','withdrawn') DEFAULT 'query',
  `nick` tinytext,
  `age` date NOT NULL,
  `contacts` text,
  `contraindication` text,
  `chronicdesease` text,
  `photo_src` tinytext,
  `updated` tinyint(3) unsigned DEFAULT NULL,
  `unread` tinyint(3) unsigned DEFAULT NULL,
  `group_owner` tinyint(3) unsigned DEFAULT NULL,
  `group_name` tinytext,
  `group_id` mediumint(8) unsigned NOT NULL,
  `room` tinytext,
  `payment` tinyint(3) unsigned DEFAULT '0',
  `payment_room` tinyint(3) unsigned DEFAULT '0',
  `payment_food` tinyint(3) unsigned DEFAULT '0',
  `surname` tinytext NOT NULL,
  `facebook` tinytext NOT NULL,
  `telegram` tinytext NOT NULL,
  `publicity` tinytext NOT NULL,
  `character_name` tinytext NOT NULL,
  `blood` tinytext NOT NULL,
  `quenta` text NOT NULL,
  `addendum` text NOT NULL,
  `possesions` text NOT NULL,
  `block` tinytext NOT NULL,
  `character_age` date NOT NULL,
  `fear` text NOT NULL,
  `wish` text NOT NULL,
  `antiwish` text NOT NULL,
  `speciality` SET('astronomy', 'herbology', 'history', 'muggle', 'transfiguration', 'runes', 'dark', 'potions', 'charms', 'divination', 'theory') NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `active` (`active`),
  KEY `email` (`email`(20))
);
CREATE TABLE `hogwarts_texts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header` text NOT NUll,
  `text_public` text NOT NULL,
  `text_private` text NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE `hogwarts_text_rights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `textid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `textid_userid` (`textid`, `userid`)
);
