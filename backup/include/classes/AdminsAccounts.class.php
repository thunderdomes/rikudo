<?php

/**
 *	Class AdminsAccounts
 *  -------------- 
 *  Description : encapsulates Admins Accounts operations & properties
 *  Updated	    : 08.05.2011
 *	Written by  : ApPHP
 *	
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct
 *  __construct
 *	AfterInsertRecord
 *	AfterUpdateRecord
 *  
 **/

class AdminsAccounts extends MicroGrid {
	
	protected $debug = false;

	protected $tableName;
	protected $primaryKey;
	protected $dataSet;
	protected $formActionURL;
	protected $params;
	protected $actions;

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

	public $error;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct($login_type = "")
	{
		$this->SetRunningTime();

		$this->params = array();		
		if(isset($_POST['first_name'])) $this->params['first_name']  = prepare_input($_POST['first_name']);
		if(isset($_POST['last_name']))	$this->params['last_name']   = prepare_input($_POST['last_name']);
		if(isset($_POST['user_name']))  $this->params['user_name']  = prepare_input($_POST['user_name']);
		if(isset($_POST['password']))	$this->params['password']   = prepare_input($_POST['password']);
		if(isset($_POST['email']))   	$this->params['email']      = prepare_input($_POST['email']);
		if(isset($_POST['preferred_language']))  $this->params['preferred_language']  = prepare_input($_POST['preferred_language']);
		if(isset($_POST['account_type'])) $this->params['account_type'] = prepare_input($_POST['account_type']);
		if(isset($_POST['date_created']))   $this->params['date_created']   = prepare_input($_POST['date_created']);
		if(isset($_POST['is_active']))  $this->params['is_active']  = (int)$_POST['is_active']; else $this->params['is_active'] = "0";
		
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_ACCOUNTS;
		$this->dataSet 		= array();
		$this->error 		= "";
		///$this->languageId  	= (isset($_REQUEST['language_id']) && $_REQUEST['language_id'] != "") ? $_REQUEST['language_id'] : Languages::GetDefaultLang();
		$this->formActionURL = "index.php?admin=admins_management";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;

		if($login_type == "owner"){
			$this->WHERE_CLAUSE = "WHERE (".TABLE_ACCOUNTS.".account_type = 'mainadmin' || ".TABLE_ACCOUNTS.".account_type = 'admin')";
		}else if($login_type == "mainadmin"){
			$this->WHERE_CLAUSE = "WHERE ".TABLE_ACCOUNTS.".account_type = 'admin'";
		}else if($login_type == "admin"){
			$this->WHERE_CLAUSE = "WHERE ".TABLE_ACCOUNTS.".account_type = 'admin'";
		}

		$this->ORDER_CLAUSE = "ORDER BY id ASC";
		$this->isAlterColorsAllowed = true;
		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;
		
		$this->isFilteringAllowed = true;
		// define filtering fields
		$this->arrFilteringFields = array(
			_FIRST_NAME => array("table"=>$this->tableName, "field"=>"first_name", "type"=>"text", "sign"=>"like%", "width"=>"80px"),
			_LAST_NAME  => array("table"=>$this->tableName, "field"=>"last_name", "type"=>"text", "sign"=>"like%", "width"=>"80px"),
		);

		// prepare languages array		
		$total_languages = Languages::GetAll();
		$arr_languages      = array();
		foreach($total_languages[0] as $key => $val){
			$arr_languages[$val['abbreviation']] = $val['lang_name'];
		}
		
		$arr_account_types = array("admin"=>_ADMIN, "mainadmin"=>_MAIN_ADMIN);
		$date_format = get_date_format();		

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									first_name,
		                            last_name,
									CONCAT(first_name, ' ', last_name) as full_name,
									user_name,
									email,
									preferred_language,
									account_type,
									last_login,
									IF(is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as is_active
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"full_name"   => array("title"=>_NAME, "type"=>"label", "align"=>"left", "width"=>""),
			"user_name"   => array("title"=>_USER_NAME,  "type"=>"label", "align"=>"left", "width"=>""),
			"is_active"   => array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"80px"),
			"account_type" => array("title"=>_ACCOUNT_TYPE, "type"=>"label", "align"=>"center", "width"=>"120px"),
			"email" 	  => array("title"=>_EMAIL_ADDRESS, "type"=>"link", "href"=>"mailto://{email}", "align"=>"left", "width"=>""),
			"last_login"  => array("title"=>_LAST_LOGIN, "type"=>"label", "align"=>"center", "width"=>"", "format"=>"date", "format_parameter"=>$date_format),
			"id"          => array("title"=>"ID", "type"=>"label", "align"=>"center", "width"=>"40px"),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
		    "separator_1"   =>array(
				"separator_info" => array("legend"=>_PERSONAL_DETAILS),
				"first_name"  	=> array("title"=>_FIRST_NAME,	"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"last_name"    	=> array("title"=>_LAST_NAME, 	"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"email" 		=> array("title"=>_EMAIL_ADDRESS,"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"email", "unique"=>true),
			),
		    "separator_2"   =>array(
				"separator_info" => array("legend"=>_ACCOUNT_DETAILS),
				"user_name"  	=> array("title"=>_USER_NAME,	"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"alpha_numeric", "unique"=>true),
				"password"  	=> array("title"=>_PASSWORD, 	"type"=>"password", "width"=>"210px", "required"=>true, "validation_type"=>"password", "cryptography"=>PASSWORDS_ENCRYPTION, "cryptography_type"=>PASSWORDS_ENCRYPTION_TYPE, "aes_password"=>PASSWORDS_ENCRYPT_KEY),
				"account_type"  => array("title"=>_ACCOUNT_TYPE, "type"=>"enum", "required"=>true, "readonly"=>false, "width"=>"120px", "source"=>$arr_account_types),
				"preferred_language" => array("title"=>_PREFERRED_LANGUAGE, "type"=>"enum", "required"=>true, "readonly"=>false, "width"=>"120px", "source"=>$arr_languages),
			),
		    "separator_3"   =>array(
				"separator_info" => array("legend"=>_OTHER),
				"last_login" 	=> array("title"=>"",           "type"=>"hidden",  "required"=>false, "default"=>""),
				"date_created" 	=> array("title"=>"",           "type"=>"hidden",  "required"=>false, "default"=>date("Y-m-d H:i:s")),
				"is_active"  	=> array("title"=>_ACTIVE,	    "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			)
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".first_name,
								".$this->tableName.".last_name,
								".$this->tableName.".user_name,
								".$this->tableName.".password,
								".$this->tableName.".email,
								".$this->tableName.".account_type,
								".$this->tableName.".preferred_language,
								".TABLE_LANGUAGES.".lang_name as preferred_lang_name,
								".$this->tableName.".date_created,
								".$this->tableName.".last_login,
								".$this->tableName.".is_active
							FROM ".$this->tableName."
								LEFT OUTER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".preferred_language = ".TABLE_LANGUAGES.".abbreviation
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		    "separator_1"   =>array(
				"separator_info" => array("legend"=>_PERSONAL_DETAILS),
				"first_name"  	 => array("title"=>_FIRST_NAME,	"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"last_name"    	 => array("title"=>_LAST_NAME, 	"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"text"),
				"email" 		 => array("title"=>_EMAIL_ADDRESS,"type"=>"textbox", "width"=>"210px", "required"=>true, "validation_type"=>"email", "unique"=>true),
			),
		    "separator_2"   =>array(
				"separator_info" => array("legend"=>_ACCOUNT_DETAILS),
				"user_name"  	 => array("title"=>_USER_NAME,	"type"=>"textbox", "width"=>"210px", "required"=>true, "readonly"=>true, "validation_type"=>"alpha_numeric", "unique"=>true),
				"account_type"   => array("title"=>_ACCOUNT_TYPE, "type"=>"enum", "required"=>true, "readonly"=>(($login_type == "owner")?false:true), "width"=>"120px", "source"=>$arr_account_types),
				"preferred_language" => array("title"=>_PREFERRED_LANGUAGE, "type"=>"enum", "required"=>true, "readonly"=>false, "width"=>"120px", "source"=>$arr_languages),
			),
		    "separator_3"   =>array(
				"separator_info" => array("legend"=>_OTHER),
				"date_created"   => array("title"=>_DATE_CREATED, "type"=>"label", "format"=>"date", "format_parameter"=>$date_format),
				"last_login" 	 => array("title"=>_LAST_LOGIN,  "type"=>"label", "format"=>"date", "format_parameter"=>$date_format),
				"last_login"     => array("title"=>"", "type"=>"hidden", "required"=>false, "default"=>""),				
				"is_active"  	 => array("title"=>_ACTIVE,	    "type"=>"checkbox", "true_value"=>"1", "false_value"=>"0"),
			)
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".first_name,
								".$this->tableName.".last_name,
								".$this->tableName.".user_name,
								".$this->tableName.".password,
								".$this->tableName.".email,
								".$this->tableName.".preferred_language,
								".TABLE_LANGUAGES.".lang_name as preferred_lang_name,
								".$this->tableName.".account_type,
								".$this->tableName.".date_created,
								".$this->tableName.".last_login,
								IF(".$this->tableName.".is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as is_active								
							FROM ".$this->tableName."
								LEFT OUTER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".preferred_language = ".TABLE_LANGUAGES.".abbreviation
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		$this->arrDetailsModeFields = array(
		    "separator_1"   =>array(
				"separator_info" => array("legend"=>_PERSONAL_DETAILS),
				"first_name"  	=> array("title"=>_FIRST_NAME,	"type"=>"label"),
				"last_name"    	=> array("title"=>_LAST_NAME, "type"=>"label"),
				"email"     	=> array("title"=>_EMAIL_ADDRESS, 	 "type"=>"label"),
			),
		    "separator_2"   =>array(
				"separator_info" => array("legend"=>_ACCOUNT_DETAILS),
				"user_name"   	=> array("title"=>_USER_NAME, "type"=>"label"),
				"account_type"  => array("title"=>_ACCOUNT_TYPE, "type"=>"label"),
				"preferred_lang_name" => array("title"=>_PREFERRED_LANGUAGE, "type"=>"label"),
			),
		    "separator_3"   =>array(
				"separator_info" => array("legend"=>_OTHER),
				"date_created"  => array("title"=>_DATE_CREATED, "type"=>"label", "format"=>"date", "format_parameter"=>$date_format),
				"last_login" 	=> array("title"=>_LAST_LOGIN, "type"=>"label", "format"=>"date", "format_parameter"=>$date_format),
				"is_active"  	=> array("title"=>_ACTIVE, "type"=>"label"),
			)
		);
	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/**
	 * After-Addition operation
	 */
	public function AfterInsertRecord()
	{
		global $objSettings, $objSiteDescription;
		
		$lang = Application::Get("lang");

		////////////////////////////////////////////////////////////
		$sender = $objSettings->GetParameter("admin_email");
		$recipiant = $this->params['email'];
		
		$objEmailTemplates = new EmailTemplates();
		$email_template = $objEmailTemplates->GetTemplate("new_account_created_by_admin", $lang);				
		$subject 		= $email_template["template_subject"];
		$body_middle 	= $email_template["template_content"];

		$body_middle = str_ireplace("{FIRST NAME}", $this->params['first_name'], $body_middle);
		$body_middle = str_ireplace("{LAST NAME}", $this->params['last_name'], $body_middle);
		$body_middle = str_ireplace("{USER NAME}", $this->params['user_name'], $body_middle);
		$body_middle = str_ireplace("{USER PASSWORD}", $this->params['password'], $body_middle);
		$body_middle = str_ireplace("{WEB SITE}", $_SERVER['SERVER_NAME'], $body_middle);
		$body_middle = str_ireplace("{BASE URL}", APPHP_BASE, $body_middle);
		$body_middle = str_ireplace("{YEAR}", date("Y"), $body_middle);
		$body_middle = str_ireplace("client=login", "admin=login", $body_middle);

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
	 * After-Updating operation
	 */
	public function AfterUpdateRecord()
	{
		global $objLogin;		
		$objLogin->UpdateLoggedEmail($this->params['email']);
	}

}
?>