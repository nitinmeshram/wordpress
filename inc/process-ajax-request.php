<?php
function checkUserLogin(){
	$data = array();
	$form_data = $_POST;
	$creds = array();
	$creds['user_login'] = isset($form_data['log_email']) ? sanitize($form_data['log_email']) : '';
	$creds['user_password'] = isset($form_data['log_password']) ? sanitize($form_data['log_password']) : '';
	$creds['remember'] = false;

	if( is_user_logged_in() ){
		$current_user = wp_get_current_user(); 
		$data['loggedin'] = '<small>You are already logged in as <strong>'.$current_user->user_email.'</strong>. Please <strong><a href="'.wp_logout_url( home_url("?logout=1") ).'">logout</a></strong> first.</small>';
	}else{
		if( empty($creds['user_login']) && empty($creds['user_password']) ) {
			$data["error"] = "Enter you email and password";
		} elseif( $form_data['nonce'] !== wp_create_nonce('lOgIn') ) { 
			$data["error"] = "Invalid user";
		} else {
			$user = wp_signon( $creds, false );
			if ( is_wp_error($user) ){
				if( isset($user->errors['invalid_email']) || isset($user->errors['invalid_username']) )
					$data["error"] = "Invalid email address";
				elseif( isset($user->errors['empty_username']) )
					$data["error"] = "The email field is empty";
				elseif( isset($user->errors['empty_password']) )
					$data["error"] = "The password field is empty";
				elseif( isset($user->errors['incorrect_password']) )
					$data["error"] = "The password you entered is incorrect.";
			}else{
				$data["success"] = "Logged in successfully";
				$data['redirect'] = isset($redirect_url) ? $redirect_url : home_url('/my-account/') ;
			}
		}
	}
	echo json_encode($data);
	die();
}
add_action('wp_ajax_checkUserLogin', 'checkUserLogin');
add_action('wp_ajax_nopriv_checkUserLogin', 'checkUserLogin'); 



function checkUserSignup(){
	$data = array();

	$error = array();
	$form_data = (object)$_POST;

	$fname 			= !empty($form_data->sg_firstname) ? sanitize($form_data->sg_firstname) : '';
	$lname 			= !empty($form_data->sg_lastname) ? sanitize($form_data->sg_lastname) : '';
	$email 			= !empty($form_data->sg_email) ? sanitize($form_data->sg_email) : '';
	$pwd 			= !empty($form_data->sg_password) ? $form_data->sg_password : '';
	$confirm_pwd 	= !empty($form_data->sg_password_confirm) ? $form_data->sg_password_confirm : '';
	$agree_terms	= isset($form_data->agree_terms) ? $form_data->agree_terms : 0;
	$pwdCheck		= checkPassword($pwd,$pwdError);

	if( is_user_logged_in() ){
		$current_user = wp_get_current_user(); 
		$data['loggedin'] = '<small>You are already logged in as <strong>'.$current_user->user_email.'</strong>. Please <strong><a href="'.wp_logout_url( home_url("?logout=1") ).'">logout</a></strong> first.</small>';
	}else{

		if( $_POST['nonce'] !== wp_create_nonce('S1gnUp')  ){
			$data["error"] = "Oops something went wrong updaing your account. Contact our support team.";
		}else {
			if( empty($fname) )
				$error['fname'] = 'Enter your first name';
			elseif( !is_alphabets($fname) )
				$error['fname'] = 'Enter only alphabets in first name';
			if( empty($lname) )
				$error['lname'] = 'Enter your last name';
			elseif( !is_alphabets($lname) )
				$error['lname'] = 'Enter only alphabets in last name';
			if( empty($email) )
				$error['email'] = 'Enter your email address';
			elseif( $email!='' && !filter_var($email, FILTER_VALIDATE_EMAIL) )
				$error['email'] = 'Invalid email address.';
			elseif( email_exists( $email )  )
				$error['email'] = 'Email already exists, please try another one';
			if( count($pwdError) > 0  )
				$error['pwd'] = implode("<br >",$pwdError);
			if( empty($confirm_pwd) )
				$error['confirm_pwd'] = 'Enter confirm password';
			if( !empty($pwd) && !empty($confirm_pwd) && $pwd !== $confirm_pwd)
				$error['confirm_pwd'] = 'Your password and confirmation password do not match.';
			if( !$agree_terms )
				$error['agree_terms'] = 'You must agree with the Terms & Conditions';

			if( count($error) > 0 ){ 
				$data['error'] = implode('<br>',$error);
			}else{
				$user_id = wp_create_user( $email, $pwd, $email );
				$new_usr = new WP_User( $user_id );
				$new_usr->set_role( 'caterer' );
				
				$email_otp =  wp_generate_password( 12, false, false );

				$date_secs = strtotime(date('Y-m-d h:i:s'));
				$nicename = $fname.$lname;
				$user_update = wp_update_user( array( 
								'ID' 					=> $user_id, 
								'user_nicename' 		=> $nicename, 
								'display_name' 			=> $fname, 
								'nickname' 				=> $fname,
								'show_admin_bar_front'	=> 'false',
								'first_name'			=> $fname,
								'last_name'				=> $lname
						) );
				update_user_meta($user_id, 'email_otp', $email_otp);
				update_user_meta($user_id, 'email_activate', '0');
				update_user_meta($user_id, 'business_name', '');

				$ipinfo = ip_info();
				if( !empty($ipinfo['city']) )
					update_user_meta($user_id, 'ip_city', $ipinfo['city']);

				$creds = array();
				$creds['user_login'] = $email;
				$creds['user_password'] = $pwd;
				$creds['remember'] = false;
				$user = wp_signon( $creds, false );
				$message = "Registered successfully";

				// Multiple recipients
				$to = $email; // note the comma
				$subject = 'Email confirmation - '.get_bloginfo('name');
				$confirm_url = home_url( '/my-account/?user='.md5($user_id).'&email_activate=1&emailkey='.$email_otp );

				$message  = get_email_template_header("Confirm your email");
				$message .= '<p style="font-family:Arial; font-size:14px; color:#666666; line-height:1.5;">Dear '.$fname.',</p>
							<p style="font-family:Arial; font-size:14px; color:#666666; line-height:1.5;">Thank you for signing up with '.get_bloginfo('name').'! Please confirm your email address by clicking the link below.</p>
							<p style="font-family:Arial; font-size:14px; color:#666666; line-height:1.5;"><a href="'.$confirm_url.'" style="color:#f93a3a;">Confirm Email</a> OR simply copy and paste below link in browser.<br />'.$confirm_url.'</p>
							<p style="font-family:Arial; font-size:14px; color:#666666; line-height:1.5;">We look forward to serving you,<br><b>'.get_bloginfo('name').' Team</b><br />'.home_url().'</p>';
				$message .= get_email_template_footer();

				$headers = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>');
				$mail = @wp_mail( $to, $subject, $message, $headers );

				$data['success'] = "Account created successfully.";
				$data['redirect'] = isset($redirect_url) ? $redirect_url : home_url('/my-account/') ;
			}
		}
	}
	echo json_encode($data);
	die();
}
add_action('wp_ajax_checkUserSignup', 'checkUserSignup');
add_action('wp_ajax_nopriv_checkUserSignup', 'checkUserSignup'); 



function forgotResetUserPassword(){
	$data = array();
	global $wpdb;

	if( $_POST['nonce'] !== wp_create_nonce('fOrgGotPwd')  ){
		$data["error"] = "Oops something went wrong updaing your account. Contact our support team.";
	}else {
		$email = trim($_POST['fp_email']);
		
		if( empty( $email ) ) {
			$data["error"] = 'Enter your e-mail address.';
		} else if( ! is_email( $email )) {
			$data["error"] = 'Invalid e-mail address.';
		} else if( ! email_exists( $email ) ) {
			$data["error"] = 'There is no user registered with given email address.';
		} else {
			$random_password = wp_generate_password( 8, false );
			$user = get_user_by( 'email', $email );
			$fname = $user->display_name;
			$update_user = wp_update_user( array (
					'ID' => $user->ID, 
					'user_pass' => $random_password
				)
			);
			// if  update user return true then lets send user an email containing the new password
			if( $update_user ) {
				$to = $email;
				$subject = 'Reset password - '.get_bloginfo('name');
				$sender = get_option('name');
				
				$message = 'Dear '.$fname.',<br><br>Your temporary password is <b>'.$random_password.'</b>. Please reset your password after login.<br>'.home_url('/my-account/').'<br><br>Regards,<br>'.get_bloginfo('name').'<br>'.home_url();

				$message  = get_email_template_header("Reset Your Password");
				$message .= '<p style="font-family:Arial; font-size:14px; color:#666666; line-height:1.5;">Dear '.$fname.',</p>
							<p style="font-family:Arial; font-size:14px; color:#666666; line-height:1.5;">Your temporary password is <b>'.$random_password.'</b>. Please reset your password after login.<br>'.home_url('/my-account/');
				$message .= get_email_template_footer();
				
				$headers = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>');
				$mail = @wp_mail( $to, $subject, $message, $headers );

				if( $mail )
					$data["success"] = 'Check your email address to login with temporary password.';
				else
					$data["error"] = 'The email could not be sent. Try again later.';
			} else {
				$data["error"] = 'Oops something went wrong with updating your account.';
			}
		}
	}
	
	echo json_encode($data);
	die();
}
add_action('wp_ajax_forgotResetUserPassword', 'forgotResetUserPassword');
add_action('wp_ajax_nopriv_forgotResetUserPassword', 'forgotResetUserPassword'); 