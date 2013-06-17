<?php

/**
 *	Class Login
 *  -------------- 
 *  Description : encapsulates login properties
 *  Updated     : 06.04.2011	
 *	Written by  : ApPHP
 *	
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct                                     DoLogin
 *  __destruct                                      GetAccountInformation
 *  IsWrongLogin                                    SetSessionVariables 
 *  DoLogout                                        GetUniqueUrl
 *  GetLastLoginTime                                UpdateAccountInfo 
 *  IsLoggedIn                                      
 *  IsLoggedInAs                                    
 *  IsLoggedInAsAdmin
 *  IsLoggedInAsClient
 *  GetLoggedType
 *  GetLoggedEmail
 *  GetLoggedName
 *  GetLoggedFirstName
 *  GetLoggedLastName
 *  UpdateLoggedEmail
 *  GetLoggedID
 *  GetPreferredLang
 *  SetPreferredLang
 *  GetActiveMenuCount
 *  DrawLoginLinks
 *  IsIpAddressBlocked
 *  IsEmailBlocked
 *  IpAddressBlocked
 *  EmailBlocked
 *  RemoveAccount
 *  
 **/

class Login {

	private $wrongLogin;
	private $ipAddressBlocked;
	private $emailBlocked;
	private $active_menu_count;
	private $account_type;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{		
		$this->ipAddressBlocked = false;
		$this->emailBlocked = false;

		$submit_login  = isset($_POST['submit_login']) ? prepare_input($_POST['submit_login']) : "";
		$submit_logout = isset($_POST['submit_logout']) ? prepare_input($_POST['submit_logout']) : "";
		$user_name 	   = isset($_POST['user_name']) ? prepare_input($_POST['user_name'], true) : "";
		$password      = isset($_POST['password']) ? prepare_input($_POST['password'], true) : "";
		$this->account_type = isset($_POST['type']) ? prepare_input($_POST['type']) : "client";
		
		$this->wrongLogin = false;		
		if(!$this->IsLoggedIn()){
			if($submit_login == "login"){
				if(empty($user_name) || empty($password)){
					$this->wrongLogin = true;							
				}else{
					$this->DoLogin($user_name, $password);
				}
			}			
		}else if($submit_logout == "logout"){
			$this->DoLogout();
		}
		$this->active_menu_count = 0;
	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/**
	 * 	Do login
	 * 		@param $user_name - system name of user
	 * 		@param $password - password of user
	 * 		@param $do_redirect - prepare redirect or not
	 */
	private function DoLogin($user_name, $password, $do_redirect = true)
	{
		$ip_address = get_current_ip();

		if($account_information = $this->GetAccountInformation($user_name, $password)){
			$this->SetSessionVariables($account_information);

			if($this->IsLoggedInAsClient(false)){
				if($this->IpAddressBlocked($ip_address)){
					$this->DoLogout();
					$this->ipAddressBlocked = true;
					$do_redirect = false;
				}else if($this->EmailBlocked($this->GetLoggedEmail(false))){
					$this->DoLogout();
					$this->emailBlocked = true;
					$do_redirect = false;			
				}
			}

			$this->UpdateAccountInfo($account_information);
			if($do_redirect){
				$GLOBALS['user_session']->SetFingerInfo();
				header("location: index.php");
				exit;
			}
		}else{
			$this->wrongLogin = true;
		}
	}
	
	/**
	 * 	Checks if login was wrong
	 */
	public function IsWrongLogin()
	{
		return ($this->wrongLogin == true) ? true : false;	
	}

	/**
	 * 	Checks if IP address was blocked
	 */
	public function IsIpAddressBlocked()
	{
		return ($this->ipAddressBlocked == true) ? true : false;	
	}

	/**
	 * 	Checks if IP address was blocked
	 * 	
	 */
	public function IsEmailBlocked()
	{
		return ($this->emailBlocked == true) ? true : false;	
	}

	/**
	 * 	Destroys the session
	 */
	public function DoLogout()
	{
		$redirect = ($this->IsLoggedInAsAdmin()) ? "index.php?admin=login" : "";
		$GLOBALS['user_session']->EndSession();
		
		if($redirect != ""){
			header("location: ".$redirect);
			exit;			
		}
	}

	/**
	 * 	Checks Ip Address
	 * 		@param $ip_address
	 */
	public function IpAddressBlocked($ip_address)
	{
		$sql = "SELECT ban_item
				FROM ".TABLE_BANLIST."
				WHERE ban_item = '".$ip_address."' AND ban_item_type = 'IP'";
		return database_query($sql, ROWS_ONLY);		
	}

	/**
	 * 	Checks Email Address
	 * 		@param $email
	 */
	public function EmailBlocked($email)
	{
		$sql = "SELECT ban_item
				FROM ".TABLE_BANLIST."
				WHERE ban_item = '".$email."' AND ban_item_type = 'Email'";
		return database_query($sql, ROWS_ONLY);		
	}

	/**
	 * 	Returns the account information
	 * 		@param $user_name - system name of user
	 * 		@param $password - password of user
	 */
	private function GetAccountInformation($user_name, $password)
	{
		if(PASSWORDS_ENCRYPTION){			
			if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
				$password = "AES_ENCRYPT('".$password."', '".PASSWORDS_ENCRYPT_KEY."')";
			}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
				$password = "MD5('".$password."')";
			}	
		}else{
			$password = "'".$password."'";
		}
		if($this->account_type == "admin"){
			$sql = "SELECT ".TABLE_ACCOUNTS.".*, user_name AS account_name
					FROM ".TABLE_ACCOUNTS."
					WHERE
						user_name = '" . $user_name . "' AND 
						password = ".$password;			
		}else{
			$sql = "SELECT ".TABLE_CLIENTS.".*, user_name AS account_name
					FROM ".TABLE_CLIENTS."
					WHERE
						user_name = '".$user_name."' AND 
						user_password = ".$password." AND
						is_removed = 0";
		}
		$sql .= " AND is_active = 1";			
		return database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
	}

	/**
	 * 	Returns last login time
	 */
	public function GetLastLoginTime()
	{
		$last_login = $GLOBALS['user_session']->GetSessionVariable("session_last_login");

		if($last_login != "" && $last_login != "0000-00-00 00:00:00" && $last_login != "NULL"){
			return $last_login;
		}else{
			return "--";
		}
	}

	/**
	 * 	Checks to see if the user is logged in
	 * 		@return a 1 if the user is logged in, 0 otherwise
	 */
	public function IsLoggedIn()
	{
		$logged = $GLOBALS['user_session']->GetSessionVariable('session_account_logged');
		$id = $GLOBALS['user_session']->GetSessionVariable('session_account_id');
		if($logged == str_replace("modules/wysiwyg/addons/imagelibrary/", "", $this->GetUniqueUrl()).$id){
			if(!$GLOBALS['user_session']->AnalyseFingerInfo()){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 	Checks to see if the user is logged in as a specific account type
	 * 		@return true if the user is logged in as specified account type, false otherwise
	 */
	public function IsLoggedInAs()
	{
		if(!$this->IsLoggedIn()) return false;

		$account_type = $GLOBALS['user_session']->GetSessionVariable("session_account_type");

		$types = func_get_args();
		foreach($types as $type){
			$type_parts = explode(",", $type);
			foreach($type_parts as $type_part){
				if($account_type == $type_part) return true;	
			}			
		}
		return false;
	}

	/**
	 * 	Checks to see if the user is logged in as a specific account type
	 * 		@return true if the user is logged in as specified account type false otherwise
	 */
	public function IsLoggedInAsAdmin()
	{
		if (!$this->IsLoggedIn()) return false;
		$account_type = $GLOBALS['user_session']->GetSessionVariable("session_account_type");
		if($account_type == "owner" || $account_type == "mainadmin" || $account_type == "admin") return true;
		return false;
	}

	/**
	 * 	Checks to see if the user is logged in as a specific account type
	 * 		@return true if the user is logged in as specified account type false otherwise
	 */
	public function IsLoggedInAsClient($check = true)
	{
		if(!$this->IsLoggedIn() && $check) return false;
		$account_type = $GLOBALS['user_session']->GetSessionVariable("session_account_type");
		if ($account_type == "client") return true;
		return false;
	}
	
	/**
	 * 	Returns logged user type
	 */
	public function GetLoggedType()
	{
		if (!$this->IsLoggedIn()) return false;				
		return $GLOBALS['user_session']->GetSessionVariable("session_account_type");
	}

	/**
	 * 	Returns logged user email
	 */
	public function GetLoggedEmail($check = true)
	{
		if(!$this->IsLoggedIn() && $check) return false;
		return $GLOBALS['user_session']->GetSessionVariable("session_user_email");
	}

	/**
	 * 	Sets the email of logged user 
	 */
	public function UpdateLoggedEmail($new_email)
	{
		if(!$this->IsLoggedIn()) return false;
		$GLOBALS['user_session']->SetSessionVariable("session_user_email", $new_email);
	}

	/**
	 * 	Returns logged user name
	 */
	public function GetLoggedName()
	{
		return $GLOBALS['user_session']->GetSessionVariable("session_user_name");
	}
	
	/**
	 * 	Returns the first name of logged user 
	 */
	public function GetLoggedFirstName()
	{
		return $GLOBALS['user_session']->GetSessionVariable("session_user_first_name");
	}
	
	/**
	 * 	Returns the last name of logged user 
	 */
	public function GetLoggedLastName()
	{
		return $GLOBALS['user_session']->GetSessionVariable("session_user_last_name");
	}

	/**
	 * 	Returns logged user ID
	 */
	public function GetLoggedID()
	{
		return $GLOBALS['user_session']->GetSessionVariable("session_account_id");
	}	

	/**
	 * 	Returns preferred language
	 */
	public function GetPreferredLang()
	{
		return $GLOBALS['user_session']->GetSessionVariable("session_preferred_language");
	}	

	/**
	 * 	Sets preferred language
	 * 		@param $lang
	 */
	public function SetPreferredLang($lang)
	{
		$GLOBALS['user_session']->SetSessionVariable("session_preferred_language", $lang);
	}	

	/**
	 * 	Sets the session variables and performs the login
	 * 		@param $account_information - array
	 */
	private function SetSessionVariables($account_information)
	{		
		$GLOBALS['user_session']->SetSessionVariable("session_account_logged", (($account_information['id']) ? $this->GetUniqueUrl().$account_information['id'] : false));			
		$GLOBALS['user_session']->SetSessionVariable("session_account_id", $account_information['id']);
		$GLOBALS['user_session']->SetSessionVariable("session_user_name", $account_information['user_name']);
		$GLOBALS['user_session']->SetSessionVariable("session_user_first_name", $account_information['first_name']);
		$GLOBALS['user_session']->SetSessionVariable("session_user_last_name", $account_information['last_name']);		
		$GLOBALS['user_session']->SetSessionVariable("session_user_email", $account_information['email']);
		$GLOBALS['user_session']->SetSessionVariable("session_account_type", (($this->account_type == "admin") ? $account_information['account_type'] : "client"));
		$GLOBALS['user_session']->SetSessionVariable("session_last_login", @date("Y-m-d H:i:s"));
		$GLOBALS['user_session']->SetSessionVariable("session_preferred_language", isset($account_information['preferred_language']) ? $account_information['preferred_language'] : "");		
	}

	/**
	 *  Returns unique URL 
	 */
	private function GetUniqueUrl()
	{
		$port = "";
		$http_host = $_SERVER['HTTP_HOST'];
		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != "80"){
			if(!strpos($_SERVER['HTTP_HOST'], ":")){
				$port = ":".$_SERVER['SERVER_PORT'];
			}
		}	
		$folder = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);	
		return $http_host.$port.$folder;
	}
	
	/**
	 * 	Returns count of active menus
	 * 		@return number on menus
	 */
	public function GetActiveMenuCount()
	{
		return $this->active_menu_count;
	}	
	
	/**
	 * 	Updates Account Info	
	 * 		@param $account_information - array
	 */
	private function UpdateAccountInfo($account_information)
	{
		if($this->account_type == "admin"){
			$sql = "UPDATE ".TABLE_ACCOUNTS."
					SET last_login = '".@date("Y-m-d H:i:s")."'
					WHERE id = ".(int)$account_information['id']."";
		}else{
			$sql = "UPDATE ".TABLE_CLIENTS."
					SET
						date_lastlogin = '".@date("Y-m-d H:i:s")."',
						last_logged_ip = '".get_current_ip()."'
					WHERE id = ".(int)$account_information['id']."";			
		}
		return database_void_query($sql);
	}	

	/**
	 * 	Removes user account
	 */	
	public function RemoveAccount()
	{
		$sql = "UPDATE ".TABLE_CLIENTS."
				SET is_removed = 1, is_active = 0, comments = CONCAT(comments, '\r\n".@date("Y-m-d H:i:s")." - account was removed by client. ') 
				WHERE id = ".(int)$this->GetLoggedID();
		return (database_void_query($sql) > 0 ? true : false);
	}

	/**
	 * 	Draws the login links and logout form
	 */
	public function DrawLoginLinks()
	{	
		if(Application::Get("preview") == "yes") return "";
		
		$menu_index = "0";
		$text_align = (Application::Get("lang_dir") == "ltr") ? "text-align: left;" : "text-align: right; padding-right:15px;";
		
		// ---------------------------------------------------------------------
		// MAIN ADMIN LINKS
		if($this->IsLoggedInAsAdmin()){
			draw_block_top(_MENUS.": [ <a id='lnk_all_open' href='javascript:void(0);' onclick='javascript:toggle_menus(1)'>"._OPEN."</a> | <a id='lnk_all_close' href='javascript:void(0);' onclick='javascript:toggle_menus(0)'>"._CLOSE."</a> ]");
			draw_block_bottom();

			draw_block_top(_GENERAL, $menu_index++, "maximized");        
				echo "<ul>";
				echo "<li><a href='index.php?admin=home'>"._HOME."</a></li>";
				if($this->IsLoggedInAs('owner','mainadmin')) echo "<li><a href='index.php?admin=settings'>"._SETTINGS."</a></li>";
				echo "<li><a href='index.php?admin=ban_list'>"._BAN_LIST."</a></li>";
				if($this->IsLoggedInAs('owner','mainadmin')) echo "<li><a href='index.php?admin=countries_management'>"._COUNTRIES."</a></li>";
				echo "<li><a href='index.php?preview=yes'>"._PREVIEW." <img src='images/external_link.gif' alt=''></a></li>";
				echo "</ul>";
			draw_block_bottom();

			draw_block_top(_ACCOUNTS_MANAGEMENT, $menu_index++);
				echo "<div class='menu_category'>";
				echo "<ul>";
				echo "<li><a href='index.php?admin=my_account'>"._MY_ACCOUNT."</a></li>";
				if($this->IsLoggedInAs('owner','mainadmin')) echo "<li><a href='index.php?admin=accounts_statistics'>"._STATISTICS."</a></li>";
				echo "</ul>";
				if($this->IsLoggedInAs('owner','mainadmin')){
					echo "<ul>";
					echo "<label>"._ADMINS_MANAGEMENT."</label>";
					echo "<li><a class='main_menu_link' href='index.php?admin=admins_management'>"._ADMINS."</a></li>";
					echo "</ul>";
				}								
				if(Modules::IsModuleInstalled("clients") && $this->IsLoggedInAs('owner','mainadmin')){
					echo "<label>"._CLIENTS_MANAGEMENT."</label>";
					echo "<ul>";
					echo "<li><a href='index.php?admin=mod_clients_groups'>"._CLIENT_GROUPS."</a></li>";
					echo "<li><a href='index.php?admin=mod_clients_management'>"._CLIENTS."</a></li>";
					echo "</ul>";
				}
				echo "</div>";
			draw_block_bottom();

			draw_block_top(_HOTEL_MANAGEMENT, $menu_index++); 
				echo "<ul>";
				echo "<li><a href='index.php?admin=hotel_info'>"._HOTEL_INFO."</a></li>";
				if($this->IsLoggedInAs('owner','mainadmin')) echo "<li><a href='index.php?admin=mod_rooms_management'>"._ROOMS_MANAGEMENT."</a></li>";
				echo "</ul>";
			draw_block_bottom();

			if(Modules::IsModuleInstalled("booking")){				
				draw_block_top(_BOOKINGS, $menu_index++);
					echo "<div class='menu_category'>";						
					if($this->IsLoggedInAs('owner','mainadmin')){
						echo "<label>"._SETTINGS."</label>";
						echo "<ul>";
						echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_currencies'>"._CURRENCIES."</a></li>";
						echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_campaigns'>"._CAMPAIGNS."</a></li>";
						echo "</ul>";
					}
					echo "<label>"._BOOKINGS_MANAGEMENT."</label>";
					echo "<ul>";			
					echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_reserve_room'>"._MAKE_RESERVATION."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_bookings'>"._BOOKINGS."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_extras'>"._EXTRAS."</a></li>";
					echo "</ul>";
					
					echo "<label>"._INFO_AND_STATISTICS."</label>";
					echo "<ul>";			
					echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_rooms_availability'>"._ROOMS_AVAILABILITY."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_reports'>"._REPORTS."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=mod_booking_statistics'>"._STATISTICS."</a></li>";
					echo "</ul>";
					echo "</div>";						
				draw_block_bottom();				
			}

			if($this->IsLoggedInAs('owner','mainadmin')) {
				draw_block_top(_MENUS_AND_PAGES, $menu_index++);
					echo "<div class='menu_category'>";						
					echo "<label>"._MENU_MANAGEMENT."</label>";
					echo "<ul>";			
					echo "<li><a class='main_menu_link' href='index.php?admin=menus_add'>"._ADD_NEW_MENU."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=menus'>"._EDIT_MENUS."</a></li>";
					echo "</ul>";			
	
					echo "<label>"._PAGE_MANAGEMENT."</label>";
					echo "<ul>";			
					echo "<li><a class='main_menu_link' href='index.php?admin=pages_add'>"._PAGE_ADD_NEW."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=pages_edit'>"._PAGE_EDIT_HOME."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=pages'>"._PAGE_EDIT_PAGES."</a></li>";
					echo "<li><a class='main_menu_link' href='index.php?admin=pages&type=system'>"._PAGE_EDIT_SYS_PAGES."</a></li>";				
					echo "<li><a class='main_menu_link' href='index.php?admin=pages_trash'>"._TRASH."</a></li>";				
					echo "</ul>";
					echo "</div>";						
				draw_block_bottom();
			}

			draw_block_top(_LANGUAGES_SETTINGS, $menu_index++);
				echo "<ul>";			
				if ($this->IsLoggedInAs('owner','mainadmin')) echo "<li><a class='main_menu_link' href='index.php?admin=languages'>"._LANGUAGES."</a></li>";
				echo "<li><a class='main_menu_link' href='index.php?admin=vocabulary&filter_by=A'>"._VOCABULARY."</a></li>";
				echo "</ul>";
			draw_block_bottom();

			if($this->IsLoggedInAs('owner','mainadmin')){
				draw_block_top(_MASS_MAIL_AND_TEMPLATES, $menu_index++);
					echo "<ul>";			
					if($this->IsLoggedInAs('owner')) echo "<li><a href='index.php?admin=email_templates'>"._EMAIL_TEMPLATES."</a></li>";
					if($this->IsLoggedInAs('owner','mainadmin')) echo "<li><a class='main_menu_link' href='index.php?admin=mass_mail'>"._MASS_MAIL."</a></li>";
					echo "</ul>";
				draw_block_bottom();
			}

			draw_block_top(_MODULES, $menu_index++);
				if($this->IsLoggedInAs('owner','mainadmin')){
					echo "<ul>";			
					echo "<li><a style='margin-bottom:5px' href='index.php?admin=modules'>"._MODULES_MANAGEMENT."</a></li>";				
					echo "</ul>";
				}
				
				// MODULES
				$sql = "SELECT * FROM ".TABLE_MODULES." WHERE is_installed = 1 AND is_system = 0 ORDER BY priority_order ASC";
				$modules = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
				
				echo "<div class='menu_category'>";
				for($i=0; $i < $modules[1]; $i++){
					$output = "";
					if($modules[0][$i]['settings_access_by'] == "" || ($modules[0][$i]['settings_access_by'] != "" && $this->IsLoggedInAs($modules[0][$i]['settings_access_by']))){
						if($modules[0][$i]['settings_const'] != "") $output .= "<li><a class='main_menu_link' href='index.php?admin=".$modules[0][$i]['settings_page']."'>".constant($modules[0][$i]['settings_const'])."</a></li>";
					}
					if($modules[0][$i]['management_access_by'] == "" || ($modules[0][$i]['management_access_by'] != "" && $this->IsLoggedInAs($modules[0][$i]['management_access_by']))){
						if($modules[0][$i]['management_const'] != "") $output .= "<li><a class='main_menu_link' href='index.php?admin=".$modules[0][$i]['management_page']."'>".constant($modules[0][$i]['management_const'])."</a></li>";
					}
					if($output){
						echo "<label>".constant($modules[0][$i]['name_const'])."</label>";
						echo "<ul>".$output."</ul>";										
					}
				}
				echo "</div>";
			draw_block_bottom();
		}
	
		// ---------------------------------------------------------------------
		// CLIENTS LINKS
		if($this->IsLoggedInAsClient()){
			draw_block_top(_MY_ACCOUNT);
				echo "<ul style='margin-bottom:15px;'>";
				echo "<li><a href='index.php?client=home'>"._HOME."</a></li>";
				echo "<li><a href='index.php?client=my_account'>"._EDIT_MY_ACCOUNT."</a></li>";
				if(Modules::IsModuleInstalled("booking")){				
					$objCartSettings = new ModulesSettings("booking");
					if($objCartSettings->GetSettings("is_active") == "yes"){
						echo "<li><a href='index.php?client=my_bookings'>"._MY_BOOKINGS."</a></li>";
					}
				}
				echo "</ul>";

				// Logout
				if($this->IsLoggedIn()){
					echo "<form action='index.php' style='margin-bottom:7px;' method='post'>&nbsp;&nbsp;<input type='hidden' name='submit_logout' value='logout' /><input class='form_button' type='submit' name='btnLogout' value='"._BUTTON_LOGOUT."' />&nbsp;&nbsp;</form>";
				}
			draw_block_bottom();
		}		
		
		$this->active_menu_count = $menu_index;
	}
	
}
?>