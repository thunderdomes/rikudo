<?php

/**
 *	Class Clients (for ApPHP HotelSite ONLY)
 *  -------------- 
 *  Description : encapsulates Clients operations & properties
 *  Updated	    : 30.06.2011
 *	Written by  : ApPHP
 *	
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct				SendPassword						
 *  __destruct              GetError
 *  DrawLoginFormBlock      ResetAccount
 *  BeforeEditRecord
 *  BeforeUpdateRecord
 *  AfterUpdateRecord
 *  AfterInsertRecord
 *  Reactivate
 *  GetAllClients
 *  
 **/

class Clients extends MicroGrid {
	
	protected $debug = false;
	
	protected $tableName;
	protected $primaryKey;
	protected $dataSet;
	protected $formActionURL;
	protected $params;
	protected $actions;
	protected $actionIcons;

	protected $languageId;
	protected $allowLanguages;

	protected $VIEW_MODE_SQL;
	protected $EDIT_MODE_SQL;
	protected $DETAILS_MODE_SQL;
	protected $WHERE_CLAUSE;
	protected $ORDER_CLAUSE;
	protected $LIMIT_CLAUSE;

	protected $arrViewModeFields;	
	protected $arrAddModeFields;	
	protected $arrEditModeFields;
	protected $arrDetailsModeFields;
	
	protected $isPagingAllowed;
	protected $pageSize;

	protected $isSortingAllowed;

	protected $isFilteringAllowed;
	protected $arrFilteringFields;

	public $error;
	static private $static_error = "";
	
	private $email_notifications;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{
		global $objSettings;
		
		$this->SetRunningTime();

		$this->params = array();
		if(isset($_POST['group_id']) && ($_POST['group_id'] != ""))  $this->params['group_id']	 = prepare_input($_POST['group_id']); else $this->params['group_id'] = "0";
		if(isset($_POST['first_name'])) $this->params['first_name']  = prepare_input($_POST['first_name']);
		if(isset($_POST['last_name']))	$this->params['last_name']   = prepare_input($_POST['last_name']);
		if(isset($_POST['birth_date']) && ($_POST['birth_date'] != ""))  $this->params['birth_date'] = prepare_input($_POST['birth_date']); else $this->params['birth_date'] = "0000-00-00";	
		if(isset($_POST['company']))   	$this->params['company']     = prepare_input($_POST['company']);
		if(isset($_POST['b_address']))  $this->params['b_address']   = prepare_input($_POST['b_address']);
		if(isset($_POST['b_address_2']))$this->params['b_address_2'] = prepare_input($_POST['b_address_2']);
		if(isset($_POST['b_city']))   	$this->params['b_city']      = prepare_input($_POST['b_city']);
		if(isset($_POST['b_state']))   	$this->params['b_state']     = prepare_input($_POST['b_state']);
		if(isset($_POST['b_country']))	$this->params['b_country']   = prepare_input($_POST['b_country']);
		if(isset($_POST['b_zipcode']))	$this->params['b_zipcode']   = prepare_input($_POST['b_zipcode']);
		if(isset($_POST['phone'])) 		$this->params['phone'] 		 = prepare_input($_POST['phone']);
		if(isset($_POST['email'])) 		$this->params['email'] 		 = prepare_input($_POST['email']);
		if(isset($_POST['url'])) 		$this->params['url'] 		 = prepare_input($_POST['url'], false, "medium");
		if(isset($_POST['user_name']))  $this->params['user_name']   = prepare_input($_POST['user_name']);
		if(isset($_POST['user_password']))  	$this->params['user_password']  = prepare_input($_POST['user_password']);
		if(isset($_POST['date_created']))  		$this->params['date_created']   = prepare_input($_POST['date_created']);
		if(isset($_POST['date_lastlogin']))  	$this->params['date_lastlogin'] = prepare_input($_POST['date_lastlogin']);
		if(isset($_POST['registered_from_ip'])) $this->params['registered_from_ip'] = prepare_input($_POST['registered_from_ip']);
		if(isset($_POST['last_logged_ip'])) 	$this->params['last_logged_ip'] 	= prepare_input($_POST['last_logged_ip']);
		if(isset($_POST['email_notifications'])) 		 $this->params['email_notifications'] 		  = (int)$_POST['email_notifications']; else $this->params['email_notifications'] = "0";
		if(isset($_POST['notification_status_changed'])) $this->params['notification_status_changed'] = prepare_input($_POST['notification_status_changed']);
		if(isset($_POST['orders_count'])) 		$this->params['orders_count'] 		= (int)$_POST['orders_count'];
		if(isset($_POST['rooms_count'])) 	    $this->params['rooms_count'] 		= (int)$_POST['rooms_count'];
		if(isset($_POST['is_active']))  		$this->params['is_active']  		= (int)$_POST['is_active']; else $this->params['is_active'] = "0";
		if(isset($_POST['is_removed'])) 		$this->params['is_removed'] 		= (int)$_POST['is_removed']; else $this->params['is_removed'] = "0";
		if(isset($_POST['comments'])) 			$this->params['comments'] 		 	= prepare_input($_POST['comments']);
		if(isset($_POST['registration_code'])) 	$this->params['registration_code'] 	= prepare_input($_POST['registration_code']);

		$objClientsSettings = new ModulesSettings("clients");
		$allow_adding_by_admin = $objClientsSettings->GetSettings("allow_adding_by_admin");
		$allow_adding = ($allow_adding_by_admin == "yes") ? true : false;
		
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_CLIENTS;
		$this->dataSet 		= array();
		$this->error 		= "";
		///$this->languageId  	= (isset($_REQUEST['language_id']) && $_REQUEST['language_id'] != "") ? $_REQUEST['language_id'] : Languages::GetDefaultLang();
		$this->formActionURL = "index.php?admin=mod_clients_management";
		$this->actions      = array("add"=>$allow_adding, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->WHERE_CLAUSE = "";		
		$this->ORDER_CLAUSE = "ORDER BY id ASC";

		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;

		$total_countries = Countries::GetAllCountries();
		$arr_countries      = array();
		foreach($total_countries[0] as $key => $val){
			$arr_countries[$val['abbrv']] = $val['name'];
		}

		$total_groups = ClientGroups::GetAllGroups();
		$arr_groups = array();
		foreach($total_groups[0] as $key => $val){
			$arr_groups[$val['id']] = $val['name'];
		}

		$this->isFilteringAllowed = true;
		// define filtering fields
		$this->arrFilteringFields = array(
			_USERNAME   => array("table"=>"c", "field"=>"user_name", "type"=>"text", "sign"=>"like%", "width"=>"80px"),			
			_LAST_NAME  => array("table"=>"c", "field"=>"last_name", "type"=>"text", "sign"=>"like%", "width"=>"80px"),
			_EMAIL_ADDRESS => array("table"=>"c", "field"=>"email", "type"=>"text", "sign"=>"like%", "width"=>"80px"),
			"Group"      => array("table"=>"c", "field"=>"group_id", "type"=>"dropdownlist", "source"=>$arr_groups, "sign"=>"=", "width"=>"85px"),
			//_ACTIVE     => array("table"=>"c", "field"=>"is_active", "type"=>"dropdownlist", "source"=>array("0"=>_NO, "1"=>_YES), "sign"=>"=", "width"=>"85px"),
			//_REMOVED    => array("table"=>"c", "field"=>"is_removed", "type"=>"dropdownlist", "source"=>array("0"=>_NO, "1"=>_YES), "sign"=>"=", "width"=>"85px"),
		);

		$datetime_format = get_date_format();
		$date_format = get_date_format(false);

		// prepare languages array		
		#$total_languages = Languages::GetAll();
		#$arr_languages      = array();
		#foreach($total_languages[0] as $key => $val){
		#	$arr_languages[$val['abbreviation']] = $val['lang_name'];
		#}

		$user_ip = get_current_ip();
		

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT
									c.".$this->primaryKey.",
		                            c.*,
									CONCAT(c.first_name, ' ',c.last_name) as full_name,
									IF(c.user_name != '', c.user_name, '<span class=gray>"._WITHOUT_ACCOUNT."</span>') as mod_user_name,
									IF(c.is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as user_active,
									cnt.name as country_name,
									cg.name as group_name									
								FROM ".$this->tableName." c
									LEFT OUTER JOIN ".TABLE_COUNTRIES." cnt ON c.b_country = cnt.abbrv
									LEFT OUTER JOIN ".TABLE_CLIENT_GROUPS." cg ON c.group_id = cg.id ";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"full_name"    => array("title"=>_CLIENT_NAME, "type"=>"label", "align"=>"left", "width"=>""),
			"mod_user_name"=> array("title"=>_USERNAME, "type"=>"label", "align"=>"left", "width"=>""),
			"email" 	   => array("title"=>_EMAIL_ADDRESS, "type"=>"link", "href"=>"mailto://{email}", "align"=>"left", "width"=>""),
			"country_name" => array("title"=>_COUNTRY, "type"=>"label", "align"=>"left", "width"=>"90px"),
			"user_active"  => array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"90px"),
			"orders_count" => array("title"=>_BOOKINGS, "type"=>"label", "align"=>"center", "width"=>"80px"),
			"group_name"   => array("title"=>"Group", "type"=>"label", "align"=>"left", "width"=>"90px"),
			"id"           => array("title"=>"ID", "type"=>"label", "align"=>"center", "width"=>"60px"),
			///"notification_status_changed" => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>""),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
		    "separator_1"   =>array(
				"separator_info" => array("legend"=>_PERSONAL_DETAILS),
				"first_name"  	=> array("title"=>_FIRST_NAME,"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"last_name"    	=> array("title"=>_LAST_NAME, "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"birth_date"    => array("title"=>_BIRTH_DATE, "type"=>"date", "width"=>"210px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"date", "unique"=>false, "visible"=>true, "min_year"=>"90", "max_year"=>"0"),
				"company" 		=> array("title"=>_COMPANY,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"url" 			=> array("title"=>_URL,		 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
			),
		    "separator_2"   =>array(
				"separator_info" => array("legend"=>_BILLING_ADDRESS),
				"b_address" 	=> array("title"=>_ADDRESS,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"b_address_2" 	=> array("title"=>_ADDRESS,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"b_city" 		=> array("title"=>_CITY,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"b_state" 		=> array("title"=>_STATE,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"b_country" 	=> array("title"=>_COUNTRY,	 "type"=>"enum",     "width"=>"210px", "source"=>$arr_countries, "required"=>true),
				"b_zipcode" 	=> array("title"=>_ZIP_CODE, "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
			),
		    "separator_3"   =>array(
				"separator_info" => array("legend"=>_CONTACT_INFORMATION),
				"phone" 		=> array("title"=>_PHONE,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"email" 		=> array("title"=>_EMAIL_ADDRESS,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"email", "unique"=>true),
			),
		    "separator_4"   =>array(
				"separator_info" => array("legend"=>_ACCOUNT_DETAILS),
				"group_id"       => array("title"=>"Client Group", "type"=>"enum", "required"=>false, "readonly"=>false, "width"=>"", "source"=>$arr_groups),
				"user_name" 	 => array("title"=>_USERNAME,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text", "validation_minlength"=>"4", "readonly"=>false, "unique"=>true),
				"user_password"  => array("title"=>_PASSWORD, 	"type"=>"password", "width"=>"210px", "required"=>true, "validation_type"=>"password", "cryptography"=>PASSWORDS_ENCRYPTION, "cryptography_type"=>PASSWORDS_ENCRYPTION_TYPE, "aes_password"=>PASSWORDS_ENCRYPT_KEY),
			),
		    "separator_5"   =>array(
				"separator_info" => array("legend"=>_OTHER),
				"date_created"		   => array("title"=>_DATE_CREATED,	"type"=>"hidden", "width"=>"210px", "required"=>true, "default"=>date("Y-m-d H:i:s")),
				"registered_from_ip"   => array("title"=>_REGISTERED_FROM_IP, "type"=>"hidden", "width"=>"210px", "required"=>true, "default"=>$user_ip),
				"last_logged_ip"	   => array("title"=>_LAST_LOGGED_IP,	  "type"=>"hidden", "width"=>"210px", "required"=>false, "default"=>""),
				"email_notifications" => array("title"=>_EMAIL_NOTIFICATIONS,	"type"=>"checkbox", "true_value"=>"1", "false_value"=>"0"),
				"orders_count"		=> array("title"=>"",  			  "type"=>"hidden", "width"=>"210px", "required"=>true, "default"=>"0"),
				"rooms_count"		=> array("title"=>_ROOMS,		  "type"=>"hidden", "width"=>"210px", "required"=>true, "default"=>"0"),
				"is_active"			=> array("title"=>_ACTIVE,		  "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
				"is_removed"		=> array("title"=>_REMOVED,		  "type"=>"hidden", "width"=>"210px", "required"=>true, "default"=>"0"),
				"comments"			=> array("title"=>_COMMENTS,	  "type"=>"textarea", "width"=>"420px", "height"=>"70px", "required"=>false, "readonly"=>false, "validation_type"=>"text"),
				"registration_code"	=> array("title"=>_REGISTRATION_CODE, "type"=>"hidden", "width"=>"210px", "required"=>false, "default"=>""),
			),
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
									c.".$this->primaryKey.",
		                            c.*,
									IF(c.user_name != '', c.user_name, '"._WITHOUT_ACCOUNT."') as mod_user_name,
									c.notification_status_changed
								FROM ".$this->tableName." c
								WHERE c.".$this->primaryKey." = _RID_";
								
		// define edit mode fields
		$this->arrEditModeFields = array(
		    "separator_1"   =>array(
				"separator_info" => array("legend"=>_PERSONAL_DETAILS),
				"first_name"  	=> array("title"=>_FIRST_NAME,"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"last_name"    	=> array("title"=>_LAST_NAME, "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"birth_date"    => array("title"=>_BIRTH_DATE, "type"=>"date", "width"=>"210px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"date", "unique"=>false, "visible"=>true, "min_year"=>"90", "max_year"=>"0"),
				"company" 		=> array("title"=>_COMPANY,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"url" 			=> array("title"=>_URL,		 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
			),
		    "separator_2"   =>array(
				"separator_info" => array("legend"=>_BILLING_ADDRESS),
				"b_address" 	=> array("title"=>_ADDRESS,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"b_address_2" 	=> array("title"=>_ADDRESS,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"b_city" 		=> array("title"=>_CITY,		 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"b_state" 		=> array("title"=>_STATE,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"b_country" 	=> array("title"=>_COUNTRY,	 "type"=>"enum",     "width"=>"210px", "source"=>$arr_countries, "required"=>true),
				"b_zipcode" 	=> array("title"=>_ZIP_CODE,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
			),
		    "separator_3"   =>array(
				"separator_info" => array("legend"=>_CONTACT_INFORMATION),
				"phone" 		=> array("title"=>_PHONE,	 "type"=>"textbox", "width"=>"210px", "required"=>false, "validation_type"=>"text"),
				"email" 		=> array("title"=>_EMAIL_ADDRESS,	 "type"=>"textbox", "width"=>"210px", "required"=>true, "readonly"=>true, "validation_type"=>"email", "unique"=>true),
			),
		    "separator_4"   =>array(
				"separator_info" => array("legend"=>_ACCOUNT_DETAILS),
				"group_id"       => array("title"=>"Client Group", "type"=>"enum", "required"=>false, "readonly"=>false, "width"=>"", "source"=>$arr_groups),
				"mod_user_name"  => array("title"=>_USERNAME,	 "type"=>"label"),
			),
		    "separator_5"   =>array(
				"separator_info" => array("legend"=>_OTHER),
				"date_created"	=> array("title"=>_DATE_CREATED, "type"=>"label", "format"=>"date", "format_parameter"=>$datetime_format),
				"date_lastlogin"=> array("title"=>_LAST_LOGIN,	"type"=>"label", "format"=>"date", "format_parameter"=>$datetime_format),
				"registered_from_ip"   => array("title"=>_REGISTERED_FROM_IP, "type"=>"label"),
				"last_logged_ip"	   => array("title"=>_LAST_LOGGED_IP,	 "type"=>"label"),
				"email_notifications" => array("title"=>_EMAIL_NOTIFICATIONS,	"type"=>"checkbox", "true_value"=>"1", "false_value"=>"0"),
				"notification_status_changed" => array("title"=>_NOTIFICATION_STATUS_CHANGED, "type"=>"label", "format"=>"date", "format_parameter"=>$datetime_format),
				"orders_count"		=> array("title"=>_BOOKINGS,  "type"=>"label"),
				"rooms_count"		=> array("title"=>_ROOMS, "type"=>"label"),
				"is_active"			=> array("title"=>_ACTIVE,		  "type"=>"checkbox", "true_value"=>"1", "false_value"=>"0"),
				"is_removed"		=> array("title"=>_REMOVED,		  "type"=>"checkbox", "true_value"=>"1", "false_value"=>"0"),
				"comments"			=> array("title"=>_COMMENTS,	  "type"=>"textarea", "width"=>"420px", "height"=>"70px", "required"=>false, "readonly"=>false, "validation_type"=>"text"),
			),
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = "SELECT
									c.".$this->primaryKey.",
		                            c.*,
									IF(c.user_name != '', c.user_name, '"._WITHOUT_ACCOUNT."') as mod_user_name,
									IF(c.email_notifications, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as email_notifications,
									IF(c.is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as client_active,
									IF(c.is_removed, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as client_removed,
									c.birth_date,
									c.notification_status_changed,
									cnt.name as country_name,
									cg.name as group_name
								FROM ".$this->tableName." c
									LEFT OUTER JOIN ".TABLE_COUNTRIES." cnt ON c.b_country = cnt.abbrv
									LEFT OUTER JOIN ".TABLE_CLIENT_GROUPS." cg ON c.group_id = cg.id
								WHERE c.".$this->primaryKey." = _RID_";
		$this->arrDetailsModeFields = array(			
		    "separator_1"   =>array(
				"separator_info" => array("legend"=>_PERSONAL_DETAILS),
				"first_name"  	=> array("title"=>_FIRST_NAME, "type"=>"label"),
				"last_name"    	=> array("title"=>_LAST_NAME,  "type"=>"label"),
				"birth_date"    => array("title"=>_BIRTH_DATE,  "type"=>"date", "format"=>"date", "format_parameter"=>$date_format),
				"company" 		=> array("title"=>_COMPANY,	   "type"=>"label"),
				"url" 			=> array("title"=>_URL,		 "type"=>"label"),
			),
		    "separator_2"   =>array(
				"separator_info" => array("legend"=>_BILLING_ADDRESS),
				"b_address" 	=> array("title"=>_ADDRESS,	 "type"=>"label"),
				"b_address_2" 	=> array("title"=>_ADDRESS,	 "type"=>"label"),
				"b_city" 		=> array("title"=>_CITY,	 "type"=>"label"),
				"b_state" 		=> array("title"=>_STATE,	 "type"=>"label"),
				"country_name" 	=> array("title"=>_COUNTRY,	 "type"=>"label"),
				"b_zipcode" 	=> array("title"=>_ZIP_CODE, "type"=>"label"),
			),
		    "separator_3"   =>array(
				"separator_info" => array("legend"=>_CONTACT_INFORMATION),
				"phone" 		=> array("title"=>_PHONE,	 "type"=>"label"),
				"email" 		=> array("title"=>_EMAIL_ADDRESS, "type"=>"label"),
			),
		    "separator_4"   =>array(
				"separator_info" => array("legend"=>_ACCOUNT_DETAILS),
				"group_name"     => array("title"=>"Client Group", "type"=>"label"),
				"mod_user_name"  => array("title"=>_USERNAME,	 "type"=>"label"),
			),
		    "separator_5"   =>array(
				"separator_info" => array("legend"=>_OTHER),
				"date_created"	=> array("title"=>_DATE_CREATED, "type"=>"label", "format"=>"date", "format_parameter"=>$datetime_format),
				"date_lastlogin"=> array("title"=>_LAST_LOGIN,	 "type"=>"label", "format"=>"date", "format_parameter"=>$datetime_format),
				"registered_from_ip"   => array("title"=>_REGISTERED_FROM_IP, "type"=>"label"),
				"last_logged_ip"	   => array("title"=>_LAST_LOGGED_IP,	 "type"=>"label"),
				"email_notifications" => array("title"=>_EMAIL_NOTIFICATIONS,	"type"=>"label"),
				"notification_status_changed" => array("title"=>_NOTIFICATION_STATUS_CHANGED, "type"=>"label", "format"=>"date", "format_parameter"=>$datetime_format),
				"orders_count"		=> array("title"=>_BOOKINGS, "type"=>"label"),
				"rooms_count"		=> array("title"=>_ROOMS, "type"=>"label"),
				"client_active"	    => array("title"=>_ACTIVE,		 "type"=>"label"),
				"client_removed"	=> array("title"=>_REMOVED,		 "type"=>"label"),
				"comments"			=> array("title"=>_COMMENTS,     "type"=>"label", "format"=>"nl2br"),
			),
		);

	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	//==========================================================================
    // PUBLIC METHODS
	//==========================================================================
	/**
	 * Draws login form in Front-End
	 */
	public function DrawLoginFormBlock(){
		draw_block_top(_AUTHENTICATION);
		echo "
		<form class='authentication-form' action='index.php?client=login' method='post'>
			<input type='hidden' name='submit_login' value='login' />
			<input type='hidden' name='type' value='client' />
			".draw_token_field(true)."
			
			<table border='0' cellspasing='2' cellpadding='1'>
			<tr><td>"._USERNAME.":</td></tr>
			<tr><td><input type='text' style='width:130px' name='user_name' id='user_name' maxlength='50' autocomplete='off' value='' /></td></tr>
			<tr><td>"._PASSWORD.":</td></tr>
			<tr><td><input type='password' style='width:130px' name='password' id='password' maxlength='20' autocomplete='off' value='' /></td></tr>
			<tr><td nowrap height='2px'></td></tr>
			<tr><td><input class='button' type='submit' name='submit' value='"._BUTTON_LOGIN."' /></td></tr>
			<tr><td></td></tr>
			<tr><td><a class='form_link' href='index.php?client=create_account'>"._CREATE_ACCOUNT."</a></td></tr>
			<tr><td><a class='form_link' href='index.php?client=password_forgotten'>"._FORGOT_PASSWORD."?</a></td></tr>
			</table>
		</form>";
		draw_block_bottom();					
	}	

	/**
	 * Before-Update operation
	 */
	public function BeforeUpdateRecord()
	{
		$sql = "SELECT email_notifications FROM ".$this->tableName." WHERE ".$this->primaryKey." = ".(int)$this->curRecordId;
		$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
        if(isset($result['email_notifications'])) $this->email_notifications = $result['email_notifications'];		
		return true;
	}

	/**
	 * After-Update operation
	 */
	public function AfterUpdateRecord()
	{
		$sql = "UPDATE ".$this->tableName."
				SET notification_status_changed = IF(email_notifications <> ".(int)$this->email_notifications.", '".@date("Y-m-d H:i:s")."', notification_status_changed)
				WHERE ".$this->primaryKey." = ".(int)$this->curRecordId;
		database_void_query($sql);
		return true;
	}

	/**
	 * After-Addition operation
	 */
	public function AfterInsertRecord()
	{
		global $objSettings, $objSiteDescription;		

		////////////////////////////////////////////////////////////
		$sender = $objSettings->GetParameter("admin_email");
		$recipiant = $this->params['email'];
		
		$objEmailTemplates = new EmailTemplates();
		$email_template = $objEmailTemplates->GetTemplate("new_account_created_by_admin", Application::Get("lang"));				
		$subject 		= $email_template["template_subject"];
		$body_middle 	= $email_template["template_content"];

		$body_middle = str_ireplace("{FIRST NAME}", $this->params['first_name'], $body_middle);
		$body_middle = str_ireplace("{LAST NAME}", $this->params['last_name'], $body_middle);
		$body_middle = str_ireplace("{USER NAME}", $this->params['user_name'], $body_middle);
		$body_middle = str_ireplace("{USER PASSWORD}", $this->params['user_password'], $body_middle);
		$body_middle = str_ireplace("{WEB SITE}", $_SERVER['SERVER_NAME'], $body_middle);
		$body_middle = str_ireplace("{BASE URL}", APPHP_BASE, $body_middle);
		$body_middle = str_ireplace("{YEAR}", date("Y"), $body_middle);

		$body   = "<div style=direction:".Application::Get("lang_dir").">";
		$body  .= $body_middle;
		$body  .= "</div>";

		$text_version = strip_tags($body);
		$html_version = nl2br($body);

		$objEmail = new Email($recipiant, $sender, $subject);                     
		$objEmail->textOnly = false;
		$objEmail->content = $html_version;                
		$objEmail->Send();
		////////////////////////////////////////////////////////////
	}

	/**
	 * Send activation email
	 *		@param $email
	 */
	public function Reactivate($email)
	{		
		global $objSettings;
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}
		
		if(!empty($email)){			
			if(!PASSWORDS_ENCRYPTION){
				$sql = "SELECT id, first_name, last_name, user_name, user_password, registration_code ";
			}else{
				if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
					$sql = "SELECT id, first_name, last_name, user_name, AES_DECRYPT(user_password, '".PASSWORDS_ENCRYPT_KEY."') as user_password, registration_code ";
				}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
					$sql = "SELECT id, first_name, last_name, user_name, '' as user_password, registration_code ";
				}				
			}
			$sql .= "FROM ".TABLE_CLIENTS." WHERE email = '".encode_text($email)."' AND registration_code != '' AND is_active = 0";
			
			$temp = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
			if(is_array($temp) && count($temp) > 0){
				$sender = $objSettings->GetParameter("admin_email");
				$recipiant = $email;

				$objEmailTemplates = new EmailTemplates();
				$email_template = $objEmailTemplates->GetTemplate("new_account_created_confirm_by_email", Application::Get("lang"));									
				$subject 		= $email_template["template_subject"];
				$body_middle 	= $email_template["template_content"];

				if(!PASSWORDS_ENCRYPTION){
					$user_password = $temp['user_password'];
				}else{
					if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
						$user_password = $temp['user_password'];
					}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
						$user_password = get_random_string(8);
						$sql = "UPDATE ".TABLE_CLIENTS." SET user_password = '".md5($user_password)."' WHERE id = ".(int)$temp['id'];
						database_void_query($sql);
					}				
				}

				$body_middle = str_ireplace("{FIRST NAME}", $temp['first_name'], $body_middle);
				$body_middle = str_ireplace("{LAST NAME}", $temp['last_name'], $body_middle);
				$body_middle = str_ireplace("{USER NAME}", $temp['user_name'], $body_middle);
				$body_middle = str_ireplace("{USER PASSWORD}", $user_password, $body_middle);
				$body_middle = str_ireplace("{REGISTRATION CODE}", $temp['registration_code'], $body_middle);
				$body_middle = str_ireplace("{WEB SITE}", $_SERVER['SERVER_NAME'], $body_middle);
				$body_middle = str_ireplace("{BASE URL}", APPHP_BASE, $body_middle);
				$body_middle = str_ireplace("{YEAR}", date("Y"), $body_middle);					
			
				$body   = "<div style=direction:".Application::Get("lang_dir").">";
				$body  .= $body_middle;
				$body  .= "</div>";

				$text_version = strip_tags($body);
				$html_version = nl2br($body);

				$objEmail = new Email($recipiant, $sender, $subject);                     
				$objEmail->textOnly = false;
				$objEmail->content = $html_version;                
				$objEmail->Send();
				
				return true;					
			}else{
				$this->error = _EMAILS_SENT_ERROR;
				return false;
			}				
		}else{
			$this->error = _EMAIL_EMPTY_ALERT;
			return false;
		}
		return true;
	}

	/**
	 * Before Edit Record
	 *
	 */
	public function BeforeEditRecord()
	{
		$registration_code = isset($this->result[0][0]['registration_code']) ? $this->result[0][0]['registration_code'] : "";
		$is_active         = isset($this->result[0][0]['is_active']) ? $this->result[0][0]['is_active'] : "";
		$reactivation_html = "";
		
        if($registration_code != "" && !$is_active){
			$reactivation_html = " &nbsp;<a href='javascript:void(\"email|reactivate\")' onclick='javascript:if(confirm(\""._PERFORM_OPERATION_COMMON_ALERT."\"))__mgDoPostBack(\"".TABLE_CLIENTS."\", \"reactivate\");'>[ "._REACTIVATION_EMAIL." ]</a>";
		}
		$this->arrEditModeFields['separator_3']['email']["post_html"] = $reactivation_html;
	}

	/**
	 *	Returns DataSet array
	 *	    @param $where_clause
	 *		@param $order_clause
	 *		@param $limit_clause
	 */
	public function GetAllClients($where_clause = "", $order_clause = "", $limit_clause = "")
	{
		$sql = "SELECT * FROM ".$this->tableName." WHERE is_active = 1 ".$where_clause." ".$order_clause." ".$limit_clause;
		if($this->debug) $this->arrSQLs['select_get_all'] = $sql;					
		return database_query($sql, DATA_AND_ROWS);
	}

	//==========================================================================
    // STATIC METHODS
	//==========================================================================
	/**
	 * Send forgotten password
	 *		@param $email
	 */
	static public function SendPassword($email)
	{		
		global $objSettings;
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			self::$static_error = _OPERATION_BLOCKED;
			return false;
		}
		
		if(!empty($email)) {
			if(check_email_address($email)){   

				if(!PASSWORDS_ENCRYPTION){
					$sql = "SELECT id, user_name, user_password FROM ".TABLE_CLIENTS." WHERE email = '".encode_text($email)."' AND is_active = 1";
				}else{
					if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
						$sql = "SELECT id, user_name, AES_DECRYPT(user_password, '".PASSWORDS_ENCRYPT_KEY."') as user_password  FROM ".TABLE_CLIENTS." WHERE email = '".encode_text($email)."' AND is_active = 1";
					}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
						$sql = "SELECT id, user_name, '' as user_password  FROM ".TABLE_CLIENTS." WHERE email = '".encode_text($email)."' AND is_active = 1";
					}				
				}
				
				$temp = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
				if(is_array($temp) && count($temp) > 0){
					$sender = $objSettings->GetParameter("admin_email");
					$recipiant = $email;

					$objEmailTemplates = new EmailTemplates();
					$email_template = $objEmailTemplates->GetTemplate("password_forgotten", Application::Get("lang"));				
					$subject 		= $email_template["template_subject"];
					$body_middle 	= $email_template["template_content"];

					if(!PASSWORDS_ENCRYPTION){
						$user_password = $temp['user_password'];
					}else{
						if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){
							$user_password = $temp['user_password'];
						}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
							$user_password = get_random_string(8);
							$sql = "UPDATE ".TABLE_CLIENTS." SET user_password = '".md5($user_password)."' WHERE id = ".(int)$temp['id'];
							database_void_query($sql);
						}				
					}

					$body_middle = str_replace("{USER NAME}", $temp['user_name'], $body_middle);
					$body_middle = str_replace("{USER PASSWORD}", $user_password, $body_middle);
					$body_middle = str_replace("{WEB SITE}", $_SERVER['SERVER_NAME'], $body_middle);
					
					$body   = "<div style=direction:".Application::Get("lang_dir").">";
					$body  .= $body_middle;
					$body  .= "</div>";
					
					$text_version = strip_tags($body);
					$html_version = nl2br($body);
	
					$objEmail = new Email($recipiant, $sender, $subject);                     
					$objEmail->textOnly = false;
					$objEmail->content = $html_version;                
					$objEmail->Send();
					
					return true;					
				}else{
					self::$static_error = _EMAIL_NOT_EXISTS;
					return false;
				}				
			}else{
				self::$static_error = _EMAIL_IS_WRONG;
				return false;								
			}
		}else{
			self::$static_error = _EMAIL_IS_EMPTY;
			return false;
		}
		return true;
	}
	
	/**
	 * Returns static methods error
	 */
	public static function GetError()
	{
		return self::$static_error;
	}
	
	/**
	 * Reset client account
	 */
	static public function ResetAccount($email)
	{
		global $objSettings;
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			self::$static_error = _OPERATION_BLOCKED;
			return false;
		}
		
		if(!empty($email)) {
			if(check_email_address($email)){
			
				$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE user_name = '' AND email = '".encode_text($email)."'";
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($result[1] > 0){
					
					$user_name = $email;
					$new_password = get_random_string(8);
					if(!PASSWORDS_ENCRYPTION){
						$user_password = encode_text($new_password);
					}else{
						if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){					
							$user_password = "AES_ENCRYPT('".encode_text($new_password)."', '".PASSWORDS_ENCRYPT_KEY."')";
						}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
							$user_password = "MD5('".encode_text($new_password)."')";
						}
					}
					
					$sql = "UPDATE ".TABLE_CLIENTS."
							SET user_name = '".encode_text($user_name)."', user_password = ".$user_password."
							WHERE email = '".encode_text($email)."'";
					database_void_query($sql);

					$sender = $objSettings->GetParameter("admin_email");
					$recipiant = $email;

					$objEmailTemplates = new EmailTemplates();
					$email_template = $objEmailTemplates->GetTemplate("new_account_created", Application::Get("lang"));				
					$subject 		= $email_template["template_subject"];
					$body_middle 	= $email_template["template_content"];
					
					$body_middle = str_replace("{FIRST NAME}", $result[0]['first_name'], $body_middle);
					$body_middle = str_replace("{LAST NAME}", $result[0]['last_name'], $body_middle);
					$body_middle = str_replace("{USER NAME}", $user_name, $body_middle);
					$body_middle = str_replace("{USER PASSWORD}", $new_password, $body_middle);
					$body_middle = str_replace("{BASE URL}", APPHP_BASE, $body_middle);
					
					$body   = "<div style=direction:".Application::Get("lang_dir").">";
					$body  .= $body_middle;
					$body  .= "</div>";
					
					$text_version = strip_tags($body);
					$html_version = nl2br($body);
	
					$objEmail = new Email($recipiant, $sender, $subject);                     
					$objEmail->textOnly = false;
					$objEmail->content = $html_version;
					$objEmail->Send();
					
					return true;
				}else{
					self::$static_error = _WRONG_PARAMETER_PASSED;
					return false;
				}
			}else{
				self::$static_error = _EMAIL_IS_WRONG;
				return false;								
			}
		}else{
			self::$static_error = _EMAIL_IS_EMPTY;
			return false;
		}
	}
	
}
?>