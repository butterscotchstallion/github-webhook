CREATE TABLE `github_push_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payload` text NOT NULL,
  `number_of_commits` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `notification_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;