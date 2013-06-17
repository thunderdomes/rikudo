<?php

/**
 *	Code Template for ClientGroups Class
 *  -------------- 
 *	Written by  : ApPHP
 *  Updated	    : 02.05.2011
 *
 *	PUBLIC				  	STATIC				 	PRIVATE
 * 	------------------	  	---------------     	---------------
 *	__construct             GetAllGroups
 *	__destruct              GetAllGroupsByClients
 *	
 *	
 **/


class ClientGroups extends MicroGrid {
	
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
	protected $allowRefresh;

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
		if(isset($_POST['name']))   	 $this->params['name'] = prepare_input($_POST['name']);
		if(isset($_POST['description'])) $this->params['description'] = prepare_input($_POST['description']);
		
		## for checkboxes 
		//$this->params['field4'] = isset($_POST['field4']) ? prepare_input($_POST['field4']) : "0";

		## for images (not necessary)
		//if(isset($_POST['icon'])){
		//	$this->params['icon'] = prepare_input($_POST['icon']);
		//}else if(isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != ""){
		//	// nothing 			
		//}else if (self::GetParameter('action') == "create"){
		//	$this->params['icon'] = "";
		//}

		## for files:
		// define nothing

		//$this->params['language_id'] = MicroGrid::GetParameter('language_id');
	
		//$this->uPrefix 		= "prefix_";
		
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_CLIENT_GROUPS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_clients_groups";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->languageId  	= ""; //($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = ""; // WHERE .... / "WHERE language_id = '".$this->languageId."'";				
		$this->ORDER_CLAUSE = ""; // ORDER BY ".$this->tableName.".date_created DESC
		
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
		/// $total_languages = Languages::GetAllActive();
		/// $arr_languages      = array();
		/// foreach($total_languages[0] as $key => $val){
		/// 	$arr_languages[$val['abbreviation']] = $val['lang_name'];
		/// }

		//---------------------------------------------------------------------- 
		// VIEW MODE
		// format: strip_tags
		// format: nl2br
		// format: "format"=>"date", "format_parameter"=>"M d, Y, g:i A" + IF(date_created = '0000-00-00 00:00:00', '', date_created) as date_created,
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									name,
									description
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"id"   => array("title"=>"ID", "type"=>"label", "align"=>"left", "width"=>"80px", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			"name" => array("title"=>_GROUP_NAME, "type"=>"label", "align"=>"left", "width"=>"140px", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			"description" => array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>true, "maxlength"=>"90", "format"=>"", "format_parameter"=>""),

			// "field1"  => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			// "field2"  => array("title"=>"", "type"=>"image", "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "image_width"=>"50px", "image_height"=>"30px", "target"=>"uploaded/", "no_image"=>""),
			// "field3"  => array("title"=>"", "type"=>"enum",  "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "source"=>array()),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address|password
		// 	 Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255... Ex.: "validation_maxlength"=>"255"
		// - Validation Max Value: 12, 255... Ex.: "validation_maximum"=>"99.99"
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(		    

			"name"        => array("title"=>_GROUP_NAME, "type"=>"textbox",  "width"=>"210px", "readonly"=>false, "maxlength"=>"50", "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>true, "visible"=>true),
			"description" => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"310px", "height"=>"90px", "editor_type"=>"simple", "readonly"=>false, "default"=>"", "required"=>false, "validation_type"=>"", "unique"=>false),

			// "field1"  => array("title"=>"", "type"=>"label"),
			// "field2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false, "visible"=>true),
			// "field3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false),
			// "field4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>date("Y-m-d H:i:s")),
			// "field5"  => array("title"=>"", "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"210px", "source"=>$arr_languages, "unique"=>false, "javascript_event"=>""),
			// "field6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			// "field7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"uploaded/", "no_image"=>"", "random_name"=>"true", "unique"=>false, "thumbnail_create"=>false, "thumbnail_field"=>"", "thumbnail_width"=>"", "thumbnail_height"=>""),
			// "field8"  => array("title"=>"", "type"=>"file",     "width"=>"210px", "required"=>true, "target"=>"uploaded/", "random_name"=>"true", "unique"=>false),
			// "field9"  => array("title"=>"", "type"=>"datetime", "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>"M d, Y, g:i A"),
			// "field10" => array("title"=>"", "type"=>"password", "width"=>"310px", "height"=>"90px", "required"=>true, "validation_type"=>"password", "cryptography"=>PASSWORDS_ENCRYPTION, "cryptography_type"=>PASSWORDS_ENCRYPTION_TYPE, "aes_password"=>PASSWORDS_ENCRYPT_KEY),
			// "language_id"  => array("title"=>_LANGUAGE, "type"=>"enum", "source"=>$arr_languages, "required"=>true, "unique"=>false),
			
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"field1"  => array("title"=>"", "type"=>"label"),
			//		"field2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false, "visible"=>true),
			//		"field3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false),
			//  )
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address|password
		//   Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255... Ex.: "validation_maxlength"=>"255"
		// - Validation Max Value: 12, 255... Ex.: "validation_maximum"=>"99.99"
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".name,
								".$this->tableName.".description
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			"name"        => array("title"=>_GROUP_NAME, "type"=>"textbox",  "width"=>"210px", "readonly"=>false, "maxlength"=>"50", "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>true, "visible"=>true),
			"description" => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"310px", "height"=>"90px", "editor_type"=>"simple", "readonly"=>false, "default"=>"", "required"=>false, "validation_type"=>"", "unique"=>false),

			// "field1"  => array("title"=>"", "type"=>"label"),
			// "field2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false, "visible"=>true),
			// "field3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false),
			// "field4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "default"=>date("Y-m-d H:i:s")),
			// "field5"  => array("title"=>"", "type"=>"enum",     "width"=>"210px", "required"=>true, "readonly"=>false, "source"=>$arr_languages, "unique"=>false, "javascript_event"=>""),
			// "field6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			// "field7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "target"=>"uploaded/", "no_image"=>"", "random_name"=>"true", "unique"=>false, "image_width"=>"120px", "image_height"=>"90px", "thumbnail_create"=>false, "thumbnail_field"=>"", "thumbnail_width"=>"", "thumbnail_height"=>""),
			// "field8"  => array("title"=>"", "type"=>"file",     "width"=>"210px", "required"=>true, "target"=>"uploaded/", "random_name"=>"true", "unique"=>false),
			// "field9"  => array("title"=>"", "type"=>"datetime", "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>"M d, Y, g:i A"),
			// "field10" => array("title"=>"", "type"=>"password", "width"=>"310px", "height"=>"90px", "required"=>true, "validation_type"=>"password", "cryptography"=>PASSWORDS_ENCRYPTION, "cryptography_type"=>PASSWORDS_ENCRYPTION_TYPE, "aes_password"=>PASSWORDS_ENCRYPT_KEY),
		
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"field1"  => array("title"=>"", "type"=>"label"),
			//		"field2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "required"=>true, "validation_type"=>"", "unique"=>false, "visible"=>true),
			//		"field3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "required"=>true, "default"=>"", "validation_type"=>"", "unique"=>false),
			//  )
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"name"        => array("title"=>_GROUP_NAME, "type"=>"label"),
			"description" => array("title"=>_DESCRIPTION, "type"=>"label"),

			// "field1"  => array("title"=>"", "type"=>"label"),
			// "field2"  => array("title"=>"", "type"=>"image", "target"=>"uploaded/", "no_image"=>"", "image_width"=>"120px", "image_height"=>"90px"),
			// "field3"  => array("title"=>"", "type"=>"datetime", "format"=>"date", "format_parameter"=>"M d, Y, g:i A"),
			// "field4"  => array("title"=>"", "type"=>"enum", "source"=>array()),
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
    // Static Methods
	//==========================================================================	
	/**
	 *	Get all groups
	 */
	static public function GetAllGroups()
	{
		$sql = "SELECT id, name, description FROM ".TABLE_CLIENT_GROUPS." ORDER BY name ASC";					
		return database_query($sql, DATA_AND_ROWS);
	}

	/**
	 *	Get all groups by clients
	 */
	static public function GetAllGroupsByClients()
	{
		$sql = "SELECT cg.id, cg.name, cg.description,
					(SELECT COUNT(*) FROM ".TABLE_CLIENTS." c WHERE c.group_id = cg.id AND c.is_active = 1 AND c.email_notifications = 1 AND c.email != '') as clients_count
				FROM ".TABLE_CLIENT_GROUPS." cg				
				ORDER BY cg.name ASC";					
		return database_query($sql, DATA_AND_ROWS);
	}

}
?>