<?php

/**
 *	Class Extras
 *  --------------
 *  Usage       : HotelSite
 *	Written by  : ApPHP
 *  Updated	    : 08.05.2011
 *
 *	PUBLIC				  	STATIC				 	PRIVATE
 * 	------------------	  	---------------     	---------------
 *	__construct             GetAllExtras
 *	__destruct              GetExtrasInfo
 *		                    GetExtrasList
 *	
 **/


class Extras extends MicroGrid {
	
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
		if(isset($_POST['name']))   		$this->params['name'] = prepare_input($_POST['name']);
		if(isset($_POST['description']))   	$this->params['description'] = prepare_input($_POST['description']);
		if(isset($_POST['price']))   		$this->params['price'] = prepare_input($_POST['price']);
		if(isset($_POST['priority_order'])) $this->params['priority_order'] = prepare_input($_POST['priority_order']);
		
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
		$this->tableName 	= TABLE_EXTRAS; // TABLE_NAME
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_booking_extras";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->languageId  	= "";//($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
		$this->WHERE_CLAUSE = ""; // WHERE .... / "WHERE language_id = '".$this->languageId."'";				
		$this->ORDER_CLAUSE = "ORDER BY ".$this->tableName.".priority_order ASC";
		
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

		$default_currency = Currencies::GetDefaultCurrency();

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
									description,
									CONCAT('".$default_currency."', price) as mod_price,
									priority_order
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(

			"name"  	     => array("title"=>_SERVICE, "type"=>"label", "align"=>"left", "width"=>"200px", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			"description"    => array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"50", "format"=>"", "format_parameter"=>""),
			"mod_price"      => array("title"=>_PRICE, "type"=>"label", "align"=>"right", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			"priority_order" => array("title"=>_ORDER, "type"=>"label", "align"=>"center", "width"=>"110px", "sortable"=>true, "nowrap"=>"", "visible"=>"", "tooltip"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>"", "movable"=>true),

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

			"name"  	     => array("title"=>_SERVICE, "type"=>"textbox",  "required"=>true, "width"=>"310px", "readonly"=>false, "maxlength"=>"255", "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true),
			"description"    => array("title"=>_DESCRIPTION, "type"=>"textarea", "required"=>true, "width"=>"390px", "height"=>"90px", "editor_type"=>"simple", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			"price"  	     => array("title"=>_PRICE, "type"=>"textbox",  "required"=>true, "width"=>"90px", "readonly"=>false, "maxlength"=>"10", "default"=>"0", "validation_type"=>"float|positive", "unique"=>false, "visible"=>true, "pre_html"=>$default_currency." "),
			"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"35px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric|positive"),
			
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
								".$this->tableName.".name,
								".$this->tableName.".description,
								".$this->tableName.".price,
								".$this->tableName.".priority_order
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			"name"  	     => array("title"=>_SERVICE, "type"=>"textbox",  "required"=>true, "width"=>"310px", "readonly"=>false, "maxlength"=>"255", "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true),
			"description"    => array("title"=>_DESCRIPTION, "type"=>"textarea", "required"=>true, "width"=>"390px", "height"=>"90px", "editor_type"=>"simple", "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false),
			"price"  	     => array("title"=>_PRICE, "type"=>"textbox",  "required"=>true, "width"=>"90px", "readonly"=>false, "maxlength"=>"10", "default"=>"0", "validation_type"=>"float|positive", "unique"=>false, "visible"=>true, "pre_html"=>$default_currency." "),
			"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"35px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric|positive"),

		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"name"  	     => array("title"=>_SERVICE, "type"=>"label"),
			"description"    => array("title"=>_DESCRIPTION, "type"=>"label"),
			"price"  	     => array("title"=>_PRICE, "type"=>"label", "pre_html"=>$default_currency),
			"priority_order" => array("title"=>_ORDER, "type"=>"label"),

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
	 * Returns all extras 
	*/
	static public function GetAllExtras()
	{
		$sql = "SELECT
					id,
					name,
					description,
					price,
					priority_order
				FROM ".TABLE_EXTRAS;		
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		return $result;
	}

	/**
	 *	Get extras price
	 *	  	@param $id
	 *	  	@param $param
	 */
	static public function GetExtrasInfo($id, $param = "")
	{
		$sql = "SELECT
					id,
					name,
					description,
					price,
					priority_order
				FROM ".TABLE_EXTRAS."
				WHERE id = ".(int)$id;		
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			if($param != ""){
				return isset($result[0][$param]) ? $result[0][$param] : "";
			}else{
				return $result[0];
			}
		}
		return "";
	}

	/**
	 * Returns list of extras for booking
	 */
	static public function GetExtrasList($arr_extras = array(), $currency = "", $type = "")
	{
		$info_extras = self::GetAllExtras();
		$currency_info = Currencies::GetCurrencyInfo($currency);
		$currency_rate = isset($currency_info['rate']) ? $currency_info['rate'] : "1";
		$output = "";
		
		if(is_array($arr_extras) && count($arr_extras) > 0){
			if($type == "email"){
				$output .= "<b>"._EXTRAS.":</b>";
				$output .= "<br />-----------------------------<br />";
				$output .= "<table style='border:1px' cellspacing='2'>";			
			}else{
				$output .= "<h4>"._EXTRAS."</h4>";		
				$output .= "<table width='100%' border='0' cellspacing='0' cellpadding='3' style='border:1px solid #d1d2d3'>";
				$output .= "<tr style='background-color:#e1e2e3;font-weight:bold;font-size:13px;'>";
			}
			$output .= "<th align='center'> # </th>";
			$output .= "<th align='left'>"._NAME."</th>";
			$output .= "<th align='right' ".(($type == "email") ? "" : "width='12%'").">"._UNIT_PRICE."</th>";
			$output .= "<th align='center' ".(($type == "email") ? "" : "width='13%'").">"._UNITS."</th>";
			$output .= "<th align='right' ".(($type == "email") ? "" : "width='7%'").">"._PRICE."</th>";
			$output .= "<th width='5px' nowrap>&nbsp;</th>";
			$output .= "</tr>";
			$extras_count = 0;
			$extras_total = 0;
			foreach($arr_extras as $key => $val){
				for($i=0; $i<$info_extras[1]; $i++){
					if($info_extras[0][$i]['id'] == $key){							
						$output .= "<tr>";
						$output .= "<td align='center' width='40px'>".(++$extras_count).".</td>";
						$output .= "<td>".$info_extras[0][$i]['name']." </td>";
						$output .= "<td align='right'>".Currencies::PriceFormat($info_extras[0][$i]['price'] / $currency_rate)."</td>";
						$output .= "<td align='center'>".$val."</td>";
						$output .= "<td align='right'>".Currencies::PriceFormat(($info_extras[0][$i]['price'] / $currency_rate) * $val)."&nbsp;</td>";
						$output .= "<td></td>";
						$output .= "</tr>";
						$extras_total += ($info_extras[0][$i]['price'] / $currency_rate) * $val;
						break;
					}						
				}
			}
			if($type == "" && $extras_total > 0){
				$output .= "<tr>";
				$output .= "<td colspan='3'></td>";
				$output .= "<td colspan='2' align='right'><span style='background-color:#e1e2e3;'>&nbsp;<b>"._TOTAL.": &nbsp;&nbsp;&nbsp;".Currencies::PriceFormat($extras_total)."</b>&nbsp;</span></td>";
				$output .= "<td></td>";
				$output .= "</tr>";		
			}
			$output .= "</table><br>";
		}
	
		return $output;
	}

}
?>