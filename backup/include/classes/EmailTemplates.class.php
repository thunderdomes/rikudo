<?php

/**
 *	EmailTemplates 
 *  -------------- 
 *  Description : encapsulates email templates properties
 *	Usage       : HotelSite ONLY
 *  Updated	    : 11.06.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.4
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct										GetAllTemplates
 *	__destruct										SendEmail
 *	GetTemplate
 *	SendMassMail
 *	DrawMassMailForm
 *	BeforeDeleteRecord
 *	
 *  1.0.4
 *      - notification_updates renamed into email_notifications
 *      - fixed bug in sending emails to clients groups
 *      -
 *      -
 *      -
 *  1.0.3
 *  	- Mail_Preview replaced with added appPopupWindow()
 *  	- JS error fixed
 *  	- label for=''
 *  	- added check for F5 button click
 *  	- fixed table name error
 *  1.0.2
 *  	- fixed bug with missed variables
 *  	- html/mail_preview.html
 *  	- fixed bug in calculating of total emails
 *  	- replaced _ with % for text holders & str_ireplace() instead of str_replace
 *  	- <br /> -> <br>
 **/


class EmailTemplates extends MicroGrid {
	
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
	
	protected $isAlterColorsAllowed;
	
	protected $isPagingAllowed;
	protected $pageSize;

	protected $isSortingAllowed;

	protected $isFilteringAllowed;
	protected $arrFilteringFields;

	public $error;
	static private $static_error = "";
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{		
		$this->SetRunningTime();

		$this->params = array();
		
		## for standard fields
		if(isset($_POST['template_code'])) $this->params['template_code'] = prepare_input($_POST['template_code']);
		if(isset($_POST['template_name'])) $this->params['template_name'] = prepare_input($_POST['template_name']);
		if(isset($_POST['template_subject'])) $this->params['template_subject'] = prepare_input($_POST['template_subject']);
		if(isset($_POST['template_content'])) $this->params['template_content'] = prepare_input($_POST['template_content']);
		
		$this->params['language_id'] = MicroGrid::GetParameter('language_id');
	
		//$this->uPrefix 		= "prefix_";
		
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_EMAIL_TEMPLATES;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=email_templates";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = true;
		$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = "WHERE language_id = '".$this->languageId."'";				
		$this->ORDER_CLAUSE = "ORDER BY ".$this->tableName.".template_code ASC";
		
		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;

		$this->isFilteringAllowed = false;
		// define filtering fields
		$this->arrFilteringFields = array(
			// "Caption_1"  => array("table"=>"", "field"=>"", "type"=>"text", "sign"=>"=|like%|%like|%like%", "width"=>"80px"),
			// "Caption_2"  => array("table"=>"", "field"=>"", "type"=>"dropdownlist", "source"=>array(), "sign"=>"=|like%|%like|%like%", "width"=>"130px"),
		);

		// prepare languages array		
		$total_languages = Languages::GetAllActive();
		$arr_languages   = array();
		foreach($total_languages[0] as $key => $val){
			$arr_languages[$val['abbreviation']] = $val['lang_name'];
		}

		//---------------------------------------------------------------------- 
		// VIEW MODE
		// format: strip_tags
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									language_id,
									template_code,
									template_name,
									template_subject,
									template_content,
									IF(is_system_template = 1, '<span class=yes>"._YES."</span>', '"._NO."') as is_system_template
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"template_subject" => array("title"=>_SUBJECT, "type"=>"label", "align"=>"left", "width"=>"35%", "sortable"=>true, "nowrap"=>"", "visible"=>"", "height"=>"", "maxlength"=>"", "format"=>""),
			"template_name"    => array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "height"=>"", "maxlength"=>"", "format"=>""),
			"is_system_template" => array("title"=>_SYSTEM, "type"=>"label", "align"=>"center", "width"=>"80px", "sortable"=>true, "nowrap"=>"", "visible"=>"", "height"=>"", "maxlength"=>"", "format"=>""),
			
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address
		// 	 Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255 ....
		//   Ex.: "validation_maxlength"=>"255"
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
		
			"language_id"      => array("title"=>_LANGUAGE, "type"=>"enum",     "required"=>true, "readonly"=>true, "width"=>"210px", "source"=>$arr_languages, "unique"=>false),
			"template_code"    => array("title"=>_TEMPLATE_CODE, "type"=>"textbox",  "width"=>"350px", "required"=>true, "readonly"=>false, "maxlength"=>"80", "default"=>"", "validation_type"=>"alpha_numeric", "unique"=>false),
			"template_name"    => array("title"=>_NAME, "type"=>"textbox",  "width"=>"350px", "required"=>true, "readonly"=>false, "maxlength"=>"80", "default"=>"", "validation_type"=>"", "unique"=>true),
			"template_subject" => array("title"=>_SUBJECT, "type"=>"textbox",  "width"=>"510px", "required"=>true, "readonly"=>false, "maxlength"=>"125", "default"=>"", "validation_type"=>"", "unique"=>false),
			"template_content" => array("title"=>_TEXT, "type"=>"textarea", "width"=>"510px", "height"=>"290px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			"is_system_template" => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>"0"),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"", "unique"=>false),
			// "parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			// "parameter4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>date("Y-m-d H:i:s")),
			// "parameter5"  => array("title"=>"", "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"210px", "source"=>$arr_languages, "unique"=>false),
			// "parameter6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			// "parameter7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"uploaded/", "no_image"=>"", "random_name"=>"true", "unique"=>false),
			// "language_id"  => array("title"=>_LANGUAGE, "type"=>"enum", "source"=>$arr_languages, "required"=>true, "unique"=>false),
			
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"parameter1"  => array("title"=>"", "type"=>"label"),
			//		"parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email", "unique"=>false),
			//		"parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email", "unique"=>false),
			//  )
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address
		//   Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255 ....
		//   Ex.: "validation_maxlength"=>"255"
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".template_name,
								".$this->tableName.".template_code,
								".$this->tableName.".template_subject,
								".$this->tableName.".template_content,
								IF(is_system_template = 1, '<span class=yes>"._YES."</span>', '"._NO."') as is_system_template
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			"language_id"      => array("title"=>_LANGUAGE, "type"=>"enum",  "required"=>true, "readonly"=>true, "width"=>"210px", "source"=>$arr_languages, "unique"=>false),
			"template_code"    => array("title"=>_TEMPLATE_CODE, "type"=>"label"),
			"template_name"    => array("title"=>_NAME, "type"=>"textbox",  "width"=>"350px", "required"=>true, "readonly"=>false, "maxlength"=>"80", "default"=>"", "validation_type"=>"", "unique"=>false),
			"template_subject" => array("title"=>_SUBJECT, "type"=>"textbox",  "width"=>"510px", "required"=>true, "readonly"=>false, "maxlength"=>"125", "default"=>"", "validation_type"=>"", "unique"=>false),
			"template_content" => array("title"=>_TEXT, "type"=>"textarea", "width"=>"510px", "height"=>"300px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			"is_system_template" => array("title"=>_SYSTEM_TEMPLATE, "type"=>"label"),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"", "unique"=>false),
			// "parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			// "parameter4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "default"=>date("Y-m-d H:i:s")),
			// "parameter5"  => array("title"=>"", "type"=>"enum",     "width"=>"210px", "required"=>true, "readonly"=>false, "source"=>$arr_languages, "unique"=>false),
			// "parameter6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			// "parameter7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "target"=>"uploaded/", "no_image"=>"", "random_name"=>"true", "unique"=>false),
		
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"parameter1"  => array("title"=>"", "type"=>"label"),
			//		"parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email", "unique"=>false),
			//		"parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email", "unique"=>false),
			//  )
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"template_name"    => array("title"=>_NAME, "type"=>"label"),
			"template_subject" => array("title"=>_SUBJECT, "type"=>"label"),
			"template_content" => array("title"=>_TEXT, "type"=>"label", "format"=>"nl2br"),
			"is_system_template" => array("title"=>_SYSTEM_TEMPLATE, "type"=>"label"),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"label"),
			// "parameter3"  => array("title"=>"", "type"=>"label"),
			// "parameter4"  => array("title"=>"", "type"=>"image", "target"=>"uploaded/", "no_image"=>""),
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
	 * Returns email template
	 * 		@param $template_code
	 * 		@param $language_id
	 */
	public function GetTemplate($template_code = "", $language_id = "")
	{
		$output = array("template_subject"=>"", "template_content"=>"", "template_name"=>"");
		$sql = "SELECT
					language_id,
					template_code,
					template_name,
					template_subject,
					template_content
				FROM ".$this->tableName."
				WHERE
					template_code = '".mysql_real_escape_string($template_code)."' AND
					language_id = '".$language_id."'";
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$output["template_subject"] = $result[0]["template_subject"];
			$output["template_content"] = $result[0]["template_content"];
			$output["template_name"] = $result[0]["template_name"];
		}
		return $output;
	}

	/**
	 * Sends mass mail	 
	 */
	public function SendMassMail()
	{
		global $objSettings;

		$template_name 	= isset($_POST['template_name']) ? prepare_input($_POST['template_name']) : "";
		$email_from 	= isset($_POST['email_from']) ? prepare_input($_POST['email_from']) : "";
		$email_to_req   = isset($_POST['email_to']) ? prepare_input($_POST['email_to']) : "";
		$subject 		= isset($_POST['subject']) ? prepare_input($_POST['subject']) : "";
		$message 		= isset($_POST['message']) ? prepare_input($_POST['message']) : "";
		$package_size 	= isset($_POST['package_size']) ? prepare_input($_POST['package_size']) : "";		
		$duration 		= isset($_POST['duration']) ? (int)$_POST['duration'] : "5";
		$send_copy_to_admin = isset($_POST['send_copy_to_admin']) ? prepare_input($_POST['send_copy_to_admin']) : "";
		$admin_email 	= $objSettings->GetParameter("admin_email");
		$email_session_code = isset($_SESSION['email_random_code']) ? prepare_input($_SESSION['email_random_code']) : "";
		$email_post_code = isset($_POST['email_random_code']) ? prepare_input($_POST['email_random_code']) : "";
		$msg = "";
		$emails_total = "0";
		$emails_sent = "0";

		if(strtolower(SITE_MODE) == "demo"){
			draw_important_message(_OPERATION_BLOCKED);
			return false;
		}

		if($email_post_code != "" && $email_session_code == $email_post_code){
			$this->error = true;
			draw_message(_OPERATION_WAS_ALREADY_COMPLETED);								
			return false;
		}			

		// handle emails sending
		if($subject != "" && $message != ""){
			$message = str_ireplace("{YEAR}", date("Y"), $message);
			$message = str_ireplace("{WEB SITE}", $_SERVER['SERVER_NAME'], $message);
			$message = str_ireplace("{BASE URL}", APPHP_BASE, $message);
			
			$email_to_parts = explode("|", $email_to_req);
			$email_to = isset($email_to_parts[0]) ? $email_to_parts[0] : "";
			$email_to_subtype = isset($email_to_parts[1]) ? $email_to_parts[1] : "";
			if($email_to_subtype == "all"){
				$client_where_clause = "";
			}else if($email_to_subtype == "uncategorized"){
				$client_where_clause = "group_id=0 AND";
			}else if($email_to_subtype != ""){
				$client_where_clause = "group_id=".$email_to_subtype." AND";
			}else{
				$client_where_clause = "";
			}

			if($email_to == "test"){
				$emails_total = "1";
				if($this->SendEmail($admin_email, $admin_email, $subject, $message)) $emails_sent = "1";
			}else{
				$clients_emails_total = database_query("SELECT email FROM ".TABLE_CLIENTS." WHERE is_active = 1 AND ".$client_where_clause." email_notifications = 1 AND email != ''", ROWS_ONLY);
				$admins_emails_total = database_query("SELECT email FROM ".TABLE_ACCOUNTS." WHERE is_active = 1 AND email != ''", ROWS_ONLY);

				if($email_to == "clients"){
					$emails_total = $clients_emails_total;
				}else if($email_to == "admins"){
					$emails_total = $admins_emails_total;
				}else if($email_to == "all"){
					$emails_total = $clients_emails_total + $admins_emails_total;
				}

				if($email_to == "clients" || $email_to == "all"){
					$sql = "SELECT id, first_name, last_name, email, user_name  
							FROM ".TABLE_CLIENTS."
							WHERE is_active = 1 AND ".$client_where_clause." email_notifications = 1 AND email != ''
							ORDER BY id ASC";
					$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
					for($i=0; $i < $result[1]; $i++){
						$body_middle = str_ireplace("{FIRST NAME}", $result[0][$i]['first_name'], $message);
						$body_middle = str_ireplace("{LAST NAME}", $result[0][$i]['last_name'], $body_middle);
						$body_middle = str_ireplace("{USER NAME}", $result[0][$i]['user_name'], $body_middle);
						if($this->SendEmail($result[0][$i]['email'], $admin_email, $subject, $body_middle)) $emails_sent++;
					}
				}
				
				if($email_to == "admins" || $email_to == "all"){					
					$sql = "SELECT id, first_name, last_name, email, user_name  
							FROM ".TABLE_ACCOUNTS."
							WHERE is_active = 1 AND email != ''
							ORDER BY id ASC";
					$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
					for($i=0; $i < $result[1]; $i++){
						$body_middle = str_ireplace("{FIRST NAME}", $result[0][$i]['first_name'], $message);
						$body_middle = str_ireplace("{LAST NAME}", $result[0][$i]['last_name'], $body_middle);
						$body_middle = str_ireplace("{USER NAME}", $result[0][$i]['user_name'], $body_middle);
						if($this->SendEmail($result[0][$i]['email'], $admin_email, $subject, $body_middle)) $emails_sent++;
					}
				}
				
				if($send_copy_to_admin == "1"){
					$this->SendEmail($admin_email, $admin_email, $subject." (admin copy)", $message);
				}									
			}
		
			if($emails_sent){
				$_SESSION['email_random_code'] = $email_post_code;
				$msg = str_replace("_SENT_", $emails_sent, _EMAILS_SUCCESSFULLY_SENT);
				$msg = str_replace("_TOTAL_", $emails_total, $msg);
				$this->error = false;
				draw_success_message($msg);			
			}else{
				$this->error = true;
				draw_important_message(_EMAILS_SENT_ERROR);			
			}			
		}else{
			draw_important_message(_EMAIL_FIELDS_EMPTY_ALERT);			
		}		
	}

	/**
	 *	Draws mass mail form
	 */
	public function DrawMassMailForm()
	{
		global $objSettings; 

		$lang = Application::Get("lang");
	
		$template_subject = "";
		$template_content = "";
		$clients_emails_count = database_query("SELECT email FROM ".TABLE_CLIENTS." WHERE is_active = 1 AND email_notifications = 1 AND email != ''", ROWS_ONLY);
		$admins_emails_count = database_query("SELECT email FROM ".TABLE_ACCOUNTS." WHERE is_active = 1 AND email != ''", ROWS_ONLY);
		$emails_count = $clients_emails_count + $admins_emails_count;
		$send_copy_to_admin = "1";

		$email_from = $objSettings->GetParameter("admin_email");
		
		$template_code = isset($_GET['template_code']) ? prepare_input($_GET['template_code']) : "";
		$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : "5";
		$clients_module_installed = Modules::IsModuleInstalled("clients");
		
		// load appropriate email template
		if($template_code != ""){
			$template = $this->GetTemplate($template_code, $lang);
			$template_subject = $template['template_subject'];
			$template_content = $template['template_content'];
		}
		
		if($this->error == true){
			$template_code    = isset($_POST['template_name']) ? prepare_input($_POST['template_name']) : "";	
			$template_subject = isset($_POST['subject']) ? prepare_input($_POST['subject']) : "";
			$template_content = isset($_POST['message']) ? prepare_input($_POST['message']) : "";			
		}
		
		$output ="		
		<script type='text/javascript'>
			function duration_OnChange(val){
				var el_package_size = (document.getElementById('package_size')) ? document.getElementById('package_size') : null;
				if(val == '' && el_package_size){
					el_package_size.selectedIndex = 0;
					el_package_size.disabled = 'disabled';
				}else{
					el_package_size.disabled = '';
				}
			}
			
			function email_to_OnChange(val){
				var el_send_copy_to_admin = (document.getElementById('send_copy_to_admin')) ? document.getElementById('send_copy_to_admin') : null;
				if(val == 'admins' && el_send_copy_to_admin){
					el_send_copy_to_admin.disabled = 'disabled';
				}else{
					el_send_copy_to_admin.disabled = '';
				}
			}
					
			function OnSubmit_Check(){
				var email_to = (document.getElementById('email_to')) ? document.getElementById('email_to').value : '';
				var email_from = (document.getElementById('email_from')) ? document.getElementById('email_from').value : '';
				var subject = (document.getElementById('subject')) ? document.getElementById('subject').value : '';
				var message = (document.getElementById('message')) ? document.getElementById('message').value : '';
				if(email_to == ''){
					alert('".str_replace("_FIELD_", _EMAIL_TO, _FIELD_CANNOT_BE_EMPTY)."');
					document.getElementById('email_to').focus();
					return false;            
				}else if(email_from == ''){
					alert('".str_replace("_FIELD_", _EMAIL_FROM, _FIELD_CANNOT_BE_EMPTY)."');
					document.getElementById('email_from').focus();
					return false;
				}else if(email_from != '' && !appIsEmail(email_from)){
					alert('".str_replace("_FIELD_", _EMAIL_FROM, _FIELD_MUST_BE_EMAIL)."');
					document.getElementById('email_from').focus();
					return false;			
				}else if(subject == ''){
					alert('".str_replace("_FIELD_", _SUBJECT, _FIELD_CANNOT_BE_EMPTY)."');
					document.getElementById('subject').focus();
					return false;
				}else if(message == ''){
					alert('".str_replace("_FIELD_", _MESSAGE, _FIELD_CANNOT_BE_EMPTY)."');
					document.getElementById('message').focus();
					return false;
				}else if(email_to == 'all'){
					if(!confirm('"._PERFORM_OPERATION_COMMON_ALERT."')){
						return false;
					}
				}
				return true;
			}
		</script>
		
		<form action='index.php?admin=mass_mail' method='post' style='margin:0px;'>
			<input type='hidden' name='task' value='send'>
			<input type='hidden' name='email_random_code' value='".get_random_string(10)."'>
			".draw_token_field(true)."
			<table border='0' cellspacing='10'>
			<tr>
				<td align='left' valign='top'>
					<fieldset style='height:410px;'>
					<legend><b>"._FORM.":</b></legend>
					<table width='97%' align='center' border='0' cellspacing='5'>
					<tr>
						<td align='right' nowrap>
							<label>"._EMAIL_TEMPLATES.":</label><br>
							<a href='index.php?admin=email_templates'>[ "._MANAGE_TEMPLATES." ]</a>				
						</td>
						<td></td>
						<td>
							<table cellpadding='0' cellspacing='0'>
							<tr valign='middle'>
								<td>
									<select name='template_name' id='template_name' style='margin-bottom:3px;' onchange=\"document.location.href='index.php?admin=mass_mail&template_code='+this.value\">
										<option value=''>-- "._NO_TEMPLATE." --</option>";
										$templates = $this->GetAllTemplates("is_system_template=0");
										for($i=0; $i < $templates[1]; $i++){
											$output .= "<option";
											$output .= (($templates[0][$i]['is_system_template'] == "1") ? " style='background-color:#ffffcc;color:#000055'" : "");
											$output .= (($template_code == $templates[0][$i]['template_code']) ? " selected='selected'" : "");
											$output .= " value=\"".encode_text($templates[0][$i]['template_code'])."\">".$templates[0][$i]['template_name']."</option>";
										}
										$output .= "
									</select>						
								</td>
							</tr>
							</table>                    
						</td>
					</tr>
					<tr>
						<td align='right' nowrap><label>"._EMAIL_TO.":</label></td>
						<td><span class='mandatory_star'>*</span></td>
						<td>
							<select name='email_to' id='email_to' style='margin-bottom:3px;' onchange='email_to_OnChange(this.value)'>
								<option value=''>-- "._SELECT." --</option>
								<option value='test' style='background-color:#ffffcc;color:#000055'>"._TEST_EMAIL." (".$email_from.")</option>";								
								if($clients_module_installed){
									$output .= "<optgroup label='"._CLIENTS."'>";
									$output .= "<option value='clients|all'>"._ALL." (".$clients_emails_count.")</option>";	
									$arrClientsGroups = ClientGroups::GetAllGroupsByClients();
									$client_groups_emails_count = 0;
									if($arrClientsGroups[1] > 0){
										foreach($arrClientsGroups[0] as $key => $val){
											if($val['clients_count']){
												$output .= "<option value='clients|".$val['id']."'>".$val['name']." (".$val['clients_count'].")</option>";
												$client_groups_emails_count += $val['clients_count'];												
											}
										}
									}
									$client_non_groups_emails = $clients_emails_count - $client_groups_emails_count;
									$output .= "<option value='clients|uncategorized'>"._UNCATEGORIZED." (".$client_non_groups_emails.")</option>";										
									$output .= "</optgroup>";
								}
								$output .= "<option value='admins'>"._ADMINS." (".$admins_emails_count.")</option>";
								if($clients_module_installed) $output .= "<option value='all'>"._ADMINS_AND_CLIENTS." (".$emails_count.")</option>";
							$output .= "</select>
						</td>
					</tr>            
					<tr>
						<td align='right' nowrap><label for='email'>"._EMAIL_FROM.":</label></td>
						<td><span class='mandatory_star'>*</span></td>
						<td>
							<input type='text' name='email_from' style='width:210px' id='email_from' value='".decode_text($email_from)."'>
						</td>
					</tr>
					<tr valign='top'>
						<td align='right' nowrap><label>"._SUBJECT.":</label></td>
						<td><span class='mandatory_star'>*</span></td>
						<td>
							<input type='text' style='width:410px' name='subject' id='subject' value='".decode_text($template_subject)."'>
						</td>
					</tr>
					<tr valign='top'>
						<td align='right' nowrap><label>"._MESSAGE.":</label></td>
						<td><span class='mandatory_star'>*</span></td>
						<td>
							<textarea style='width:465px;margin-right:10px;' rows='10' name='message' id='message'>".$template_content."</textarea>
						</td>
					</tr>";
					
					$output .= "
					<tr valign='middle'>
						<td colspan=2></td>
						<td><img src='images/question_mark.png' alt=''>"._MASS_MAIL_ALERT."</td>
					</tr>";						
					
					#<tr valign='middle'>
					#	<td colspan=2></td>
					#	<td>
					#		Package Size: <img class='help' src='images/question_mark.gif' title='Usually public hostings have a limit of 200 emails per hour'>
					#		<select name='package_size' id='package_size' title='package_size' style='margin-bottom:3px;'>
					#			<option value='10'>10</option>
					#			<option value='20'>20</option>
					#			<option value='30'>30</option>
					#			<option value='40'>40</option>
					#			<option value='50'>50</option>
					#			<option value='75'>75</option>
					#			<option value='100'>100</option>
					#		</select>    						
					#		
					#		&nbsp;&nbsp;&nbsp;
					#
					#		Duration:						
					#		<select name='duration' id='duration' style='margin-bottom:3px;' onchange='duration_OnChange(this.value)'>
					#			<option ".(($duration == "") ? "selected='selected'" : "")." value=''>-- no --</option>
					#			<option ".(($duration == "1") ? "selected='selected'" : "")." value='1'>1 min</option>
					#			<option ".(($duration == "2") ? "selected='selected'" : "")."value='2'>2 min</option>
					#			<option ".(($duration == "5") ? "selected='selected'" : "")."value='5'>5 min</option>
					#			<option ".(($duration == "10") ? "selected='selected'" : "")."value='10'>10 min</option>
					#			<option ".(($duration == "15") ? "selected='selected'" : "")."value='15'>15 min</option>
					#			<option ".(($duration == "20") ? "selected='selected'" : "")."value='20'>20 min</option>
					#			<option ".(($duration == "30") ? "selected='selected'" : "")."value='30'>30 min</option>
					#			<option ".(($duration == "45") ? "selected='selected'" : "")."value='45'>45 min</option>
					#			<option ".(($duration == "60") ? "selected='selected'" : "")."value='60'>60 min</option>
					#		</select>
					#	</td>
					#</tr>
					
					$output .= "
					<tr><td colspan='3' nowrap style='height:6px;'></td></tr>
					<tr>
						<td align='right' nowrap><a href='javascript:void(0);' onclick=\"appPopupWindow('mail_preview.html','message')\">[ "._PREVIEW." ]</a></td>
						<td></td>
						<td>
							<div style='float:left'><input type='checkbox' class='form_checkbox' name='send_copy_to_admin' id='send_copy_to_admin' ".(($send_copy_to_admin == "1") ? "checked='checked'" : "")." value='1'> <label for='send_copy_to_admin'>"._SEND_COPY_TO_ADMIN."</label></div>
							<div style='float:right'><input class='form_button' type='submit' name='btnSubmit' value='"._SEND."' onclick='return OnSubmit_Check();'>&nbsp;&nbsp;</div>
						</td>
					</tr>
					</table>
					</fieldset>
				</td>        
				<td align='left' valign='top'>
					<fieldset style='padding-".Application::Get("defined_right").":10px;'>
					<legend>"._PREDEFINED_CONSTANTS.":</legend>
					<ul>
						<li>{FIRST NAME} <br><font color=a0a0a0>"._PC_FIRST_NAME_TEXT."</font></li>
						<li>{LAST NAME} <br><font color=a0a0a0>"._PC_LAST_NAME_TEXT."</font></li>
						<li>{USER NAME} <br><font color=a0a0a0>"._PC_USER_NAME_TEXT."</font></li>
						<li>{BASE URL} <br><font color=a0a0a0>"._PC_WEB_SITE_BASED_URL_TEXT."</font></li>
						<li>{WEB SITE} <br><font color=a0a0a0>"._PC_WEB_SITE_URL_TEXT."</font></li>
						<li>{YEAR} <br><font color=a0a0a0>"._PC_YEAR_TEXT."</font></li>
					</ul>
					</fieldset>
				</td>
			</tr>
			</table>    
		</form>";
	
		echo $output;
	}

	/**
	 * Before-Delete record
	 */
	public function BeforeDeleteRecord()
	{
		$sql = "SELECT is_system_template FROM ".$this->tableName." WHERE id = ".(int)$this->curRecordId;
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			if($result[0]["is_system_template"] == "1"){
				$this->error = _SYSTEM_EMAIL_DELETE_ALERT;
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns all email templates
	 * 		@param @where_clause
	 */
	private function GetAllTemplates($where_clause = "")
	{
		$lang = Application::Get("lang");
		
		$sql = "SELECT
					language_id,
					template_code,
					template_name,
					template_subject,
					template_content,
					is_system_template
				FROM ".$this->tableName."
				WHERE language_id = '".$lang."' ".(($where_clause != "") ? " AND ".$where_clause : "")."
				ORDER BY is_system_template ASC";
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		return $result;
	}

	/**
	 * Sends email
	 * 		@param $email_to
	 * 		@param $email_from
	 * 		@param $subject
	 * 		@param $message
	 */
	private function SendEmail($email_to, $email_from, $subject, $message)
	{		
		$body = "<div style=direction:".Application::Get("lang_dir").">".$message."</div>";
		$text_version = strip_tags($body);
		$html_version = nl2br($body);

		$objEmail = new Email($email_to, $email_from, $subject);                     
		$objEmail->textOnly = false;
		$objEmail->content = $html_version;
		
		return ($objEmail->Send()) ? true : false;		
	}
		
}
?>