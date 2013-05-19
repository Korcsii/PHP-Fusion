CREATE TABLE `fusion67513_news` (
  `news_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `news_subject` varchar(200) NOT NULL DEFAULT '',
  `news_image` varchar(100) NOT NULL DEFAULT '',
  `news_image_t1` varchar(100) NOT NULL DEFAULT '',
  `news_image_t2` varchar(100) NOT NULL DEFAULT '',
  `news_cat` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `news_news` text NOT NULL,
  `news_extended` text NOT NULL,
  `news_breaks` char(1) NOT NULL DEFAULT '',
  `news_name` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `news_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `news_start` int(10) unsigned NOT NULL DEFAULT '0',
  `news_end` int(10) unsigned NOT NULL DEFAULT '0',
  `news_visibility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `news_reads` int(10) unsigned NOT NULL DEFAULT '0',
  `news_draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `news_sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `news_allow_comments` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `news_allow_ratings` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `news_template` varchar(100) NOT NULL,
  PRIMARY KEY (`news_id`),
  KEY `news_datestamp` (`news_datestamp`),
  KEY `news_reads` (`news_reads`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `fusion67513_news` (`news_id`, `news_subject`, `news_image`, `news_image_t1`, `news_image_t2`, `news_cat`, `news_news`, `news_extended`, `news_breaks`, `news_name`, `news_datestamp`, `news_start`, `news_end`, `news_visibility`, `news_reads`, `news_draft`, `news_sticky`, `news_allow_comments`, `news_allow_ratings`, `news_template`) VALUES
(1, 'News Templates', 'blog_icons.jpg', 'blog_icons_t1.jpg', 'blog_icons_t2.jpg', 1, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '<strong>Extension</strong>\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'y', 1, 1368115470, 0, 0, 0, 0, 0, 0, 0, 0, ''),
(2, 'Beautifully Done', 'blog_themes.jpg', 'blog_themes_t1.jpg', 'blog_themes_t2.jpg', 2, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '<strong>Extended:</strong>\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'y', 1, 1368115544, 0, 0, 0, 3, 0, 0, 1, 1, '');

CREATE TABLE `fusionQ56J1_news_cats` (
  `news_cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `news_cat_name` varchar(100) NOT NULL DEFAULT '',
  `news_cat_image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`news_cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

INSERT INTO `fusion67513_news_cats` (`news_cat_id`, `news_cat_name`, `news_cat_image`) VALUES
(1, 'Bugs', 'bugs.gif'),
(2, 'Downloads', 'downloads.gif'),
(3, 'Games', 'games.gif'),
(4, 'Graphics', 'graphics.gif'),
(5, 'Hardware', 'hardware.gif'),
(6, 'Journal', 'journal.gif'),
(7, 'Members', 'members.gif'),
(8, 'Mods', 'mods.gif'),
(9, 'Movies', 'movies.gif'),
(10, 'Network', 'network.gif'),
(11, 'News', 'news.gif'),
(12, 'PHP-Fusion', 'php-fusion.gif'),
(13, 'Security', 'security.gif'),
(14, 'Software', 'software.gif'),
(15, 'Themes', 'themes.gif'),
(16, 'Windows', 'windows.gif');
