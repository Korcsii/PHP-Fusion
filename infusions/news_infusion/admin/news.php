
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
+--------------------------------------------------------
| Infusions: Recoded by Frederick (Hien) for PHP-Fusion 8
+--------------------------------------------------------*/
require_once "../../../maincore.php";

//if (!checkrights("N") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

require_once THEMES."templates/admin_header_mce.php";
require_once THEMES."templates/atomcore.php";

load_bootstrap();
load_select2();
load_ckeditor();
//load_editor();
//load_localstorage();
global_styles();

include LOCALE.LOCALESET."admin/news.php";

if (isset($_GET['error']) && isnum($_GET['error'])) {
	if ($_GET['error'] == 1) {
		$message = $locale['413'];
	} elseif ($_GET['error'] == 2) {
		$message = sprintf($locale['414'], parsebytesize($settings['news_photo_max_b']));
	} elseif ($_GET['error'] == 3) {
		$message = $locale['415'];
	} elseif ($_GET['error'] == 4) {
		$message = sprintf($locale['416'], $settings['news_photo_max_w'], $settings['news_photo_max_h']);
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}
if (isset($_GET['status'])) {
	if ($_GET['status'] == "sn") {
		$message = $locale['410'];
	} elseif ($_GET['status'] == "su") {
		$message = $locale['411'];
	} elseif ($_GET['status'] == "del") {
		$message = $locale['412'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_POST['save'])) {
	$error = "";
	$news_subject = stripinput($_POST['news_subject']);
	$news_cat = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
	if (isset($_FILES['news_image']) && is_uploaded_file($_FILES['news_image']['tmp_name'])) {
		require_once INCLUDES."photo_functions_include.php";

		$image = $_FILES['news_image'];
		$image_name = stripfilename(str_replace(" ", "_", strtolower(substr($image['name'], 0, strrpos($image['name'], ".")))));
		$image_ext = strtolower(strrchr($image['name'],"."));

		if ($image_ext == ".gif") { $filetype = 1;
		} elseif ($image_ext == ".jpg") { $filetype = 2;
		} elseif ($image_ext == ".png") { $filetype = 3;
		} else { $filetype = false; }

		if (!preg_match("/^[-0-9A-Z_\.\[\]]+$/i", $image_name)) {
			$error = 1;
		} elseif ($image['size'] > $settings['news_photo_max_b']){
			$error = 2;
		} elseif (!$filetype) {
			$error = 3;
		} else {
			$image_t1 = image_exists(IMAGES_N_T, $image_name."_t1".$image_ext);
			$image_t2 = image_exists(IMAGES_N_T, $image_name."_t2".$image_ext);
			$image_full = image_exists(IMAGES_N, $image_name.$image_ext);

			move_uploaded_file($_FILES['news_image']['tmp_name'], IMAGES_N.$image_full);
			if (function_exists("chmod")) { chmod(IMAGES_N.$image_full, 0644); }
			$imagefile = @getimagesize(IMAGES_N.$image_full);
			if ($imagefile[0] > $settings['news_photo_max_w'] || $imagefile[1] > $settings['news_photo_max_h']) {
				$error = 4;
				unlink(IMAGES_N.$image_full);
			} else {
				createthumbnail($filetype, IMAGES_N.$image_full, IMAGES_N_T.$image_t1, $settings['news_photo_w'], $settings['news_photo_h']);
				if ($settings['news_thumb_ratio'] == 0) {
					createthumbnail($filetype, IMAGES_N.$image_full, IMAGES_N_T.$image_t2, $settings['news_thumb_w'], $settings['news_thumb_h']);
				} else {
					createsquarethumbnail($filetype, IMAGES_N.$image_full, IMAGES_N_T.$image_t2, $settings['news_thumb_w']);
				}
			}
		}
		if (!$error) {
			$news_image = $image_full;
			$news_image_t1 = $image_t1;
			$news_image_t2 = $image_t2;
		} else {
			$news_image = "";
			$news_image_t1 = "";
			$news_image_t2 = "";
		}
	} else {
		$news_image = (isset($_POST['news_image']) ? $_POST['news_image'] : "");
		$news_image_t1 = (isset($_POST['news_image_t1']) ? $_POST['news_image_t1'] : "");
		$news_image_t2 = (isset($_POST['news_image_t2']) ? $_POST['news_image_t2'] : "");
	}
	$body = addslash($_POST['body']);
	if ($_POST['body2']) {
		$body2 = addslash(preg_replace("(^<p>\s</p>$)", "", $_POST['body2']));
	} else {
		$body2 = "";
	}
	$news_start_date = 0; $news_end_date = 0;
	if ($_POST['news_start']['mday']!="--" && $_POST['news_start']['mon']!="--" && $_POST['news_start']['year']!="----") {
		$news_start_date = mktime($_POST['news_start']['hours'],$_POST['news_start']['minutes'],0,$_POST['news_start']['mon'],$_POST['news_start']['mday'],$_POST['news_start']['year']);
	}
	if ($_POST['news_end']['mday']!="--" && $_POST['news_end']['mon']!="--" && $_POST['news_end']['year']!="----") {
		$news_end_date = mktime($_POST['news_end']['hours'],$_POST['news_end']['minutes'],0,$_POST['news_end']['mon'],$_POST['news_end']['mday'],$_POST['news_end']['year']);
	}
	$news_visibility = isnum($_POST['news_visibility']) ? $_POST['news_visibility'] : "0";
	$news_draft = isset($_POST['news_draft']) ? "1" : "0";
	$news_sticky = isset($_POST['news_sticky']) ? "1" : "0";
	if ($settings['tinymce_enabled'] != 1) { $news_breaks = isset($_POST['line_breaks']) ? "y" : "n"; } else { $news_breaks = "n"; }
	$news_comments = isset($_POST['news_comments']) ? "1" : "0";
	$news_ratings = isset($_POST['news_ratings']) ? "1" : "0";
	if (isset($_POST['news_id']) && isnum($_POST['news_id'])) {
		$result = dbquery("SELECT news_image, news_image_t1, news_image_t2 FROM ".DB_NEWS." WHERE news_id='".$_POST['news_id']."' LIMIT 1");
		if (dbrows($result)) {
			$data = dbarray($result);
			if ($news_sticky == "1") { $result = dbquery("UPDATE ".DB_NEWS." SET news_sticky='0' WHERE news_sticky='1'"); }
			if (isset($_POST['del_image'])) {
				if (!empty($data['news_image']) && file_exists(IMAGES_N.$data['news_image'])) { unlink(IMAGES_N.$data['news_image']); }
				if (!empty($data['news_image_t1']) && file_exists(IMAGES_N_T.$data['news_image_t1'])) { unlink(IMAGES_N_T.$data['news_image_t1']); }
				if (!empty($data['news_image_t2']) && file_exists(IMAGES_N_T.$data['news_image_t2'])) { unlink(IMAGES_N_T.$data['news_image_t2']); }
				$news_image = "";
				$news_image_t1 = "";
				$news_image_t2 = "";
			}
			$result = dbquery("UPDATE ".DB_NEWS." SET news_subject='$news_subject', news_cat='$news_cat', news_end='$news_end_date', news_image='$news_image', news_news='$body', news_extended='$body2', news_breaks='$news_breaks',".($news_start_date != 0 ? " news_datestamp='$news_start_date'," : "")." news_start='$news_start_date', news_image_t1='$news_image_t1', news_image_t2='$news_image_t2', news_visibility='$news_visibility', news_draft='$news_draft', news_sticky='$news_sticky', news_allow_comments='$news_comments', news_allow_ratings='$news_ratings' WHERE news_id='".$_POST['news_id']."'");
			redirect(FUSION_SELF.$aidlink."&status=su".($error ? "&error=$error" : ""));
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} else {
		if ($news_sticky == "1") { $result = dbquery("UPDATE ".DB_NEWS." SET news_sticky='0' WHERE news_sticky='1'"); }
		$result = dbquery("INSERT INTO ".DB_NEWS." (news_subject, news_cat, news_news, news_extended, news_breaks, news_name, news_datestamp, news_start, news_end, news_image, news_image_t1, news_image_t2, news_visibility, news_draft, news_sticky, news_reads, news_allow_comments, news_allow_ratings) VALUES ('$news_subject', '$news_cat', '$body', '$body2', '$news_breaks', '".$userdata['user_id']."', '".($news_start_date != 0 ? $news_start_date : time())."', '$news_start_date', '$news_end_date', '$news_image', '$news_image_t1', '$news_image_t2', '$news_visibility', '$news_draft', '$news_sticky', '0', '$news_comments', '$news_ratings')");
		redirect(FUSION_SELF.$aidlink."&status=sn".($error ? "&error=$error" : ""));
	}
} 


else if (isset($_POST['delete']) && (isset($_POST['news_id']) && isnum($_POST['news_id']))) {
	$result = dbquery("SELECT news_image, news_image_t1, news_image_t2 FROM ".DB_NEWS." WHERE news_id='".$_POST['news_id']."' LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		if (!empty($data['news_image']) && file_exists(IMAGES_N.$data['news_image'])) { unlink(IMAGES_N.$data['news_image']); }
		if (!empty($data['news_image_t1']) && file_exists(IMAGES_N_T.$data['news_image_t1'])) { unlink(IMAGES_N_T.$data['news_image_t1']); }
		if (!empty($data['news_image_t2']) && file_exists(IMAGES_N_T.$data['news_image_t2'])) { unlink(IMAGES_N_T.$data['news_image_t2']); }
		$result = dbquery("DELETE FROM ".DB_NEWS." WHERE news_id='".$_POST['news_id']."'");
		$result = dbquery("DELETE FROM ".DB_COMMENTS."  WHERE comment_item_id='".$_POST['news_id']."' and comment_type='N'");
		$result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='".$_POST['news_id']."' and rating_type='N'");
		redirect(FUSION_SELF.$aidlink."&status=del");
	} else {
		redirect(FUSION_SELF.$aidlink);
	}
} else {
		
	## News Editor??
	function news_editor() { 
	global $locale;
	$result = dbquery("SELECT news_id, news_subject, news_draft FROM ".DB_NEWS." ORDER BY news_draft DESC, news_datestamp DESC");
		if (dbrows($result) != 0) {
		$editlist = ""; $sel = "";
		while ($data = dbarray($result)) {
			if ((isset($_POST['news_id']) && isnum($_POST['news_id'])) || (isset($_GET['news_id']) && isnum($_GET['news_id']))) {
				$news_id = isset($_POST['news_id']) ? $_POST['news_id'] : $_GET['news_id'];
				$sel = ($news_id == $data['news_id'] ? " selected='selected'" : "");
			}
			$editlist .= "<option value='".$data['news_id']."'$sel>".($data['news_draft'] ? $locale['438']." " : "").$data['news_subject']."</option>\n";
		}
		opentable($locale['400']);
		echo "<div style='text-align:center'>\n<form name='selectform' method='post' action='".FUSION_SELF.$aidlink."&amp;action=edit'>\n";
		echo "<select name='news_id' class='textbox' style='width:250px'>\n".$editlist."</select>\n";
		echo "<input type='submit' name='edit' value='".$locale['420']."' class='button' />\n";
		echo "<input type='submit' name='delete' value='".$locale['421']."' onclick='return DeleteNews();' class='button' />\n";
		echo "</form>\n</div>\n";
		closetable();
		}
	}
	
	## Form open and close
	function news_form_header() { 
	$html ="<form  class='form-horizontal' name='inputform' method='post' action='".FUSION_SELF.$aidlink."' enctype='multipart/form-data' onsubmit='return ValidateForm(this);'>\n";
	//if ((isset($_POST['edit']) && (isset($_POST['news_id']) && isnum($_POST['news_id']))) || (isset($_POST['preview']) && (isset($_POST['news_id']) && isnum($_POST['news_id']))) || (isset($_GET['news_id']) && isnum($_GET['news_id']))) {
	$html .="<div class='form-actions controls-row' style='padding-left:20px !important;'>\n";

	$html .="<div class='span5' style='margin-left:0;'>\n";
	$html .="<input type='hidden' name='news_id' value='".(isset($_POST['news_id']) ? $_POST['news_id'] : $_GET['news_id'])."' />\n";
	$html .="<button type='submit' name='save' class='btn btn-success' /><i class='icon-edit icon-white' accesskey='S'></i> Post News (S)</button>\n";
	$html .="<button type='submit' name='draft' class='btn btn-warning' /><i class='icon-hdd icon-white' accesskey='D'></i> Save as Draft (D)</button>\n";
	$html .="<button type='submit' name='preview' class='btn' style='padding: 5px 12px !important;' accesskey='P'/><i class='icon-eye-open'></i> Preview (P)</button>\n";
	$html .="</div>\n";
	
	$html .="</div>\n";	
	//}	
	return $html;
	}
	function news_form_end() { 
	return "</form>\n";
	}
	
	## All Forms
	function news_form_body_1() { 
	
	global $locale; $settings; $news_subject; $news_cat; $news_tags; $teaser; $body;  
	
		if (isset($_POST['news_subject'])) { $news_subject = $_POST['news_subject']; } else { $news_subject = ""; }
		if (isset($_POST['news_cat'])) { $news_cat = $_POST['news_cat']; } else { $news_cat = ""; }
		if (isset($_POST['news_tags'])) { $news_tags = $_POST['news_tags']; } else { $news_tags = ""; }
		if (isset($_POST['teaser'])) { $teaser = stripinput($_POST['teaser']); } else { $teaser = ""; }
		if (isset($_POST['body'])) { $body = stripinput($_POST['body']); } else { $body = ""; }
			
	$result = dbquery("SELECT news_cat_id, news_cat_name FROM ".DB_NEWS_CATS." ORDER BY news_cat_name");
	$news_cat_opts = ""; $sel = "";
	if (dbrows($result)) {
		while ($data = dbarray($result)) {
			if (isset($news_cat)) $sel = ($news_cat == $data['news_cat_id'] ? " selected='selected'" : "");
			$news_cat_opts .= "<option value='".$data['news_cat_id']."'$sel>".$data['news_cat_name']."</option>\n";
		}
	}

	$html ="<div class='control-group'>\n";		
	$html .="<label class='control-label' for='newsTitle'>Title*</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div class='input-prepend'><span class='add-on'><i class='icon-file'></i></span><input id='newsTitle' type='text' name='news_subject' value='".$news_subject."' style='width: 300px' placeholder='News Title Subject'/>\n</div>\n";
	$html .="<label style='display:inline; padding:0px 20px;'>Category*</label>  \n";
	$html .="<select id='newsCategory' name='news_cat' style='width:250px;'>\n";
	$html .="<option value='0'>".$locale['424']."</option>\n".$news_cat_opts."</select>\n";
	$html .="</div></div>\n";
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='newsTags'>News Tags</label>\n";
	$html .="<div class='controls'><input id='newsTags' type='text' name='news_tags' style='width:350px;'>\n";
	$html .="</div>\n";
	$html .="</div>\n";
	
//	$html .="<div class='control-group'>\n";
//	$html .="<label class='control-label' for='newsAttachment'>News Attachments</label>\n";
//	$html .="<div class='controls'>\n";
//	if ($news_attachment != "") {
//	$html .="<label><a href=''>filename here</a><br />\n";
//	$html .="<input type='checkbox' name='del_attachment' value='y' /> ".$locale['421']."</label>\n";
//	$html .="<input type='hidden' name='news_attachment' value='".$news_image."' />\n";
//	} else {
//	$html .="<input id='newsAttachment' type='file' name='news_attachment' class='textbox' style='width:250px;' /><br />\n";
//	$html .=sprintf($locale['440'], parsebytesize($settings['news_photo_max_b']))."\n";
//	}
//	$html .="</div>\n</div>\n";
		
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label for='teaserNews'>News Teaser</label>\n";
	$html .="<div class='controls'>\n<textarea id='teaserNews' name='teaser' rows='10' style='width:98%' placeholder='Teaser News'>".$teaser."</textarea></div>\n";
	$html .="</div>\n";
	$html .= ckeditor_full_DOM("teaser");
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label for='bodyNews'>News Content</label>\n";
	$html .="<div class='controls'>\n<textarea id='bodyNews' name='body' rows='10' style='width:98%' placeholder='Extended News'>".$body."</textarea></div>\n";
	$html .="</div>\n";
	$html .= ckeditor_full_DOM("body");
	
	// Jquery Section
	$html .= open_jquery();
	$html .= select2_tags_DOM("newsTags", construct_array("News,Updates,Announcements"), construct_array($news_tags));
	$html .= select2_dropdown_DOM("newsCategory");
	$html .= select2_dropdown_DOM("newsPriority");
	$html .= select2_dropdown_DOM("newsVisibility");
	$html .= close_jquery();	
	return $html;
	}
	function news_form_body_2() { 
	global $news_start; $news_end; $news_outdated; 
	
	$news_start = stripinput($_POST['news_start']);
	$news_end = stripinput($_POST['news_end']);
	$news_outdated = stripinput($_POST['news_outdated']);
	
	
	$html = "";
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='startDate'>News Published On</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div id='date1' class='input-append'>
    <input id='startDate' name='news_start' data-format='MM/dd/yyyy HH:mm:ss PP' type='text' value='$news_start'></input>
    <span class='add-on'><i data-time-icon='icon-time' data-date-icon='icon-calendar'></i></span>\n</div>\n";
	$html .="</div>\n</div>\n";
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='endDate'>News Shutdown On</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div id='date2' class='input-append'>
    <input id='endDate' name='news_end' data-format='MM/dd/yyyy HH:mm:ss PP' type='text' value='$news_end'></input>
    <span class='add-on'><i data-time-icon='icon-time' data-date-icon='icon-calendar'></i></span>\n</div>\n";
	$html .="</div>\n</div>\n";
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='outdated'>News Outdated On</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div id='date3' class='input-append'>
    <input id='outdated' name='news_outdated' data-format='MM/dd/yyyy HH:mm:ss PP' type='text' value='$news_outdated'></input>
    <span class='add-on'><i data-time-icon='icon-time' data-date-icon='icon-calendar'></i></span>\n</div>\n";
	$html .="</div>\n</div>\n";
	
	$html .= open_jquery();
	$html .= datepicker_DOM("date1");
	$html .= datepicker_DOM("date2");
	$html .= datepicker_DOM("date3");
	$html .= close_jquery();
	
	return $html;
	}
	function news_form_body_3() { 

	// Poll Function
	global $news_poll_active; $news_poll_subject; $news_poll_startdate; $news_poll_enddate; $news_poll_option;
	
	$news_poll_active = stripinput($_POST['news_poll_active']);
	$news_poll_subject = stripinput($_POST['news_poll_subject']);
	$news_poll_startdate = stripinput($_POST['news_poll_startdate']);
	$news_poll_enddate = stripinput($_POST['news_poll_enddate']);
	$news_poll_option = stripinput($_POST['news_poll_option']);
	
	$html ="";

	$html ="<div class='control-group'>\n";			
	$html .="<label class='control-label' for='newspollEnable'>Enable Poll?</label>\n";
	$html .="<div class='controls'>";
	$control_opts = construct_array("Enabled,Disabled");
	$html .="<select id='newspollEnable' name='news_poll_active'>\n";
		foreach ($control_opts as $arr=>$v) { 
		$html .="<option value='$arr'>$v</option>\n";
		}
	$html .="</select>\n";
	$html .="</div>\n</div>\n";	

	$html .="<div class='control-group'>\n";			
	$html .="<label class='control-label' for='pollTitle'>Poll Title</label>\n";
	$html .="<div class='controls'><div class='input-prepend'><span class='add-on'><i class='icon-fire'></i></span><input id='pollTitle' type='text' name='news_poll_subject' value='".$news_poll_subject."' style='width: 400px' placeholder='News Poll Subject'/>\n</div>\n";
	$html .="</div>\n</div>\n";	
	
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='pollstartDate'>Poll Start Date</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div id='pollsdate' class='input-append'>
    <input id='pollstartDate' name='news_poll_startdate' data-format='MM/dd/yyyy HH:mm:ss PP' type='text' value='".$news_poll_startdate."'></input>
    <span class='add-on'><i data-time-icon='icon-time' data-date-icon='icon-calendar'></i></span>\n</div>\n";
	$html .="</div>\n</div>\n";
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='pollendDate'>Poll End Date</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div id='pollsdate2' class='input-append'>
    <input id='pollendDate' name='news_poll_enddate' data-format='MM/dd/yyyy HH:mm:ss PP' type='text' value='".$news_poll_enddate."'></input>
    <span class='add-on'><i data-time-icon='icon-time' data-date-icon='icon-calendar'></i></span>\n</div>\n";
	$html .="</div>\n</div>\n";
	
	$html .="<style>#survey { margin-left:150px; margin-bottom:30px; } #survey td { padding-bottom:15px; } </style>\n";

	

	$html .="<table id='survey' width='80%'>\n";
	
	if (is_array($news_poll_option)) {
	//$html .= print_r($news_poll_option);
				foreach ($news_poll_option as $arr=>$v) { 
				$html .="<tr><td><input type='checkbox'></td><td>\n";
				$html .="<div class='input-prepend'><span class='add-on'><i class='icon-volume-down'></i></span><input id='pollOption' type='text' name='news_poll_option[]' placeholder='Poll Options' style='width: 300px' value='".$v."'/>\n</div> (Options)\n";
				$html .="</td></tr>\n";
				}
	} else { 
	$html .="<tr><td><input type='checkbox'></td><td>\n";
	$html .="<div class='input-prepend'><span class='add-on'><i class='icon-volume-down'></i></span><input id='pollOption' type='text' name='news_poll_option[]' placeholder='Poll Options' style='width: 300px' />\n</div> (Options)\n";
	$html .="</td></tr>\n";
	$html .="<tr><td><input type='checkbox'></td><td>\n";
	$html .="<div class='input-prepend'><span class='add-on'><i class='icon-volume-down'></i></span><input id='pollOption' type='text' name='news_poll_option[]' placeholder='Poll Options' style='width: 300px' />\n</div> (Options)\n";
	$html .="</td></tr>\n";
	}
	
	
	$html .="</table>\n";

	
	$html .="<div class='row'>\n<div class='span4 offset2'>\n";


	$html .='<button class="btn btn-primary" type="button" onclick="addRow(\'survey\')" style="margin-right:10px;"/><i class="icon-plus icon-white"></i>Add Poll Option</button>';
	$html .='<button class="btn btn-inverse" type="button" onclick="deleteRow(\'survey\')" /><i class="icon-remove icon-white"></i> Remove Poll Option</button>';

	$html .="</div>\n</div>\n";

	$html .= load_dynamic_table();	
	
	return $html;
	}
	function news_form_body_4() { 
	global $news_meta_description; $news_meta_keywords; $news_author; $news_rights; $news_source;
	
			$news_meta_description = stripinput($_POST['news_meta_description']);
			$news_meta_keywords = stripinput($_POST['news_meta_keywords']);
			$news_author = stripinput($_POST['news_author']);
			$news_rights = stripinput($_POST['news_rights']);
			$news_source = stripinput($_POST['news_source']);
	
	// Validation - news author, if untyped, automate to userdata
	$replacement = "Posted on behalf of";
	if (($news_author !== $userdata['user_name']) || ($news_author !== ucwords($userdata['user_name']))) {  
		if (strpos($news_author,$replacement) !== false) { 
	  	
	  	} else { $news_author = str_replace($news_author,"$replacement $news_author", $news_author); }
	}
	
	$html ="<div class='control-group'>\n";
	$html .="<label class='control-label for='metadescNews'>News Meta Description</label>\n";
	$html .="<div class='controls'>\n<textarea id='metadescNews' name='news_meta_description' rows='3' style='width:98%' placeholder='Enter news meta data description'>".$news_meta_description."</textarea></div>\n";
	$html .="</div>\n";
	
	$html .="<div class='control-group'>\n";
	$html .="<label class='control-label' for='metakeyNews'>News Keywords</label>\n";
	$html .="<div class='controls'><input id='metakeyNews' type='text' name='news_meta_keywords' style='width:80%;'>\n";
	$html .="</div>\n";
	$html .="</div>\n";
	
	$html .="<div class='control-group'>\n";		
	$html .="<label class='control-label' for='authorNews'>Author</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div class='input-prepend'><span class='add-on'><i class='icon-user'></i></span><input id='authorNews' type='text' name='news_author' value='".$news_author."' style='width: 200px' placeholder='News Authors or Source'/>\n</div>\n";
	$html .="</div></div>\n";
	
	$news_rights_locale = construct_array("Copyright All Rights Reserved,Abandonedware,Affero General Public License (AGPL),Commercial Licensed (CL),Commercial Core License,Creative Commons (CC),Copyright Removal License (CRL),General Public License (GPL),Limited Copyright,MIT License (MIT),Restricted Copyright,Shareware");
	
	$html .="<div class='control-group'>\n";		
	$html .="<label class='control-label' for='licenseNews'>Content Rights</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<select id='licenseNews' name='news_rights' style='width:250px;'>\n";
			foreach ($news_rights_locale as $arr=>$v) { 
			$html .="<option value='$arr'>$v</option>\n";
			}
	$html .="</select>\n";
	$html .="</div></div>\n";
	
	$html .="<div class='control-group'>\n";		
	$html .="<label class='control-label' for='sourceNews'>External Source</label>\n";
	$html .="<div class='controls'>\n";
	$html .="<div class='input-prepend'><span class='add-on'><i class='icon-magnet'></i></span><input id='sourceNews' type='text' name='news_source' value='".$news_source."' style='width: 300px' placeholder='News External Source'/>\n</div>\n";
	$html .="</div></div>\n";
	
	$html .= open_jquery();
	$html .= select2_tags_DOM("metakeyNews", construct_array("PHP-Fusion,News,Updates"), construct_array($news_meta_keywords));
	$html .= select2_dropdown_DOM("newspollEnable");
	$html .= select2_dropdown_DOM("licenseNews");
	$html .= datepicker_DOM("pollsdate");
	$html .= datepicker_DOM("pollsdate2");
	$html .= close_jquery();	
		
	return $html;
	}
		
	function news_form_sidebar() { 
	
	global $news_enabled; $news_editorial; $news_visibility; $news_priority; $news_comments; $news_ratings;
	
	if (isset($_POST['news_enabled'])) { $news_enabled = $_POST['news_enabled']; } else { $news_enabled = ""; }
	if (isset($_POST['news_editorial'])) { $news_editorial = $_POST['news_editorial']; } else { $news_editorial = ""; }
	if (isset($_POST['news_visibility'])) { $news_visibility = $_POST['news_visibility']; } else { $news_visibility = ""; }
	if (isset($_POST['news_priority'])) { $news_priority = $_POST['$news_priority']; } else { $news_priority = ""; }
	if (isset($_POST['news_comments'])) { $news_comments = $_POST['news_comments']; } else { $news_comments = ""; }
	if (isset($_POST['news_ratings'])) { $news_ratings = $_POST['news_ratings']; } else { $news_ratings = ""; }
	$button_opts = construct_array("Enabled,Disabled");
	
	$visibility_opts = ""; $sel = "";
	$user_groups = getusergroups();
	while(list($key, $user_group) = each($user_groups)){
		$sel = ($news_visibility == $user_group['0'] ? " selected='selected'" : "");
		$visibility_opts .= "<option value='".$user_group['0']."'$sel>".$user_group['1']."</option>\n";
	}
	
	$editor_opts = ""; $sel = "";
	$user_groups = getusergroups();
	while(list($key, $user_group) = each($user_groups)){
		$sel = ($news_editorial == $user_group['0'] ? " selected='selected'" : "");
		$editor_opts .= "<option value='".$user_group['0']."'$sel>".$user_group['1']."</option>\n";
	}
	//$html .="news comment:$news_comments, news ratings: $news_ratings";	

	$html .="<div class='row' style='margin-bottom:20px;'>\n";
	$html .= btn_toggle("News Enabled?","news_enabled",$button_opts,$news_enabled);
	$html .="</div>\n";


	
	$html .="<div class='row' style='margin-bottom:20px'>\n";
	$html .="<label>News Editor Level</label>\n";
	$html .="<select id='editorNews' name='news_editorial' style='width:250px'>\n".$editor_opts."</select>\n";
	$html .="</div>\n";
	
	$html .="<div class='row' style='margin-bottom:20px'>\n";
	$html .="<label>News Visibility Level</label>\n";
	$html .="<select id='visibleNews' name='news_visibility' style='width:250px'>\n".$visibility_opts."</select>\n";
	$html .="</div>\n";
	
	$priority_opts = ""; $select = "";
	$priority_level = construct_array("Normal,Headline,Top Priority");
	foreach ($priority_level as $arr=>$v) { 
	$select = "";
	$priority_opts .= "<option value='$arr'>$v</option>\n";
	}
	
	$html .="<div class='row' style='margin-bottom:20px;'>\n";
	$html .="<label>News Priority Level (Stickiness)</label>\n";
	$html .="<select id='priorityNews' name='news_priority' style='width:250px;'>\n".$priority_opts."</select>\n";
	$html .="</div>\n";
	
	$html .="<div class='row' style='margin-bottom:20px;'>\n";
	$html .= btn_toggle("Enable Comments?","news_comments",$button_opts,$news_comments);
	$html .="</div>\n";

	$html .="<div class='row' style='margin-bottom:20px;'>\n";
	$html .= btn_toggle("Enable News Ratings?","news_ratings",$button_opts,$news_ratings);
	$html .="</div>\n"; 
		
	$html .= open_jquery();
	$html .= select2_dropdown_DOM("enableNews");
	$html .= select2_dropdown_DOM("editorNews");
	$html .= select2_dropdown_DOM("visibleNews");
	$html .= select2_dropdown_DOM("priorityNews");
	$html .= close_jquery();
	

	
	
	
		
//	$html .="<div class='control-group'>\n";
//	$html .="<div class='controls'>\n";
//	$html .="<label class='checkbox'><input type='checkbox' name='news_sticky' value='yes'".$news_sticky." /> Sticky this News</label>\n";
//	$html .="</div></div>\n";	
	
//	$html .="<div class='control-group'>\n";
//	$html .="<div class='controls'>\n";
//	$html .="<label class='checkbox'><input type='checkbox' name='news_comments' value='yes' onclick='SetRatings();'".$news_comments." /> Enable Comments</label>\n";
//	$html .="</div></div>\n";

//	$html .="<div class='control-group'>\n";
//	$html .="<div class='controls'>\n";
//	$html .="<label class='checkbox'><input type='checkbox' name='news_ratings' value='yes'".$news_ratings." /> Enable Viewers Ratings</label>\n";
//	$html .="</div></div>\n";
	return $html;
	
	
	}
	function form_script() { 
	
	// SCRIPTS
	$html .= open_jquery();    


	$html .= "
	
	$('#resetpoll').click(function () { location.reload(); });
	$('#addpoll').click(function(){
	     $('#poll_target').clone().find('input').val('').end().appendTo('#pollgroup');
	     return false;
	});
	
	
	";
	$html .= close_jquery();


	
	/*$html .="<script type='text/javascript'>\n"."function DeleteNews() {\n";
	$html .="return confirm('".$locale['451']."');\n}\n";
	$html .="function ValidateForm(frm) {\n"."if(frm.news_subject.value=='') {\n";
	$html .="alert('".$locale['450']."');\n"."return false;\n}\n}\n";
	$html .="function SetRatings() {\n"."if (inputform.news_comments.checked == false) {\n";
	$html .="inputform.news_ratings.checked = false;\n"."inputform.news_ratings.disabled = true;\n";
	$html .="} else {\n"."inputform.news_ratings.disabled = false;\n}\n}\n</script>\n";
	*/
	return $html;
	}
		
	
	if (isset($_POST['preview'])) {
	echo "<script type='text/javascript'>\n";
	echo "$(function() {	$('#myModal').modal('show');	});";
	echo "</script>\n";	
	
	echo "<style>
	.previewhead h3 {
	font: 2.1em Arial, Helvetica, Tahoma, Verdana;
	line-height: 1.2em;
	}
	.previewhead {
	color: rgb(188, 46, 43);
	}
	.previewhead:hover {
	color: rgb(108, 16, 18);
	text-decoration:underline;
	}
	
	.newsteaser h3 {
	margin-bottom:1em;
	font-size: 1.4em;
	line-height: 1.4em;
	}
	
	.img-polaroid {
	margin: 10px 10px 10px 10px;	
	}
	
	.newsbody {
	font: 13px/1.231 arial,helvetica,clean,sans-serif;
	color: rgb(51,51,51);
	}
	
	.label {
	font-size: 0.85em !important;
	}
	
	#ptag {
	list-style: none outside none; font-size: 0.85em; color: rgb(170, 170, 170); margin-left:5px;
	}
	
	#ptag li {
	float: left; margin-right: 10px;	
	}
	
	</style>\n";
	
		function spanbullet($text, $type) { 
		switch ($type) { 
		case success : 
		$label = "label-success";
		break;
		case warning : 
		$label = "label-warning";
		break;
		case important : 
		$label = "label-important";
		break;
		case info : 
		$label = "label-info";
		break;
		case inverse : 
		$label = "label-inverse";
		break;
		default: $label = "";
		}	
	return "<span class='label ".$label."'>$text</span>\n"; 
	}		
	
	
		
		## Tab 1 Input Fields		
		if (isset($_POST['news_subject'])) { $news_subject = stripinput($_POST['news_subject']); } else { $news_subject = "Untitled News"; } 
		
		if (isset($news_cat)) { $news_cat = stripinput($_POST['news_cat']); } else { $news_cat = spanbullet("Uncategorized", "important"); } 
		$news_cat = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
		if ($news_cat !==0) { 
		$result = dbquery("SELECT * FROM ".DB_NEWS_CATS." WHERE news_cat_id='$news_cat'");
		$catdata = dbarray($result);
		$news_cat_name = stripinput($catdata['news_cat_name']);
		} 
		
		if (isset($_POST['news_tags'])) {
		$news_tags = stripinput($_POST['news_tags']);
		if (strlen($news_tags)>0) {
		$bullet = ""; 
		$txt_array = construct_array($news_tags);
			foreach ($txt_array as $arr=>$v) { 
			$bullet .=  spanbullet($v, "default");
			}
		} else {
			$bullet = spanbullet("Untagged", "inverse");	
		}
		}
		
		if (isset($_POST['teaser'])) {
		$teaser = str_get_html($_POST['teaser']);
		$teaser->find('img', 0)->class = 'img-polaroid';
		$teaser->find('p', 0)->plaintext;
		}
		if (isset($_POST['body'])) {
		$body = str_get_html($_POST['body']);
		$body->find('img', 0)->class = 'img-polaroid';
		$body->find('p', 0)->plaintext;
		}
				
		//$body2preview = str_replace("src='".str_replace("../", "", IMAGES_N), "src='".IMAGES_N, stripslash($_POST['body']));
	
		## Tab 2 Input Fields
		if (isset($_POST['news_start'])) { 
			if (strlen($_POST['news_start']) >0) {
				$news_start = stripinput($_POST['news_start']);
		
			if (isset($_POST['news_end'])) {
				if (strlen($_POST['news_end'])>0) {
				$news_end = stripinput($_POST['news_end']);
				} else { $news_end = spanbullet("No End Date", "important"); }
				
			} 
			
		} else { $news_start = spanbullet("No Start Date", "important"); $news_end = ""; }
		}

		
		if (isset($_POST['news_outdated'])) { 
			if (strlen($_POST['news_outdated'])>0) {
			$news_outdated = stripinput($_POST['news_outdated']);
			$degrade = spanbullet("Degrade on $news_oudated", "default");
			} else { $degrade = spanbullet("Persistant News", "info"); }
		 } else { $degrade = spanbullet("Persistant News", "info");  } 
		
		## Tab 3 Input Fields
		if (isset($_POST['news_poll_active'])) {
			$news_poll_active = isnum($_POST['news_poll_active']) ? $_POST['news_poll_active'] : "0";
			if ($news_poll_active == "1") {
			$news_poll = spanbullet("Poll Active", "info");
			} else { $news_poll = spanbullet("No Poll", "default"); }
		}
		// Will do options here
		
		## Tab 4 Input Fields
		// get field global $news_meta_description; $news_meta_keywords; $news_author; $news_rights; $news_source;
		
		if (isset($_POST['news_author'])) {
		if (strlen($_POST['news_author'])>0) { $news_author = stripinput($_POST['news_author']); } else { 
		$news_author = "<a href='".BASEDIR."profile.php?lookup=1'>Admin</a>"; }
		} 
		
		if (isset($_POST['news_rights'])) {
		$news_rights = spanbullet("aaa","default");
		} else { $news_rights = spanbullet("No rights", "inverse"); }
		
		if (isset($_POST['news_source'])) {
			if (strlen($_POST['news_source'])>0) { $news_source = stripinput($_POST['news_source']); } else {	
			$news_source = "<a href='".BASEDIR."".$settings['siteurl']."'>".$settings['sitename']."</a>";	
			}
		}
	
		
		
		
	/*	$news_image = (isset($_POST['news_image']) ? $_POST['news_image'] : "");
		$news_image_t1 = (isset($_POST['news_image_t1']) ? $_POST['news_image_t1'] : "");
		$news_image_t2 = (isset($_POST['news_image_t2']) ? $_POST['news_image_t2'] : ""); */
		
		$news_visibility = isnum($_POST['news_visibility']) ? $_POST['news_visibility'] : "0";
		$news_draft = isset($_POST['news_draft']) ? " checked='checked'" : "";
		$news_sticky = isset($_POST['news_sticky']) ? " checked='checked'" : "";
		$news_comments = isset($_POST['news_comments']) ? " checked='checked'" : "";
		$news_ratings = isset($_POST['news_ratings']) ? " checked='checked'" : "";

		
	
	
	echo "<div id='myModal' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='width:900px; left:40% !important;'>
		<div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> x </button>
		<h4 id='myModalLabel' style='color:#444 !important;'>Previewing News ...</h3>
		</div>
		<div class='modal-body' style='max-height:580px !important; font: 13px/1.231 arial,helvetica,clean,sans-serif; '>
		
		<h3 class='previewhead'>$news_subject</h4>
		
		<ul id='ptag'>
		<li><i class='icon-calendar'></i> <span style='margin-right:20px;'>$news_start $news_end $degrade</span></li>
		<li><i class='icon-user'></i> <span style='margin-right:20px;'>Written by $news_author</span></li>
		<li><i class='icon-folder-close'></i> <span style='margin-right:20px;'>$news_cat_name</span></li>		
		<li><i class='icon-tag'></i> <span style='margin-right:20px;'>Tags: $bullet</li>
		<li><i class='icon-fire'></i> <span style='margin-right:20px;'>$news_poll</li>
		<li><i class='icon-asterisk'></i> <span style='margin-right:20px;'>Source: $news_source</li>
		</ul>
		
		<div class='newsteaser'>".$teaser."</div>
		<div class='newsbody'>".$body."</div>
		
		</div>
		<div class='modal-footer'>
		<button class='btn' data-dismiss='modal' aria-hidden='true'>Close</button>
		<button class='btn btn-primary'>Save changes</button>
		</div>\n</div>";
	
	// Prevent Memory Leak
	$teaser->clear();
	unset($teaser);
	$body->clear();
	unset($body);
	
	}
	

	
	
	
	


	if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_POST['news_id']) && isnum($_POST['news_id'])) || (isset($_GET['news_id']) && isnum($_GET['news_id']))) {
		$result = dbquery("SELECT news_subject, news_cat, news_news, news_extended, news_start, news_end, news_image, news_image_t1, news_image_t2, news_visibility, news_draft, news_sticky, news_breaks, news_allow_comments, news_allow_ratings FROM ".DB_NEWS." WHERE news_id='".(isset($_POST['news_id']) ? $_POST['news_id'] : $_GET['news_id'])."' LIMIT 1");
		if (dbrows($result)) {
			$data = dbarray($result);
			$news_subject = $data['news_subject'];
			$news_cat = $data['news_cat'];
			$body = phpentities(stripslashes($data['news_news']));
			$body2 = phpentities(stripslashes($data['news_extended']));
			if ($data['news_start'] > 0) $news_start = getdate($data['news_start']);
			if ($data['news_end'] > 0) $news_end = getdate($data['news_end']);
			$news_image = $data['news_image'];
			$news_image_t1 = $data['news_image_t1'];
			$news_image_t2 = $data['news_image_t2'];
			$news_visibility = $data['news_visibility'];
			$news_draft = $data['news_draft'] == "1" ? " checked='checked'" : "";
			$news_sticky = $data['news_sticky'] == "1" ? " checked='checked'" : "";
			$news_breaks = $data['news_breaks'] == "y" ? " checked='checked'" : "";
			$news_comments = $data['news_allow_comments'] == "1" ? " checked='checked'" : "";
			$news_ratings = $data['news_allow_ratings'] == "1" ? " checked='checked'" : "";
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}
	
	if ((isset($_POST['news_id']) && isnum($_POST['news_id'])) || (isset($_GET['news_id']) && isnum($_GET['news_id']))) {
		add_to_title($locale['402']);
					
		
	} else {

		if (!isset($_POST['preview'])) {

		}
		add_to_title($locale['401']);
	}

	
	echo news_form_header();
	echo "<div class='row-fluid'>\n";
	echo "<div class='span9'>\n";
	$tab_title = construct_array("News,Date,Poll,Metadata");
	echo opentab_title($tab_title, 0);
	## Set 1
	echo opentab_body($tab_title, 0, 1);
	echo news_form_body_1();
	echo opentab_closebody();
	## Set 2
	echo opentab_body($tab_title, 1, 0);
	echo news_form_body_2();
	echo opentab_closebody();
	## Set 3
	echo opentab_body($tab_title, 2, 0);
	echo news_form_body_3();
	echo opentab_closebody();
	## Set 4
	echo opentab_body($tab_title, 3, 0);
	echo news_form_body_4();
	echo opentab_closebody();
	## Scripts
	echo form_script();
	echo opentab_close();
	echo "</div>\n";
	echo "<div class='span3' style='padding-left:20px;'>\n";
	echo news_form_sidebar();
	echo "</div>\n";	
	echo "</div>\n";
	echo news_form_end();

//	echo persist_tab();
	
	
	
	
	
}

require_once THEMES."templates/footer.php";
?>
