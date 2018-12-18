<?php
if( !function_exists('sitename_shortcode_function') ){
	// Enqueue scripts and styles.
	function my_theme_scripts() {
		wp_enqueue_style( 'twentynineteen-style', get_template_directory_uri().'/style.css', array(), null );
		wp_enqueue_script('my-custom-script', get_stylesheet_directory_uri() .'/js/custom.js', array('jquery'), null, true);
	}
	add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );
}

if( !function_exists('sitename_shortcode_function') ){
	// [sitename] shortcode to get site name
	function sitename_shortcode_function() {
		return get_bloginfo('name');
	}
	add_shortcode( 'sitename', 'sitename_shortcode_function' );
}

if( !function_exists('sanitize') ){
	// sanitize string and array
	function sanitize($string, $trim = false){
		if (is_array($string)){
			$strng = array();
			foreach($string as $strk => $strv){
				$string1 = filter_var($strv, FILTER_SANITIZE_STRING);
				$string1 = trim($string1);
				$string1 = stripslashes($string1);
				$string1 = strip_tags($string1);
				$string1 = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string1);
				if ($trim)
					$string1 = substr($string1, 0, $trim);
				
				$strng[$strk] = $string1;
			}
			$string = $strng;
		} else {
			$string = filter_var($string, FILTER_SANITIZE_STRING);
			$string = trim($string);
			$string = stripslashes($string);
			$string = strip_tags($string);
			$string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);
			if ($trim)
				$string = substr($string, 0, $trim);
		}
		return $string;
	}
}

if( !function_exists('decode_sanitized') ){
	// de_sanitize string and array
	function decode_sanitized($str){
		if (is_array($str)){
			$strng = array();
			foreach($str as $strk => $strv){
				$strng[$strk] = html_entity_decode( $strv, ENT_QUOTES );
			}
			return $strng;
		}else{
			return html_entity_decode( $str, ENT_QUOTES );
		}
	}
}

if( !function_exists('checkPassword') ){
	// check password strength
	function checkPassword($pwd, &$errors) {
		$errors_init = $errors;
		if( empty($pwd) )
			$errors[] = "Enter password";
		elseif (strlen($pwd) < 8)
			$errors[] = "Passwords must be at least 8 characters in length";
		elseif (!preg_match("#[0-9]+#", $pwd))
			$errors[] = "Password must include at least one number";
		elseif (!preg_match("#[a-zA-Z]+#", $pwd))
			$errors[] = "Password must include at least one letter";

		return ($errors == $errors_init);
	}
}

if( !function_exists('get_file_extension') ){
	// get file extension
	function get_file_extension($filename=""){
		$filename = strtolower($filename);
		$getDotPos = strrpos($filename,".")+1;
		$extn = substr($filename,$getDotPos,strlen($filename));
		return $extn;
	}
}

if( !function_exists('is_valid_image') ){
	// check if file format is image 
	function is_valid_image($filename=""){
		$valid_file_types = array('jpg','jpeg','png','gif','bmp');
		$extn = get_file_extension($filename);
		if( in_array($extn,$valid_file_types) )
			return true;
		else
			return false;
	}
}

if( !function_exists('is_alphabets') ){
	// check valid alphabets
	function is_alphabets( $name="" ){
		if (!preg_match("/^[a-zA-Z ]*$/",$name))
			return false;
		else
			return true;
	}
}

if( !function_exists('is_alphabets') ){
	// check valid email
	function is_valid_email( $email="" ){
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			return false;
		else
			return true;
	}
}

if( !function_exists('get_tiny_url') ){
	// get tiny url
	function get_tiny_url($url)  {  
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
}

if( !function_exists('convert_date_dmy_ymd') ){
	// convert date dmy to ymd
	function convert_date_dmy_ymd($date, $input_format='d-m-Y', $output_format='Y-m-d'){
		$sptrDash = strpos($date,'-');
		$sptrSlash = strpos($date,'/');
		$new_date = $date;
		if( $sptrDash !== false ){
			$date_arr = explode('-',$date);
			$new_date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
		}
		if( $sptrSlash !== false ){
			$date_arr = explode('/',$date);
			$new_date = $date_arr[2].'/'.$date_arr[1].'/'.$date_arr[0];
		}
		return $new_date;
	}
}

if( !function_exists('ip_info') ){
	// function to get client ip info
	function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}
		return $output;
	}
}

if( !function_exists('rearrange') ){
	// rearrange files array 
	function rearrange( $arr ){
		foreach( $arr as $key => $all ){
			foreach( $all as $i => $val ){
				$new[$i][$key] = $val;   
			}   
		}
		return $new;
	}
}

if( !function_exists('insert_attachment') ){
	// insert attachments
	function insert_attachment($file_handler,$post_id,$setthumb='false') {
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK){ 
			return __return_false(); 
		} 
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		$attach_id = media_handle_upload( $file_handler, $post_id );
		//set post thumbnail if setthumb is 1
		if ($setthumb == 1) update_post_meta($post_id,'_thumbnail_id',$attach_id);
		return $attach_id;
	}
}

if( !function_exists('get_current_page_url') ){
	function get_current_page_url(){
		global $wp;  
		$current_url = home_url(add_query_arg(array(),$wp->request));
		return $current_url;
	}
}


if( !function_exists('the_breadcrumb') ){
	// breadcrumb function
	function the_breadcrumb() {
		/* === OPTIONS === */
		$text['home']     = '<span class="glyphicon glyphicon glyphicon-home" aria-hidden="true"></span>'; // text for the 'Home' link
		$text['category'] = '%s'; // text for a category page
		$text['brand'] = '%s'; // text for a category page
		$text['search']   = 'Search Results for "%s"'; // text for a search results page
		$text['tag']      = 'Posts Tagged "%s"'; // text for a tag page
		$text['author']   = 'Ad posted by %s'; // text for an author page
		$text['404']      = 'Error 404'; // text for the 404 page

		$show_current   = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show
		$show_on_home   = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
		$show_title     = 1; // 1 - show the title for the links, 0 - don't show
		$delimiter      = ' &raquo; '; // delimiter between crumbs
		$before         = '<span>'; // tag before the current crumb
		$after          = '</span>'; // tag after the current crumb
		$link_before	= '';
		$link_after		= '';
		/* === END OF OPTIONS === */

		global $post;
		$home_link    = home_url('/');

		$link_attr    = ' rel="v:url" property="v:title"';
		$link         = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
		$parent_id    = $parent_id_2 = isset($post->post_parent) ? $post->post_parent : 0;
		$frontpage_id = get_option('page_on_front');

		if (is_home() || is_front_page()) {

			if ($show_on_home == 1) echo '<p class="crumb"><a href="' . $home_link . '">' . $text['home'] . '</a></p>';

		} else {

			echo '<p class="crumb">';
			if ($show_home_link == 1) {
				echo '<a href="' . $home_link . '" rel="v:url" property="v:title">' . $text['home'] . '</a>';
				if ($frontpage_id == 0 || $parent_id != $frontpage_id) echo $delimiter;
			}

			if ( is_category() ) {
				$this_cat = get_category(get_query_var('cat'), false);
				if ($this_cat->parent != 0) {
					$cats = get_category_parents($this_cat->parent, TRUE, $delimiter);
					if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
					$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
					$cats = str_replace('</a>', '</a>' . $link_after, $cats);
					if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
					echo $cats;
				}
				if ($show_current == 1) echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

			} elseif ( is_tax('brand') ) {
				$this_cat = get_query_var('brand');
				if ($show_current == 1) echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

			} elseif ( is_search() ) {
				echo $before . sprintf($text['search'], get_search_query()) . $after;

			} elseif ( is_day() ) {
				echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
				echo $before . get_the_time('d') . $after;

			} elseif ( is_month() ) {
				echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				echo $before . get_the_time('F') . $after;

			} elseif ( is_year() ) {
				echo $before . get_the_time('Y') . $after;

			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					printf($link, $home_link . $slug['slug'] . '/', $post_type->labels->singular_name);
					if ($show_current == 1) echo $delimiter . $before . get_the_title() . $after;
				} else {
					$cat = get_the_category(); $cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, $delimiter);
					if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
					$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
					$cats = str_replace('</a>', '</a>' . $link_after, $cats);
					if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
					echo $cats;
					if ($show_current == 1) echo $before . get_the_title() . $after;
				}

			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				echo $before . $post_type->labels->singular_name . $after;

			} elseif ( is_attachment() ) {
				$parent = get_post($parent_id);
				$cat = get_the_category($parent->ID); $cat = $cat[0];
				if ($cat) {
					$cats = get_category_parents($cat, TRUE, $delimiter);
					$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
					$cats = str_replace('</a>', '</a>' . $link_after, $cats);
					if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
					echo $cats;
				}
				printf($link, get_permalink($parent), $parent->post_title);
				if ($show_current == 1) echo $delimiter . $before . get_the_title() . $after;

			} elseif ( is_page() && !$parent_id ) {
				if ($show_current == 1) echo $before . get_the_title() . $after;

			} elseif ( is_page() && $parent_id ) {
				if ($parent_id != $frontpage_id) {
					$breadcrumbs = array();
					while ($parent_id) {
						$page = get_page($parent_id);
						if ($parent_id != $frontpage_id) {
							$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
						}
						$parent_id = $page->post_parent;
					}
					$breadcrumbs = array_reverse($breadcrumbs);
					for ($i = 0; $i < count($breadcrumbs); $i++) {
						echo $breadcrumbs[$i];
						if ($i != count($breadcrumbs)-1) echo $delimiter;
					}
				}
				if ($show_current == 1) {
					if ($show_home_link == 1 || ($parent_id_2 != 0 && $parent_id_2 != $frontpage_id)) echo $delimiter;
					echo $before . get_the_title() . $after;
				}

			} elseif ( is_tag() ) {
				echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo $before . sprintf($text['author'], $userdata->display_name) . $after;

			} elseif ( is_404() ) {
				echo $before . $text['404'] . $after;

			} elseif ( has_post_format() && !is_singular() ) {
				echo get_post_format_string( get_post_format() );
			}

			if ( get_query_var('paged') ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
				echo __('Page') . ' ' . get_query_var('paged');
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
			}

			echo '</p><!-- .crumb -->';

		}
	} // end the_breadcrumb()
}

if( !function_exists('get_excerpt_from_content') ){
	// get excerpt from content
	function get_excerpt_from_content($postcontent, $length){
		$this_excerpt = strip_shortcodes( $postcontent );
		$this_excerpt = strip_tags($this_excerpt);
		$this_excerpt = substr($this_excerpt, 0, $length);
		return $this_excerpt;
	}
}

if( !function_exists('get_email_template_header') ){
	function get_email_template_header($title=""){
		$title = empty($title) ? get_bloginfo('name') : $title;
		$header_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>'.$title.'</title><meta name="viewport" content="width=device-width, initial-scale=1.0"/><style>a{color:#f93a3a; text-decoration:none;}</style></head><body style="margin:0; padding:0; font-family:Arial; font-size:18px;"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align:center" bgcolor="#e6ebee"><tr><td height="20"></td></tr><tr><td align="center"><table align="center" border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#ffffff" style="border-collapse:collapse;"><tr><td><table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr><td width="45" bgcolor="#ffffff"></td><td width="510" bgcolor="#ffffff" ><table align="center" border="0" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr><td colspan="3" height="20"></td></tr><tr><td colspan="3" align="center"><a href="'.home_url('/').'" target="_blank">'.get_bloginfo('name').'</a></td></tr><tr><td colspan="3" height="15"></td></tr><tr><td colspan="3"><table align="center" width="100%" border="0" cellpadding="0" bgcolor="#eeeeee" cellspacing="0" style="border-collapse:collapse;"><tr><td height="10"></td></tr><tr><td><p style="font-family:Arial; font-size:16px; color:#666666; line-height:1.75; padding:0; margin:0; text-align:center;"><strong>'.$title.'</strong></p></td></tr><tr><td height="10"></td></tr></table></td></tr><tr><td colspan="3" height="30"></td></tr><tr><td colspan="3">';
		return $header_html;
	}
}

if( !function_exists('get_email_template_footer') ){
	function get_email_template_footer(){
		$footer_html = '</td></tr><tr><td colspan="3" height="20"></td></tr><tr><td colspan="3" height="20"></td></tr></table></td><td width="45" bgcolor="#ffffff"></td></tr><tr><td colspan="3" bgcolor="#4d4d4f" style="text-align:center; color:#fff;">&nbsp;<br><p style="font-family:Arial; font-size:14px; color:#ffffff; line-height:1.75; padding:0; margin:0; text-align:center;"><a href="mailto:'.get_bloginfo('admin_email').'" style="color:#ffffff; text-decoration:none;">'.get_bloginfo('admin_email').'</a></p>&nbsp;</td></tr></table></td></tr></table></td></tr><tr><td height="20"></td></tr></table></body></html>';
		return $footer_html;
	}
}

// load custom widgets
require get_stylesheet_directory() . '/inc/lib/widgets.php';

// load custom post types and taxonomies
require get_stylesheet_directory() . '/inc/lib/post_types.php';

// load shortcodes
require get_stylesheet_directory() . '/inc/lib/shortcodes.php';

// process ajax request
require get_stylesheet_directory() . '/inc/process-ajax-request.php';