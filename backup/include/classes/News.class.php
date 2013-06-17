<?php

/**
 *	Class News
 *  -------------- 
 *  Description : encapsulates News operations & properties
 *	Usage       : MicroCMS, HotelSite, ShoppingCart, BusinessDirectory
 *  Updated	    : 05.06.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.3
 *	
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct             GetNewsId
 *	__destruct
 *	SetSQLs
 *	DrawNewsBlock
 *	GetNews
 *	GetAllNews
 *	DrawRegistrationForm
 *	AfterDetailsMode
 *	CacheAllowed
 *
 *  1.0.3
 *  	-
 *  	-
 *  	-
 *  	-
 *  	-
 *  1.0.2
 *  	- removed 0000-00-00 preparing for date fields
 *  	- added GetNewsId()
 *  	- added new field for news table - news_code
 *  	- fixed bug with double registering the same users
 *  	- added token key for submission form
 *  1.0.1
 *  	- added Date Created in Add mode
 *  	- added "news" ad default value for DDL
 *  	- future news now are not showing on Front-End News Block
 *  	- improved GetAllNews()
 *  	- added CacheAllowed()
 *	
 **/

class News extends MicroGrid {
	
	protected $debug = false;

	protected $tableName;
	protected $primaryKey;
	protected $dataSet;
	protected $formActionURL;
	protected $params;
	protected $actions;
	protected $isHtmlEncoding;

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

	public $error;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{
		$this->SetRunningTime();
		
		$this->params = array();
		if(isset($_POST['news_code']))     $this->params['news_code']    = prepare_input($_POST['news_code']);
		if(isset($_POST['header_text']))   $this->params['header_text']  = prepare_input($_POST['header_text']);
		if(isset($_POST['body_text'])) 	   $this->params['body_text']    = prepare_input($_POST['body_text'], false, "medium");
		if(isset($_POST['type']))   	   $this->params['type']         = prepare_input($_POST['type']);
		if(isset($_POST['date_created']))  $this->params['date_created'] = prepare_input($_POST['date_created']);
		$this->params['language_id'] 	   = MicroGrid::GetParameter('language_id');
	
		$this->isHtmlEncoding = true;
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_NEWS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->formActionURL = "index.php?admin=mod_news_management";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;		

		$this->allowLanguages = true;
		$this->WHERE_CLAUSE = "WHERE language_id = '".$this->languageId."'";		
		$this->ORDER_CLAUSE = "ORDER BY date_created DESC";

		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;


		// prepare languages array		
		$total_languages = Languages::GetAllActive();
		$arr_languages      = array();
		foreach($total_languages[0] as $key => $val){
			$arr_languages[$val['abbreviation']] = $val['lang_name'];
		}
		
		$arr_types = array("news"=>_NEWS, "events"=>_EVENTS);
		$date_format = get_date_format();

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									type,
									header_text,
									body_text,
									date_created,
									CASE
										WHEN type = 'events' THEN
											CONCAT('<a href=javascript:void(0) onclick=javascript:__mgDoPostBack(\"".$this->tableName."\",\"details\",\"', ".$this->primaryKey.", '\")>events', ' (', (SELECT COUNT(*) as cnt FROM ".TABLE_EVENTS_REGISTERED." er WHERE er.event_id = ".$this->tableName.".".$this->primaryKey."), ')</a>')
										ELSE type										
									END as type_link
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"date_created" => array("title"=>_DATE_CREATED, "type"=>"label", "align"=>"left", "width"=>"190px", "format"=>"date", "format_parameter"=>$date_format),
			"header_text"  => array("title"=>_HEADER,       "type"=>"label", "align"=>"left", "width"=>""),
			"type_link"    => array("title"=>_TYPE, "type"=>"label", "align"=>"center", "width"=>"9%"),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
			"header_text"  => array("title"=>_HEADER, 	    "type"=>"textbox", "required"=>true, "width"=>"370px"),
			"body_text"    => array("title"=>_TEXT,   	    "type"=>"textarea", "width"=>"490px", "height"=>"200px", "editor_type"=>"wysiwyg", "readonly"=>false, "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false),
			"type"  	   => array("title"=>_TYPE,         "type"=>"enum", "source"=>$arr_types, "required"=>true, "default"=>"news"),
			"date_created" => array("title"=>_DATE_CREATED, "type"=>"datetime", "required"=>true, "readonly"=>false, "default"=>date("Y-m-d H:i:s"), "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>$date_format, "min_year"=>"10", "max_year"=>"5"),
			"language_id"  => array("title"=>_LANGUAGE,     "type"=>"enum", "source"=>$arr_languages, "required"=>true),
			"news_code"    => array("title"=>"",            "type"=>"hidden",  "required"=>true, "readonly"=>false, "default"=>random_string()),
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".type,
								".$this->tableName.".header_text,
								".$this->tableName.".body_text,
								".$this->tableName.".language_id,
								".$this->tableName.".date_created,
								".TABLE_LANGUAGES.".lang_name as language_name 
							FROM ".$this->tableName."
								INNER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".language_id = ".TABLE_LANGUAGES.".abbreviation
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
			"header_text"  => array("title"=>_HEADER, 	   "type"=>"textbox", "required"=>true, "width"=>"370px"),
			"body_text"    => array("title"=>_TEXT,   	   "type"=>"textarea", "width"=>"490px", "height"=>"200px", "editor_type"=>"wysiwyg", "readonly"=>false, "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false),
			"type"  	   => array("title"=>_TYPE,        "type"=>"enum", "source"=>$arr_types, "required"=>true),
			"date_created" => array("title"=>_DATE_CREATED,"type"=>"datetime", "required"=>true, "readonly"=>false, "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>$date_format, "min_year"=>"10", "max_year"=>"5"),
			"language_id"  => array("title"=>_LANGUAGE,    "type"=>"enum", "source"=>$arr_languages, "required"=>true, "readonly"=>true),
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(
			"header_text"   => array("title"=>_HEADER,   "type"=>"label"),
			"body_text"     => array("title"=>_TEXT,     "type"=>"label"),
			"type"          => array("title"=>_TYPE,     "type"=>"label"),
			"date_created"  => array("title"=>_DATE_CREATED, "type"=>"datetime", "format"=>"date", "format_parameter"=>$date_format),
			"language_name" => array("title"=>_LANGUAGE, "type"=>"label"),
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
	 * After-operation Details mode
	 */
	function AfterDetailsMode()
	{
		$sql = "SELECT
					er.first_name,
					er.last_name,
					er.email,
					er.phone,
					er.date_registered
				FROM ".TABLE_EVENTS_REGISTERED." er
					INNER JOIN ".$this->tableName." e ON er.event_id = e.".$this->primaryKey."
				WHERE
					e.type = 'events' AND 
					er.event_id = ".(int)$this->curRecordId;
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		
		if($result[1] > 0){
			echo "<table class='mgrid_table' border='0' cellspacing='0' cellpadding='2'>";
			echo "<tr>";
			echo "<th align='left'><label></label></th>";
			echo "<th align='left'><label>"._FIRST_NAME."</label></th>";
			echo "<th align='left'><label>"._LAST_NAME."</label></th>";
			echo "<th align='left'><label>"._EMAIL_ADDRESS."</label></th>";
			echo "<th align='left'><label>"._PHONE."</label></th>";
			echo "<th align='left'><label>"._REGISTERED."</label></th>";
			echo "</tr>";
			echo "<tr><td colspan='6' height='3px' nowrap='nowrap'><div class='no_margin_line'><img src='images/line_spacer.gif' width='100%' height='1px' alt='' /></div></td></tr>";
	
			for($i=0; $i<$result[1]; $i++){
				echo "<tr>";
				echo "<td>".($i+1).".</td>";
				echo "<td>".$result[0][$i]['first_name']."</td>";
				echo "<td>".$result[0][$i]['last_name']."</td>";
				echo "<td>".$result[0][$i]['email']."</td>";
				echo "<td>".$result[0][$i]['phone']."</td>";
				echo "<td>".format_datetime($result[0][$i]['date_registered'])."</td>";	
				echo "</tr>";
			}
			echo "</tr>";
			echo "</table>";
			
		}
		
	}

	/**
	 *	Sets system SQLs
	 *		@param $key
	 *		@param $msg
	 */	
	public function SetSQLs($key, $msg)
	{
		if($this->debug) $this->arrSQLs[$key] = $msg;					
	}
	
	/**
	 *	Draws news block
	 */	
	public function DrawNewsBlock()
	{	
	    $text_align_left = (Application::Get("lang_dir") == "ltr") ? "text-align:left;" : "text-align:right; padding-right:15px;";
		$text_align_right = (Application::Get("lang_dir") == "ltr") ? "text-align:right; padding-right:15px;" : "text-align:left;";

		$objNewsSettings = new ModulesSettings("news");
		$news_header_length = $objNewsSettings->GetSettings("news_header_length");
		$news_count = $objNewsSettings->GetSettings("news_count");

		$this->WHERE_CLAUSE = "WHERE date_created < '".date("Y-m-d H:i:s")."' AND language_id = '".((isset($_SESSION['lang'])) ? $_SESSION['lang'] : Languages::GetDefaultLang())."'";		
		$all_news = $this->GetAll($this->ORDER_CLAUSE);
		draw_block_top(_NEWS_AND_EVENTS);
		echo "<ul class='news-block'>";		
		for($news_ind = 0; $news_ind < $all_news[1]; $news_ind++)
		{
			if($news_ind+1 > $news_count) break; // Show first X news
			$news_str = $all_news[0][$news_ind]['header_text']; // Display Y first chars
			$news_str = (strlen($news_str) > $news_header_length) ? substr($all_news[0][$news_ind]['header_text'],0,$news_header_length)."..." : $news_str;
			echo "<li>".$news_str."<br />";
			echo prepare_link("news", "nid", $all_news[0][$news_ind]['id'], $news_str, "<i>"._READ_MORE." &raquo;</i>", "category-news");
			echo "</li>";
		}
		if($news_ind == 0){
			echo "<li>"._NO_NEWS."</li>";
		}
		echo "</ul>";
		draw_block_bottom();	
	}	
	
	/***
	 *	Returns certain news
	 *		@param $news_id
	 */
	public function GetNews($news_id = "0")
	{
		$sql = $this->VIEW_MODE_SQL." WHERE ".$this->primaryKey." = ".(int)$news_id." ".$this->ORDER_CLAUSE;
		$news = $this->GetRecord($sql);
		return $news;		
	}
	
	/**
	 *	Returns all news
	 *		@param $type
	 */
	public function GetAllNews($type = "")
	{
		$type_where_clause = ($type == "previous") ? " AND date_created <= '".date("Y-m-d 23:59:59")."'" : "";
		$sql = $this->VIEW_MODE_SQL." ".$this->WHERE_CLAUSE.$type_where_clause." ".$this->ORDER_CLAUSE;		
		return database_query($sql, DATA_AND_ROWS);	
	}
	
	/**
	 *	Draws registration form
	 *		@param $news_id
	 *		@param $return
	 */
	public function DrawRegistrationForm($news_id = "0", $event_title = "", $return = false)
	{
		if(!$news_id) return "";
		
		global $objSettings, $objLogin;
		
		$lang = Application::Get("lang");		
		$focus_element = "first_name";

		// post fields
		$task             = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
		$event_id		  = isset($_POST['event_id']) ? (int)$_POST['event_id'] : "0";
		$first_name       = isset($_POST['first_name']) ? prepare_input($_POST['first_name']) : "";
		$last_name        = isset($_POST['last_name']) ? prepare_input($_POST['last_name']) : "";
		$email            = isset($_POST['email']) ? prepare_input($_POST['email']) : "";
		$phone            = isset($_POST['phone']) ? prepare_input($_POST['phone']) : "";
		$message          = isset($_POST['message']) ? prepare_input($_POST['message']) : "";
		$captcha_code 	  = isset($_POST['captcha_code']) ? prepare_input($_POST['captcha_code']) : "";
		$admin_email	  = $objSettings->GetParameter("admin_email");
		$msg              = "";

		if($task == "register_to_event")
		{
			include_once("modules/captcha/securimage.php");
			$objImg = new Securimage();			
		
			if($first_name == ""){
				$msg = draw_important_message(_FIRST_NAME_EMPTY_ALERT, false);
				$focus_element = "first_name";
			}else if($last_name == ""){
				$msg = draw_important_message(_LAST_NAME_EMPTY_ALERT, false);
				$focus_element = "last_name";
			}else if($email == ""){
				$msg = draw_important_message(_EMAIL_EMPTY_ALERT, false);
				$focus_element = "email";
			}else if(($email != "") && (!check_email_address($email))){
				$msg = draw_important_message(_EMAIL_VALID_ALERT, false);
				$focus_element = "email";
			}else if($phone == ""){        
				$msg = draw_important_message(str_replace("_FIELD_", _PHONE, _FIELD_CANNOT_BE_EMPTY), false);
				$focus_element = "phone";
			}else if(!$objImg->check($captcha_code)){
				$msg = draw_important_message(_WRONG_CODE_ALERT, false);
				$focus_element = "captcha_code";
			}else{
				$sql = "SELECT * FROM ".TABLE_EVENTS_REGISTERED." WHERE event_id = '".(int)$event_id."' AND email = '".$email."'";
				if(database_query($sql, ROWS_ONLY, FIRST_ROW_ONLY) > 0){
					$msg = draw_important_message(_EVENT_USER_ALREADY_REGISTERED, false);
				}				
			}
			
			// deny all operations in demo version
			if(strtolower(SITE_MODE) == "demo"){
				$msg = draw_important_message(_OPERATION_BLOCKED, false);
			}						

			if($msg == ""){
				if($objLogin->IpAddressBlocked(get_current_ip())){
					$msg = draw_important_message(_IP_ADDRESS_BLOCKED, false);
				}else if($objLogin->EmailBlocked($email)){
					$msg = draw_important_message(_EMAIL_BLOCKED, false);
				}else{
					$sql = "INSERT INTO ".TABLE_EVENTS_REGISTERED." (id, event_id, first_name, last_name, email, phone, message, date_registered)
							VALUES (NULL, ".(int)$event_id.", '".encode_text($first_name)."', '".encode_text($last_name)."', '".encode_text($email)."', '".encode_text($phone)."', '".encode_text($message)."', '".date("Y-m-d H:i:s")."')";
					if(database_void_query($sql)){
						$msg = draw_success_message(_EVENT_REGISTRATION_COMPLETED, false);
	
						////////////////////////////////////////////////////////////
						$sender = $email;
						$recipiant = $admin_email;
					
						$objEmailTemplates = new EmailTemplates();
						$email_template = $objEmailTemplates->GetTemplate("events_new_registration", $lang);				
						$subject 		= $email_template["template_subject"];
						$body_middle 	= $email_template["template_content"];
						$template_name 	= $email_template["template_name"];				
		
						$body_middle = str_replace("{FIRST NAME}", $first_name, $body_middle);
						$body_middle = str_replace("{LAST NAME}", $last_name, $body_middle);
						$body_middle = str_replace("{EVENT}", "<b>".$event_title."</b>", $body_middle);
					
						$body   = "<div style=direction:".Application::Get("lang_dir").">";
						$body  .= $body_middle;
						$body  .= "</div>";
		
						$text_version = strip_tags($body);
						$html_version = nl2br($body);
		
						// send email to registered user
						$objEmail = new Email($email, $admin_email, $subject);                     
						$objEmail->textOnly = false;
						$objEmail->content = $html_version;                
						$objEmail->Send();
		
						// copy to admin
						$objEmail = new Email($admin_email, $admin_email, $template_name." (admin copy)");                     
						$objEmail->textOnly = false;
						$objEmail->content = $html_version;                
						$objEmail->Send();
						////////////////////////////////////////////////////////////		
	
						$first_name = $last_name = $email = $phone = $message = "";
					}else{
						///echo mysql_error();
						$msg = draw_important_message(_TRY_LATER, false);
					}					
				}
			}
		}

		$output = "
		".(($msg != "") ? $msg."<br />" : "<br />")."
		<fieldset style='border:1px solid #cccccc;padding-left:10px;margin:0px 12px 12px 12px;'>
		<legend><b>"._REGISTRATION_FORM."</b></legend>
		<form method='post' name='frmEventRegistration' id='frmEventRegistration'>
		    <input type='hidden' name='task' value='register_to_event' />
			<input type='hidden' name='event_id' value='".$news_id."' />
			".draw_token_field(true)."

			<table cellspacing='1' cellpadding='2' border='0' width='100%'>
			<tbody>
			<tr>
				<td width='25%' align='".Application::Get("defined_right")."'>"._FIRST_NAME.":</td>
				<td><font class='mandatory_star'>*</font></td>
				<td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type='text' id='first_name' name='first_name' size='34' maxlength='32' value='".decode_text($first_name)."' autocomplete='off' /></td>
			</tr>
			<tr>
				<td align='".Application::Get("defined_right")."'>"._LAST_NAME.":</td>
				<td><font class='mandatory_star'>*</font></td>
				<td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type='text' id='last_name' name='last_name' size='34' maxlength='32' value='".decode_text($last_name)."' autocomplete='off' /></td>
			</tr>
			<tr>
				<td align='".Application::Get("defined_right")."'>"._EMAIL_ADDRESS.":</td>
				<td><font class='mandatory_star'>*</font></td>
				<td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type='text' id='email' name='email' size='34' maxlength='128' value='".decode_text($email)."' autocomplete='off' /></td>
			</tr>
			<tr>
				<td align='".Application::Get("defined_right")."'>"._PHONE.":</td>
				<td><font class='mandatory_star'>*</font></td>
				<td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type='text' id='phone' name='phone' size='22' maxlength='32' value='".decode_text($phone)."' autocomplete='off' /></td>
			</tr>
		    <tr valign='top'>
                <td align='".Application::Get("defined_right")."'>"._MESSAGE.":</td>
                <td></td>
                <td nowrap='nowrap' align='".Application::Get("defined_left")."'>
                    <textarea id='message' name='message' style='width:390px;' rows='4'>".$message."</textarea>                
                </td>
		    </tr>
			<tr>
				<td colspan='2'></td>
				<td colspan='2'>";				
					
					$output .= "<table border='0' cellspacing='2' cellpadding='2'>
					<tr>
						<td>
							<img id='captcha_image' src='modules/captcha/securimage_show.php?sid=".md5(uniqid(time()))."' />
						</td>	
						<td>
							<img style='cursor:pointer; padding:0px; margin:0px;' id='captcha_image_reload' src='modules/captcha/images/refresh.gif' style='cursor:pointer;' onclick=\"document.getElementById('captcha_image').src = 'modules/captcha/securimage_show.php?sid=' + Math.random(); return false\" title='"._REFRESH."' alt='"._REFRESH."' /><br />
							<a href='modules/captcha/securimage_play.php'><img border='0' style='padding:0px; margin:0px;' id='captcha_image_play' src='modules/captcha/images/audio_icon.gif' title='"._PLAY."' alt='"._PLAY."' /></a>						
						</td>					
						<td align='left'>
							"._TYPE_CHARS."<br />								
							<input type='text' name='captcha_code' id='captcha_code' style='width:175px;margin-top:5px;' value='' autocomplete='off' />
						</td>
					</tr>
					</table>";

				$output .= "</td>
			</tr>
			<tr><td height='20' colspan='3'>&nbsp;</td></tr>            
			<tr>
				<td colspan='3' align='center'>
				<input type='submit' class='form_button' name='btnSubmitPD' id='btnSubmitPD' value=' "._SEND." '>
				</td>
			</tr>
			<tr><td colspan='3'>&nbsp;</td></tr>		    		    
			</table>
			</form>
			
		</form>
		</fieldset>";
		
		if($focus_element != "") $output .= "<script type='text/javascript'>appSetFocus('".$focus_element."');</script>";
	
		if($return) return $output;
		else echo $output;		
	}

	/**
	 *	Checks if the page with news may be cached
	 *		@param $news_id
	 */
	public function CacheAllowed($news_id)
	{
		$result = $this->GetNews($news_id);
		if($result[1] > 0){			
			return true;		
		}
		return false;	
	}
	
	/**
	 *	Return news id for spesific language
	 *		@param $pid
	 *		@param $lang
	 **/
	static public function GetNewsId($nid = "", $lang = "")
	{
		if($nid != "" && $lang != ""){
			$sql = "SELECT id
					FROM ".TABLE_NEWS."
					WHERE language_id = '".$lang."' AND 
						  news_code = (SELECT news_code FROM ".TABLE_NEWS." WHERE id = ".(int)$nid.")";
			$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
			return isset($result['id']) ? $result['id'] : "";			
		}else{
			return "";	
		}		
	}
	
}
?>