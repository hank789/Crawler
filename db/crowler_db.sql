CREATE TABLE `hosts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `host` varchar(124) NOT NULL,
  `https` int(1) NOT NULL,
  `port` int(6) NOT NULL DEFAULT '80',
  `url_follow_rule` varchar(1024) DEFAULT NULL,
  `link_post_date_rule` varchar(124) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`host`,`port`,`https`),
  KEY `host` (`host`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `urls` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `host_id` bigint(20) NOT NULL,
  `path` varchar(255) NOT NULL,
  `linktext` varchar(512),
  `link_post_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_path` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;