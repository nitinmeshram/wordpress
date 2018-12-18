<?php 

if( !function_exists('logout_link_func') ){
	// function to get logout link
	function logout_link_func( $atts ){
		$a = shortcode_atts( array(
				'text' => 'Logout',
			), $atts );
		return '<a href="'.wp_logout_url( home_url("?logout=1") ).'">'.$a['text'].'</a>';
	}
	add_shortcode( 'logout_link', 'logout_link_func' );
}

if( !function_exists('profile_form_func') ){
	// function to get my account
	function profile_form_func( $atts ){
		$a = shortcode_atts( array(
				'form_title' => 'Profile',
			), $atts );
			
		$profile = '<div class="row">
				<div class="col-sm-6"><small>First Name:</small><br><strong>My First Name</strong></div>
				<div class="col-sm-6"><small>Last Name:</small><br><strong>My Last Name</strong></div>
			</div>
			<form>
				<input type="text">
			</form>';
		return $profile;
	}
	add_shortcode( 'profile_form', 'profile_form_func' );
}

if( !function_exists('login_form_func') ){
	// function to get login form shortcode
	function login_form_func( $atts ){
		$form = '<form autocomplete="off" method="post" name="loginform" id="loginform">
				<input autocomplete="false" name="hidden" type="text" style="display:none;">
				<h2 class="text-center panel-title">Login</h2>
				<p class="hidden" id="loginResponse"></p>
				<p><input type="text" name="log_email" value="" placeholder="Enter your email"></p>
				<p><input type="password" name="log_password" value="" placeholder="Enter your Password"></p>
				<p><button type="submit" data-loading-text="<i class=\'fas fa-spinner fa-spin\'></i> Processing..." id="btn_submit_login" class="btn btn-primary btn-lg btn-block"><i class="fa fa-unlock" aria-hidden="true"></i> Login</button></p>
				<input type="hidden" name="nonce" id="log_nonce" value="'.wp_create_nonce('lOgIn').'">
				<input type="hidden" name="action" value="checkUserLogin"/>
			</form>
			<script>
			jQuery("#loginform").submit(ajaxSubmitLogin);
			function ajaxSubmitLogin(){
				var userLoginForm = jQuery(this).serialize();
				var btn = jQuery("#btn_submit_login");
				var btnText = btn.text();
				jQuery.ajax({
					type:"POST",
					url: "'.home_url().'/wp-admin/admin-ajax.php",
					data: userLoginForm,
					beforeSend: function() { btn.text("Processing..."); },
					complete: function() { btn.text(btnText); },
					success:function(data){
						var obj = JSON.parse(data);
						jQuery("#loginResponse").removeClass("hidden");
						if( obj.error ){
							jQuery("#loginResponse").removeClass("text-success").addClass("text-danger").html( obj.error );
						}
						if( obj.loggedin ){
							jQuery("#loginResponse").removeClass("bg-danger").addClass("text-warning").html( obj.loggedin );
						}
						if( obj.success ){
							jQuery("#loginResponse").removeClass("text-danger").addClass("text-success").html( obj.success );
							window.location.href=obj.redirect;
						}
						jQuery("html, body").animate({
							scrollTop: jQuery("#loginform").offset().top
						}, 500);
					}
				});
				return false;
			}
			</script>
		';
		return $form;
	}
	add_shortcode( 'login_form', 'login_form_func' );
}

if( !function_exists('signup_form_func') ){
	// function to get signup form shortcode
	function signup_form_func( $atts ){
		$form = '		
			<form autocomplete="off" method="post" name="signup_form" id="signup_form">
				<input autocomplete="false" name="hidden" type="text" style="display:none;">
				<h2 class="text-center panel-title">Sign Up</h2>
				<p class="hidden" id="signupResponse"></p>
				<p><input type="text" class="form-control" name="sg_firstname" value="" placeholder="First Name"></p>
				<p><input type="text" class="form-control" name="sg_lastname" value="" placeholder="Last Name"></p>
				<p><input type="text" class="form-control" name="sg_email" value="" placeholder="Enter your email"></p>
				<p><input type="password" class="form-control" name="sg_password" value="" placeholder="Enter Password"></p>
				<p><input type="password" class="form-control" name="sg_password_confirm" value="" placeholder="Re-enter Password"></p>
				<p><label><input type="checkbox" name="agree_terms" id="agree_tandc" value="1"> You must agree to the <a href="#terms_link">terms and conditions</a>.</label></p>
				<p>
					<button type="submit" data-loading-text="<i class=\'fas fa-spinner fa-spin\'></i> Processing..." id="btn_submit_signup" class="btn btn-primary btn-lg btn-block"><i class="fas fa-user-plus"></i> Submit</button>
				</p>
				<input type="hidden" name="nonce" id="reg_nonce" value="'.wp_create_nonce('S1gnUp').'">
				<input type="hidden" name="action" value="checkUserSignup"/>
			</form>
			<script>
			jQuery("#signup_form").submit(ajaxSubmitSignup);
			function ajaxSubmitSignup(){
				var userSignupForm = jQuery(this).serialize();
				var signupBtn = jQuery("#btn_submit_signup");
				var signupBtnText = signupBtn.text();
				jQuery.ajax({
					type:"POST",
					url: "'.home_url().'/wp-admin/admin-ajax.php",
					data: userSignupForm,
					beforeSend: function() { signupBtn.text("Processing"); },
					complete: function() { signupBtn.text(signupBtnText); },
					success:function(data){
						var obj = JSON.parse(data);
						jQuery("#signupResponse").removeClass("hidden");
						if( obj.error ){
							jQuery("#signupResponse").removeClass("text-success").addClass("text-danger").html( obj.error );
						}
						if( obj.loggedin ){
							jQuery("#signupResponse").removeClass("bg-danger").addClass("text-warning").html( obj.loggedin );
						}
						if( obj.success ){
							jQuery("#signupResponse").removeClass("text-danger").addClass("text-success").html( obj.success );
							window.location.href=obj.redirect;
						}
						jQuery("html, body").animate({
							scrollTop: jQuery("#signup_form").offset().top
						}, 500);
					}
				});
				return false;
			}
			</script>';
		return $form;
	}
	add_shortcode( 'signup_form', 'signup_form_func' );
}

if( !function_exists('forgot_password_form_func') ){
	// function to get forgot password form shortcode
	function forgot_password_form_func( $atts ){
		$form = '<form autocomplete="off" method="post" name="forgot_pwd_form" id="forgot_pwd_form">
				<input autocomplete="false" name="hidden" type="text" style="display:none;">
				<h2 class="text-center panel-title">Lost your password?</h2>
				<p class="text-center">No worries, enter your email address and you will receive a link to create a new password via email.</p>
				<p class="hidden" id="fp_return_message"></p>
				<p><input type="text" class="form-control input-sm" name="fp_email" id="fp_email" value="" placeholder="Enter your email"></p>
				<p><button type="submit" data-loading-text="<i class=\'fas fa-spinner fa-spin\'></i> Processing..." id="submit_forgot_password" class="btn btn-primary btn-lg"><i class="fas fa-lock-open"></i> Get New Password</button></p>
				<input type="hidden" name="nonce" id="reset_pwd_nonce" value="'.wp_create_nonce('fOrgGotPwd').'">
				<input type="hidden" name="action" value="forgotResetUserPassword"/>	
			</form>
			<script>
			jQuery("#forgot_pwd_form").submit(ajaxSubmitForgotPwd);
			function ajaxSubmitForgotPwd(){
				var forgotPwdForm = jQuery(this).serialize();
				var fPwdBtn = jQuery("#submit_forgot_password");
				var fPwdBtnText = fPwdBtn.text();
				jQuery.ajax({
					type:"POST",
					url: "'.home_url().'/wp-admin/admin-ajax.php",
					data: forgotPwdForm,
					beforeSend: function() { fPwdBtn.text("Processing..."); },
					complete: function() { fPwdBtn.text(fPwdBtnText) },
					success:function(data){
						var objf = JSON.parse(data);
						jQuery("#fp_return_message").removeClass("hidden");
						if( objf.error ){
							jQuery("#fp_return_message").removeClass("text-success").addClass("text-danger").html( objf.error );
						}
						if( objf.success ){
							jQuery("#fp_return_message").removeClass("text-danger").addClass("text-success").html( objf.success );
							jQuery("#forgot_pwd_form input[type=text]").val("");
							//window.location.href="'.home_url('/my-account/').'";
						}
						jQuery("html, body").animate({
							scrollTop: jQuery("#forgot_pwd_form").offset().top
						}, 500);
					}
				});
				return false;
			}
			</script>';
		return $form;
	}
	add_shortcode( 'forgot_password_form', 'forgot_password_form_func' );
}