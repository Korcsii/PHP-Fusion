<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Infusion: News
| Filename: infusion.php
| Version: 1.0
| Author: PHP-Fusion Web Team Dev 8
| Web: 8.php-fusion.net
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (file_exists(INFUSIONS."news_infusion/locale/".$settings['locale'].".php")) {
    include INFUSIONS."news_infusion/locale/".$settings['locale'].".php";
} else {
    include INFUSIONS."news_infusion/locale/English.php";
}

$inf_title = $locale['adg_title'];
$inf_description = $locale['adg_description'];
$inf_version = "1.0";
$inf_developer = "Hien";
$inf_email = "admin@phpfusion.me";
$inf_weburl = "http://8.php-fusion.net";

$inf_folder = "news_infusion";


# SQL
$inf_newtable[1] = DB_NEWS_SETTINGS." (
	ns_id` int(10) NOT NULL AUTO_INCREMENT,
  	ns_news_per_page mediumint(8) NOT NULL DEFAULT '16',
  	ns_news_showcase tinyint(1) NOT NULL DEFAULT '1',
  	ns_news_column mediumint(5) NOT NULL DEFAULT '2',
  	ns_news_template varchar(200) NOT NULL,
  	ns_admin mediumint(8) NOT NULL,
  	ns_log text NOT NULL,
  	ns_datestamp text NOT NULL,
  	PRIMARY KEY (ns_id)
) ".(strnatcmp(mysql_get_server_info(),'4.0.18') >= 0 ? "ENGINE" : "TYPE")."=MyISAM;";

$inf_newtable[2] = DB_NEWS." (
	news_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  	news_subject varchar(200) NOT NULL DEFAULT '',
  	news_tags text NOT NULL,
  	news_image varchar(100) NOT NULL DEFAULT '',
  	news_cat mediumint(8) unsigned NOT NULL DEFAULT '0',
  	news_teaser text NOT NULL,
  	news_body text NOT NULL,
  	news_datestamp int(10) unsigned NOT NULL DEFAULT '0',
  	news_start int(10) unsigned NOT NULL DEFAULT '0',
  	news_end int(10) unsigned NOT NULL DEFAULT '0',
  	news_degrade int(10) NOT NULL,
  	news_meta text NOT NULL,
  	news_keywords text NOT NULL,
  	news_author tinyint(10) NOT NULL,
  	news_rights tinyint(3) NOT NULL,
  	news_source text NOT NULL,
  	news_reads int(10) unsigned NOT NULL DEFAULT '0',
  	news_enable tinyint(1) unsigned NOT NULL DEFAULT '0',
  	news_access int(3) NOT NULL,
  	news_visibility int(3) NOT NULL,
  	news_priority tinyint(1) unsigned NOT NULL DEFAULT '0',
  	news_allow_comments tinyint(1) unsigned NOT NULL DEFAULT '1',
  	news_allow_ratings tinyint(1) unsigned NOT NULL DEFAULT '1',
  	news_allow_poll tinyint(1) NOT NULL,
  	news_poll_name varchar(200) NOT NULL,
  	news_poll_start int(10) NOT NULL,
  	news_poll_end int(10) NOT NULL,
  	news_poll_options text NOT NULL,
  PRIMARY KEY (news_id)
) ".(strnatcmp(mysql_get_server_info(),'4.0.18') >= 0 ? "ENGINE" : "TYPE")."=MyISAM;";


// Here
$inf_newtable[3] = DB_NEWS_CATS." (
  news_cat_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  news_cat_name varchar(100) NOT NULL DEFAULT '',
  news_cat_image varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`news_cat_id)
) ".(strnatcmp(mysql_get_server_info(),'4.0.18') >= 0 ? "ENGINE" : "TYPE")."=MyISAM;";


$inf_newtable[3] = DB_NEWS_CATS." (
  news_cat_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  news_cat_name varchar(100) NOT NULL DEFAULT '',
  news_cat_image varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`news_cat_id`)
) ".(strnatcmp(mysql_get_server_info(),'4.0.18') >= 0 ? "ENGINE" : "TYPE")."=MyISAM;";

  `news_comment_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `news_comment_item_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `news_comment_name` varchar(50) NOT NULL DEFAULT '',
  `news_comment_message` text NOT NULL,
  `news_comment_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `news_comment_ip` varchar(45) NOT NULL DEFAULT '',
  `news_comment_ip_type` tinyint(1) unsigned NOT NULL DEFAULT '4',
  `news_comment_hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`news_comment_id`),
  KEY `news_comment_datestamp` (`news_comment_datestamp`)



$inf_droptable[1] = DB_NEWS;
$inf_droptable[1] = DB_NEWS;
$inf_droptable[1] = DB_NEWS;
$inf_droptable[1] = DB_NEWS;


/*
if(isset($_POST['infuse'])){ 
    dbquery(" ALTER TABLE ".DB_USERS."  
                ADD user_newsletter tinyint(1) not null default '0';
            ");
}
if(isset($_GET['defuse'])){ 
    dbquery(" ALTER TABLE ".DB_USERS."  
                DROP user_newsletter;
            ");
}
*/






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








$inf_sitelink[1] = array(
	"title" => $locale['adg_link'],
	"url" => "../../adgallery.php",
	"visibility" => "0"
);



?>