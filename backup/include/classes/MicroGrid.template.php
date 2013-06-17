<?php

/**
 *	Code Template for MicroGrid Class
 *  --------------
 *  Usage       : MicroCMS, MicroBlog, HotelSite, ShoppingCart, BusinessDirectory
 *	Written by  : ApPHP
 *  Updated	    : 10.05.2011
 *
 *	PUBLIC				  	STATIC				 	PRIVATE
 * 	------------------	  	---------------     	---------------
 *	__construct
 *	__destruct
 *	
 *	NOTES:
 *	------
 *	1.	for fiels and images "target"=>"" must be relative path to the site's directory
 *
 *	NON-DOCUMENTED:
 *	---------------
 *	"pre_html"
 *	"post_html"
 *	"validation_maxlength"			- for add/edit modes - check max length of string
 *	"validation_minlength"			- for add/edit modes - check min length of string
 *	"validation_maximum"			- for add/edit modes - check maximum value of argument
 *	"validation_minimum"			- for add/edit modes - check minimum value of argument
 *	"image_name_pefix"=>"" 			- for images
 *	"movable"=>true 				- for priority order in view mode
 *	"sort_type"=>"string|numeric" 	- for fields sorting in view mode
 *	"sort_by"=>"field_name" 		- for fields sorting in view mode
 *	
 *	TEMPLATES:
 *	-------
 *  1. Standard multi-language: 1 table :: 1 page + field lang
 *  2. Advanced multi-language: 2 tables :: 2 pages + link on main grid [description] to another page
 *  3. Professional multi-language: 2 table :: 2 pages + all lang fields on add/edit/detail modes
 *	
 *	TRICKS:
 *	-------
 *	to make image resize with out creating additional thumbnail - make "thumbnail_field"=>"image field"
 *	
 **/


class MyClass extends MicroGrid {
	
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
		if(isset($_POST['field1']))   $this->params['field1'] = prepare_input($_POST['field1']);
		if(isset($_POST['field2']))   $this->params['field2'] = prepare_input($_POST['field2']);
		if(isset($_POST['field3']))   $this->params['field3'] = prepare_input($_POST['field3']);
		
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

		$this->params['language_id'] = MicroGrid::GetParameter('language_id');
	
		//$this->uPrefix 		= "prefix_";
		
		$this->primaryKey 	= "id";
		$this->tableName 	= DB_PREFIX."table"; // TABLE_NAME
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=page_name";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = true;
		$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
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
									field1,
									field2,
									field3
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(

			// "field1"  => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			// "field2"  => array("title"=>"", "type"=>"image", "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "image_width"=>"50px", "image_height"=>"30px", "target"=>"uploaded/", "no_image"=>""),
			// "field3"  => array("title"=>"", "type"=>"enum",  "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "source"=>array()),
			// "field4"  => array("title"=>"", "type"=>"link",  "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>"", "href"=>"http://{field4}|mailto://{field4}", "target"=>""),

		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address|password|date
		// 	 Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255... Ex.: "validation_maxlength"=>"255"
		// - Validation Min Length: 4, 6... Ex.: "validation_minlength"=>"4"
		// - Validation Max Value: 12, 255... Ex.: "validation_maximum"=>"99.99"
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(		    

			// "field1"  => array("title"=>"", "type"=>"label"),
			// "field2"  => array("title"=>"", "type"=>"textbox",  "required"=>true, "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true),
			// "field3"  => array("title"=>"", "type"=>"textarea", "required"=>true, "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			// "field4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>date("Y-m-d H:i:s")),
			// "field5"  => array("title"=>"", "type"=>"enum",     "required"=>true, "width"=>"210px", "readonly"=>false, "default"=>"", "source"=>$arr_languages, "unique"=>false, "javascript_event"=>""),
			// "field6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			// "field7"  => array("title"=>"", "type"=>"image",    "required"=>true, "width"=>"210px", "readonly"=>false, "target"=>"uploaded/", "no_image"=>"", "random_name"=>true, "overwrite_image"=>false, "unique"=>false, "thumbnail_create"=>false, "thumbnail_field"=>"", "thumbnail_width"=>"", "thumbnail_height"=>""),
			// "field8"  => array("title"=>"", "type"=>"file",     "required"=>true, "width"=>"210px", "target"=>"uploaded/", "random_name"=>true, "overwrite_image"=>false, "unique"=>false),
			// "field9"  => array("title"=>"", "type"=>"date",     "required"=>true, "readonly"=>false, "unique"=>false, "visible"=>true, "default"=>"", "validation_type"=>"date", "format"=>"date", "format_parameter"=>"M d, Y, g:i A", "min_year"=>"90", "max_year"=>"10"),
			// "field10" => array("title"=>"", "type"=>"datetime", "required"=>true, "readonly"=>false, "unique"=>false, "visible"=>true, "default"=>"", "validation_type"=>"", "format"=>"date", "format_parameter"=>"M d, Y, g:i A", "min_year"=>"90", "max_year"=>"10"),
			// "field11" => array("title"=>"", "type"=>"password", "required"=>true, "width"=>"310px", "height"=>"90px", "validation_type"=>"password", "cryptography"=>PASSWORDS_ENCRYPTION, "cryptography_type"=>PASSWORDS_ENCRYPTION_TYPE, "aes_password"=>PASSWORDS_ENCRYPT_KEY),
			// "language_id"  => array("title"=>_LANGUAGE, "type"=>"enum", "source"=>$arr_languages, "required"=>true, "unique"=>false),
			
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"field1"  => array("title"=>"", "type"=>"label"),
			// 		"field2"  => array("title"=>"", "type"=>"textbox",  "required"=>true, "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true),
			// 		"field3"  => array("title"=>"", "type"=>"textarea", "required"=>true, "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			//  )
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address|password|date
		//   Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255... Ex.: "validation_maxlength"=>"255"
		// - Validation Min Length: 4, 6... Ex.: "validation_minlength"=>"4"
		// - Validation Max Value: 12, 255... Ex.: "validation_maximum"=>"99.99"
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".field1,
								".$this->tableName.".field2,
								".$this->tableName.".field3
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			// "field1"  => array("title"=>"", "type"=>"label"),
			// "field2"  => array("title"=>"", "type"=>"textbox",  "required"=>true, "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true),
			// "field3"  => array("title"=>"", "type"=>"textarea", "required"=>true, "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			// "field4"  => array("title"=>"", "type"=>"hidden",   "required"=>true, "default"=>date("Y-m-d H:i:s")),
			// "field5"  => array("title"=>"", "type"=>"enum",     "required"=>true, "width"=>"210px", "readonly"=>false, "default"=>"", "source"=>$arr_languages, "unique"=>false, "javascript_event"=>""),
			// "field6"  => array("title"=>"", "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
			// "field7"  => array("title"=>"", "type"=>"image",    "required"=>true, "width"=>"210px", "readonly"=>false, "target"=>"uploaded/", "no_image"=>"", "random_name"=>true, "overwrite_image"=>false, "unique"=>false, "image_width"=>"120px", "image_height"=>"90px", "thumbnail_create"=>false, "thumbnail_field"=>"", "thumbnail_width"=>"", "thumbnail_height"=>""),
			// "field8"  => array("title"=>"", "type"=>"file",     "required"=>true, "width"=>"210px", "readonly"=>false, "target"=>"uploaded/", "random_name"=>true, "overwrite_image"=>false, "unique"=>false),
			// "field9"  => array("title"=>"", "type"=>"date",     "required"=>true, "readonly"=>false, "unique"=>false, "visible"=>true, "default"=>"", "validation_type"=>"date", "format"=>"date", "format_parameter"=>"M d, Y, g:i A", "min_year"=>"90", "max_year"=>"10"),
			// "field10" => array("title"=>"", "type"=>"datetime", "required"=>true, "readonly"=>false, "unique"=>false, "visible"=>true, "default"=>"", "validation_type"=>"", "format"=>"date", "format_parameter"=>"M d, Y, g:i A", "min_year"=>"90", "max_year"=>"10"),
			// "field11" => array("title"=>"", "type"=>"password", "required"=>true, "width"=>"310px", "height"=>"90px", "validation_type"=>"password", "cryptography"=>PASSWORDS_ENCRYPTION, "cryptography_type"=>PASSWORDS_ENCRYPTION_TYPE, "aes_password"=>PASSWORDS_ENCRYPT_KEY),
		
			//  "separator_X"   =>array(
			//		"separator_info" => array("legend"=>"Legend Text"),
			//		"field1"  => array("title"=>"", "type"=>"label"),
			// 		"field2"  => array("title"=>"", "type"=>"textbox",  "required"=>true, "width"=>"210px", "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true),
			// 		"field3"  => array("title"=>"", "type"=>"textarea", "required"=>true, "width"=>"310px", "height"=>"90px", "editor_type"=>"simple|wysiwyg", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			//  )
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			// "field1"  => array("title"=>"", "type"=>"label"),
			// "field2"  => array("title"=>"", "type"=>"image", "target"=>"uploaded/", "no_image"=>"", "image_width"=>"120px", "image_height"=>"90px"),
			// "field3"  => array("title"=>"", "type"=>"date", "format"=>"date", "format_parameter"=>"M d, Y"),
			// "field3"  => array("title"=>"", "type"=>"datetime", "format"=>"date", "format_parameter"=>"M d, Y, g:i A"),
			// "field4"  => array("title"=>"", "type"=>"enum", "source"=>array()),
			// "field1"  => array("title"=>"", "type"=>"object", "width"=>"240px", "height"=>"200px"),
		);

	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }


	/***
	 *	Get all DataSet array
	 *
	 *  static public function GetAll() { }
	 *	
	 **/
	
	/***
	 *	Add record
	 *
	 *	public function AddRecord($params = array()) { 	}
	 *
	 *
	 *  public function BeforeInsertRecord(){ 
	 *     return true;
	 * 	}
	 * 	
	 *	public function AfterInsertRecord(){
	 *      // $this->lastInsertId - currently inserted record
	 *	}
	 *	
	 **/

	/***
	 *	Edit record
	 *
	 *  public function BeforeEditRecord(){ 
	 *		// $this->curRecordId - currently editing record
	 *		// $this->result - current record info
	 * 	}
	 * 	
	 **/

	/***
	 *	Update record
	 *
	 *	public function UpdateRecord($params = array()) { 	}
	 *
	 *  public function BeforeUpdateRecord(){
	 *     	// $this->curRecordId - current record
	 *     	return true;
	 * 	}
	 * 	
	 *	public function AfterUpdateRecord(){
	 *		// $this->curRecordId - currently updated record
	 *	}
	 *	
	 **/
	
	/***
	 *	Delete record
	 *
	 *	public function DeleteRecord($rid = "") { 	}
	 *
	 *  public function BeforeDeleteRecord(){
	 *     	// $this->curRecordId - current record
	 *     	return true;
	 * 	}
	 * 	
	 *	public function AfterDeleteRecord(){
	 *		// $this->curRecordId - currently deleted record
	 *	}
	 *	
	 **/

	/***
	 *	Details mode
	 *
	 *	public function AfterDetailsMode(){
	 *
	 *	}
	 *	
	 **/
	
}
?>