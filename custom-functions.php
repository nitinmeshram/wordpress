<?php
// [sitename] shortcode to get site name
function sitename_shortcode_function() {
	return get_bloginfo('name');
}
add_shortcode( 'sitename', 'sitename_shortcode_function' );

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

// get file extension
function get_file_extension($filename=""){
	$filename = strtolower($filename);
	$getDotPos = strrpos($filename,".")+1;
	$extn = substr($filename,$getDotPos,strlen($filename));
	return $extn;
}

?>