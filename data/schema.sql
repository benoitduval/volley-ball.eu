--
-- Table structure for table `comment`
--
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` varchar(255) NOT NULL DEFAULT '',
  `eventId` varchar(255) NOT NULL DEFAULT '',
  `comment` text,
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `event`
--
DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `date` timestamp NULL DEFAULT NULL,
  `comment` text,
  `groupId` int(5) DEFAULT NULL,
  `place` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(48) DEFAULT NULL,
  `zipCode` int(5) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `long` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `group`
--
DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brand` varchar(150) NOT NULL,
  `description` text,
  `address` text,
  `schedule` text,
  `gymnasium` varchar(150) NULL DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `long` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `usergroup`
--
DROP TABLE IF EXISTS `userGroup`;
CREATE TABLE `userGroup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `groupId` int(11) unsigned NOT NULL,
  `admin` tinyint(4) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`userId`, `groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `guest`
--
DROP TABLE IF EXISTS `guest`;
CREATE TABLE `guest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `response` tinyint(4) NOT NULL DEFAULT '0',
  `groupId` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`userId`, `eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `join`
--
DROP TABLE IF EXISTS `join`;
CREATE TABLE `join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `response` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`userId`, `groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `licence` int(11) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `display` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `recurent`
--
DROP TABLE IF EXISTS `recurent`;
CREATE TABLE `recurent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `groupId` int(5) DEFAULT NULL,
  `name` varchar(20) NOT NULL,
  `emailDay` varchar(15) DEFAULT NULL,
  `eventDay` varchar(15) DEFAULT NULL,
  `time` varchar(5) DEFAULT NULL,
  `place` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(48) DEFAULT NULL,
  `zipCode` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `match`
--
DROP TABLE IF EXISTS `match`;
CREATE TABLE `match` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(11) unsigned NOT NULL,
  `sets` varchar(20) NULL DEFAULT NULL,
  `victory` tinyint(4) NULL DEFAULT NULL,
  `set1Team1` varchar(4) NULL DEFAULT NULL,
  `set1Team2` varchar(4) NULL DEFAULT NULL,
  `set2Team1` varchar(4) NULL DEFAULT NULL,
  `set2Team2` varchar(4) NULL DEFAULT NULL,
  `set3Team1` varchar(4) NULL DEFAULT NULL,
  `set3Team2` varchar(4) NULL DEFAULT NULL,
  `set4Team1` varchar(4) NULL DEFAULT NULL,
  `set4Team2` varchar(4) NULL DEFAULT NULL,
  `set5Team1` varchar(4) NULL DEFAULT NULL,
  `set5Team2` varchar(4) NULL DEFAULT NULL,
  `debrief` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `notification`
--
DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `status` tinyint(4) NULL DEFAULT NULL,
  `notification` tinyint(4) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `notification`
--
DROP TABLE IF EXISTS `absent`;
CREATE TABLE `absent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `from` timestamp NULL DEFAULT NULL,
  `to` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;