/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `durm_sessions` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `durm_users` (
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
  `status` enum('query','rejected','accepted','archive') DEFAULT 'query',
  `nick` tinytext,
  `age` date NOT NULL,
  `contacts` text,
  `contraindication` text,
  `chronicdesease` text,
  `wishes` text,
  `publicity` tinytext,
  `character_name` tinytext,
  `character_age` date NOT NULL,
  `country` tinytext,
  `birth` tinytext,
  `rank` tinytext,
  `quota` tinytext,
  `quenta` text,
  `wishes2` text,
  `photo_src` tinytext,
  `master_note` text,
  `updated` tinyint(3) unsigned DEFAULT NULL,
  `unread` tinyint(3) unsigned DEFAULT NULL,
  `go_aqua_mortis` tinyint(3) unsigned DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `active` (`active`),
  KEY `email` (`email`(20))
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `durm_config` (
  `key` tinytext NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `key` (`key`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `durm_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `authorid` int(10) unsigned NOT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
