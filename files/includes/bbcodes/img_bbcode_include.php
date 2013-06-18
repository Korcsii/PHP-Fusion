<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: img_bbcode_include.php
| Author: Wooya
| Co-Author: Euforia33
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

if (!function_exists("img_bbcode_callback")) {
	function img_bbcode_callback($matches) {

	// Include locales
	if (file_exists(LOCALE.LOCALESET."bbcodes/img.php")) {
		include (LOCALE.LOCALESET."bbcodes/img.php");
	} elseif (file_exists(LOCALE."English/bbcodes/img.php")) {
		include (LOCALE."English/bbcodes/img.php");
	}

		if (substr($matches[3], -1, 1) != "/") {
			$il = "$matches[1]$matches[3]$matches[4]";

			if (function_exists('curl_init')) { // If cURL is available, use it. 
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $il);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // This MUST be kept as it is used to block one of the XSRF image bypassing techniques.
				curl_setopt($curl, CURLOPT_FILETIME, true);
				curl_setopt($curl, CURLOPT_NOBODY, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_TIMEOUT, 1); // execution time allowed in seconds
				$header = curl_exec($curl);
				$headers = explode("\n", $header);
				$content_types = curl_getinfo($curl);
				$content_size = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
				curl_close($curl);
				$content_type = $content_types["content_type"];
				$content_size = (int) $content_size;
			} else {
				if (isset(get_headers($il,2)['Content-Type'])) {			
					$chksize = get_headers($il,1)['Content-Length'];
					$chksize = (int) $chksize;
					
					if ($chksize >= 1 && $chksize != NULL && $chksize != false) {
						$content_type = get_headers($il,2)['Content-Type'];
						$content_size = $chksize;
						$imageIsValid = true;
					} else {
						$imageIsValid = false;
						$err_msg = $locale['bb_img_error_not_valid'];
					}
				} else {
					$imageIsValid = false;
					$err_msg = $locale['bb_img_error_not_valid'];
				}
			}

			$content_type = $content_type; // This can still be faked, but as the FOLLOWLOCATION is used, they cannot fake YOUR header responses.

			if (!preg_match("/image|jpg|jpeg|gif|png/i", $content_type)) {
				$imageIsValid = false;
				$err_msg = $locale['bb_img_error_not_valid'];
			} else {
				if (preg_match("/-/i", $content_size) || $content_size <= 2) {
					$imageIsValid = false;
					$err_msg = $locale['bb_img_error_fake'];
				} else {
					$imageIsValid = true;
				}
			}

			// If image is valid
			if ($imageIsValid == true) {	
				return "<span style='display: block; max-width: 300px; max-height: 300px; overflow: auto;' class='forum-img-wrapper'><img src='".$matches[1].str_replace(array("?","&amp;","&","="), "", $matches[3]).$matches[4]."' alt='".$matches[3].$matches[4]."' style='border:0px' class='forum-img' /></span>";
			} else {
				return "<span class='invalid-image' style='text-decoration: line-through' title='".$err_msg."'>$matches[1]$matches[3]$matches[4]</span>";
			}

		// End of BBCode IMG tag patch by Euforia33
		} else {
			return "<span class='invalid-image' style='text-decoration: line-through' title='".$locale['bb_img_error_other']."'>$matches[1]$matches[3]$matches[4]</span>";
		}
	}
}

$text = preg_replace_callback("#\[img\]((http|ftp|https|ftps)://)(.*?)(\.(jpg|jpeg|gif|png|JPG|JPEG|GIF|PNG|PHP))\[/img\]#si", "img_bbcode_callback", $text);
?>