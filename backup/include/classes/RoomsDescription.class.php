<?php

/**
 *	RoomsDescription
 *  -------------- 
 *	Written by  : ApPHP
 *  Updated	    : 24.11.2010
 *	Written by  : ApPHP
 *
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct										
 *  __destruct
 *	
 **/


class RoomsDescription extends MicroGrid {
	
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
		if(isset($_POST['room_type'])) $this->params['room_type'] = prepare_input($_POST['room_type']);
		if(isset($_POST['room_short_description'])) $this->params['room_short_description'] = prepare_input($_POST['room_short_description']);
		if(isset($_POST['room_long_description'])) $this->params['room_long_description'] = prepare_input($_POST['room_long_description']);

		//$default_lang = Languages::GetDefaultLang();
		//$default_currency = Currencies::GetDefaultCurrency();	
		
		// for checkboxes
		/// if(isset($_POST['parameter4']))   $this->params['parameter4'] = $_POST['parameter4']; else $this->params['parameter4'] = "0";
		
		//$this->params['language_id'] 	  = MicroGrid::GetParameter('language_id');
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_ROOMS_DESCRIPTION;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=room_description&room_id=".$room_id;
		$this->actions      = array("add"=>false, "edit"=>true, "details"=>true, "delete"=>false);
		$this->actionIcons  = true;
		
		$this->allowLanguages = false;
		$this->languageId  	= ""; //($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = "WHERE ".$this->tableName.".room_id  = '".$room_id."'";
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
									".$this->tableName.".room_id,
									".$this->tableName.".language_id,
									".$this->tableName.".room_type,									
									".$this->tableName.".room_short_description,
									".$this->tableName.".room_long_description, 
									".TABLE_LANGUAGES.".lang_name  
								FROM ".$this->tableName."
									INNER JOIN ".TABLE_ROOMS." ON ".$this->tableName.".room_id = ".TABLE_ROOMS.".id
									INNER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".language_id = ".TABLE_LANGUAGES.".abbreviation
								";

		// define view mode fields
		$this->arrViewModeFields = array(
			"room_type"  	   => array("title"=>_NAME, "type"=>"label", "align"=>"left", "width"=>"120px", "maxlength"=>""),
			"room_short_description" => array("title"=>_SHORT_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"", "maxlength"=>"60", "format"=>"strip_tags"),
			//"room_long_description" => array("title"=>_LONG_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"", "maxlength"=>"65", "format"=>"strip_tags"),
			"lang_name"    	   => array("title"=>_LANGUAGE, "type"=>"label", "align"=>"center", "width"=>"120px", "maxlength"=>""),

			// "parameter1"  => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"", "maxlength"=>""),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
		
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT ".$this->tableName.".".$this->primaryKey.",
									".$this->tableName.".room_id,
									".$this->tableName.".language_id,
									".$this->tableName.".room_type,
									".$this->tableName.".room_short_description,
									".$this->tableName.".room_long_description,
									".TABLE_LANGUAGES.".lang_name  
								FROM ".$this->tableName."
									INNER JOIN ".TABLE_ROOMS." ON ".$this->tableName.".room_id = ".TABLE_ROOMS.".id
									INNER JOIN ".TABLE_LANGUAGES." ON ".$this->tableName.".language_id = ".TABLE_LANGUAGES.".abbreviation
								WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		

		// define edit mode fields
		$this->arrEditModeFields = array(

			"lang_name"              => array("title"=>_LANGUAGE, "type"=>"label"),
			"room_type" 	         => array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"text"),
			"room_short_description" => array("title"=>_SHORT_DESCRIPTION, "type"=>"textarea", "editor_type"=>"wysiwyg", "width"=>"470px", "height"=>"80px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"text", "validation_maxlength"=>"255"),
			"room_long_description"  => array("title"=>_LONG_DESCRIPTION, "type"=>"textarea", "editor_type"=>"wysiwyg", "width"=>"470px", "height"=>"240px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"text"),
			
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"lang_name"        		 => array("title"=>_LANGUAGE, "type"=>"label"),
			"room_type"  			 => array("title"=>_NAME, "type"=>"label"),
			"room_short_description" => array("title"=>_SHORT_DESCRIPTION, "type"=>"label"),
			"room_long_description"  => array("title"=>_LONG_DESCRIPTION, "type"=>"label"),
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