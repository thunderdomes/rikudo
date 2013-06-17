<?php

/**
 *	Class Countries
 *  -------------- 
 *  Description : encapsulates countries properties
 *  Updated	    : 03.11.2010
 *	Written by  : ApPHP
 *
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct				GetAllCountries
 *  __destruct
 *  UpdateVAT
 *	
 **/


class Countries extends MicroGrid {
	
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

	private $id;
	protected $countries;

	
	//==========================================================================
    // Class Constructor
	//		@param $id
	//==========================================================================
	function __construct($id = "") {
		
		$this->SetRunningTime();

		$this->params = array();
		
		## for standard fields
		if(isset($_POST['name']))      $this->params['name'] = prepare_input($_POST['name']);
		if(isset($_POST['abbrv']))     $this->params['abbrv'] = prepare_input($_POST['abbrv']);
		if(isset($_POST['vat_value'])) $this->params['vat_value'] = prepare_input($_POST['vat_value']);
		if(isset($_POST['priority_order']))  $this->params['priority_order'] = (int)$_POST['priority_order'];
		

		$this->id = $id;
		if($this->id != ""){
			$sql = "SELECT
						id, abbrv, name, vat_value, priority_order 
					FROM ".TABLE_COUNTRIES."
					WHERE id = '".intval($this->id)."'";
			$this->countries = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
		}else{
			$this->countries['id'] = "";
			$this->countries['abbrv  '] = "";
			$this->countries['name'] = "";
			$this->countries['vat_value'] = "";
			$this->countries['priority_order'] = "";
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

		// $this->params['language_id'] 	  = MicroGrid::GetParameter('language_id');
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_COUNTRIES;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=countries_management";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		//$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = ""; // WHERE .... / "WHERE language_id = '".$this->languageId."'";				
		$this->ORDER_CLAUSE = "ORDER BY priority_order DESC, name ASC"; // ORDER BY ".$this->tableName.".date_created DESC
		
		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;

		$this->isFilteringAllowed = true;
		// define filtering fields
		$this->arrFilteringFields = array(
			_NAME  => array("table"=>$this->tableName, "field"=>"name", "type"=>"text", "sign"=>"like%", "width"=>"80px"),
			
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
									abbrv,
									name,
									CASE
										WHEN vat_value = '0' THEN CONCAT('<span class=gray>', vat_value, ' %</span>') 
										ELSE CONCAT(vat_value, '%')
									END as m_vat_value,
									priority_order
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(

			"name"  => array("title"=>_NAME, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
			"abbrv"  => array("title"=>_ABBREVIATION, "type"=>"label", "align"=>"center", "width"=>"100px", "height"=>"", "maxlength"=>""),
			"m_vat_value" => array("title"=>_VAT, "type"=>"label", "align"=>"center", "width"=>"100px", "height"=>"", "maxlength"=>""),
			"priority_order"  => array("title"=>_ORDER, "type"=>"label", "align"=>"center", "width"=>"100px", "height"=>"", "maxlength"=>""),

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

			"name"  	=> array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"70", "default"=>"", "validation_type"=>"text"),
			"abbrv"  	=> array("title"=>_ABBREVIATION, "type"=>"textbox",  "width"=>"30px", "required"=>true, "readonly"=>false, "unique"=>true, "maxlength"=>"2", "default"=>"", "validation_type"=>"alpha"),
			"vat_value" => array("title"=>_VAT, "type"=>"textbox",  "width"=>"50px", "required"=>false, "readonly"=>false, "unique"=>false, "maxlength"=>"5", "default"=>"0", "validation_type"=>"float|positive", "validation_maximum"=>"99", "post_html"=>" %"),
			"priority_order"  => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"40px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric"),

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
								".$this->tableName.".name,
								".$this->tableName.".abbrv,
								".$this->tableName.".vat_value,
								".$this->tableName.".priority_order
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			"name"  	=> array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"70", "default"=>"", "validation_type"=>"text"),
			"abbrv"  	=> array("title"=>_ABBREVIATION, "type"=>"textbox",  "width"=>"30px", "required"=>true, "readonly"=>false, "unique"=>true, "maxlength"=>"2", "default"=>"", "validation_type"=>"alpha"),
			"vat_value" => array("title"=>_VAT, "type"=>"textbox",  "width"=>"50px", "required"=>false, "readonly"=>false, "unique"=>false, "maxlength"=>"5", "default"=>"0", "validation_type"=>"float|positive", "validation_maximum"=>"99", "post_html"=>" %"),
			"priority_order"  => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"40px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric"),

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

			"name"  	=> array("title"=>_NAME, "type"=>"label"),
			"abbrv"  	=> array("title"=>_ABBREVIATION, "type"=>"label"),
			"vat_value" => array("title"=>_VAT, "type"=>"label", "post_html"=>"%"),
			"priority_order"  => array("title"=>_ORDER, "type"=>"label"),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"label"),
			// "parameter3"  => array("title"=>"", "type"=>"label"),
			// "parameter4"  => array("title"=>"", "type"=>"image", "target"=>"uploaded/", "no_image"=>""),
		);
	}

	//==========================================================================
    // Static Methods
	//==========================================================================	
	/***
	 *	Returns array of all countries
	 *		@param $order - order clause
	 *		@param $join_table - join tables
	 **/
	public static function GetAllCountries($order = " priority_order DESC", $join_table = "")
	{
		$where_clause = "";
		
		// Build ORDER BY CLAUSE
		if($order=="")$order_clause = "";
		else $order_clause = "ORDER BY $order";		

		// Build JOIN clause
		$join_clause = "";
		$join_select_fields = "";
		
		$sql = "SELECT
					id, abbrv, name, priority_order 
				FROM ".TABLE_COUNTRIES."
					$join_clause
				$order_clause";			
		
		return database_query($sql, DATA_AND_ROWS);
	}
	
	/**
	 *	Updates VAT value for all countries 
	 *		@param $value
	 */
	public function UpdateVAT($value = "0")
	{
		$sql = "UPDATE ".TABLE_COUNTRIES." SET vat_value = ".number_format($value, 2, ".", "");
		if(database_void_query($sql)){
			return true;
		}else{
			$this->error = _TRY_LATER;
			return false;
		}				
	}
	
}
?>
