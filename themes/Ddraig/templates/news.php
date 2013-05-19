<?php

// Ported to News Template to support news infusions

function render_news($subject, $news, $info) {
global $locale, $settings;
opentable($subject, "post", $info, "N");
	echo "<ul class='news-info'>\n";
	//Author
	echo "<li class='author'>".profile_link($info['user_id'], $info['user_name'], $info['user_status'])."</li>\n";
	//Date
	echo "<li class='dated'>".showdate("%d %b %Y", $info['news_date'])."</li>\n";
	//Category
	echo "<li class='cat'>\n";
		if ($info['cat_id']) { echo "<a href='".BASEDIR."news_cats.php?cat_id=".$info['cat_id']."'>".$info['cat_name']."</a>\n";
	} else { echo "<a href='".BASEDIR."news_cats.php?cat_id=0'>".$locale['global_080']."</a>"; }
	echo "</li>\n";
	//Reads
	if ($info['news_ext'] == "y" || ($info['news_allow_comments'] && $settings['comments_enabled'] == "1")) {
	echo "<li class='reads'>\n";
		echo $info['news_reads'].$locale['global_074']; 
	echo "</li>\n";}
	//Comments
	if ($info['news_allow_comments'] && $settings['comments_enabled'] == "1") { echo "<li class='comments'><a ".(isset($_GET['readmore']) ? "class='scroll'" : "")." href='".BASEDIR."news.php?readmore=".$info['news_id']."#comments'>".$info['news_comments']."".($info['news_comments'] == 1 ? $locale['global_073b'] : $locale['global_073'])."</a></li>\n"; }
	echo "</ul>\n";
	//The message
	echo $info['cat_image'].$news;

	//Read more button
	if (!isset($_GET['readmore']) && $info['news_ext'] == "y") {
		echo "<div class='flright'><a href='".BASEDIR."news.php?readmore=".$info['news_id']."' class='button'><img alt='".$locale['global_072']."' class='rightarrow icon' src='".THEME."images/blank.gif' />".$locale['global_072']."</a></div>\n";
	}
closetable();
}


?>