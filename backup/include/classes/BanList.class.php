<?php

/**
 *	BanList Class
 *  -------------- 
 *	Written by  : ApPHP
 *  Updated	    : 21.09.2010
 *	Written by  : ApPHP
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct
 *	__destruct
 *	
 **/


class BanList extends MicroGrid {
	
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
		if(isset($_POST['ban_item']))      $this->params['ban_item'] = prepare_input($_POST['ban_item']);
		if(isset($_POST['ban_reason']))    $this->params['ban_reason'] = prepare_input($_POST['ban_reason']);
		
		$item_validation_type = "";
		if(isset($_POST['ban_item_type'])){
			$this->params['ban_item_type'] = prepare_input($_POST['ban_item_type']);
			if($this->params['ban_item_type'] == "IP"){
				$item_validation_type = "ip_address";
			}else if($this->params['ban_item_type'] == "Email"){
				$item_validation_type = "email";
			}
		}
		
		## for checkboxes 
		//if(isset($_POST['parameter4']))   $this->params['parameter4'] = $_POST['parameter4']; else $this->params['parameter4'] = "0";

		## for images
		//if(isset($_POST['icon'])){
		//	$this->params['icon'] = $_POST['icon'];
		//}else if(isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != ""){
		//	// nothing 			
		//}else if (self::GetParameter('action') == "create"){
		//	$this->params['icon'] = "";
		//}

		$this->params['language_id'] = MicroGrid::GetParameter('language_id');
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_BANLIST;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=ban_list";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = ""; // WHERE .... / "WHERE language_id = '".$this->languageId."'";				
		$this->ORDER_CLAUSE = ""; // ORDER BY ".$this->tableName.".date_created DESC
		
		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;

		$this->isFilteringAllowed = true;
		$arr_ban_types = array("IP"=>_IP_ADDRESS, "Email"=>_EMAIL_ADDRESS);
		// define filtering fields
		$this->arrFilteringFields = array(
			_TYPE  => array("table"=>$this->tableName, "field"=>"ban_item_type", "type"=>"dropdownlist", "source"=>$arr_ban_types, "sign"=>"=", "width"=>"130px"),

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
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									ban_item,
									CASE
										WHEN ban_item_type = 'IP' THEN '"._IP_ADDRESS."'
										WHEN ban_item_type = 'Email' THEN '"._EMAIL_ADDRESS."'
										ELSE '"._UNKNOWN."'
									END ban_item_type,
									ban_reason
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"ban_item" => array("title"=>_BAN_ITEM, "type"=>"label",  "align"=>"left", "width"=>"170px", "height"=>"", "maxlength"=>""),
			"ban_item_type" => array("title"=>_TYPE, "type"=>"label", "align"=>"left", "width"=>"150px", "height"=>"", "maxlength"=>""),
			"ban_reason"  => array("title"=>_REASON, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),

			// "parameter1"  => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
			// "parameter2"  => array("title"=>"", "type"=>"image", "align"=>"center", "width"=>"", "image_width"=>"50px", "image_height"=>"30px", "target"=>"uploaded/", "no_image"=>""),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		// Validation Type: alpha|numeric|float|alpha_numeric|text|email
		// Validation Sub-Type: positive (for numeric and float)
		// Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(		    

			"ban_item"      => array("title"=>_BAN_ITEM, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "unique"=>true, "maxlength"=>"70", "default"=>"", "validation_type"=>$item_validation_type),
			"ban_item_type" => array("title"=>_TYPE, "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"130px", "source"=>$arr_ban_types),
			"ban_reason"    => array("title"=>_REASON, "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>false, "readonly"=>false, "default"=>"Spam from this IP/Email", "validation_type"=>""),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>""),
			// "parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>""),
			// "parameter4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>date("Y-m-d H:i:s")),
			// "parameter5"  => array("title"=>"", "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"210px", "source"=>$arr_languages),
			// "parameter6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
			// "parameter7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"uploaded/", "no_image"=>"", "random_name"=>"true"),
			// "language_id"  => array("title"=>_LANGUAGE, "type"=>"enum", "source"=>$arr_languages, "required"=>true),
			
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"parameter1"  => array("title"=>"", "type"=>"label"),
			//		"parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email"),
			//		"parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email"),
			//  )
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		// Validation Type: alpha|numeric|float|alpha_numeric|text|email
		// Validation Sub-Type: positive (for numeric and float)
		// Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".ban_item,
								".$this->tableName.".ban_item_type,
								".$this->tableName.".ban_reason
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			"ban_item"      => array("title"=>_BAN_ITEM, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "unique"=>true, "maxlength"=>"70", "default"=>"", "validation_type"=>$item_validation_type),
			"ban_item_type" => array("title"=>_TYPE, "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"130px", "source"=>$arr_ban_types),
			"ban_reason"    => array("title"=>_REASON, "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>false, "readonly"=>false, "default"=>"Spam from this IP/Email", "validation_type"=>""),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>""),
			// "parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>""),
			// "parameter4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "default"=>date("Y-m-d H:i:s")),
			// "parameter5"  => array("title"=>"", "type"=>"enum",     "width"=>"210px", "required"=>true, "readonly"=>false, "source"=>$arr_languages),
			// "parameter6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
			// "parameter7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "target"=>"uploaded/", "no_image"=>"", "random_name"=>"true"),
		
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"parameter1"  => array("title"=>"", "type"=>"label"),
			//		"parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email"),
			//		"parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|float|alpha_numeric|text|email"),
			//  )
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"ban_item"  	=> array("title"=>_BAN_ITEM, "type"=>"label"),
			"ban_item_type" => array("title"=>_TYPE, "type"=>"label"),
			"ban_reason"    => array("title"=>_REASON, "type"=>"label"),

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
	
}
?>