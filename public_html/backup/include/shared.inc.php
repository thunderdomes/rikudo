<?php

// *** remote file inclusion, check for strange characters in $_GET keys
// *** all keys with "/" or "\" or ":" are blocked, so it becomes virtually impossible
// *** to inject other pages or websites
foreach($_GET as $get_key => $get_value){
	if(is_string($get_value) &&
  	 ((preg_match("/\//", $get_value)) ||
	  (preg_match("/\[\\\]/", $get_value)) ||
	  (preg_match("/:/", $get_value))
	 ))
	{
		eval("unset(\${$get_key});");
		die("A hacking attempt has been detected. For security reasons, we're blocking any code execution.");
	}
}

// *** check token for POST requests
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){	
	//if(isset($_POST['token']) && isset($_SESSION['token']) && $_POST['token'] != $_SESSION['token']){
	    $token_post = isset($_POST['token']) ? $_POST['token'] : 'post';
    $token_session = isset($_SESSION['token']) ? $_SESSION['token'] : 'session';
    if($token_session != $token_post){

		unset($_POST['submition_type']); // for settings page
										 //     vocabulary
										 //     backup
        unset($_POST['submit_type']);    // for Admin my_account page
		unset($_POST['mg_action']);      // for MicroGrid pages
		unset($_POST['task']);           // for room prices,
										 //	    room availability 
										 //     booking_payment
										 //     mass_mail
										 //     client/confirm_registration
										 //     client/my_account page
		unset($_POST['act']);            // for booking_details page
		                                 // 	menus
										 // 	pages
										 // 	languages
										 //     vocabulary
										 //     client/create_account
										 //     client/password_forgotten
        //unset($_POST['tabid']);        // for Tabs operations
	    //unset($_POST['submit_login']); // for login page
	}
}

// *** disabling magic quotes at runtime
if(get_magic_quotes_gpc()){
    function stripslashes_gpc(&$value) {
		$value = stripslashes($value);	
	}
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

?>