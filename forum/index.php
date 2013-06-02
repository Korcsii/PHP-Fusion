<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: index.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../maincore.php";
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."forum/main.php";

if (!isset($lastvisited) || !isnum($lastvisited)) { $lastvisited = time(); }

$catWhere = ""; $catID = "";
if (isset($_GET['cat']) && isnum($_GET['cat'])) {
	$check = dbcount("(forum_id)", DB_FORUMS, "forum_id='cat' AND forum_cat='0'");
	if ($check == 0) {
		$catID = $_GET['cat'];
		$catWhere = "f2.forum_id='".$catID."' AND";
	}
}

add_to_title($locale['global_200'].$locale['400']);

opentable($locale['400']);

$forum_list = ""; $current_cat = ""; $forumCollapsed = false; $forumCollapse = true;
$result = dbquery(
	"SELECT	f.forum_id, f.forum_cat, f.forum_name, f.forum_description, f.forum_moderators, f.forum_lastpost, f.forum_postcount,
	f.forum_threadcount, f.forum_lastuser, f.forum_access, f2.forum_name AS forum_cat_name,
	t.thread_id, t.thread_lastpost, t.thread_lastpostid, t.thread_subject,
	u.user_id, u.user_name, u.user_status
	FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_FORUMS." f2 ON f.forum_cat = f2.forum_id
	LEFT JOIN ".DB_THREADS." t ON f.forum_id = t.forum_id AND f.forum_lastpost=t.thread_lastpost
	LEFT JOIN ".DB_USERS." u ON f.forum_lastuser = u.user_id
	WHERE ".$catWhere." ".groupaccess('f.forum_access')." AND f.forum_cat!='0'
	GROUP BY forum_id ORDER BY f2.forum_order ASC, f.forum_order ASC, t.thread_lastpost DESC"
);

$i = 0;

if (dbrows($result) != 0) {
	while ($data = dbarray($result)) {
		if ($catID != "") {
			add_to_title($locale['global_201'].$data['forum_cat_name']);
			set_meta("description", $data['forum_cat_name']);
		}
		if ($data['forum_cat_name'] != $current_cat) {
			if ($i > 0) { echo "</table>\n".($forumCollapse ? "</div>\n" : "")."<br />\n"; }
			$current_cat = $data['forum_cat_name'];
			echo "<!--pre_forum_idx-->";
			echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border forum_idx_table'>\n";
			$forumStatus = ($forumCollapsed ? "off" : "on");
			$boxname = "forum_".$data['forum_id'];
			echo "<tr>\n<td colspan='4' class='forum-caption forum_cat_name'><!--forum_cat_name-->";
			echo "<a href='".FORUM."index.php?cat=".$data['forum_cat']."'>".$data['forum_cat_name']."</a>";
			echo "<div style='float:right;'>".($forumCollapse ? panelbutton($forumStatus, $boxname) : "")."</div>\n</td>\n</tr>\n";
			echo ($forumCollapse ? "</table>".panelstate($forumStatus, $boxname)."<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n" : "");
		}
		$i++;

		$moderators = "";
		if ($data['forum_moderators']) {
			$mod_groups = explode(".", $data['forum_moderators']);
			foreach ($mod_groups as $mod_group) {
				if ($moderators) $moderators .= ", ";
				$moderators .= $mod_group<101 ? "<a href='".BASEDIR."profile.php?group_id=".$mod_group."'>".getgroupname($mod_group)."</a>" : getgroupname($mod_group);
			}
		}
		$forum_match = "\|".$data['forum_lastpost']."\|".$data['forum_id'];
		if ($data['forum_lastpost'] > $lastvisited) {
			if (iMEMBER && ($data['forum_lastuser'] == $userdata['user_id'] || preg_match("({$forum_match}\.|{$forum_match}$)", $userdata['user_threads']))) {
				$fim = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
			} else {
				$fim = "<img src='".get_image("foldernew")."' alt='".$locale['560']."' />";
			}
		} else {
			$fim = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
		}
		echo "<tr>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$fim."</td>\n";
		echo "<td class='tbl1 forum_name'><!--forum_name--><a href='viewforum.php?forum_id=".$data['forum_id']."'>".$data['forum_name']."</a><br />\n";
		if ($data['forum_description'] || $moderators) {
			echo "<span class='small'>".nl2br(parseubb($data['forum_description'])).($data['forum_description'] && $moderators ? "<br />\n" : "");
			echo ($moderators ? "<strong>".$locale['411']."</strong>".$moderators."</span>\n" : "</span>\n")."\n";
		}
		echo "</td>\n";
		echo "<td align='center' width='10%' class='tbl2' style='white-space:nowrap'>".$locale['402'].": ".$data['forum_threadcount']."<br />\n";
		echo $locale['403'].": ".$data['forum_postcount']."</td>\n";
		echo "<td class='tbl1' width='20%' style='white-space:nowrap'>";
		if ($data['forum_lastpost'] == 0) {
			echo $locale['405']."</td>\n</tr>\n";
		} else {
			echo "<a href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."' title='".$data['thread_subject']."'> ".trimlink($data['thread_subject'], 25)."</a> <a href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."&amp;pid=".$data['thread_lastpostid']."#post_".$data['thread_lastpostid']."' title='".$data['thread_subject']."'>";
			if ($data['forum_lastpost'] > $lastvisited) {
			if (iMEMBER && preg_match("({$forum_match}\.|{$forum_match}$)", $userdata['user_threads'])) {
			$fim = "<img src='".get_image("lastpost")."' alt='".$locale['404']."' title='".$locale['404']."' />";
		} else {
			$fim = "<img src='".get_image("lastpostnew")."' alt='".$locale['404']."' title='".$locale['404']."' />";
		}
		} else {
			$fim = "<img src='".get_image("lastpost")."' alt='".$locale['404']."' title='".$locale['404']."' />";
		}
			echo $fim;
			echo "</a><br />\n";
			echo "<span class='small'>".$locale['406'].profile_link($data['forum_lastuser'], $data['user_name'], $data['user_status'])."</span><br />\n";
			echo "<span class='small'>".showdate("forumdate", $data['forum_lastpost'])."</span></td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";
} else {
	echo $locale['407']."\n";
}
echo "<!--sub_forum_idx_table-->\n<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
echo "<td class='forum'><br />\n";
echo "<img src='".get_image("foldernew")."' alt='".$locale['560']."' style='vertical-align:middle;' /> - ".$locale['409']."<br />\n";
echo "<img src='".get_image("folder")."' alt='".$locale['561']."' style='vertical-align:middle;' /> - ".$locale['410']."\n";
echo "</td><td align='right' valign='bottom' class='forum'>\n";
echo "<form name='searchform' method='get' action='".BASEDIR."search.php?stype=forums'>\n";
echo "<input type='hidden' name='stype' value='forums' />\n";
echo "<input type='text' name='stext' class='textbox' style='width:150px' />\n";
echo "<input type='submit' name='search' value='".$locale['550']."' class='button' />\n";
echo "</form>\n</td>\n</tr>\n</table><!--sub_forum_idx-->\n";

if ($i > 0) { echo "</div>\n"; }

closetable();

require_once THEMES."templates/footer.php";
?>