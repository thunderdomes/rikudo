<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

	$m = isset($_REQUEST['m']) ? prepare_input($_REQUEST['m']) : "";
	$act = isset($_POST['act']) ? prepare_input($_POST['act']) : "";

	if(!@$objLogin->IsLoggedIn()){
		$current_client_id = (isset($_SESSION['current_client_id'])) ? (int)$_SESSION['current_client_id'] : "";
	}else{
		$current_client_id = @$objLogin->GetLoggedID();
	}

	$objReservation = new Reservation(); 
	//--------------------------------------------------------------------------
    // *** redirect if reservation cart is empty
	if($objReservation->IsCartEmpty()){
        header("location: index.php?page=booking");
        echo "<p>if your browser doesn't support redirection please click <a href='index.php?page=booking'>here</a>.</p>";        
        exit;		
	}
		
	//--------------------------------------------------------------------------
    // *** redirect if account was created (if not from checkout shipping)
    if($current_client_id != "" && $m != "edit"){
        header("location: index.php?page=booking_checkout&m=3");
        echo "<p>if your browser doesn't support redirection please click <a href='index.php?page=booking_checkout&m=3'>here</a>.</p>";        
        exit;
    }

	$objBookingSettings = new ModulesSettings("booking");
	$registration_required = $objBookingSettings->GetSettings("client_registration_required");
	$is_active = $objBookingSettings->GetSettings("is_active");

	include_once("modules/captcha/securimage.php");
	$objImg = new Securimage();
	
	$send_updates = isset($_POST['send_updates']) ? (int)$_POST['send_updates'] : "1";
	$first_name  = isset($_POST['first_name']) ? prepare_input($_POST['first_name']) : "";
	$last_name   = isset($_POST['last_name']) ? prepare_input($_POST['last_name']) : "";

	$birth_date_year = isset($_POST['birth_date__nc_year']) ? prepare_input($_POST['birth_date__nc_year']) : "";
	$birth_date_month = isset($_POST['birth_date__nc_month']) ? prepare_input($_POST['birth_date__nc_month']) : "";
	$birth_date_day = isset($_POST['birth_date__nc_day']) ? prepare_input($_POST['birth_date__nc_day']) : "";
	$birth_date = $birth_date_year."-".$birth_date_month."-".$birth_date_day;
	if($birth_date == "--") $birth_date = "0000-00-00";

	$company     = isset($_POST['company']) ? prepare_input($_POST['company']) : "";
	$b_address   = isset($_POST['b_address']) ? prepare_input($_POST['b_address']) : "";
	$b_address_2 = isset($_POST['b_address_2']) ? prepare_input($_POST['b_address_2']) : "";
	$b_city      = isset($_POST['b_city']) ? prepare_input($_POST['b_city']) : "";
	$b_state     = isset($_POST['b_state']) ? prepare_input($_POST['b_state']) : "";
	$b_country   = isset($_POST['b_country']) ? prepare_input($_POST['b_country']) : "";
	$b_zipcode   = isset($_POST['b_zipcode']) ? prepare_input($_POST['b_zipcode']) : "";
	$phone       = isset($_POST['phone']) ? prepare_input($_POST['phone']) : "";
	$email       = isset($_POST['email']) ? prepare_input($_POST['email']) : "";
	$url         = isset($_POST['url']) ? prepare_input($_POST['url'], false, "medium") : "";
	$user_name   = isset($_POST['user_name']) ? prepare_input($_POST['user_name']) : "";
	$user_password1 = isset($_POST['user_password1']) ? prepare_input($_POST['user_password1']) : "";
	$user_password2 = isset($_POST['user_password2']) ? prepare_input($_POST['user_password2']) : "";
	$agree       = isset($_POST['agree']) ? (int)$_POST['agree'] : "";	
	$captcha_code= isset($_POST['captcha_code']) ? prepare_input($_POST['captcha_code']) : "";
	$focus_field  = "first_name";

	$msg_default = draw_message(_ACCOUNT_CREATE_MSG, false);
	$msg = "";

	$account_created = false;
	
	if($act == "create" || $act == "update")
	{
		if($first_name == ""){
			$msg = draw_important_message(_FIRST_NAME_EMPTY_ALERT, false);
		}else if($last_name == ""){
			$msg = draw_important_message(_LAST_NAME_EMPTY_ALERT, false);
		}else if($birth_date != "" && !check_date($birth_date)){
			$msg = draw_important_message(_BIRTH_DATE_VALID_ALERT, false);
		}else if($b_address == ""){
			$msg = draw_important_message(_ADDRESS_EMPTY_ALERT, false);
		}else if($b_city == ""){
			$msg = draw_important_message(_CITY_EMPTY_ALERT, false);
		}else if($b_country == ""){
			$msg = draw_important_message(_COUNTRY_EMPTY_ALERT, false);
		}else if($b_zipcode == ""){
			$msg = draw_important_message(_ZIPCODE_EMPTY_ALERT, false);
		}else if($phone == ""){
			$msg = draw_important_message(_PHONE_EMPTY_ALERT, false);
		}else if($email == ""){
			$msg = draw_important_message(_EMAIL_EMPTY_ALERT, false);
			$focus_field = "email";
		}else if(($email != "") && (!check_email_address($email))){
			$msg = draw_important_message(_EMAIL_VALID_ALERT, false);
			$focus_field = "email";
		}else{
			if($registration_required == "yes"){
				if($user_name == ""){
					$msg = draw_important_message(_USERNAME_EMPTY_ALERT, false);
					$focus_field = "user_name";
				}else if(($user_name != "") && (strlen($user_name) < 6)){
					$msg = draw_important_message(_USERNAME_LENGTH_ALERT, false);
					$focus_field = "user_name";
				}else if($user_password1 == ""){
					$msg = draw_important_message(_PASSWORD_IS_EMPTY, false);					
					$user_password1 = $user_password2 = "";
					$focus_field = "user_password1";
				}else if(($user_password1 != "") && (strlen($user_password1) < 6)){
					$msg = draw_important_message(_PASSWORD_IS_EMPTY, false);
					$user_password1 = $user_password2 = "";
					$focus_field = "user_password1";
				}else if(($user_password1 != "") && ($user_password2 == "")){
					$msg = draw_important_message(_CONF_PASSWORD_IS_EMPTY, false);
					$user_password1 = $user_password2 = "";
					$focus_field = "user_password2";
				}else if(($user_password1 != "") && ($user_password2 != "") && ($user_password1 != $user_password2)){
					$msg = draw_important_message(_CONF_PASSWORD_MATCH, false);
					$user_password1 = $user_password2 = "";
					$focus_field = "user_password2";
				}				
			}			
			if($msg == "" && $m != "edit"){
				if($agree == ""){
					$msg = draw_important_message(_CONFIRM_TERMS_CONDITIONS, false);
				}else if(!$objImg->check($captcha_code)){
					$msg = draw_important_message(_WRONG_CODE_ALERT, false);
					$focus_field = "user_password2";
				}
			}
		}		
	}

	if($act == "create"){
		if($registration_required == "yes"){
			if($msg == ""){
				// check if email already exists                    
				$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE email = '".$email."'";
				$result = database_query($sql, DATA_AND_ROWS);
				if($result[1] > 0){
					$msg = draw_important_message(_USER_EMAIL_EXISTS_ALERT, false);
				}			
			}			

			if($msg == ""){
				// check if such user already exists                    
				$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE user_name != '' AND user_name = '".$user_name."'";
				$result = database_query($sql, DATA_AND_ROWS);
				if($result[1] > 0){
					$msg = draw_important_message(_USER_EXISTS_ALERT, false);
				}
			}			
		}else{
			if($msg == ""){
				// check if such user already exists
				$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE email = '".$email."' AND user_name != ''";
				$result = database_query($sql, DATA_AND_ROWS);
				if($result[1] > 0){
					$msg = draw_important_message(_USER_EMAIL_EXISTS_ALERT, false);
				}			
			}
		}

		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			$msg = draw_important_message(_OPERATION_BLOCKED, false);
		}				

		if($msg == ""){
			$user_ip = get_current_ip();
			
			$registration_code = "";
			$is_active = "1";

			if($registration_required != "yes"){
				$user_password = "''";
			}else{
				if(!PASSWORDS_ENCRYPTION){
					$user_password = "'".$user_password1."'";
				}else{
					if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){					
						$user_password = "AES_ENCRYPT('".$user_password1."', '".PASSWORDS_ENCRYPT_KEY."')";
					}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
						$user_password = "MD5('".$user_password1."')";
					}				
				}
			}
		
			// insert new user
			$sql = "INSERT INTO ".TABLE_CLIENTS."(
						first_name,
						last_name,
						birth_date,
						company,
						b_address,
						b_address_2,
						b_city,
						b_state,
						b_country,
						b_zipcode,
						phone,
						email,
						url,
						user_name,
						user_password,
						date_created,
						registered_from_ip,
						last_logged_ip,
						email_notifications,
						comments,
						is_active,
						is_removed,
						registration_code)
					VALUES(
						'".$first_name."',
						'".$last_name."',
						'".$birth_date."',
						'".$company."',
						'".$b_address."',
						'".$b_address_2."',
						'".$b_city."',
						'".$b_state."',
						'".$b_country."',
						'".$b_zipcode."',
						'".$phone."',
						'".$email."',
						'".$url."',
						'".$user_name."',
						".$user_password.",
						'".date("Y-m-d H:i:s")."',
						'".$user_ip."',
						'',
						'".$send_updates."',
						'',
						".$is_active.",
						0,
						'".$registration_code."') 
					ON DUPLICATE KEY UPDATE
						first_name  = VALUES(first_name),
						last_name   = VALUES(last_name),
						company     = VALUES(company),
						b_address   = VALUES(b_address),
						b_address_2 = VALUES(b_address_2),
						b_city      = VALUES(b_city),
						b_state     = VALUES(b_state),
						b_country   = VALUES(b_country),
						b_zipcode   = VALUES(b_zipcode),
						phone       = VALUES(phone),
						email       = VALUES(email),
						url         = VALUES(url),
						last_logged_ip = VALUES(last_logged_ip),
						email_notifications = VALUES(email_notifications)";
			if(database_void_query($sql) > 0){		
				$account_created = true;
                $_SESSION['current_client_id'] = mysql_insert_id();
                header("location: index.php?page=booking_checkout&m=1");
                exit;		
			}else{
				$msg = draw_important_message(_CREATING_ACCOUNT_ERROR, false);
			}                    		
		}		
	}else if($act == "update"){

		// check if email already exists                    
		$sql = "SELECT COUNT(*) as cnt FROM ".TABLE_CLIENTS." WHERE email = '".$email."' AND id != ".$current_client_id;
		$result = database_query($sql, DATA_ONLY);
		if($result[0]['cnt'] > 0){
			$msg = draw_important_message(_USER_EMAIL_EXISTS_ALERT, false);
		}			
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			$msg = draw_important_message(_OPERATION_BLOCKED, false);
		}				

		if($msg == ""){			
			// insert new user
			$sql = "UPDATE ".TABLE_CLIENTS." SET
						first_name = '".$first_name."',
						last_name = '".$last_name."',
						company = '".$company."',
						b_address = '".$b_address."',
						b_address_2 = '".$b_address_2."',
						b_city = '".$b_city."',
						b_state = '".$b_state."',
						b_country = '".$b_country."',
						b_zipcode = '".$b_zipcode."',
						phone = '".$phone."',
						email = '".$email."',
						url = '".$url."'
					WHERE id = ".$current_client_id;
			if(database_void_query($sql) > 0){		
				$account_created = true;
                header("location: index.php?page=booking_checkout&m=2");
                exit;		
			}else{
				$msg = draw_important_message(_UPDATING_ACCOUNT_ERROR, false);
			}
		}
	}
	

    if($current_client_id != "" && $m == "edit"){		
		$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE id = ".(int)$current_client_id;
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$send_updates = $result[0]['email_notifications'];
			$first_name  = $result[0]['first_name'];
			$last_name   = $result[0]['last_name'];
			$birth_date  = $result[0]['birth_date'];
			$company     = $result[0]['company'];
			$b_address   = $result[0]['b_address'];
			$b_address_2 = $result[0]['b_address_2'];
			$b_city      = $result[0]['b_city'];
			$b_state     = $result[0]['b_state'];
			$b_country   = $result[0]['b_country'];
			$b_zipcode   = $result[0]['b_zipcode'];
			$phone       = $result[0]['phone'];
			$email       = $result[0]['email'];
			$url         = $result[0]['url'];
			$user_name   = $result[0]['user_name'];
			$user_password1 = $result[0]['user_password'];
			$user_password2 = $result[0]['user_password'];
		}
	}


?>