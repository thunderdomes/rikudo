<?php

/**
 * 	Class Banners
 *  -------------- 
 *  Description : encapsulates banners properties
 *	Usage       : MicroCMS, HotelSite, ShoppingCart, BusinessDirectory 
 *  Updated	    : 29.05.2011
 *	Written by  : ApPHP
 *	Version     : 1.1.5
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct             GetBanners              
 *	__destruct              GetRandomBanner
 *	AfterInsertRecord       GetBannersArray
 *	AfterUpdateRecord
 *	AfterDeleteRecord
 *
 *
 *	ChangeLog:
 *  1.1.5
 *  	- added default value for priority_order in Add Mode
 *  	- removed unexpected "default"=>val["value"] from add mode fields
 *  	- 
 *  	-
 *  	-
 *  1.1.4
 *  	- added encapsulation of working with table description (for multi-languages)
 *  	- re-done SELECTs in View and Edit Modes
 *  	- added maxlength for edit mode and reduced width
 *  	- added tramesets for add/edit/detail mode
 *  	- added automatical addition of http:// for "link_url" fields
 *  1.1.3
 *  	- added thumbnails
 *  	- added "(http://)" reminder for link_url
 *  	- added new method
 *  	- added "no_image" to View/Details modes
 *  	- added "movable"=>true
 *	
 *	
 **/


class Banners extends MicroGrid {
	
	protected $debug = false;
	
	protected $tableName;
	protected $primaryKey;
	protected $dataSet;

	protected $formActionURL;
	protected $params;
	protected $actions;
	protected $actionIcons;
	protected $errorField;

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
	private $arrLanguages = "";
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{
		global $objLogin;
		
		$this->SetRunningTime();

		$this->params = array();		
		if(isset($_POST['link_url'])){
			$link_url = prepare_input($_POST['link_url'], false, "medium");
			if(preg_match("/www./i", $link_url) && !preg_match("/http:/i", $link_url)){
				$link_url = "http://".$link_url;
			}
			$this->params['link_url'] = $link_url;
		}
		if(isset($_POST['priority_order'])) $this->params['priority_order'] = prepare_input($_POST['priority_order']);
		if(isset($_POST['is_active']))  $this->params['is_active'] = (int)$_POST['is_active']; else $this->params['is_active'] = "0";
		///$this->params['language_id'] 	= MicroGrid::GetParameter('language_id');

		## for images
		if(isset($_POST['image_file'])){
			$this->params['image_file'] = prepare_input($_POST['image_file']);
		}else if(isset($_FILES['image_file']['name']) && $_FILES['image_file']['name'] != ""){
			// nothing 			
		}else if (self::GetParameter('action') == "create"){
			$this->params['image_file'] = "";
		}
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_BANNERS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_banners_management";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->languageId  	=  @$objLogin->GetPreferredLang();
		$this->WHERE_CLAUSE = ""; //"WHERE ";		
		$this->ORDER_CLAUSE = "ORDER BY priority_order ASC"; // ORDER BY date_created DESC

		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;

		$this->isFilteringAllowed = false;
		// define filtering fields
		$this->arrFilteringFields = array(
			//"parameter1" => array("title"=>"",  "type"=>"text", "sign"=>"=|like%|%like|%like%", "width"=>"80px"),
			//"parameter2"  => array("title"=>"",  "type"=>"text", "sign"=>"=|like%|%like|%like%", "width"=>"80px"),
		);

		#// prepare languages array		
		$total_languages    = Languages::GetAllActive();
		$this->arrLanguages = array();
		foreach($total_languages[0] as $key => $val){
			$this->arrLanguages[$val['abbreviation']] = array("name"=>$val['lang_name'], "value"=>self::GetParameter('image_text_'.$val['abbreviation'], false));
		}
		$sql = "SELECT id, banner_id, language_id, image_text
				FROM ".TABLE_BANNERS_DESCRIPTION."
				WHERE banner_id = ".(int)self::GetParameter("rid");
		$banners_description = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		$sql_banners_description = "";
		for($i=0; $i<$banners_description[1]; $i++){
			$sql_banners_description .= "'".mysql_real_escape_string($banners_description[0][$i]['image_text'])."' as image_text_".$banners_description[0][$i]['language_id'].",";
		}
		
		
		//---------------------------------------------------------------------- 
		// VIEW MODE
		//----------------------------------------------------------------------
		$this->VIEW_MODE_SQL = "SELECT b.".$this->primaryKey.",
									b.image_file,
									b.image_file_thumb,
									bd.image_text as image_text, 
									b.priority_order,
									IF(b.is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as my_is_active
								FROM (".$this->tableName." b
									LEFT OUTER JOIN ".TABLE_BANNERS_DESCRIPTION." bd ON b.id = bd.banner_id AND bd.language_id = '".$this->languageId."')";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"image_file_thumb" => array("title"=>_BANNER_IMAGE, "type"=>"image", "sortable"=>false, "align"=>"left", "width"=>"150px", "image_width"=>"120px", "image_height"=>"30px", "target"=>"images/banners/", "no_image"=>"no_image.png"),
			"image_text"   	 => array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>""),
			"priority_order" => array("title"=>_ORDER, "type"=>"label", "align"=>"center", "width"=>"60px", "movable"=>true),
			"my_is_active"   => array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"130px"),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
			"separator_general"   =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"image_file"   => array("title"=>_BANNER_IMAGE, "type"=>"image", "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"images/banners/", "no_image"=>"", "random_name"=>"true", "unique"=>false, "thumbnail_create"=>true, "thumbnail_field"=>"image_file_thumb", "thumbnail_width"=>"140px", "thumbnail_height"=>"30px"),
				"link_url"     => array("title"=>_URL." (http://)", "type"=>"textbox",  "width"=>"270px", "required"=>false, "readonly"=>false, "validation_type"=>"text"),
				"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"50px", "required"=>true, "default"=>"0", "readonly"=>false, "validation_type"=>"numeric", "maxlength"=>"2"),
				"is_active"    => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
			)
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		// define edit mode fields
		$this->EDIT_MODE_SQL = "SELECT
								".$this->primaryKey.",
								image_file,
								image_file_thumb,
								".$sql_banners_description."
								link_url,
								priority_order,
								is_active,
								CONCAT('<img src=\"images/banners/', image_file_thumb, '\" alt=\"\" width=\"140px\ height=\"30px\"/>') as my_image_file,
								IF(is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as my_is_active
							FROM ".$this->tableName."
							WHERE ".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
			"separator_general"   =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"image_file"   => array("title"=>_BANNER_IMAGE, "type"=>"image", "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"images/banners/", "no_image"=>"", "image_width"=>"280px", "image_height"=>"90px", "random_name"=>"true", "unique"=>false, "thumbnail_create"=>true, "thumbnail_field"=>"image_file_thumb", "thumbnail_width"=>"140px", "thumbnail_height"=>"30px"),
				"link_url"     => array("title"=>_URL." (http://)", "type"=>"textbox",  "width"=>"270px", "required"=>false, "readonly"=>false, "validation_type"=>"text"),
				"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"50px", "required"=>true, "readonly"=>false, "validation_type"=>"numeric", "maxlength"=>"2"),
				"is_active"    => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "true_value"=>"1", "false_value"=>"0"),
			)
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(
			"separator_general"   =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"image_file" => array("title"=>_BANNER_IMAGE, "type"=>"image", "target"=>"images/banners/", "image_width"=>"280px", "image_height"=>"90px", "no_image"=>"no_image.png"),
				"link_url"       => array("title"=>_URL, "type"=>"label"),
				"priority_order" => array("title"=>_ORDER, "type"=>"label"),
				"my_is_active"   => array("title"=>_ACTIVE, "type"=>"label"),
			)
		);

		foreach($this->arrLanguages as $key => $val){
		    $this->arrAddModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['name']),
				"image_text_".$key => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"410px", "height"=>"90px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"", "validation_maxlength"=>"255"),
			);
		    $this->arrEditModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['name']),
				"image_text_".$key => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"410px", "height"=>"90px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"", "validation_maxlength"=>"255"),
			);
		    $this->arrDetailsModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['name']),
				"image_text_".$key => array("title"=>_DESCRIPTION, "type"=>"label"),
			);
		}
	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/**
	 * Returns random banner image
	 */
	static public function GetBanners()
	{
		$lang = Application::Get("lang");
		$output = "";
		
		$sql = "SELECT
					b.id, b.image_file, b.link_url, b.priority_order,
					bd.image_text
				FROM ".TABLE_BANNERS." b
					LEFT OUTER JOIN ".TABLE_BANNERS_DESCRIPTION." bd ON b.id = bd.banner_id
				WHERE b.is_active = 1 AND b.image_file != '' AND bd.language_id = '".encode_text($lang)."' 
				ORDER BY RAND() ASC";
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$image = "<img src='images/banners/".$result[0]['image_file']."' width='723px' height='140px' alt='' />";	
			if($result[0]['link_url'] != "" && $result[0]['link_url'] != "http://"){
				$output .= "<a href='".$result[0]['link_url']."' title='".$result[0]['image_text']."'>".$image."</a>";
			}else{
				$output .= $image;
			}			
		}
	    return $output;
	}

	/**
	 * Returns random banner
	 */
	static public function GetRandomBanner()
	{
		$lang = Application::Get("lang");
		$output = "";
		
		$sql = "SELECT 
					b.id, b.image_file, b.link_url, b.priority_order,
					bd.image_text
				FROM ".TABLE_BANNERS." b
					LEFT OUTER JOIN ".TABLE_BANNERS_DESCRIPTION." bd ON b.id = bd.banner_id
				WHERE b.is_active = 1 AND b.image_file != '' AND bd.language_id = '".encode_text($lang)."' 
				ORDER BY RAND() ASC";
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$image = "<img src='images/banners/".$result[0]['image_file']."' title='".$result[0]['image_text']."' width='100%' height='140px' alt='' />";
			if($result[0]['link_url'] != "" && $result[0]['link_url'] != "http://"){
				$output .= "<a href='".$result[0]['link_url']."' title='".$result[0]['image_text']."'>".$image."</a>";
			}else{
				$output .= $image;
			}			
		}
	    return $output;
	}

	/**
	 * Returns banners array
	 */
	static public function GetBannersArray()
	{
		$lang = Application::Get("lang");
		$output = array();
		
		$sql = "SELECT 
					b.id, b.image_file, b.link_url, b.priority_order,
					bd.image_text
				FROM ".TABLE_BANNERS." b
					LEFT OUTER JOIN ".TABLE_BANNERS_DESCRIPTION." bd ON b.id = bd.banner_id
				WHERE b.is_active = 1 AND b.image_file != '' AND bd.language_id = '".encode_text($lang)."' 
				ORDER BY priority_order ASC";
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		if($result[1] > 0){
			$output = $result[0];
		}
	    return $output;
	}
	
	////////////////////////////////////////////////////////////////////
	// POST METHODS
	///////////////////////////////////////////////////////////////////
	
	/**
	 * After-Addition - add banner descriptions to description table
	 */
	public function AfterInsertRecord()
	{
		$sql = "INSERT INTO ".TABLE_BANNERS_DESCRIPTION."(id, banner_id, language_id, image_text) VALUES ";
		$count = 0;
		foreach($this->arrLanguages as $key => $val){
			if($count > 0) $sql .= ",";
			$sql .= "(NULL, ".(int)$this->lastInsertId.", '".encode_text($key)."', '".encode_text($val['value'])."')";
			$count++;
		}
		if(database_void_query($sql)){
			return true;
		}else{
			return false;
		}
	}	

	/**
	 * After-Updating - update banner descriptions to description table
	 */
	public function AfterUpdateRecord()
	{
		foreach($this->arrLanguages as $key => $val){
			$sql = "UPDATE ".TABLE_BANNERS_DESCRIPTION."
					SET image_text = '".encode_text($val['value'])."'
					WHERE banner_id = ".$this->curRecordId." AND language_id = '".encode_text($key)."'";
			if(database_void_query($sql)){
				//
			}else{
				//echo mysql_error();
			}
		}
	}	

	/**
	 * After-Deleting - delete banner descriptions from description table
	 */
	public function AfterDeleteRecord()
	{
		$sql = "DELETE FROM ".TABLE_BANNERS_DESCRIPTION." WHERE banner_id = ".(int)$this->curRecordId;
		if(database_void_query($sql)){
			return true;
		}else{
			return false;
		}
	}
	
}
?>