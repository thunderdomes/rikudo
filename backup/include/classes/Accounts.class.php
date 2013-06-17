<?php

/***
 *	Class Accounts
 *  -------------- 
 *  Description : encapsulates account properties
 *	Usage       : MicroCMS, HotelSite, ShoppingCart, BusinessDirectory
 *  Updated	    : 31.05.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.1
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct
 *	__destruct
 *	GetParameter
 *	ChangePassword
 *	ChangeEmail
 *	ChangeLang
 *	SendPassword
 *	
 **/

class Accounts {

	public $account_name;	
	public $error;

	protected $account_id;
	
	private $account_password;
	private $account_email;
	private $preferred_language;
	private $account_type;
	
	//==========================================================================
    // Class Constructor
	// 		@param $account
	//==========================================================================
	function __construct($account = 0)
	{
		$this->account_id = $account;
		$this->error      = "";
		
		// Get account information only if the class was created with some valid account_id
		if($this->account_id!="0"){
			$sql = "SELECT * FROM ".TABLE_ACCOUNTS." WHERE id = ".(int)$this->account_id;
			$temp = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
			if(is_array($temp)){
				$this->account_email = isset($temp['email']) ? $temp['email'] : "";
				$this->account_name = isset($temp['user_name']) ? $temp['user_name'] : "";
				$this->account_password = isset($temp['password']) ? $temp['password'] : "";
				$this->preferred_language = isset($temp['preferred_language']) ? $temp['preferred_language'] : "";
				$this->account_type = isset($temp['account_type']) ? $temp['account_type'] : ""; 
			}			
		}
	}

	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/***
	 * Get Parameter
	 *		@param $param
	 **/
	public function GetParameter($param = "")
	{
		if($param == "email"){
			return $this->account_email;
		}else if($param == "preferred_language"){
			return $this->preferred_language;
		}else if($param == "account_type"){
			return $this->account_type;
		}
		return "";
	}

	/***
	 * Change Password
	 *		@param $password
	 *		@param $confirmation - confirm password
	 **/
	public function ChangePassword($password, $confirmation)
	{
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			return draw_important_message(_OPERATION_BLOCKED, false);
		}
				
		if(!empty($password) && !empty($confirmation) && strlen($password) >= 6) {
			if($password == $confirmation){
				if(!PASSWORDS_ENCRYPTION){
					$sql = "UPDATE ".TABLE_ACCOUNTS." SET password = '".encode_text($password)."' WHERE id = ".(int)$this->account_id;
				}else{
					if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
						$sql = "UPDATE ".TABLE_ACCOUNTS." SET password = AES_ENCRYPT('".$password."', '".PASSWORDS_ENCRYPT_KEY."') WHERE id = ".(int)$this->account_id;
					}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
						$sql = "UPDATE ".TABLE_ACCOUNTS." SET password = '".md5($password)."' WHERE id = ".(int)$this->account_id;
					}else{
						$sql = "UPDATE ".TABLE_ACCOUNTS." SET password = AES_ENCRYPT('".$password."', '".PASSWORDS_ENCRYPT_KEY."') WHERE id = ".(int)$this->account_id;
					}
				}
				if(database_void_query($sql)){
					return draw_success_message(_PASSWORD_CHANGED, false);
				}else{
					return draw_important_message(_PASSWORD_NOT_CHANGED, false);
				}								
			}else return draw_important_message(_PASSWORD_DO_NOT_MATCH, false);
		}else return draw_important_message(_PASSWORD_IS_EMPTY, false);
	}

	/***
	 * Change Email
	 *		@param $email
	 **/
	public function ChangeEmail($email)
	{
		global $objLogin;
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			return draw_important_message(_OPERATION_BLOCKED, false);
		}
				
		if(!empty($email)){
			if(check_email_address($email)){
				$sql = "UPDATE ".TABLE_ACCOUNTS." SET email = '".encode_text($email)."' WHERE id = ".(int)$this->account_id;
				if(database_void_query($sql)){
					$this->account_email = $email;
					$objLogin->UpdateLoggedEmail($email);
					return draw_success_message(_CHANGES_SAVED, false);
				}else{
					return draw_important_message(_TRY_LATER, false);
				}
			}else return draw_important_message(_EMAIL_IS_WRONG, false);
		}else return draw_important_message(_EMAIL_EMPTY_ALERT, false);
	}

	/***
	 * Change Parameter
	 *		@param $param_val
	 **/
	public function ChangeLang($param_val)
	{
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			return draw_important_message(_OPERATION_BLOCKED, false);
		}
		
		global $objLogin;
				
		if(!empty($param_val)){
			$sql = "UPDATE ".TABLE_ACCOUNTS." SET preferred_language = '".encode_text($param_val)."' WHERE id = ".(int)$this->account_id;
			if(database_void_query($sql)){
				$this->preferred_language = $param_val;
				@$objLogin->SetPreferredLang($param_val);
				return draw_success_message(_SETTINGS_SAVED, false);
			}else{
				return draw_important_message(_TRY_LATER, false);
			}
		}else return draw_important_message(str_replace("_FIELD_", _PREFERRED_LANGUAGE, _FIELD_CANNOT_BE_EMPTY), false);
	}

	/***
	 * Send forgotten password
	 *		@param $email
	 **/
	public function SendPassword($email)
	{
		global $objSettings;
		
		$lang = Application::Get("lang");
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}
				
		if(!empty($email)) {
			if(check_email_address($email)){   

				if(!PASSWORDS_ENCRYPTION){
					$sql = "SELECT id, first_name, last_name, user_name, password FROM ".TABLE_ACCOUNTS." WHERE email = '".encode_text($email)."' AND is_active = 1";
				}else{
					if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
						$sql = "SELECT id, first_name, last_name, user_name, AES_DECRYPT(password, '".PASSWORDS_ENCRYPT_KEY."') as password FROM ".TABLE_ACCOUNTS." WHERE email = '".encode_text($email)."' AND is_active = 1";
					}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
						$sql = "SELECT id, first_name, last_name, user_name, '' as password FROM ".TABLE_ACCOUNTS." WHERE email = '".encode_text($email)."' AND is_active = 1";
					}				
				}
				
				$temp = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
				if(is_array($temp) && count($temp) > 0){

					//////////////////////////////////////////////////////////////////
					$sender = $objSettings->GetParameter("admin_email");
					$recipiant = $email;
	
					$objEmailTemplates = new EmailTemplates();
					$email_template = $objEmailTemplates->GetTemplate("password_forgotten", $lang);				
					$subject 		= $email_template["template_subject"];
					$body_middle 	= $email_template["template_content"];

					if(!PASSWORDS_ENCRYPTION){
						$password = $temp['password'];
					}else{
						if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
							$password = $temp['password'];
						}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
							$password = get_random_string(8);
							$sql = "UPDATE ".TABLE_ACCOUNTS." SET password = '".md5($password)."' WHERE id = ".(int)$temp['id'];
							database_void_query($sql);
						}				
					}

					$body_middle = str_ireplace("{FIRST NAME}", $temp['first_name'], $body_middle);
					$body_middle = str_ireplace("{LAST NAME}", $temp['last_name'], $body_middle);
					$body_middle = str_ireplace("{USER NAME}", $temp['user_name'], $body_middle);
					$body_middle = str_ireplace("{USER PASSWORD}", $password, $body_middle);
					$body_middle = str_ireplace("{WEB SITE}", $_SERVER['SERVER_NAME'], $body_middle);
					$body_middle = str_ireplace("{BASE URL}", APPHP_BASE, $body_middle);
					$body_middle = str_ireplace("{YEAR}", date("Y"), $body_middle);					

					$body  = "<div style=direction:".Application::Get("lang_dir").">";
					$body .= $body_middle;
					$body .= "</div>";
	
					$text_version = strip_tags($body);
					$html_version = nl2br($body);
	
					$objEmail = new Email($recipiant, $sender, $subject);                     
					$objEmail->textOnly = false;
					$objEmail->content = $html_version;                
					$objEmail->Send();
					//////////////////////////////////////////////////////////////////
					
					return true;					
				}else{
					$this->error = _EMAIL_NOT_EXISTS;
					return false;
				}				
			}else{
				$this->error = _EMAIL_IS_WRONG;
				return false;								
			}
		}else{
			$this->error = _EMAIL_EMPTY_ALERT;
			return false;
		}
		return true;
	}

}
?>