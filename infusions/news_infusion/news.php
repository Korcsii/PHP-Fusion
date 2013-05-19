<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: news.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------+
| Infusions-Mod: Hien 2013
| Must have Atom SDK installed in themes/templates to work
| Few functions are using it.
+--------------------------------------------------------*/


require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once THEMES."templates/atomcore.php"; // atom framework.

load_bootstrap();

// Predefined variables, do not edit these values
$i = 0;

// Number of news displayed
//$items_per_page = $settings['newsperpage'];

add_to_title($locale['global_200'].$locale['global_077']);


// Version 1.0 so just lay with simple stuff first. We can always expand to moon later.
// this will be always one row.
	define ("NEWS_SETTINGS", DB_PREFIX."news_settings");
	define ("DB_NEWS_COMMENTS", DB_PREFIX."news_comments");
	
	$news_settings_result = dbquery("SELECT * FROM ".NEWS_SETTINGS." ORDER BY ns_id ASC");
	$newsdata = dbarray($news_settings_result);
	
	$news_per_page = stripinput($newsdata['ns_news_per_page']); // take off settings news per page
	$news_showcase = stripinput($newsdata['ns_news_showcase']); // news shocase 1 for on 0 for off
	$col = stripinput($newsdata['ns_news_column']); // column of view 3 2 or 1 use dropdown.
	$news_tag = stripinput($newsdata['ns_news_tag']);
	$news_template = stripinput($newsdata['ns_news_template']); // CHANGES: store dir/file

	if (!isset($_GET['readmore']) || !isnum($_GET['readmore'])) {
	$rows = dbcount("(news_id)", DB_NEWS, groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0'");
	
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

	
	
	
			function render_news_title($news_subject, $news_info) {
			echo "<div class='row-fluid'>\n";
			echo "<div class='span3'><img src='".FUSION_SELF."images/".$news_info['news_image']."'></div>\n";
			echo "<div class='span9'>\n";
			echo "<h4><a href=''>$news_subject</a></h4>\n";
			echo "</div>\n";
			echo "</div>\n";
			}
	
	
	
	if ($rows) {
	
	function get_newscat($category_id) {
	if ($category_id !==0) { $sql = "AND news_cat_id='".stripinput($category_id)."'"; } else {
	$sql = "";  }
	}

	$result = dbquery(
			"SELECT tn.*, tc.*, tu.user_id, tu.user_name, tu.user_status
			FROM ".DB_NEWS." tn
			LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
			LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
			WHERE ".groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().")
			AND (news_end='0'||news_end>=".time().") AND news_draft='0' ".$sql."
			GROUP BY news_id
			ORDER BY news_sticky DESC, news_datestamp DESC LIMIT ".$_GET['rowstart'].",".$news_per_page."");
	
	$numrows = dbrows($result);
	
	
	// Viewing Options
	function news($category_id, $limit, $col, $news_template, $shownnav) {

	get_newscat($category_id);

	global $result, $numrows;
		
	$i = 1;
	
			if ($col !==1) { echo "<div class='row-fluid'>\n"; }
			while (($data = dbarray($result)) && $limit>=$i) {
			if ($col ==1) { echo "<div class='row-fluid'>\n"; } 

			$i++;
			
			
			$comments = dbcount("(news_comment_id)", DB_NEWS_COMMENTS." WHERE news_comment_hidden='0' AND news_comment_item_id='".$data['news_id']."'");
			$news_cat_image = "";
			$news_subject = "<a name='news_".$data['news_id']."' id='news_".$data['news_id']."'></a>".stripslashes($data['news_subject']);
			$news_cat_image = "<a href='".($settings['news_image_link'] == 0 ? "news_cats.php?cat_id=".$data['news_cat']
																				: FUSION_SELF."?readmore=".$data['news_id'] )."'>";
			if ($data['news_image_t2'] && $settings['news_image_frontpage'] == 0) {
				$news_cat_image .= "<img src='".IMAGES_N_T.$data['news_image_t2']."' alt='".$data['news_subject']."' class='news-category' /></a>";
			} elseif ($data['news_cat_image']) {
				$news_cat_image .= "<img src='".get_image("nc_".$data['news_cat_name'])."' alt='".$data['news_cat_name']."' class='news-category' /></a>";
			} else {
				$news_cat_image = "";
			}
			$news_news = preg_replace("/<!?--\s*pagebreak\s*-->/i", "", ($data['news_breaks'] == "y" ? nl2br(stripslashes($data['news_news'])) : stripslashes($data['news_news'])));
			$news_info = array(
				"news_id" => $data['news_id'],
				"user_id" => $data['user_id'],
				"user_name" => $data['user_name'],
				"user_status" => $data['user_status'],
				"news_date" => $data['news_datestamp'],
				"cat_id" => $data['news_cat'],
				"cat_name" => $data['news_cat_name'],
				"cat_image" => $news_cat_image,
				"news_subject" => $data['news_subject'],
				"news_ext" => $data['news_extended'] ? "y" : "n",
				"news_reads" => $data['news_reads'],
				"news_comments" => $comments,
				"news_allow_comments" => $data['news_allow_comments'],
				"news_sticky" => $data['news_sticky']
			);

			echo "<!--news_prepost_".$i."-->\n";

			echo "<div class='".span_splitter($col)."'>\n"; 
			
			switch($news_template) {
			case "1":	render_news_1($news_subject, $news_news, $news_info);
				break;
			case "2":	render_news_2($news_subject, $news_news, $news_info);
				break;
			case "3": 	render_news_3($news_subject, $news_news, $news_info);
				break;
			default:	render_news($news_subject, $news_news, $news_info);				
			}
			
			
			echo "</div>\n";

			if ($col == 1) { echo "</div>\n"; }
		}
			if ($col !==1) {echo "</div>\n"; }

		echo "<!--sub_news_idx-->\n";
		
		if ($shownav == 1) {
		if ($rows > $news_per_page) echo "<div align='center' style=';margin-top:5px;'>\n".makepagenav($_GET['rowstart'],$news_per_page,$rows,3)."\n</div>\n";
		}
	}
	
	
	
	
	} else {
		opentable($locale['global_077']);
		echo "<div style='text-align:center'><br />\n".$locale['global_078']."<br /><br />\n</div>\n";
		closetable();
	}
} else 

{
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
	$result = dbquery(
		"SELECT tn.*, tc.*, tu.user_id, tu.user_name, tu.user_status FROM ".DB_NEWS." tn
		LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
		LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
		WHERE ".groupaccess('news_visibility')." AND news_id='".$_GET['readmore']."' AND news_draft='0'
		LIMIT 1"
	);
	if (dbrows($result)) {
	//	include INCLUDES."comments_include.php";
	//	include INCLUDES."ratings_include.php";
		$data = dbarray($result);
		if (!isset($_POST['post_comment']) && !isset($_POST['post_rating'])) {
			$result2 = dbquery("UPDATE ".DB_NEWS." SET news_reads=news_reads+1 WHERE news_id='".$_GET['readmore']."'");
			$data['news_reads']++;
		}
		$news_cat_image = "";
		$news_subject = $data['news_subject'];
		if ($data['news_image_t1'] && $settings['news_image_readmore'] == "0") {
			$img_size = @getimagesize(IMAGES_N.$data['news_image']);
			$news_cat_image = "<a href=\"javascript:;\" onclick=\"window.open('".IMAGES_N.$data['news_image']."','','scrollbars=yes,toolbar=no,status=no,resizable=yes,width=".($img_size[0]+20).",height=".($img_size[1]+20)."')\"><img src='".IMAGES_N_T.$data['news_image_t1']."' alt='".$data['news_subject']."' class='news-category' /></a>";
		} elseif ($data['news_cat_image']) {
			$news_cat_image = "<a href='news_cats.php?cat_id=".$data['news_cat']."'><img src='".get_image("nc_".$data['news_cat_name'])."' alt='".$data['news_cat_name']."' class='news-category' /></a>";
		}
		$news_news = preg_split("/<!?--\s*pagebreak\s*-->/i", $data['news_breaks'] == "y" ? nl2br(stripslashes($data['news_extended'] ? $data['news_extended'] : $data['news_news'])) : stripslashes($data['news_extended'] ? $data['news_extended'] : $data['news_news']));    
		$pagecount = count($news_news);
		$news_info = array(
			"news_id" => $data['news_id'],
			"user_id" => $data['user_id'],
			"user_name" => $data['user_name'],
			"user_status" => $data['user_status'],
			"news_date" => $data['news_datestamp'],
			"cat_id" => $data['news_cat'],
			"cat_name" => $data['news_cat_name'],
			"cat_image" => $news_cat_image,
			"news_subject" => $data['news_subject'],
			"news_ext" => "n",
			"news_reads" => $data['news_reads'],
			"news_comments" => dbcount("(comment_id)", DB_COMMENTS, "comment_type='N' AND comment_item_id='".$data['news_id']."' AND comment_hidden='0'"),
			"news_allow_comments" => $data['news_allow_comments'],
			"news_sticky" => $data['news_sticky'],
			"news_template" => $data['news_template']
		);
		add_to_title($locale['global_201'].$news_subject);
		echo "<!--news_pre_readmore-->";
		
		
		// This is newly added using atom core. find it in /templates/tc.php;
		
		//echo infusions_template_socket("news");
		
		render_news($news_subject, $news_news[$_GET['rowstart']], $news_info);		
		
		echo "<!--news_sub_readmore-->";
		if ($pagecount > 1) {
			echo "<div align='center' style='margin-top:5px;'>\n".makepagenav($_GET['rowstart'], 1, $pagecount, 3, FUSION_SELF."?readmore=".$_GET['readmore']."&amp;")."\n</div>\n";
		}
		if ($data['news_allow_comments']) { showcomments("N", DB_NEWS, "news_id", $_GET['readmore'], FUSION_SELF."?readmore=".$_GET['readmore']); }
		if ($data['news_allow_ratings']) { showratings("N", $_GET['readmore'], FUSION_SELF."?readmore=".$_GET['readmore']); }
	} else {
		redirect(FUSION_SELF);
	}
}

require_once THEMES."templates/footer.php";
?>