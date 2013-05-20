<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
| CVS Version: 1.00
| Author: INSERT NAME HERE
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

if (!defined("DB_NEWS_SETTINGS")) {
	define("DB_NEWS_SETTINGS", DB_PREFIX."news_settings");
}

if (!defined("DB_NEWS")) {
	define("DB_NEWS", DB_PREFIX."news");
}

if (!defined("DB_NEWS_CATS")) {
	define("DB_NEWS_CATS", DB_PREFIX."news_cats");
}

if (!defined("DB_NEWS_COMMENTS")) {
	define("DB_NEWS_COMMENTS", DB_PREFIX."news_comments");
}

if (!defined("DB_NEWS_POLL")) {
	define("DB_NEWS_POLL", DB_PREFIX."news_poll");
}


?>
