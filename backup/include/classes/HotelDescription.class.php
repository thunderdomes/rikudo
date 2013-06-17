<?php

/**
 *	HotelDescription
 *  -------------- 
 *  Description : encapsulates Hotel Description class properties
 *  Updated	    : 12.11.2010
 *	Written by  : ApPHP
 *
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct				
 *  __destruct              
 *	
 **/


class HotelDescription extends MicroGrid {
	
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
		
		$room_id 	= isset($_GET['room_id']) ? (int)$_GET['room_id'] : "0";
		
		$this->params = array();		
		if(isset($_POST['name'])) $this->params['name'] = prepare_input($_POST['name']);
		if(isset($_POST['address'])) $this->params['address'] = prepare_input($_POST['address']);
		if(isset($_POST['description'])) $this->params['description'] = prepare_input($_POST['description'], false, "medium");

		//$default_lang = Languages::GetDefaultLang();
		//$default_currency = Currencies::GetDefaultCurrency();	
		
		
		// for checkboxes
		/// if(isset($_POST['parameter4']))   $this->params['parameter4'] = $_POST['parameter4']; else $this->params['parameter4'] = "0";
		
		//$this->params['language_id'] 	  = MicroGrid::GetParameter('language_id');
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_HOTEL_DESCRIPTION;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=hotel_info";
		$this->actions      = array("add"=>false, "edit"=>true, "details"=>true, "delete"=>false);
		$this->actionIcons  = true;
		
		$this->allowLanguages = false;
		$this->languageId  	= ""; //($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = "WHERE ".$this->tableName.".hotel_id = '1'";
		$this->ORDER_CLAUSE = "ORDER BY ".$this->tableName.".id ASC";
		
		$this->isAlterColorsAllowed = true;
        
		$this->isPagingAllowed = false;
		$this->pageSize = 100;
        
		$this->isSortingAllowed = true;
        
		$this->isFilteringAllowed = false;
		// define filtering fields
		// $this->arrFilteringFields = array(
		//	"parameter1" => array("title"=>"",  "type"=>"text", "sign"=>"=|like%|%like|%like%", "width"=>"80px"),
		//	"parameter2"  => array("title"=>"",  "type"=>"text", "sign"=>"=|like%|%like|%like%", "width"=>"80px"),
		// );
		
		// prepare languages array
		//$total_languages = Languages::GetAllActive();
		//$arr_languages      = array();
		//foreach($total_languages[0] as $key => $val){
		//	$arr_languages[$val['abbreviation']] = $val['lang_name'];
		//}
		

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->tableName.".".$this->primaryKey.",
									".$this->tableName.".hotel_id,
									".$this->tableName.".language_id,
									".$this->tableName.".name,									
									".$this->tableName.".address,
									".$this->tableName.".description, 
									".TABLE_LANGUAGES.".lang_name  
								FROM ".$this->tableName."
									INNER JOIN ".TABLE_HOTEL." ON ".$this->tableName.".hotel_id = ".TABLE_HOTEL.".id
									INNER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".language_id = ".TABLE_LANGUAGES.".abbreviation
								";

		// define view mode fields
		$this->arrViewModeFields = array(
			"name"  	   	=> array("title"=>_NAME, "type"=>"label", "align"=>"left", "width"=>"120px", "maxlength"=>""),
			"address" 		=> array("title"=>_ADDRESS, "type"=>"label", "align"=>"left", "width"=>"", "maxlength"=>"60", "formate"=>"strip_tags"),
			"lang_name"     => array("title"=>_LANGUAGE, "type"=>"label", "align"=>"center", "width"=>"120px", "maxlength"=>""),

			// "parameter1"  => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"", "maxlength"=>""),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
		
			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			// "parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>true, "readonly"=>false, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			// "parameter4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>date("Y-m-d H:i:s")),
			// "parameter5"  => array("title"=>"", "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"210px", "source"=>$arr_languages),
			// "parameter6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
			// "parameter7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"uploaded/"),
			// "language_id"  => array("title"=>_LANGUAGE,     "type"=>"enum", "source"=>$arr_languages, "required"=>true),
			
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"parameter1"  => array("title"=>"", "type"=>"label"),
			//		"parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			//		"parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			//  )
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT ".$this->tableName.".".$this->primaryKey.",
									".$this->tableName.".hotel_id,
									".$this->tableName.".language_id,
									".$this->tableName.".name,									
									".$this->tableName.".address,
									".$this->tableName.".description, 
									".TABLE_LANGUAGES.".lang_name  
								FROM ".$this->tableName."
									INNER JOIN ".TABLE_HOTEL." ON ".$this->tableName.".hotel_id = ".TABLE_HOTEL.".id
									INNER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".language_id = ".TABLE_LANGUAGES.".abbreviation
								WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		

		// define edit mode fields
		$this->arrEditModeFields = array(

			"lang_name"     => array("title"=>_LANGUAGE, "type"=>"label"),
			"name" 	   		=> array("title"=>_NAME, "type"=>"textbox",  "width"=>"270px", "required"=>true, "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"text"),
			"address" 		=> array("title"=>_ADDRESS, "type"=>"textarea", "width"=>"480px", "height"=>"55px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"text"),
			"description" 	=> array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"460px", "height"=>"150px", "editor_type"=>"wysiwyg", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"text"),
			
			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			// "parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"310px", "height"=>"90px", "required"=>true, "readonly"=>false, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			// "parameter4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "default"=>date("Y-m-d H:i:s")),
			// "parameter5"  => array("title"=>"", "type"=>"enum",     "width"=>"210px", "required"=>true, "readonly"=>false, "source"=>$arr_languages),
			// "parameter6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
			// "parameter7"  => array("title"=>"", "type"=>"image",    "width"=>"210px", "required"=>true, "target"=>"uploaded/"),
		
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"parameter1"  => array("title"=>"", "type"=>"label"),
			//		"parameter2"  => array("title"=>"", "type"=>"textbox",  "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			//		"parameter3"  => array("title"=>"", "type"=>"textarea", "width"=>"210px", "required"=>true, "validation_type"=>"alpha|numeric|alpha_numeric|text|email"),
			//  )
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"lang_name"   => array("title"=>_LANGUAGE, "type"=>"label"),
			"name"  	  => array("title"=>_NAME, "type"=>"label"),
			"address"  	  => array("title"=>_ADDRESS, "type"=>"label"),
			"description" => array("title"=>_DESCRIPTION, "type"=>"label", "format"=>"strip_tags"),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"label"),
			// "parameter3"  => array("title"=>"", "type"=>"label"),
			// "parameter4"  => array("title"=>"", "type"=>"image", "target"=>"uploaded/"),
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