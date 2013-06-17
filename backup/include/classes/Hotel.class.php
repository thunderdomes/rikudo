<?php

/**
 *	Hotel Class 
 *  --------------
 *  Description : encapsulates Hotel class properties
 *  Updated	    : 16.05.2010
 *	Written by  : ApPHP
 *
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct				DrawLocalTime
 *  __destruct              GetHotelInfo
 *  DrawAboutUs             GetHotelFullInfo
 *	
 **/


class Hotel extends MicroGrid {
	
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
		if(isset($_POST['name']))    $this->params['name'] = prepare_input($_POST['name']);
		if(isset($_POST['address'])) $this->params['address'] = prepare_input($_POST['address']);
		if(isset($_POST['phone']))   $this->params['phone'] = prepare_input($_POST['phone']);
		if(isset($_POST['fax']))   	 $this->params['fax'] = prepare_input($_POST['fax']);
		if(isset($_POST['description'])) $this->params['description'] = prepare_input($_POST['description'], false, "medium");
		if(isset($_POST['map_code'])) $this->params['map_code'] = prepare_input($_POST['map_code'], false, "low");
		if(isset($_POST['time_zone']))   $this->params['time_zone'] = prepare_input($_POST['time_zone']);
		
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

		/// $this->params['language_id'] 	  = MicroGrid::GetParameter('language_id');
		
		$this->uPrefix 		= "htl_";
		
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_HOTEL;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=hotel_info";
		$this->actions      = array("add"=>false, "edit"=>true, "details"=>false, "delete"=>false);
		$this->actionIcons  = false;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		///$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
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

		$arr_time_zones = array();
		$arr_time_zones["-12"] = "[UTC - 12] Baker Island Time";
		$arr_time_zones["-11"] = "[UTC - 11] Niue Time, Samoa Standard Time";
		$arr_time_zones["-10"] = "[UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time";
		$arr_time_zones["-9.5"] = "[UTC - 9:30] Marquesas Islands Time";
		$arr_time_zones["-9"] = "[UTC - 9] Alaska Standard Time, Gambier Island Time";
		$arr_time_zones["-8"] = "[UTC - 8] Pacific Standard Time";
		$arr_time_zones["-7"] = "[UTC - 7] Mountain Standard Time";
		$arr_time_zones["-6"] = "[UTC - 6] Central Standard Time";
		$arr_time_zones["-5"] = "[UTC - 5] Eastern Standard Time";
		$arr_time_zones["-4.5"] = "[UTC - 4:30] Venezuelan Standard Time";
		$arr_time_zones["-4"] = "[UTC - 4] Atlantic Standard Time";
		$arr_time_zones["-3.5"] = "[UTC - 3:30] Newfoundland Standard Time";
		$arr_time_zones["-3"] = "[UTC - 3] Amazon Standard Time, Central Greenland Time";
		$arr_time_zones["-2"] = "[UTC - 2] Fernando de Noronha, S. Georgia &amp; the S. Sandwich Islands (Time)";
		$arr_time_zones["-1"] = "[UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time";
		$arr_time_zones["0"] = "[UTC] Western European Time, Greenwich Mean Time";
		$arr_time_zones["1"] = "[UTC + 1] Central European Time, West African Time";
		$arr_time_zones["2"] = "[UTC + 2] Eastern European Time, Central African Time";
		$arr_time_zones["3"] = "[UTC + 3] Moscow Standard Time, Eastern African Time";
		$arr_time_zones["3.5"] = "[UTC + 3:30] Iran Standard Time";
		$arr_time_zones["4"] = "[UTC + 4] Gulf Standard Time, Samara Standard Time";
		$arr_time_zones["4.5"] = "[UTC + 4:30] Afghanistan Time";
		$arr_time_zones["5"] = "[UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time";		
		$arr_time_zones["5.5"] = "[UTC + 5:30] Indian Standard Time, Sri Lanka Time";
		$arr_time_zones["5.75"] = "[UTC + 5:45] Nepal Time";
		$arr_time_zones["6"] = "[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time";
		$arr_time_zones["6.5"] = "[UTC + 6:30] Cocos Islands Time, Myanmar Time";
		$arr_time_zones["7"] = "[UTC + 7] Indochina Time, Krasnoyarsk Standard Time";
		$arr_time_zones["8"] = "[UTC + 8] Chinese, Australian Western, Irkutsk (Standard Time)";
		$arr_time_zones["8.75"] = "[UTC + 8:45] Southeastern Western Australia Standard Time";
		$arr_time_zones["9"] = "[UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time";
		$arr_time_zones["9.30"] = "[UTC + 9:30] Australian Central Standard Time";
		$arr_time_zones["10"] = "[UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time";
		$arr_time_zones["10.5"] = "[UTC + 10:30] Lord Howe Standard Time";
		$arr_time_zones["11"] = "[UTC + 11] Solomon Island Time, Magadan Standard Time";
		$arr_time_zones["11.5"] = "[UTC + 11:30] Norfolk Island Time";
		$arr_time_zones["12"] = "[UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time";
		$arr_time_zones["12.75"] = "[UTC + 12:45] Chatham Islands Time";
		$arr_time_zones["13"] = "[UTC + 13] Tonga Time, Phoenix Islands Time";
		$arr_time_zones["14"] = "[UTC + 14] Line Island Time";

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									parameter1,
									parameter2,
									parameter3
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(

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
								".$this->tableName.".phone,
								".$this->tableName.".fax,
								".$this->tableName.".time_zone,
								".$this->tableName.".map_code,
								".TABLE_HOTEL_DESCRIPTION.".name,
								".TABLE_HOTEL_DESCRIPTION.".address,
								".TABLE_HOTEL_DESCRIPTION.".description								
							FROM ".$this->tableName."
								INNER JOIN ".TABLE_HOTEL_DESCRIPTION." ON ".$this->tableName.".id = ".TABLE_HOTEL_DESCRIPTION.".hotel_id
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
							
							///".$this->tableName.".".$this->primaryKey.",							
		// define edit mode fields
		$this->arrEditModeFields = array(
			"phone"  		=> array("title"=>_PHONE, "type"=>"textbox",  "required"=>false, "width"=>"150px", "readonly"=>false, "maxlength"=>"50", "default"=>"", "validation_type"=>"text"),
			"fax"  		   	=> array("title"=>_FAX, "type"=>"textbox",  "required"=>false, "width"=>"150px", "readonly"=>false, "maxlength"=>"50", "default"=>"", "validation_type"=>"text"),
			"time_zone"     => array("title"=>_TIME_ZONE, "type"=>"enum",  "required"=>true, "width"=>"480px", "readonly"=>false, "source"=>$arr_time_zones),
			"map_code"      => array("title"=>_MAP_CODE, "type"=>"textarea", "required"=>false, "width"=>"480px", "height"=>"100px", "editor_type"=>"simple", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
		
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

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
	 * Draws About Us block
	 */
	public function DrawAboutUs()
	{		
		$lang = Application::Get("lang");		
		$output = "";
		
		$sql = "SELECT
					h.phone,
					h.fax,
					h.map_code,	
					hd.name,									
					hd.address,
					hd.description 
				FROM ".TABLE_HOTEL." h
					INNER JOIN ".TABLE_HOTEL_DESCRIPTION." hd ON h.id = hd.hotel_id
					INNER JOIN ".TABLE_LANGUAGES." l ON hd.language_id = l.abbreviation
				WHERE hd.language_id = '".$lang."'";
				
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){
			$output .= "<h3>".$result[0]['name']."</h3>";
			$output .= "<p>".$result[0]['description']."</p><br /><br />";		
			$output .= "<p>"._ADDRESS.": ".$result[0]['address']."</p>";
			$output .= "<p>"._PHONE.": ".$result[0]['phone']."<br />"._FAX.": ".$result[0]['fax']."</p>";
			if($result[0]['map_code']) $output .= "<p>"._OUR_LOCATION.":<br /> ".$result[0]['map_code']."</p>";
		}
		return $output;
	}

	/**
	 * Draws Local Time block
	 */
	static public function DrawLocalTime()
	{
		$lang = Application::Get("lang");		
		$output = "";
		
		if($lang != "en" && function_exists("iconv")){
			setlocale(LC_TIME, $lang, Application::Get("lang_name"));
			//$res1 = strftime("%d %B %Y", time() + $offset);
			//$res2 = strftime("%A %I:%M %p", time() + $offset);				
			$res1 = strftime("%d %B %Y", time());
			$res2 = strftime("%A %I:%M %p", time());				
			$output1 = (@iconv('ISO-8859-1', 'UTF-8', $res1));
			$output2 = (@iconv('ISO-8859-1', 'UTF-8', $res2));
		}else{
			$output1 = @date("dS \of F Y", time());
			$output2 = @date("l g:i A", time());
		}
		echo $output1."<br />".$output2;
	}

	/**
	 * Returns Hotel info
	 */
	static public function GetHotelInfo()
	{
		$output = array();
		$sql = "SELECT * FROM ".TABLE_HOTEL;		
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){
			$output = $result[0];
		}
		return $output;
	}

	/**
	 * Returns Hotel full info
	 */
	static public function GetHotelFullInfo()
	{
		$output = array();
		$sql = "SELECT
					h.*,
					hd.name,
					hd.address,
					hd.description
				FROM ".TABLE_HOTEL." as h
					INNER JOIN ".TABLE_HOTEL_DESCRIPTION." hd ON h.id = hd.hotel_id";
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){
			$output = $result[0];
		}
		return $output;
	}
	
}
?>