<?php

/**
 *	Code Template for Campaigns Class
 *  -------------- 
 *	Written by  : ApPHP
 *  Updated	    : 10.05.2011
 *
 *	PUBLIC				  	STATIC				 	PRIVATE
 * 	------------------	  	---------------     	---------------
 *	__construct             DrawCampaignBanner      CheckStartFinishDate
 *	__destruct              GetCampaignInfo
 *	BeforeInsertRecord      UpdateStatus
 *	BeforeUpdateRecord
 *	
 **/


class Campaigns extends MicroGrid {
	
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
		if(isset($_POST['campaign_name']))   $this->params['campaign_name'] = prepare_input($_POST['campaign_name']);
		if(isset($_POST['start_date']))   $this->params['start_date'] = prepare_input($_POST['start_date']);
		if(isset($_POST['finish_date']))  $this->params['finish_date'] = prepare_input($_POST['finish_date']);
		if(isset($_POST['discount_percent']))  $this->params['discount_percent'] = prepare_input($_POST['discount_percent']);
		
		## for checkboxes 
		$this->params['is_active'] = isset($_POST['is_active']) ? (int)$_POST['is_active'] : "0";

		## for images
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
		$this->tableName 	= TABLE_CAMPAIGNS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_booking_campaigns";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowRefresh = true;
		$this->allowLanguages = false;
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
		$date_format = get_date_format(false);
		
		$arr_active = array("0"=>_NO, "1"=>_YES);
		$arr_discount = array();
		for($i=0; $i<100; $i+=5){
			$arr_discount[$i] = $i;
		}
		

		//---------------------------------------------------------------------- 
		// VIEW MODE
		// format: strip_tags
		// format: nl2br
		// format: "format"=>"date", "format_parameter"=>"M d, Y, g:i A" + IF(date_created = '0000-00-00 00:00:00', '', date_created) as date_created,
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT ".$this->primaryKey.",
									campaign_name,
									start_date,
									finish_date,
									discount_percent,
									is_active,
									IF(is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as m_is_active
								FROM ".$this->tableName."";		
		// define view mode fields
		$this->arrViewModeFields = array(

			"campaign_name"  	=> array("title"=>_NAME, "type"=>"label", "align"=>"left", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
			"start_date"  		=> array("title"=>_START_DATE, "type"=>"label", "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "maxlength"=>"", "format"=>"date", "format_parameter"=>$date_format),
			"finish_date"  		=> array("title"=>_FINISH_DATE, "type"=>"label", "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "maxlength"=>"", "format"=>"date", "format_parameter"=>$date_format),
			"discount_percent"  => array("title"=>_DISCOUNT, "type"=>"label", "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>"", "post_html"=>"%"),
			"m_is_active"       => array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"", "sortable"=>true, "nowrap"=>"", "visible"=>"", "maxlength"=>"", "format"=>"", "format_parameter"=>""),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address|password
		// 	 Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255 ....
		//   Ex.: "validation_maxlength"=>"255"
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(		    

			"campaign_name"  	=> array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"50", "default"=>"Campaign #_ ".@date("M Y"), "validation_type"=>"text", "unique"=>true, "visible"=>true),
			"start_date"  		=> array("title"=>_START_DATE, "type"=>"date", "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>"M d, Y, g:i A"),
			"finish_date"  		=> array("title"=>_FINISH_DATE, "type"=>"date", "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>"M d, Y, g:i A"),
			"discount_percent"  => array("title"=>_DISCOUNT, "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"65px", "source"=>$arr_discount, "unique"=>false, "javascript_event"=>"", "validation_minimum"=>"1", "post_html"=>" %"),
			"is_active"         => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		// - Validation Type: alpha|numeric|float|alpha_numeric|text|email|ip_address|password
		//   Validation Sub-Type: positive (for numeric and float)
		//   Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		// - Validation Max Length: 12, 255 ....
		//   Ex.: "validation_maxlength"=>"255"
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".campaign_name,
								".$this->tableName.".start_date,
								".$this->tableName.".finish_date,
								".$this->tableName.".discount_percent,
								".$this->tableName.".is_active,
								IF(".$this->tableName.".is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as m_is_active
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		
			"campaign_name"  	=> array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "maxlength"=>"50", "default"=>"Campaign #_ ".@date("M Y"), "validation_type"=>"text", "unique"=>true, "visible"=>true),
			"start_date"  		=> array("title"=>_START_DATE, "type"=>"date", "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>$date_format),
			"finish_date"  		=> array("title"=>_FINISH_DATE, "type"=>"date", "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"", "unique"=>false, "visible"=>true, "format"=>"date", "format_parameter"=>$date_format),
			"discount_percent"  => array("title"=>_DISCOUNT, "type"=>"enum",     "required"=>true, "readonly"=>false, "width"=>"65px", "source"=>$arr_discount, "unique"=>false, "javascript_event"=>"", "validation_minimum"=>"1", "post_html"=>" %"),
			"is_active"         => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "default"=>"0", "true_value"=>"1", "false_value"=>"0", "unique"=>false),
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(

			"campaign_name"  	=> array("title"=>_NAME, "type"=>"label"),
			"start_date"  		=> array("title"=>_START_DATE, "type"=>"datetime", "format"=>"date", "format_parameter"=>$date_format),
			"finish_date"  		=> array("title"=>_FINISH_DATE, "type"=>"datetime", "format"=>"date", "format_parameter"=>$date_format),
			"discount_percent"  => array("title"=>_DISCOUNT, "type"=>"label", "post_html"=>" %"),
			"m_is_active"       => array("title"=>_ACTIVE, "type"=>"label"),
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
	 * Draws campaign banner
	 */
	static public function GetCampaignInfo()
	{
		$output = array("id"=>"", "discount_percent"=>"");
		$sql = "SELECT
					id,
					discount_percent,
					DATE_FORMAT(start_date, '%M %d') as start_date,
					DATE_FORMAT(finish_date, '%M %d, %Y') as finish_date,
					DATE_FORMAT(finish_date, '%m/%d/%Y') as formated_finish_date,
					DATE_FORMAT(finish_date, '%Y') as fd_y,
					DATE_FORMAT(finish_date, '%m') as fd_m,
					DATE_FORMAT(finish_date, '%d') as fd_d
				FROM ".TABLE_CAMPAIGNS."
				WHERE
                    '".@date("Y-m-d")."' >= start_date AND
                    '".@date("Y-m-d")."' <= finish_date AND
                    is_active = 1
				ORDER BY start_date DESC";
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$output["id"] = $result[0]['id']; 
			$output["discount_percent"] = $result[0]['discount_percent']; 
		}
		return $output;		
	}
	
	/**
	 * Draws campaign banner
	 */
	static public function DrawCampaignBanner()
	{
		$output = "";
		
		$sql = "SELECT
					id,
					discount_percent,
					start_date,
					finish_date,
					DATE_FORMAT(start_date, '%M %d') as mod_start_date,
					DATE_FORMAT(finish_date, '%M %d, %Y') as mod_finish_date,
					DATE_FORMAT(finish_date, '%m/%d/%Y') as formated_finish_date,
					DATE_FORMAT(finish_date, '%Y') as fd_y,
					DATE_FORMAT(finish_date, '%m') as fd_m,
					DATE_FORMAT(finish_date, '%d') as fd_d
				FROM ".TABLE_CAMPAIGNS."
				WHERE
                    '".@date("Y-m-d")."' >= start_date AND
                    '".@date("Y-m-d")."' <= finish_date AND
                    is_active = 1
				ORDER BY start_date DESC";

		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$discount_campaign_text = _DISCOUNT_CAMPAIGN_TEXT;
			if($result[0]['start_date'] != $result[0]['finish_date']){
				$discount_campaign_text = str_replace("_FROM_", _FROM." <b>".$result[0]['mod_start_date']."</b>", $discount_campaign_text);
				$discount_campaign_text = str_replace("_TO_", _TO." <b>".$result[0]['mod_finish_date']."</b>", $discount_campaign_text);
				$discount_campaign_text = str_replace("_PERCENT_", number_format($result[0]['discount_percent'], 0)."%", $discount_campaign_text);
			}else{
				$discount_campaign_text = str_replace("_FROM_", "", $discount_campaign_text);
				$discount_campaign_text = str_replace("_TO_", "<b>".$result[0]['mod_finish_date']."</b> "._ONLY, $discount_campaign_text);
				$discount_campaign_text = str_replace("_PERCENT_", number_format($result[0]['discount_percent'], 0)."%", $discount_campaign_text);
			}
			$msg = "<table width='100%' border='0'>
				<tr>
					<td valign='top' align='".Application::Get("defined_left")."' style='font-size:13px;'>
						".$discount_campaign_text."
					</td>
					<td valign='middle' align='center'></td>
					<td valign='middle' align='".Application::Get("defined_right")."'>
						<img src='images/discount_tag.gif' alt='' />
						<br />
						<font style='color:#a13a3a; font-weight:bold;font-size:18px;'><b>".number_format($result[0]['discount_percent'], 0)."%</b></font>
					</td>
				</tr>
				</table>";
			$output .= draw_message($msg, false);			
		}
		echo $output;
	}

	/***
	 *	Before-add record
	 */
	public function BeforeInsertRecord()
	{
		return $this->CheckStartFinishDate();
	}

	/***
	 *	Before-update record
	 */
	public function BeforeUpdateRecord()
	{
		return $this->CheckStartFinishDate();
	}
	
	/**
	 * Check if start date is greater than finish date
	 */
	private function CheckStartFinishDate()
	{
		$start_date = MicroGrid::GetParameter('start_date', false);
		$finish_date = MicroGrid::GetParameter('finish_date', false);
		
		if($start_date > $finish_date){
			$this->error = _START_FINISH_DATE_ERROR;
			return false;
		}	
		return true;		
	}	

	/**
	 * Updates campaign status
	 */
	static public function UpdateStatus()
	{
		$sql = "UPDATE ".TABLE_CAMPAIGNS." SET is_active = 0 WHERE finish_date < '".@date("Y-m-d")."' AND is_active = 1";    
		database_void_query($sql);
	}

}
?>