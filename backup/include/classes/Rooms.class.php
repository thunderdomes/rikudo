<?php

/**
 *	Rooms Class (for HotelSite ONLY)
 *  -------------- 
 *	Written by  : ApPHP
 *  Updated	    : 20.04.2011
 *	Written by  : ApPHP
 *
 *	PUBLIC:						STATIC:							PRIVATE:
 *  -----------					-----------						-----------
 *  __construct					GetRoomAvalibilityForWeek		GetRoomPrice
 *  __destruct                  GetRoomAvalibilityForMonth      GetRoomDefaultPrice 
 *  DrawRoomAvailabilitiesForm  GetRoomInfo 					GetRoomWeekDefaultPrice
 *  DrawRoomPricesForm          GetRoomTypes                    CheckRoomWeekDefaultAvailability
 *  GetRoomPricesTable          GetMonthLastDay                 ConvertToDecimal 
 *  DeleteRoomAvailability      DrawSearchAvailabilityBlock
 *  DeleteRoomPrices            DrawSearchAvailabilityFooter
 *  AddRoomAvailability         DrawRoomsInfo 
 *  AddRoomPrices
 *  UpdateRoomAvailability
 *  UpdateRoomPrices
 *  AfterInsertRecord
 *	AfterDeleteRecord
 *	SearchFor
 *	DrawSearchResult
 *	DrawRoomDescription
 *	
 **/


class Rooms extends MicroGrid {
	
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
	private $arrAvailableRooms;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{		
		$this->SetRunningTime();

		$this->arrAvailableRooms = array();
		$this->params = array();
		
		## for standard fields
		if(isset($_POST['room_type']))  $this->params['room_type'] = prepare_input($_POST['room_type']);
		if(isset($_POST['room_short_description'])) $this->params['room_short_description'] = prepare_input($_POST['room_short_description']);
		if(isset($_POST['room_long_description'])) $this->params['room_long_description'] = prepare_input($_POST['room_long_description']);
		if(isset($_POST['max_adults'])) $this->params['max_adults'] = (int)$_POST['max_adults'];
		if(isset($_POST['max_children'])) $this->params['max_children'] = (int)$_POST['max_children'];
		if(isset($_POST['room_count'])) $this->params['room_count'] = (int)$_POST['room_count'];		
		if(isset($_POST['default_price'])) $this->params['default_price'] = prepare_input($_POST['default_price']);		
		if(isset($_POST['priority_order'])) $this->params['priority_order'] = prepare_input($_POST['priority_order']);		
		
		## for checkboxes 
		if(isset($_POST['is_active']))   			$this->params['is_active'] = (int)$_POST['is_active']; else $this->params['is_active'] = "0";
		if(isset($_POST['default_availability']))   $this->params['default_availability'] = (int)$_POST['default_availability']; else $this->params['default_availability'] = "0";

		## for images
		if(isset($_POST['room_icon'])) { 
			$this->params['room_icon'] = prepare_input($_POST['room_icon']);
		}else if(isset($_FILES['room_icon']['name']) && $_FILES['room_icon']['name'] != ""){
			// nothing 			
		}else if (self::GetParameter('action') == "create"){
			$this->params['room_icon'] = "";
		}
		
		if(isset($_POST['room_picture_1'])){ $this->params['room_icon'] = prepare_input($_POST['room_picture_1']); }
		else if(isset($_FILES['room_picture_1']['name']) && $_FILES['room_picture_1']['name'] != ""){ }
		else if (self::GetParameter('action') == "create"){ $this->params['room_picture_1'] = ""; }
		
		if(isset($_POST['room_picture_2'])){ $this->params['room_icon'] = prepare_input($_POST['room_picture_1']); }
		else if(isset($_FILES['room_picture_2']['name']) && $_FILES['room_picture_2']['name'] != ""){ }
		else if (self::GetParameter('action') == "create"){ $this->params['room_picture_2'] = ""; }

		if(isset($_POST['room_picture_3'])){ $this->params['room_icon'] = prepare_input($_POST['room_picture_3']); }
		else if(isset($_FILES['room_picture_3']['name']) && $_FILES['room_picture_3']['name'] != ""){ }
		else if (self::GetParameter('action') == "create"){ $this->params['room_picture_3'] = ""; }


		$this->params['language_id'] = MicroGrid::GetParameter('language_id');
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_ROOMS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_rooms_management";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;
		
		$this->allowLanguages = false;
		$this->languageId  	= ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();
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

		// prepare languages array		
		/// $total_languages = Languages::GetAllActive();
		/// $arr_languages      = array();
		/// foreach($total_languages[0] as $key => $val){
		/// 	$arr_languages[$val['abbreviation']] = $val['lang_name'];
		/// }
		
		$default_currency = Currencies::GetDefaultCurrency();
		
		$random_name = true;

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT
									".$this->tableName.".".$this->primaryKey.",
									".$this->tableName.".max_adults,
									".$this->tableName.".max_children,
									".$this->tableName.".room_count,
									".$this->tableName.".default_price,
									".$this->tableName.".room_icon,
									".$this->tableName.".room_icon_thumb,
									".$this->tableName.".room_picture_1,
									".$this->tableName.".room_picture_1_thumb,
									".$this->tableName.".room_picture_2,
									".$this->tableName.".room_picture_2_thumb,
									".$this->tableName.".room_picture_3,
									".$this->tableName.".room_picture_3_thumb,
									".$this->tableName.".priority_order,
									".$this->tableName.".is_active,
									CONCAT('<a href=\"index.php?admin=room_prices&rid=', ".$this->tableName.".".$this->primaryKey.",'\" title=\""._CLICK_TO_MANAGE."\">', '[ "._PRICES." ]', '</a>') as link_prices,
									CONCAT('<a href=\"index.php?admin=room_availability&rid=', ".$this->tableName.".".$this->primaryKey.",'\" title=\""._CLICK_TO_MANAGE."\">', '[ "._AVAILABILITY." ]', '</a>') as link_room_availability,
									IF(is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as my_is_active,
									CONCAT('<a href=\"index.php?admin=room_description&room_id=', ".$this->tableName.".".$this->primaryKey.", '\" title=\""._CLICK_TO_MANAGE."\">[ ', '"._DESCRIPTION."', ' ]</a>') as link_room_description,
									rd.room_type,
									rd.room_short_description,
									rd.room_long_description
								FROM ".$this->tableName."
									LEFT OUTER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON ".$this->tableName.".".$this->primaryKey." = rd.room_id
								WHERE
									rd.language_id = '".$this->languageId."'";
		// define view mode fields
		$this->arrViewModeFields = array(

			"room_icon_thumb" => array("title"=>_ICON_IMAGE, "type"=>"image", "align"=>"center", "width"=>"80px", "image_width"=>"60px", "image_height"=>"30px", "target"=>"images/rooms_icons/", "no_image"=>"no_image.png"),
			"room_type"  	  => array("title"=>_TYPE, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
			"priority_order"  => array("title"=>_ORDER, "type"=>"label", "align"=>"center", "width"=>"70px", "height"=>"", "maxlength"=>"", "movable"=>true),
			"my_is_active" 	  => array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"50px", "height"=>"", "maxlength"=>""),
			"room_count" 	  => array("title"=>_ROOMS, "type"=>"label", "align"=>"center", "width"=>"50px", "height"=>"", "maxlength"=>""),
			"max_adults"      => array("title"=>_ADULTS, "type"=>"label", "align"=>"center", "width"=>"50px", "height"=>"", "maxlength"=>""),
			"max_children"    => array("title"=>_CHILDREN, "type"=>"label", "align"=>"center", "width"=>"50px", "height"=>"", "maxlength"=>""),
			"link_room_description" => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"80px", "height"=>"", "maxlength"=>""),			
			"link_prices" 	  => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"60px", "height"=>"", "maxlength"=>""),
			"link_room_availability" => array("title"=>"", "type"=>"label", "align"=>"left", "width"=>"100px", "height"=>"", "maxlength"=>""),

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
			"room_type"  	=> array("title"=>_TYPE, "type"=>"textbox",  "width"=>"270px", "required"=>true, "readonly"=>false, "maxlength"=>"", "default"=>"", "validation_type"=>"text"),
			"room_short_description" => array("title"=>_SHORT_DESCRIPTION, "type"=>"textarea", "editor_type"=>"wysiwyg", "width"=>"410px", "height"=>"40px", "required"=>true, "readonly"=>false, "default"=>"", "validation_type"=>"text", "validation_maxlength"=>"255"),
			"room_long_description" => array("title"=>_LONG_DESCRIPTION, "type"=>"textarea", "editor_type"=>"wysiwyg", "width"=>"410px", "height"=>"70px", "required"=>false, "readonly"=>false, "default"=>"", "validation_type"=>"text"),
			"max_adults"    => array("title"=>_MAX_ADULTS, "type"=>"textbox",  "width"=>"30px", "required"=>true, "readonly"=>false, "maxlength"=>"2", "default"=>"1", "validation_type"=>"numeric|positive"),
			"max_children"  => array("title"=>_MAX_CHILDREN, "type"=>"textbox",  "width"=>"30px", "required"=>true, "readonly"=>false, "maxlength"=>"2", "default"=>"0", "validation_type"=>"numeric|positive"),			
			"room_count"    => array("title"=>_ROOMS_COUNT, "type"=>"textbox",  "width"=>"40px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"1", "validation_type"=>"numeric|positive"),
			"default_price" => array("title"=>_DEFAULT_PRICE, "type"=>"textbox",  "width"=>"60px", "required"=>true, "readonly"=>false, "maxlength"=>"10", "default"=>"0", "validation_type"=>"float|positive", "pre_html"=>$default_currency." "),
			"default_availability" => array("title"=>_DEFAULT_AVAILABILITY, "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0", "post_html"=>" [V] - "._AVAILABLE." [&nbsp;] - "._NOT_AVAILABLE),
			"room_icon"      => array("title"=>_ICON_IMAGE, "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"icon_", "thumbnail_create"=>true, "thumbnail_field"=>"room_icon_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"room_picture_1" => array("title"=>_IMAGE." 1", "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"view1_", "thumbnail_create"=>true, "thumbnail_field"=>"room_picture_1_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"room_picture_2" => array("title"=>_IMAGE." 2", "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"view2_", "thumbnail_create"=>true, "thumbnail_field"=>"room_picture_2_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"room_picture_3" => array("title"=>_IMAGE." 3", "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"view3_", "thumbnail_create"=>true, "thumbnail_field"=>"room_picture_3_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"35px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric|positive"),
			"is_active"      => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		// Validation Type: alpha|numeric|float|alpha_numeric|text|email
		// Validation Sub-Type: positive (for numeric and float)
		// Ex.: "validation_type"=>"numeric", "validation_type"=>"numeric|positive"
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".room_type,
								".$this->tableName.".room_short_description,
								".$this->tableName.".room_long_description,
								".$this->tableName.".max_adults,
								".$this->tableName.".max_children,
								".$this->tableName.".room_count,
								".$this->tableName.".default_price,								
								".$this->tableName.".room_icon,
								".$this->tableName.".room_icon_thumb,
								".$this->tableName.".room_picture_1,
								".$this->tableName.".room_picture_1_thumb,
								".$this->tableName.".room_picture_2,
								".$this->tableName.".room_picture_2_thumb,
								".$this->tableName.".room_picture_3,
								".$this->tableName.".room_picture_3_thumb,
								".$this->tableName.".priority_order,
								".$this->tableName.".is_active,
								IF(is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as my_is_active,
								rd.room_type as m_room_type
							FROM ".$this->tableName."
								LEFT OUTER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON ".$this->tableName.".".$this->primaryKey." = rd.room_id
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
		    "m_room_type"    => array("title"=>_ROOM_TYPE, "type"=>"label"),
			"max_adults"     => array("title"=>_MAX_ADULTS, "type"=>"textbox",  "width"=>"30px", "required"=>true, "readonly"=>false, "maxlength"=>"2", "default"=>"", "validation_type"=>"numeric|positive"),
			"max_children"   => array("title"=>_MAX_CHILDREN, "type"=>"textbox",  "width"=>"30px", "required"=>true, "readonly"=>false, "maxlength"=>"2", "default"=>"", "validation_type"=>"numeric|positive"),
			"room_count"     => array("title"=>_COUNT, "type"=>"textbox",  "width"=>"40px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric|positive"),
			"default_price"  => array("title"=>_DEFAULT_PRICE, "type"=>"textbox",  "width"=>"60px", "required"=>true, "readonly"=>false, "maxlength"=>"10", "default"=>"0", "validation_type"=>"float|positive", "pre_html"=>$default_currency." "),
			"room_icon"      => array("title"=>_ICON_IMAGE, "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"icon_", "thumbnail_create"=>true, "thumbnail_field"=>"room_icon_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"room_picture_1" => array("title"=>_IMAGE." 1", "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"view1_", "thumbnail_create"=>true, "thumbnail_field"=>"room_picture_1_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"room_picture_2" => array("title"=>_IMAGE." 2", "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"view2_", "thumbnail_create"=>true, "thumbnail_field"=>"room_picture_2_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"room_picture_3" => array("title"=>_IMAGE." 3", "type"=>"image",    "width"=>"210px", "required"=>false, "target"=>"images/rooms_icons/", "no_image"=>"", "random_name"=>$random_name, "image_name_pefix"=>"view3_", "thumbnail_create"=>true, "thumbnail_field"=>"room_picture_3_thumb", "thumbnail_width"=>"190px", "thumbnail_height"=>""),
			"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"35px", "required"=>true, "readonly"=>false, "maxlength"=>"3", "default"=>"0", "validation_type"=>"numeric|positive"),
			"is_active"      => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(
			"room_type"  	 => array("title"=>_TYPE, "type"=>"label"),
			"max_adults" 	 => array("title"=>_MAX_ADULTS, "type"=>"label"),
			"max_children" 	 => array("title"=>_MAX_CHILDREN, "type"=>"label"),
			"room_count"     => array("title"=>_COUNT, "type"=>"label"),
			"default_price"  => array("title"=>_DEFAULT_PRICE, "type"=>"label"),
			"my_is_active"   => array("title"=>_ACTIVE, "type"=>"label"),
			"room_icon"      => array("title"=>_ICON_IMAGE, "type"=>"image", "target"=>"images/rooms_icons/", "no_image"=>"no_image.png"),
			"room_picture_1" => array("title"=>_IMAGE." 1", "type"=>"image", "target"=>"images/rooms_icons/", "no_image"=>"no_image.png"),
			"room_picture_2" => array("title"=>_IMAGE." 2", "type"=>"image", "target"=>"images/rooms_icons/", "no_image"=>"no_image.png"),
			"room_picture_3" => array("title"=>_IMAGE." 3", "type"=>"image", "target"=>"images/rooms_icons/", "no_image"=>"no_image.png"),
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
	 *	Draws room availabilities form
	 *		@param $rid
	 */
	public function DrawRoomAvailabilitiesForm($rid)
	{
		global $objSettings;

		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$calendar_date_format = "%m-%d-%Y";
			$field_date_format = "M d, Y";
		}else{
			$calendar_date_format = "%d-%m-%Y";
			$field_date_format = "d M, Y";
		}

		$room_type 	   = isset($_REQUEST['room_type']) ? prepare_input($_REQUEST['room_type']) : "";
		$from_new 	   = isset($_POST['from_new']) ? prepare_input($_POST['from_new']) : "";
		$to_new 	   = isset($_POST['to_new']) ? prepare_input($_POST['to_new']) : "";		
		$ids_list 	   = "";
		$width 		   = "48px";	
		
		$output  = "<style type='text/css'>@import url(modules/jscalendar/skins/aqua/theme.css);</style>\n";
		$output  .= "<script type='text/javascript'>
			function submitAvailabilityForm(task, rpid){
				<!--
				if(task == 'refresh'){
					document.getElementById('task').value = task;
					document.getElementById('frmRoomAvailability').submit();				
				}else if(task == 'delete'){
					if(confirm('"._DELETE_WARNING_COMMON."')){
						document.getElementById('task').value = task;
						document.getElementById('rpid').value = rpid;
						document.getElementById('frmRoomAvailability').submit();
					}				
				}else if((task == 'update') || (task == 'add_new')){
					document.getElementById('task').value = task;
					document.getElementById('frmRoomAvailability').submit();
				}
				//-->
			}
			function toggleSelection(selection_type, rid){
				var selection_type = (selection_type == 1) ? true : false;
				
				document.getElementById('aval_'+rid+'_mon').checked = selection_type;
				document.getElementById('aval_'+rid+'_tue').checked = selection_type;
				document.getElementById('aval_'+rid+'_wed').checked = selection_type;
				document.getElementById('aval_'+rid+'_thu').checked = selection_type;
				document.getElementById('aval_'+rid+'_fri').checked = selection_type;
				document.getElementById('aval_'+rid+'_sat').checked = selection_type;
				document.getElementById('aval_'+rid+'_sun').checked = selection_type;
				
			}
		</script>\n";
		$output .= "<script type='text/javascript' src='modules/jscalendar/calendar.js'></script>\n";
		$output .= "<script type='text/javascript' src='modules/jscalendar/lang/calendar-en.js'></script>\n";
		$output .= "<script type='text/javascript' src='modules/jscalendar/calendar-setup.js'></script>\n";
		
		$output .= "<form action='index.php?admin=room_availability' id='frmRoomAvailability' method='post'>";
		$output .= "<input type='hidden' name='task' id='task' value='update' />";
		$output .= "<input type='hidden' name='rid' id='rid' value='".$rid."' />";
		$output .= "<input type='hidden' name='rpid' id='rpid' value='' />";
		$output .= "<input type='hidden' name='room_type' id='room_type' value='".$room_type."' />";
		$output .= draw_token_field(true);
		$output .= "<table cellpadding='1' cellspacing='0'>";
		$output .= "<tr style='text-align:center;font-weight:bold;'>";
		$output .= "  <td width='5px'></td>";
		$output .= "  <td colspan='3' width='215px'></td>";
		$output .= "  <td width='20px'></td>";
		$output .= "  <td width='40px'><img src='images/check_all.gif' style='margin-left:1px' alt='' /></td>";
		$output .= "  <td>"._MON."</td>";
		$output .= "  <td>"._TUE."</td>";
		$output .= "  <td>"._WED."</td>";
		$output .= "  <td>"._THU."</td>";
		$output .= "  <td>"._FRI."</td>";
		$output .= "  <td style='background-color:#ffcc33;'>"._SAT."</td>";
		$output .= "  <td style='background-color:#ffcc33;'>"._SUN."</td>";
		$output .= "  <td colspan='2'></td>";
		$output .= "</tr>";

		$sql = "SELECT * FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE room_id = ".(int)$rid." ORDER BY is_default DESC, date_from ASC";
		$room = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		for($i=0; $i < $room[1]; $i++){
			
			$aval_mon = isset($_POST['aval_'.$room[0][$i]['id'].'_mon']) ? "1" : $room[0][$i]['mon'];
			$aval_tue = isset($_POST['aval_'.$room[0][$i]['id'].'_tue']) ? "1" : $room[0][$i]['tue'];
			$aval_wed = isset($_POST['aval_'.$room[0][$i]['id'].'_wed']) ? "1" : $room[0][$i]['wed'];
			$aval_thu = isset($_POST['aval_'.$room[0][$i]['id'].'_thu']) ? "1" : $room[0][$i]['thu'];
			$aval_fri = isset($_POST['aval_'.$room[0][$i]['id'].'_fri']) ? "1" : $room[0][$i]['fri'];
			$aval_sat = isset($_POST['aval_'.$room[0][$i]['id'].'_sat']) ? "1" : $room[0][$i]['sat'];
			$aval_sun = isset($_POST['aval_'.$room[0][$i]['id'].'_sun']) ? "1" : $room[0][$i]['sun'];
			
			$output .= "<tr align='center' style='".(($i%2==0) ? "" : "background-color:#f1f2f3;")."'>";
			
			$output .= "  <td></td>";
			if($i == 0){
				$output .= "  <td align='left' nowrap='nowrap' colspan='3'><b>"._DEFAULT_AVAILABILITY."</b></td>";	
			}else{
				$output .= "  <td width='105px' align='left' nowrap='nowrap'><input type='text' readonly='readonly' name='date_from_".$room[0][$i]['id']."' style='width:85px;border:0px;".(($i%2==0) ? "" : "background-color:#f1f2f3;")."' value='".format_datetime($room[0][$i]['date_from'], $field_date_format)."' /></td>";
				$output .= "  <td width='10px' align='left' nowrap='nowrap'>-</td>";
				$output .= "  <td width='105px' align='right' nowrap='nowrap'><input type='text' readonly='readonly' name='date_to_".$room[0][$i]['id']."' style='width:85px;border:0px;".(($i%2==0) ? "" : "background-color:#f1f2f3;")."' value='".format_datetime($room[0][$i]['date_to'], $field_date_format)."' /></td>";	
			}
			$output .= "  <td></td>";
			$output .= "  <td><input type='checkbox' class='form_checkbox' onclick=\"toggleSelection(this.checked,'".$room[0][$i]['id']."')\" /></td>";
			$output .= "  <td><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_mon' id='aval_".$room[0][$i]['id']."_mon' ".(($aval_mon == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_tue' id='aval_".$room[0][$i]['id']."_tue' ".(($aval_tue == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_wed' id='aval_".$room[0][$i]['id']."_wed' ".(($aval_wed == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_thu' id='aval_".$room[0][$i]['id']."_thu' ".(($aval_thu == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_fri' id='aval_".$room[0][$i]['id']."_fri' ".(($aval_fri == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td style='background-color:#ffcc33;'><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_sat' id='aval_".$room[0][$i]['id']."_sat' ".(($aval_sat == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td style='background-color:#ffcc33;'><input type='checkbox' class='form_checkbox' name='aval_".$room[0][$i]['id']."_sun' id='aval_".$room[0][$i]['id']."_sun' ".(($aval_sun == "1") ? "checked='checked'" : "")." /></td>";
			$output .= "  <td width='30px' align='center'>".(($i > 0) ? "<img src='images/delete.gif' alt='"._DELETE_WORD."' title='"._DELETE_WORD."' style='cursor:pointer;' onclick='javascript:submitAvailabilityForm(\"delete\", \"".$room[0][$i]['id']."\")' />" : "")."</td>";
			$output .= "  <td></td>";
			$output .= "</tr>";
			if($ids_list != "") $ids_list .= ",".$room[0][$i]['id'];
			else $ids_list = $room[0][$i]['id'];
		}
		$output .= "<tr><td colspan='11'></td><td colspan='2' style='height:5px;background-color:#ffcc33;'></td><td></td></tr>";

		$output .= "<tr><td colspan='14'>&nbsp;</td></tr>";
		$output .= "<tr>";
		$output .= "  <td align='right' colspan='14'>
						<input type='button' class='form_button' style='width:100px' onclick='javascript:submitAvailabilityForm(\"refresh\")' value='"._REFRESH."'>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='button' class='form_button' style='width:130px' onclick='javascript:submitAvailabilityForm(\"update\")' value='"._BUTTON_SAVE_CHANGES."'>
					  </td>	
					  <td></td>";
		$output .= "</tr>";
		$output .= "<tr><td colspan='15'>&nbsp;</td></tr>";
		$output .= "<tr align='center'>";
		$output .= "  <td></td>";
		$output .= "  <td colspan='3' style='padding-right:5px;' align='right'>"._FROM.": <input type='text' id='from_new' name='from_new' style='color:#808080;width:80px' readonly='readonly' value='".$from_new."' /><img id='from_new_cal' src='images/cal.gif' alt='' title='"._SET_DATE."' style='margin-left:5px;margin-right:5px;cursor:pointer;' /><br />"._TO.": <input type='text' id='to_new' name='to_new' style='color:#808080;width:80px' readonly='readonly' value='".$to_new."' /><img id='to_new_cal' src='images/cal.gif' alt='' title='"._SET_DATE."' style='margin-left:5px;margin-right:5px;cursor:pointer;' /></td>";
		$output .= "  <td></td>";
		$output .= "  <td><input type='checkbox' class='form_checkbox' onclick=\"toggleSelection(this.checked,'new')\" checked='checked' /></td>";
		$output .= "  <td width='60px'><input type='checkbox' class='form_checkbox' name='aval_new_mon' id='aval_new_mon' value='1' checked='checked' /></td>";
		$output .= "  <td width='60px'><input type='checkbox' class='form_checkbox' name='aval_new_tue' id='aval_new_tue' value='1' checked='checked' /></td>";
		$output .= "  <td width='60px'><input type='checkbox' class='form_checkbox' name='aval_new_wed' id='aval_new_wed' value='1' checked='checked' /></td>";
		$output .= "  <td width='60px'><input type='checkbox' class='form_checkbox' name='aval_new_thu' id='aval_new_thu' value='1' checked='checked' /></td>";
		$output .= "  <td width='60px'><input type='checkbox' class='form_checkbox' name='aval_new_fri' id='aval_new_fri' value='1' checked='checked' /></td>";
		$output .= "  <td width='60px' style='background-color:#ffcc33;'><input type='checkbox' class='form_checkbox' name='aval_new_sat' id='aval_new_sat' value='1' checked='checked' /></td>";
		$output .= "  <td width='60px' style='background-color:#ffcc33;'><input type='checkbox' class='form_checkbox' name='aval_new_sun' id='aval_new_sun' value='1' checked='checked' /></td>";
		$output .= "  <td colspan='2'></td>";
		$output .= "</tr>";			

		$output .= "<tr><td colspan='15'>&nbsp;</td></tr>";
		$output .= "<tr>";
		$output .= "  <td align='right' colspan='14'><input type='button' class='form_button' style='width:130px' onclick='javascript:submitAvailabilityForm(\"add_new\")' value='"._ADD_NEW."'></td>";
		$output .= "  <td></td>";
		$output .= "</tr>";
		$output .= "</table>";
		$output .= "<input type='hidden' name='ids_list' value='".$ids_list."' />";
		$output .= "</form>";
		
		$output .= "
		<script type='text/javascript'> 
		<!--
		Calendar.setup({firstDay : 0, inputField : 'from_new', ifFormat : '".$calendar_date_format."', showsTime : false, button : 'from_new_cal'});
		Calendar.setup({firstDay : 0, inputField : 'to_new', ifFormat : '".$calendar_date_format."', showsTime : false, button : 'to_new_cal'});
		//-->
		</script>";
		
		echo $output;
	}

	/**
	 *	Draws room prices form
	 *		@param $rid
	 */
	public function DrawRoomPricesForm($rid)
	{		
		global $objSettings;

		$default_currency_info = Currencies::GetDefaultCurrencyInfo();
		if($default_currency_info['symbol_placement'] == "left"){
			$currency_l_sign = $default_currency_info['symbol'];
			$currency_r_sign = "";
		}else{
			$currency_l_sign = "";
			$currency_r_sign = $default_currency_info['symbol'];			
		}
		
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$calendar_date_format = "%m-%d-%Y";
			$field_date_format = "M d, Y";
		}else{
			$calendar_date_format = "%d-%m-%Y";
			$field_date_format = "d M, Y";
		}

		$sql = "SELECT * FROM ".TABLE_ROOMS." WHERE id = ".(int)$rid;
		$room = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room[1] > 0){
			$default_price = $room[0]['default_price'];
		}else{
			$default_price = "0";
		}

		$room_type 	   = isset($_REQUEST['room_type']) ? prepare_input($_REQUEST['room_type']) : "";
		$from_new 	   = isset($_POST['from_new']) ? prepare_input($_POST['from_new']) : "";
		$to_new 	   = isset($_POST['to_new']) ? prepare_input($_POST['to_new']) : "";		
		$price_new_mon = isset($_POST['price_new_mon']) ? prepare_input($_POST['price_new_mon']) : $default_price;
		$price_new_tue = isset($_POST['price_new_tue']) ? prepare_input($_POST['price_new_tue']) : $default_price;
		$price_new_wed = isset($_POST['price_new_wed']) ? prepare_input($_POST['price_new_wed']) : $default_price;
		$price_new_thu = isset($_POST['price_new_thu']) ? prepare_input($_POST['price_new_thu']) : $default_price;
		$price_new_fri = isset($_POST['price_new_fri']) ? prepare_input($_POST['price_new_fri']) : $default_price;
		$price_new_sat = isset($_POST['price_new_sat']) ? prepare_input($_POST['price_new_sat']) : $default_price;
		$price_new_sun = isset($_POST['price_new_sun']) ? prepare_input($_POST['price_new_sun']) : $default_price;
		$ids_list 	   = "";
		$width         = "48px";	

		$output  = "<style type='text/css'>@import url(modules/jscalendar/skins/aqua/theme.css);</style>\n";
		$output  .= "<script type='text/javascript'>
			function submitPriceForm(task, rpid){
				<!--
				if(task == 'refresh'){
					document.getElementById('task').value = task;
					document.getElementById('frmRoomPrices').submit();				
				}else if(task == 'delete'){
					if(confirm('"._DELETE_WARNING_COMMON."')){
						document.getElementById('task').value = task;
						document.getElementById('rpid').value = rpid;
						document.getElementById('frmRoomPrices').submit();
					}				
				}else if((task == 'update') || (task == 'add_new')){
					document.getElementById('task').value = task;
					document.getElementById('frmRoomPrices').submit();
				}				
				//-->
			}
		</script>\n";
		$output .= "<script type='text/javascript' src='modules/jscalendar/calendar.js'></script>\n";
		$output .= "<script type='text/javascript' src='modules/jscalendar/lang/calendar-en.js'></script>\n";
		$output .= "<script type='text/javascript' src='modules/jscalendar/calendar-setup.js'></script>\n";
		
		$output .= "<form action='index.php?admin=room_prices' id='frmRoomPrices' method='post'>";
		$output .= "<input type='hidden' name='task' id='task' value='update' />";
		$output .= "<input type='hidden' name='rid' id='rid' value='".$rid."' />";
		$output .= "<input type='hidden' name='rpid' id='rpid' value='' />";
		$output .= "<input type='hidden' name='room_type' id='room_type' value='".$room_type."' />";
		$output .= draw_token_field(true);
		$output .= "<table width='90%' border='0' cellpadding='1' cellspacing='0'>";
		$output .= "<tr style='text-align:center;font-weight:bold;'>";
		$output .= "  <td width='5px'></td>";
		$output .= "  <td colspan='3' width='145px'></td>";
		$output .= "  <td width='10px'></td>";
		$output .= "  <td>"._MON."</td>";
		$output .= "  <td>"._TUE."</td>";
		$output .= "  <td>"._WED."</td>";
		$output .= "  <td>"._THU."</td>";
		$output .= "  <td>"._FRI."</td>";
		$output .= "  <td style='background-color:#ffcc33;'>"._SAT."</td>";
		$output .= "  <td style='background-color:#ffcc33;'>"._SUN."</td>";
		$output .= "  <td></td>";
		$output .= "</tr>";

		$sql = "SELECT * FROM ".TABLE_ROOMS_PRICES." WHERE room_id = ".(int)$rid." ORDER BY is_default DESC, date_from ASC";
		$room = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		for($i=0; $i < $room[1]; $i++){
			$output .= "<tr align='center' style='".(($i%2==0) ? "" : "background-color:#f1f2f3;")."'>";

			$output .= "  <td></td>";
			if($i == 0){
				$output .= "  <td align='left' nowrap='nowrap' colspan='3'><b>"._STANDARD_PRICE."</b></td>";	
			}else{
				$output .= "  <td align='left' nowrap='nowrap'><input type='text' readonly='readonly' name='date_from_".$room[0][$i]['id']."' style='width:85px;border:0px;".(($i%2==0) ? "" : "background-color:#f1f2f3;")."' value='".format_datetime($room[0][$i]['date_from'], $field_date_format)."' /></td>";
				$output .= "  <td align='left' nowrap='nowrap' width='20px'>-</td>";
				$output .= "  <td align='left' nowrap='nowrap'><input type='text' readonly='readonly' name='date_to_".$room[0][$i]['id']."' style='width:85px;border:0px;".(($i%2==0) ? "" : "background-color:#f1f2f3;")."' value='".format_datetime($room[0][$i]['date_to'], $field_date_format)."' /></td>";	
			}			
			$output .= "  <td></td>";
			$output .= "  <td nowrap>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_mon' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_mon']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_mon']) : $room[0][$i]['mon'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td nowrap>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_tue' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_tue']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_tue']) : $room[0][$i]['tue'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td nowrap>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_wed' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_wed']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_wed']) : $room[0][$i]['wed'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td nowrap>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_thu' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_thu']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_thu']) : $room[0][$i]['thu'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td nowrap>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_fri' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_fri']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_fri']) : $room[0][$i]['fri'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td style='background-color:#ffcc33;'>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_sat' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_sat']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_sat']) : $room[0][$i]['sat'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td style='background-color:#ffcc33;'>".$currency_l_sign." <input type='text' name='price_".$room[0][$i]['id']."_sun' value='".(isset($_POST['price_'.$room[0][$i]['id'].'_sun']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_sun']) : $room[0][$i]['sun'])."' maxlength='7' style='width:".$width."' /> ".$currency_r_sign."</td>";
			$output .= "  <td width='30px' align='center'>".(($i > 0) ? "<img src='images/delete.gif' alt='"._DELETE_WORD."' title='"._DELETE_WORD."' style='cursor:pointer;' onclick='javascript:submitPriceForm(\"delete\", \"".$room[0][$i]['id']."\")' />" : "")."</td>";
			$output .= "</tr>";
			if($ids_list != "") $ids_list .= ",".$room[0][$i]['id'];
			else $ids_list = $room[0][$i]['id'];
		}		
		$output .= "<tr><td colspan='10'></td><td colspan='2' style='height:5px;background-color:#ffcc33;'></td><td></td></tr>";

		$output .= "<tr><td colspan='13'>&nbsp;</td></tr>";
		$output .= "<tr>";
		$output .= "  <td colspan='8'></td>
					  <td align='center' colspan='2'><input type='button' class='form_button' style='width:100px' onclick='javascript:submitPriceForm(\"refresh\")' value='"._REFRESH."'></td>
					  <td align='center' colspan='2'><input type='button' class='form_button' style='width:130px' onclick='javascript:submitPriceForm(\"update\")' value='"._BUTTON_SAVE_CHANGES."'></td>
					  <td></td>";
		$output .= "</tr>";
		$output .= "<tr><td colspan='13'>&nbsp;</td></tr>";
		$output .= "<tr align='center'>";
		$output .= "  <td></td>";
		$output .= "  <td colspan='3' align='right'>"._FROM.": <input type='text' id='from_new' name='from_new' style='color:#808080;width:80px' readonly='readonly' value='".$from_new."' /><img id='from_new_cal' src='images/cal.gif' alt='' title='"._SET_DATE."' style='margin-left:5px;margin-right:5px;cursor:pointer;' />  <br />"._TO.": <input type='text' id='to_new' name='to_new' style='color:#808080;width:80px' readonly='readonly' value='".$to_new."' /><img id='to_new_cal' src='images/cal.gif' alt='' title='"._SET_DATE."' style='margin-left:5px;margin-right:5px;cursor:pointer;' /></td>";
		$output .= "  <td></td>";
		$output .= "  <td>".$currency_l_sign." <input type='text' name='price_new_mon' value='".$price_new_mon."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td>".$currency_l_sign." <input type='text' name='price_new_tue' value='".$price_new_tue."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td>".$currency_l_sign." <input type='text' name='price_new_wed' value='".$price_new_wed."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td>".$currency_l_sign." <input type='text' name='price_new_thu' value='".$price_new_thu."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td>".$currency_l_sign." <input type='text' name='price_new_fri' value='".$price_new_fri."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td style='background-color:#ffcc33;'>".$currency_l_sign." <input type='text' name='price_new_sat' value='".$price_new_sat."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td style='background-color:#ffcc33;'>".$currency_l_sign." <input type='text' name='price_new_sun' value='".$price_new_sun."' maxlength='7' style='color:#808080; width:".$width."' /> ".$currency_r_sign."</td>";
		$output .= "  <td></td>";
		$output .= "</tr>";			

		$output .= "<tr><td colspan='13'>&nbsp;</td></tr>";
		$output .= "<tr>";
		$output .= "  <td colspan='10'></td>";
		$output .= "  <td align='center' colspan='2'><input type='button' class='form_button' style='width:130px' onclick='javascript:submitPriceForm(\"add_new\")' value='"._ADD_NEW."'></td>";
		$output .= "  <td></td>";
		$output .= "</tr>";
		$output .= "</table>";
		$output .= "<input type='hidden' name='ids_list' value='".$ids_list."' />";
		$output .= "</form>";
		
		$output .= "
		<script type='text/javascript'> 
		<!--
		Calendar.setup({firstDay : 0, inputField : 'from_new', ifFormat : '".$calendar_date_format."', showsTime : false, button : 'from_new_cal'});
		Calendar.setup({firstDay : 0, inputField : 'to_new', ifFormat : '".$calendar_date_format."', showsTime : false, button : 'to_new_cal'});
		//-->
		</script>";

		echo $output;
	}

	/**
	 *	Returns a table with prices for certain room
	 *		@param $rid
	 */
	public function GetRoomPricesTable($rid)
	{		
		global $objSettings, $objLogin;
		
		$currency_rate = ($objLogin->IsLoggedInAsAdmin()) ? "1" : Application::Get("currency_rate");
	
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$calendar_date_format = "%m-%d-%Y";
			$field_date_format = "M d, Y";
		}else{
			$calendar_date_format = "%d-%m-%Y";
			$field_date_format = "d M, Y";
		}

		$sql = "SELECT * FROM ".TABLE_ROOMS." WHERE id = ".(int)$rid;
		$room = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room[1] > 0){
			$default_price = $room[0]['default_price'];
		}else{
			$default_price = "0";
		}

		$room_type 	   = isset($_REQUEST['room_type']) ? prepare_input($_REQUEST['room_type']) : "";
		$from_new 	   = isset($_POST['from_new']) ? prepare_input($_POST['from_new']) : "";
		$to_new 	   = isset($_POST['to_new']) ? prepare_input($_POST['to_new']) : "";		
		$price_new_mon = isset($_POST['price_new_mon']) ? prepare_input($_POST['price_new_mon']) : $default_price;
		$price_new_tue = isset($_POST['price_new_tue']) ? prepare_input($_POST['price_new_tue']) : $default_price;
		$price_new_wed = isset($_POST['price_new_wed']) ? prepare_input($_POST['price_new_wed']) : $default_price;
		$price_new_thu = isset($_POST['price_new_thu']) ? prepare_input($_POST['price_new_thu']) : $default_price;
		$price_new_fri = isset($_POST['price_new_fri']) ? prepare_input($_POST['price_new_fri']) : $default_price;
		$price_new_sat = isset($_POST['price_new_sat']) ? prepare_input($_POST['price_new_sat']) : $default_price;
		$price_new_sun = isset($_POST['price_new_sun']) ? prepare_input($_POST['price_new_sun']) : $default_price;
		$ids_list = "";
		$width = "48px";	

		$output = "<table class='room_prices' border='0' cellpadding='0' cellspacing='0'>";
		$output .= "<tr class='header'>";
		$output .= "  <th width='5px'>&nbsp;</td>";
		$output .= "  <th colspan=3 width='145px'>&nbsp;</td>";
		$output .= "  <th width='10px'>&nbsp;</td>";
		$output .= "  <th>"._MON."</td>";
		$output .= "  <th>"._TUE."</td>";
		$output .= "  <th>"._WED."</td>";
		$output .= "  <th>"._THU."</td>";
		$output .= "  <th>"._FRI."</td>";
		$output .= "  <th>"._SAT."</td>";
		$output .= "  <th>"._SUN."</td>";
		$output .= "</tr>";

		$sql = "SELECT * FROM ".TABLE_ROOMS_PRICES."
				WHERE
					room_id = ".(int)$rid." AND
					(
						is_default = 1 OR
						(is_default = 0 AND date_from >= '".date("Y")."-01-01') OR
						(is_default = 0 AND date_to >= '".date("Y")."-01-01')
					)
				ORDER BY is_default DESC, date_from ASC";
		$room = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		$output .= "<tr><td colspan='12' nowrap height='5px'></td></tr>";
		for($i=0; $i < $room[1]; $i++){
			//style='".(($i%2==0) ? "" : "background-color:#f1f2f3;")."'
			$output .= "<tr align='center' >";
			$output .= "  <td></td>";
			if($i == 0){
				$output .= "  <td align='left' nowrap='nowrap' colspan='3'><b>"._STANDARD_PRICE."</b></td>";	
			}else{
				$output .= "  <td align='left' nowrap='nowrap'>".format_datetime($room[0][$i]['date_from'], $field_date_format)."</td>";
				$output .= "  <td align='left' nowrap='nowrap' width='20px'>-</td>";
				$output .= "  <td align='left' nowrap='nowrap'>".format_datetime($room[0][$i]['date_to'], $field_date_format)."</td>";	
			}			
			$output .= "  <td></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_mon']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_mon']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['mon']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['mon'])) )."</span></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_tue']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_tue']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['tue']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['tue'])) )."</span></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_wed']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_wed']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['wed']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['wed'])) )."</span></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_thu']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_thu']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['thu']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['thu'])) )."</span></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_fri']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_fri']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['fri']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['fri'])) )."</span></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_sat']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_sat']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['sat']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['sat'])) )."</span></td>";
			$output .= "  <td><span>".(isset($_POST['price_'.$room[0][$i]['id'].'_sun']) ? prepare_input($_POST['price_'.$room[0][$i]['id'].'_sun']) : ((!$objLogin->IsLoggedInAsAdmin()) ? Currencies::PriceFormat($room[0][$i]['sun']/$currency_rate) : Currencies::PriceFormat($room[0][$i]['sun'])) )."</span></td>";
			$output .= "</tr>";
			if($ids_list != "") $ids_list .= ",".$room[0][$i]['id'];
			else $ids_list = $room[0][$i]['id'];
		}		
		$output .= "<tr><td colspan='12' nowrap height='5px'></td></tr>";
		$output .= "</table>";

		return $output;
	}	
	
    /**
	 * Deletes room availability
	 * 		@param $rid
	 */
	public function DeleteRoomAvailability($rpid)
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		$sql = "DELETE FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE id = ".(int)$rpid;
		if(!database_void_query($sql)){
			$this->error = _TRY_LATER;
			return false;
		}
		return true;
	}

    /**
	 * Deletes room prices
	 * 		@param $rpid
	 */
	public function DeleteRoomPrices($rpid)
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		$sql = "DELETE FROM ".TABLE_ROOMS_PRICES." WHERE id = ".(int)$rpid;
		if(!database_void_query($sql)){
			$this->error = _TRY_LATER;
			return false;
		}
		return true;
	}
	
    /**
	 * Adds room availability
	 * 		@param $rid
	 */
	public function AddRoomAvailability($rid)
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		global $objSettings;

		$task 	  = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
		$from_new = isset($_POST['from_new']) ? prepare_input($_POST['from_new']) : "";
		$to_new   = isset($_POST['to_new']) ? prepare_input( $_POST['to_new']) : "";		
		$aval_mon = isset($_POST['aval_new_mon']) ? "1" : "0";
		$aval_tue = isset($_POST['aval_new_tue']) ? "1" : "0";
		$aval_wed = isset($_POST['aval_new_wed']) ? "1" : "0";
		$aval_thu = isset($_POST['aval_new_thu']) ? "1" : "0";
		$aval_fri = isset($_POST['aval_new_fri']) ? "1" : "0";
		$aval_sat = isset($_POST['aval_new_sat']) ? "1" : "0";
		$aval_sun = isset($_POST['aval_new_sun']) ? "1" : "0";
	
				
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$from_new = substr($from_new, 6, 4)."-".substr($from_new, 0, 2)."-".substr($from_new, 3, 2);
			$to_new = substr($to_new, 6, 4)."-".substr($to_new, 0, 2)."-".substr($to_new, 3, 2);
		}else{
			// dd/mm/yyyy
			$from_new = substr($from_new, 6, 4)."-".substr($from_new, 3, 2)."-".substr($from_new, 0, 2);
			$to_new = substr($to_new, 6, 4)."-".substr($to_new, 3, 2)."-".substr($to_new, 0, 2);
		}

		if($from_new == "--" || $to_new == "--"){
			$this->error = _DATE_EMPTY_ALERT;
			return false;
		}else if($from_new > $to_new){
			$this->error = _FROM_TO_DATE_ALERT;
			return false;			
		}else{
			$sql = "SELECT * FROM ".TABLE_ROOMS_AVAILABILITIES."
					WHERE
						room_id = ".(int)$rid." AND
						is_default = 0 AND 
						((('".$from_new."' >= date_from) AND ('".$from_new."' <= date_to)) OR
						(('".$to_new."' >= date_from) AND ('".$to_new."' <= date_to))) ";	
			$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
			if($result[1] > 0){
				$this->error = _TIME_PERIOD_OVERLOADED_ALRT;
				return false;
			}
		}

		if($from_new != "" && $to_new != ""){
			$sql = "INSERT INTO ".TABLE_ROOMS_AVAILABILITIES." (id, room_id, date_from, date_to, mon, tue, wed, thu, fri, sat, sun, is_default)
					VALUES (NULL, ".(int)$rid.", '".$from_new."', '".$to_new."', ".$aval_mon.", ".$aval_tue.", ".$aval_wed.", ".$aval_thu.", ".$aval_fri.", ".$aval_sat.", ".$aval_sun.", 0)";
			if(database_void_query($sql)){
				unset($_POST);
				return true;
			}else{
				$this->error = _TRY_LATER;
				return false;
			}
		}
	}

    /**
	 * Adds room prices
	 * 		@param $rid
	 */
	public function AddRoomPrices($rid)
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		global $objSettings;

		$task 	       = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
		$from_new 	   = isset($_POST['from_new']) ? prepare_input($_POST['from_new']) : "";
		$to_new 	   = isset($_POST['to_new']) ? prepare_input($_POST['to_new']) : "";		
		$price_new_mon = isset($_POST['price_new_mon']) ? prepare_input($_POST['price_new_mon']) : "";
		$price_new_tue = isset($_POST['price_new_tue']) ? prepare_input($_POST['price_new_tue']) : "";
		$price_new_wed = isset($_POST['price_new_wed']) ? prepare_input($_POST['price_new_wed']) : "";
		$price_new_thu = isset($_POST['price_new_thu']) ? prepare_input($_POST['price_new_thu']) : "";
		$price_new_fri = isset($_POST['price_new_fri']) ? prepare_input($_POST['price_new_fri']) : "";
		$price_new_sat = isset($_POST['price_new_sat']) ? prepare_input($_POST['price_new_sat']) : "";
		$price_new_sun = isset($_POST['price_new_sun']) ? prepare_input($_POST['price_new_sun']) : "";		
				
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$from_new = substr($from_new, 6, 4)."-".substr($from_new, 0, 2)."-".substr($from_new, 3, 2);
			$to_new = substr($to_new, 6, 4)."-".substr($to_new, 0, 2)."-".substr($to_new, 3, 2);
		}else{
			// dd/mm/yyyy
			$from_new = substr($from_new, 6, 4)."-".substr($from_new, 3, 2)."-".substr($from_new, 0, 2);
			$to_new = substr($to_new, 6, 4)."-".substr($to_new, 3, 2)."-".substr($to_new, 0, 2);
		}

		if($from_new == "--" || $to_new == "--"){
			$this->error = _DATE_EMPTY_ALERT;
			return false;
		}else if($from_new > $to_new){
			$this->error = _FROM_TO_DATE_ALERT;
			return false;			
		}else if($price_new_mon == "" || $price_new_tue == "" || $price_new_wed == "" || $price_new_thu == "" || $price_new_fri == "" || $price_new_sat == "" || $price_new_sun == ""){
			$this->error = _PRICE_EMPTY_ALERT;
			return false;
		}else if(!$this->IsFloat($price_new_mon) || $price_new_mon < 0){
			$this->error = str_replace("_FIELD_", _MON, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else if(!$this->IsFloat($price_new_tue) || $price_new_tue < 0){
			$this->error = str_replace("_FIELD_", _TUE, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else if(!$this->IsFloat($price_new_wed) || $price_new_wed < 0){
			$this->error = str_replace("_FIELD_", _WED, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else if(!$this->IsFloat($price_new_thu) || $price_new_thu < 0){
			$this->error = str_replace("_FIELD_", _THU, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else if(!$this->IsFloat($price_new_fri) || $price_new_fri < 0){
			$this->error = str_replace("_FIELD_", _FRI, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else if(!$this->IsFloat($price_new_sat) || $price_new_sat < 0){
			$this->error = str_replace("_FIELD_", _SAT, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else if(!$this->IsFloat($price_new_sun) || $price_new_sun < 0){
			$this->error = str_replace("_FIELD_", _SUN, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			return false;
		}else{
			$sql = "SELECT * FROM ".TABLE_ROOMS_PRICES."
					WHERE
						room_id = ".(int)$rid." AND
						is_default = 0 AND 
						((('".$from_new."' >= date_from) AND ('".$from_new."' <= date_to)) OR
						(('".$to_new."' >= date_from) AND ('".$to_new."' <= date_to))) ";	
			$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
			if($result[1] > 0){
				$this->error = _TIME_PERIOD_OVERLOADED_ALRT;
				return false;
			}
		}

		if($from_new != "" && $to_new != ""){
			$sql = "INSERT INTO ".TABLE_ROOMS_PRICES." (id, room_id, date_from, date_to, mon, tue, wed, thu, fri, sat, sun, is_default)
					VALUES (NULL, ".(int)$rid.", '".$from_new."', '".$to_new."', ".$price_new_mon.", ".$price_new_tue.", ".$price_new_wed.", ".$price_new_thu.", ".$price_new_fri.", ".$price_new_sat.", ".$price_new_sun.", 0)";
			if(database_void_query($sql)){
				unset($_POST);
				return true;
			}else{
				$this->error = _TRY_LATER;
				return false;
			}
		}
	}
	
    /**
	 * Updates room availability
	 * 		@param $rid
	 */
	public function UpdateRoomAvailability($rid)
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		$ids_list = isset($_POST['ids_list']) ? prepare_input($_POST['ids_list']) : "";
		$ids_list_array = explode(",", $ids_list);

		// input validation
		$arrPrices = array();			
		$count = 0;
		foreach($ids_list_array as $key){
			
			$aval_mon = isset($_POST['aval_'.$key.'_mon']) ? "1" : "0";
			$aval_tue = isset($_POST['aval_'.$key.'_tue']) ? "1" : "0";
			$aval_wed = isset($_POST['aval_'.$key.'_wed']) ? "1" : "0";
			$aval_thu = isset($_POST['aval_'.$key.'_thu']) ? "1" : "0";
			$aval_fri = isset($_POST['aval_'.$key.'_fri']) ? "1" : "0";
			$aval_sat = isset($_POST['aval_'.$key.'_sat']) ? "1" : "0";
			$aval_sun = isset($_POST['aval_'.$key.'_sun']) ? "1" : "0";
		
			$sql = "UPDATE ".TABLE_ROOMS_AVAILABILITIES."
					SET
						".(($count == 0) ? "date_from = '0000-00-00'," : "")."
						".(($count == 0) ? "date_to = '0000-00-00'," : "")."
						mon = '".$aval_mon."',
						tue = '".$aval_tue."',
						wed = '".$aval_wed."',
						thu = '".$aval_thu."',
						fri = '".$aval_fri."',
						sat = '".$aval_sat."',
						sun = '".$aval_sun."',
						is_default = ".(($count == 0) ? "1" : "0")."
					WHERE id = ".$key." AND room_id = ".(int)$rid;
			if(!database_void_query($sql)){
				$this->error = _TRY_LATER;
				return false;
			}
			$count++;
		}
		unset($_POST);
		return true;		
	}

    /**
	 * Updates room prices
	 * 		@param $rid
	 */
	public function UpdateRoomPrices($rid)
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		$ids_list = isset($_POST['ids_list']) ? prepare_input($_POST['ids_list']) : "";
		$ids_list_array = explode(",", $ids_list);

		// input validation
		$arrPrices = array();			
		$count = 0;
		foreach($ids_list_array as $key){
			
			$price_mon = (isset($_POST['price_'.$key.'_mon']) ? prepare_input($_POST['price_'.$key.'_mon']) : "0");
			$price_tue = (isset($_POST['price_'.$key.'_tue']) ? prepare_input($_POST['price_'.$key.'_tue']) : "0");
			$price_wed = (isset($_POST['price_'.$key.'_wed']) ? prepare_input($_POST['price_'.$key.'_wed']) : "0");
			$price_thu = (isset($_POST['price_'.$key.'_thu']) ? prepare_input($_POST['price_'.$key.'_thu']) : "0");
			$price_fri = (isset($_POST['price_'.$key.'_fri']) ? prepare_input($_POST['price_'.$key.'_fri']) : "0");
			$price_sat = (isset($_POST['price_'.$key.'_sat']) ? prepare_input($_POST['price_'.$key.'_sat']) : "0");
			$price_sun = (isset($_POST['price_'.$key.'_sun']) ? prepare_input($_POST['price_'.$key.'_sun']) : "0");
			
			if(!$this->IsFloat($price_mon) || $price_mon < 0){
				$this->error = str_replace("_FIELD_", _MON, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}else if(!$this->IsFloat($price_tue) || $price_tue < 0){
				$this->error = str_replace("_FIELD_", _TUE, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}else if(!$this->IsFloat($price_wed) || $price_wed < 0){
				$this->error = str_replace("_FIELD_", _WED, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}else if(!$this->IsFloat($price_thu) || $price_thu < 0){
				$this->error = str_replace("_FIELD_", _THU, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}else if(!$this->IsFloat($price_fri) || $price_fri < 0){
				$this->error = str_replace("_FIELD_", _FRI, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}else if(!$this->IsFloat($price_sat) || $price_sat < 0){
				$this->error = str_replace("_FIELD_", _SAT, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}else if(!$this->IsFloat($price_sun) || $price_sun < 0){
				$this->error = str_replace("_FIELD_", _SUN, _FIELD_MUST_BE_NUMERIC_POSITIVE);
				return false;
			}

			$sql = "UPDATE ".TABLE_ROOMS_PRICES."
					SET
						".(($count == 0) ? "date_from = '0000-00-00'," : "")."
						".(($count == 0) ? "date_to = '0000-00-00'," : "")."
						mon = '".$price_mon."',
						tue = '".$price_tue."',
						wed = '".$price_wed."',
						thu = '".$price_thu."',
						fri = '".$price_fri."',
						sat = '".$price_sat."',
						sun = '".$price_sun."',
						is_default = ".(($count == 0) ? "1" : "0")."
					WHERE id = ".$key." AND room_id = ".(int)$rid;
			if(!database_void_query($sql)){
				$this->error = _TRY_LATER;
				return false;
			}
			$count++;
		}
		unset($_POST);
		return true;		
	}

    /**
	 * After-Insert opearation
	 */
	public function AfterInsertRecord()
	{		
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		// add room prices
		$default_price = isset($_POST['default_price']) ? prepare_input($_POST['default_price']) : "0";				
		$sql = "SELECT * FROM ".TABLE_ROOMS_PRICES." WHERE room_id = ".$this->lastInsertId;
		$room = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room[1] > 0){
			$sql = "UPDATE ".TABLE_ROOMS_PRICES."
					SET
						date_from = NULL,
						date_to   = NULL,
						mon = '".$default_price."',
						tue = '".$default_price."',
						wed = '".$default_price."',
						thu = '".$default_price."',
						fri = '".$default_price."',
						sat = '".$default_price."',
						sun = '".$default_price."',
						is_default = 1
					WHERE room_id = ".$this->lastInsertId;
			$result = database_void_query($sql);			
		}else{
			$sql = "INSERT INTO ".TABLE_ROOMS_PRICES." (id, room_id, date_from, date_to, mon, tue, wed, thu, fri, sat, sun, is_default)
					VALUES (NULL, ".$this->lastInsertId.", '0000-00-00', '0000-00-00', '".$default_price."', '".$default_price."', '".$default_price."', '".$default_price."', '".$default_price."', '".$default_price."', '".$default_price."', 1)";
			$result = database_void_query($sql);			
		}
		
		// add room availability
		$default_availability = (isset($_POST['default_availability'])) ? "1" : "0";
		$sql = "SELECT * FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE room_id = ".$this->lastInsertId;
		$room = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room[1] > 0){
			$sql = "UPDATE ".TABLE_ROOMS_AVAILABILITIES."
					SET
						date_from = NULL,
						date_to   = NULL,
						mon = '".$default_availability."',
						tue = '".$default_availability."',
						wed = '".$default_availability."',
						thu = '".$default_availability."',
						fri = '".$default_availability."',
						sat = '".$default_availability."',
						sun = '".$default_availability."',
						is_default = 1
					WHERE room_id = ".$this->lastInsertId;
			$result = database_void_query($sql);			
		}else{
			$sql = "INSERT INTO ".TABLE_ROOMS_AVAILABILITIES." (id, room_id, date_from, date_to, mon, tue, wed, thu, fri, sat, sun, is_default)
					VALUES (NULL, ".$this->lastInsertId.", '0000-00-00', '0000-00-00', '".$default_availability."', '".$default_availability."', '".$default_availability."', '".$default_availability."', '".$default_availability."', '".$default_availability."', '".$default_availability."', 1)";
			$result = database_void_query($sql);			
		}		

		$room_type 	 = (isset($_POST['room_type'])) ? prepare_input($_POST['room_type']) : "";
		$room_short_description = (isset($_POST['room_short_description'])) ? prepare_input($_POST['room_short_description']) : "";
		$room_long_description = (isset($_POST['room_long_description'])) ? prepare_input($_POST['room_long_description']) : "";
		
		// languages array		
		$total_languages = Languages::GetAllActive();
		foreach($total_languages[0] as $key => $val){			
			$sql = "INSERT INTO ".TABLE_ROOMS_DESCRIPTION."(
						id, room_id, language_id, room_type, room_short_description, room_long_description
					)VALUES(
						NULL, ".$this->lastInsertId.", '".$val['abbreviation']."', '".encode_text($room_type)."', '".encode_text($room_short_description)."', '".encode_text($room_long_description)."'
					)";
			if(!database_void_query($sql)){
				///echo mysql_error();
			}else{
				
			}		
		}		
	}	
	
    /**
	 * After-Delete opearation
	 */
	public function AfterDeleteRecord()
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		$rid = self::GetParameter('rid');

		$sql = "DELETE FROM ".TABLE_ROOMS_PRICES." WHERE room_id = ".(int)$rid;
		database_void_query($sql);	

		$sql = "DELETE FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE room_id = ".(int)$rid;
		database_void_query($sql);	

		$sql = "DELETE FROM ".TABLE_ROOMS_DESCRIPTION." WHERE room_id = ".(int)$rid;		
		database_void_query($sql);
	}
	
    /**
	 * Search available rooms
	 * 		@param $params
	 */
	public function SearchFor($params = array())
	{		
		$checkin_date 	= $params['from_year']."-".$params['from_month']."-".$params['from_day'];
		$checkout_date 	= $params['to_year']."-".$params['to_month']."-".$params['to_day'];
		$max_adults 	= isset($params['max_adults']) ? $params['max_adults'] : "";
		$max_children 	= isset($params['max_children']) ? $params['max_children'] : "";

		$objBookingSettings      = new ModulesSettings("booking");
		$show_fully_booked_rooms = $objBookingSettings->GetSettings("show_fully_booked_rooms");

    	$sql = "SELECT
					id,
					room_count
			    FROM ".TABLE_ROOMS."
				WHERE 1=1 AND 
					is_active = 1 
					".(($max_adults != "") ? "AND max_adults >= ".(int)$max_adults : "")."
					".(($max_children != "") ? "AND max_children >= ".(int)$max_children : "")."
				ORDER BY id ASC";
				
		$rooms = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		if($rooms[1] > 0){
			for($i=0; $i < $rooms[1]; $i++){
				//echo "<br />".$rooms[0][$i]['id']." ".$rooms[0][$i]['room_count'];

				$maximal_rooms = (int)$rooms[0][$i]['room_count'];
				
				$total_booked_rooms = "0";
				$sql = "SELECT
							SUM(".TABLE_BOOKINGS_ROOMS.".rooms) as total_rooms
						FROM ".TABLE_BOOKINGS."
							INNER JOIN ".TABLE_BOOKINGS_ROOMS." ON ".TABLE_BOOKINGS.".booking_number = ".TABLE_BOOKINGS_ROOMS.".booking_number
						WHERE
                            (".TABLE_BOOKINGS.".status = 1 OR ".TABLE_BOOKINGS.".status = 2) AND
							".TABLE_BOOKINGS_ROOMS.".room_id = ".(int)$rooms[0][$i]['id']." AND
							(
								('".$checkin_date."' <= checkin AND '".$checkout_date."' > checkin) 
								OR
								('".$checkin_date."' < checkout AND '".$checkout_date."' >= checkout)
								OR
								('".$checkin_date."' >= checkin  AND '".$checkout_date."' < checkout)
							)";
				$rooms_booked = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($rooms_booked[1] > 0){
					$total_booked_rooms = (int)$rooms_booked[0]['total_rooms'];
				}
				
				$available_rooms = (int)($maximal_rooms - $total_booked_rooms);
				if($available_rooms > 0){
					if($this->CheckRoomWeekDefaultAvailability($rooms[0][$i]['id'], $checkin_date, $checkout_date)){
						$this->arrAvailableRooms[] = array("id"=>$rooms[0][$i]['id'], "available_rooms"=>$available_rooms);						
					}
				}else if($show_fully_booked_rooms == "yes"){
					$this->arrAvailableRooms[] = array("id"=>$rooms[0][$i]['id'], "available_rooms"=>"0");						
				}
			}
		}
		return count($this->arrAvailableRooms);		
	}

    /**
	 * Draws search result
	 * 		@param $p
	 * 		@param $params
	 * 		@param $draw
	 */
	public function DrawSearchResult($p, $params, $draw = true)
	{		
		$currency_rate = Application::Get("currency_rate");
		
		$lang = Application::Get("lang");
		$output = "";

		//echo "<pre>";
		//print_r($this->arrAvailableRooms);
		//echo "</pre>";
		
		$rooms_total = count($this->arrAvailableRooms);
		$rooms_count = 0;
		if($rooms_total > 0)
		{
			foreach($this->arrAvailableRooms as $key)
			{
				$sql = "SELECT
							r.id,
							r.room_type,
							r.room_icon,
							IF(r.room_icon_thumb != '', r.room_icon_thumb, 'no_image.png') as room_icon_thumb,
							r.room_picture_1,
							r.room_picture_2,
							r.room_picture_3,
							CASE
								WHEN r.room_picture_1 != '' THEN r.room_picture_1
								WHEN r.room_picture_2 != '' THEN r.room_picture_2
								WHEN r.room_picture_3 != '' THEN r.room_picture_3
								ELSE ''
							END as first_room_image,
							r.max_adults,
							r.max_children, 
							r.default_price as price,
							rd.room_type as loc_room_type,
							rd.room_short_description as loc_room_short_description
						FROM ".TABLE_ROOMS." r
							INNER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON r.id = rd.room_id
						WHERE
							r.id = ".$key['id']." AND
							rd.language_id = '".$lang."'";
							
				$room = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($room[1] > 0){
					$output .= "<br />";
					$output .= "<form action='index.php?page=booking' method='post'>";
					$output .= "<input type='hidden' name='room_id' value='".$room[0]['id']."' />";
					$output .= "<input type='hidden' name='from_date' value='".$params['from_date']."' />";
					$output .= "<input type='hidden' name='to_date' value='".$params['to_date']."' />";
					$output .= "<input type='hidden' name='nights' value='".$params['nights']."' />";
					$output .= draw_token_field(true);
					$output .= "<table border='0'>";
					$output .= "<tr valign='top'>";
						$output .= "<td>";
							$output .= "<table border='0' width='420px'>";					
							$room_price = $this->GetRoomPrice($room[0]['id'], $params);							
							if(!$key['available_rooms']) $output .= "<tr><td colspan='2'><h4><a href='index.php?page=room_description&room_id=".$room[0]['id']."'>".$room[0]['loc_room_type']."</a> ("._FULLY_BOOKED.")</h4></td></tr>";
							else $output .= "<tr><td colspan='2'><h4><a href='index.php?page=room_description&room_id=".$room[0]['id']."'>".$room[0]['loc_room_type']."</a> (".$key['available_rooms']." "._ROOMS_LEFT.")</h4></td></tr>";
							$output .= "<tr><td colspan='2' height='70px'>".$room[0]['loc_room_short_description']."</td></tr>";
							$output .= "<tr><td colspan='2' nowrap height='5px'></td></tr>";
							$output .= "<tr><td colspan='2'>"._MAX_ADULTS.": ".$room[0]['max_adults'].", "._MAX_CHILDREN.": ".$room[0]['max_children']."</td></tr>";
							$output .= "<tr><td colspan='2'>"._RATE_PER_NIGHT.": ".Currencies::PriceFormat(($room_price / $currency_rate) / $params['nights'])."</td></tr>";
							if($key['available_rooms']){ 
								$output .= "<tr><td>"._ROOMS.": <select name='available_rooms' class='available_rooms_ddl'>";							
									$options = "";
									for($i = 1; $i <= $key['available_rooms']; $i++){								
										$room_price_i = $room_price * $i;
										$room_price_i_formatted = Currencies::PriceFormat(($room_price * $i) / $currency_rate);
										$options .= "<option value='".$i."-".$room_price_i."' "; 
										$options .= ($i == "0") ? "selected='selected' " : "";
										$options .= ">".$i.(($i != 0) ? " (".$room_price_i_formatted.")" : "")."</option>";
									}
									$output .= $options."</select></td>";							
									$output .= "<td align='right'></td>";
								$output .= "</tr>";								
							}
							$output .= "</table>";
						$output .= "</td>";
						$output .= "<td width='200px' align='center'>";					
							if($room[0]['first_room_image'] != "") $output .= "<a href='images/rooms_icons/".$room[0]['first_room_image']."' rel='lyteshow_".$room[0]['id']."' title='"._IMAGE." 1'>";
							$output .= "<img class='room_icon' src='images/rooms_icons/".$room[0]['room_icon_thumb']."' width='165px' alt='' />";
							if($room[0]['first_room_image'] != "") $output .= "</a>";
							
							if($room[0]['room_picture_1'] != "") $output .= "  <a href='images/rooms_icons/".$room[0]['room_picture_1']."' rel='lyteshow_".$room[0]['id']."' title='"._IMAGE." 1'></a>";					
							if($room[0]['room_picture_2'] != "") $output .= "  <a href='images/rooms_icons/".$room[0]['room_picture_2']."' rel='lyteshow_".$room[0]['id']."' title='"._IMAGE." 2'></a>";					
							if($room[0]['room_picture_3'] != "") $output .= "  <a href='images/rooms_icons/".$room[0]['room_picture_3']."' rel='lyteshow_".$room[0]['id']."' title='"._IMAGE." 3'></a>";					
							if($key['available_rooms']) $output .= "<input type='submit' class='form_button_middle' style='margin-top:10px;' value='"._BOOK_NOW."!' />";
						$output .= "</td>";
					$output .= "</tr>";
					if($rooms_count < ($rooms_total - 1)) $output .= "<tr><td colspan='2'><div class='line-hor'></div><td></tr>";
					else $output .= "<tr><td colspan='2'><br /><td></tr>";
					$output .= "</table>";
					$output .= "</form>";
				}
				$rooms_count++;
			}
		}
		if($draw){
			echo $output;
		}else{
			return $output;
		}
	}

    /**
	 * Draws room description
	 * 		@param $room_id
	 * 		@param $back_button
	 */
	public function DrawRoomDescription($room_id, $back_button = true)
	{		
		$lang = Application::Get("lang");		
		$output = "";
		
		$sql = "SELECT
					r.id,
					r.room_type,
					r.room_count,
					r.max_adults,
					r.max_children, 
					r.default_price,
					r.room_icon,
					r.room_icon_thumb,
					r.room_picture_1,
					r.room_picture_1_thumb,
					r.room_picture_2,
					r.room_picture_2_thumb,
					r.room_picture_3,
					r.room_picture_3_thumb,
					r.is_active,
					rd.room_type as loc_room_type,
					rd.room_long_description as loc_room_long_description
				FROM ".TABLE_ROOMS." r
					INNER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON r.id = rd.room_id
				WHERE
					r.id = ".(int)$room_id." AND
					rd.language_id = '".$lang."'";
					
		$room_info = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);

		$room_type 		  = isset($room_info['loc_room_type']) ? $room_info['loc_room_type'] : "";
		$room_long_description = isset($room_info['loc_room_long_description']) ? $room_info['loc_room_long_description'] : "";
		$room_count       = isset($room_info['room_count']) ? $room_info['room_count'] : "";
		$max_adults       = isset($room_info['max_adults']) ? $room_info['max_adults'] : "";
		$max_children  	  = isset($room_info['max_children']) ? $room_info['max_children'] : "";
		$default_price    = isset($room_info['default_price']) ? $room_info['default_price'] : "";
		$room_icon        = isset($room_info['room_icon']) ? $room_info['room_icon'] : "";
		$room_picture_1	  = isset($room_info['room_picture_1']) ? $room_info['room_picture_1'] : "";
		$room_picture_2	  = isset($room_info['room_picture_2']) ? $room_info['room_picture_2'] : "";
		$room_picture_3	  = isset($room_info['room_picture_3']) ? $room_info['room_picture_3'] : "";
		$room_picture_1_thumb = isset($room_info['room_picture_1_thumb']) ? $room_info['room_picture_1_thumb'] : "";
		$room_picture_2_thumb = isset($room_info['room_picture_2_thumb']) ? $room_info['room_picture_2_thumb'] : "";
		$room_picture_3_thumb = isset($room_info['room_picture_3_thumb']) ? $room_info['room_picture_3_thumb'] : "";
		$is_active		  = (isset($room_info['is_active']) && $room_info['is_active'] == 1) ? _AVAILABLE : _NOT_AVAILABLE;		

		if(count($room_info) > 0){
			$output .= "<table border='0' class='room_description'>";
			$output .= "<tr valign='top'>";
			$output .= "<td width='220px'>";
			///$output .= "  <img class='room_icon' src='images/rooms_icons/".$room_icon."' width='165px' alt='' />";
			if($room_picture_1 == "" && $room_picture_2 == "" && $room_picture_3 == ""){
				$output .= "<img class='room_icon' src='images/rooms_icons/no_image.png' width='165px' alt='' />";
			}
			if($room_picture_1 != "") $output .= " <a href='images/rooms_icons/".$room_picture_1."' rel='lyteshow' title='"._IMAGE." 1'><img class='room_icon' src='images/rooms_icons/".$room_picture_1_thumb."' width='165px' alt='' /></a><br />";
			if($room_picture_2 != "") $output .= " <a href='images/rooms_icons/".$room_picture_2."' rel='lyteshow' title='"._IMAGE." 2'><img class='room_icon' src='images/rooms_icons/".$room_picture_2_thumb."' width='165px' alt='' /></a><br />";
			if($room_picture_3 != "") $output .= " <a href='images/rooms_icons/".$room_picture_3."' rel='lyteshow' title='"._IMAGE." 3'><img class='room_icon' src='images/rooms_icons/".$room_picture_3_thumb."' width='165px' alt='' /></a><br />";
			$output .= "</td>";
			$output .= "<td>";
				$output .= "<table border='0'>";
				$output .= "<tr><td><h4>".$room_type."</h4></td></tr>";
				$output .= "<tr><td>".$room_long_description."</td></tr>";
				$output .= "<tr><td>&nbsp;</td></tr>";
				$output .= "<tr><td><b>"._COUNT.":</b> ".$room_count."</td></tr>";
				$output .= "<tr><td><b>"._MAX_ADULTS.":</b> ".$max_adults."</td></tr>";
				$output .= "<tr><td><b>"._MAX_CHILDREN.":</b> ".$max_children."</td></tr>";
				//$output .= "<tr><td><b>"._DEFAULT_PRICE.":</b> ".Currencies::PriceFormat($default_price)."</td></tr>";
				$output .= "<tr><td><b>"._AVAILABILITY.":</b> ".$is_active."</td></tr>";
				$output .= "</tr>";
				$output .= "</table>";
			$output .= "</td>";
			$output .= "</tr>";

			// draw prices table
			$output .= "<tr><td colspan='2' nowrap height='5px'><td></tr>";
			$output .= "<tr><td colspan='2'><h4>"._PRICES."</h4><td></tr>";
			$output .= "<tr><td colspan='2'>".$this->GetRoomPricesTable($room_id)."<td></tr>";
			$output .= "<tr><td colspan='2' nowrap height='10px'><td></tr>";
			
			if($back_button){
				$http_referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");
				$parse_url = parse_url($http_referer);
				$back_page = isset($parse_url['query']) ? $parse_url['query'] : "";			
				
				$output .= "<tr><td colspan='2' style='padding:0 5px' align='right'><input type='button' class='form_button' onclick=\"javascript:appGoTo('".$back_page."')\" value='"._BUTTON_BACK."' /></td></tr>";
			}
			$output .= "</table>";
			
			
		}else{
			$output .= draw_important_message(_WRONG_PARAMETER_PASSED, false);		
		}
		
		echo $output;	
	}
	
	/**
	 *	Get room price for certain periof of time
	 *		@param $room_id
	 *		@param $params
	 */
	private function GetRoomPrice($room_id, $params)
	{		
		// improve: how to make it takes defult price if not found another ?
		// make check periods for 2, 3 days?
		$debug = false;
		
		$date_from = $params["from_year"]."-".$this->ConvertToDecimal($params["from_month"])."-".$this->ConvertToDecimal($params["from_day"]);
		$date_to = $params["to_year"]."-".$this->ConvertToDecimal($params["to_month"])."-".$this->ConvertToDecimal($params["to_day"]);
		$room_default_price = $this->GetRoomDefaultPrice($room_id);
		$arr_week_default_price = $this->GetRoomWeekDefaultPrice($room_id);

		$total_price = "0";
		$offset = 0;
		while($date_from < $date_to){

			$curr_date_from = $date_from;

			$offset++;			
			$current = getdate(mktime(0,0,0,$params["from_month"],$params["from_day"]+$offset,$params["from_year"]));
			$date_from = $current['year']."-".$this->ConvertToDecimal($current['mon'])."-".$this->ConvertToDecimal($current['mday']);
			
			$curr_date_to = $date_from;
			if($debug) echo "<br> ($curr_date_from == $curr_date_to) ";
			
			$sql = "SELECT
						r.id,
						r.default_price,
						rp.mon,
						rp.tue,
						rp.wed,
						rp.thu,
						rp.fri,
						rp.sat,
						rp.sun,
						rp.sun,
						rp.is_default
					FROM ".TABLE_ROOMS." r
						INNER JOIN ".TABLE_ROOMS_PRICES." rp ON r.id = rp.room_id
					WHERE
						r.id = ".(int)$room_id." AND
						(
							(rp.date_from <= '".$curr_date_from."' AND rp.date_to = '".$curr_date_from."') OR
							(rp.date_from <= '".$curr_date_from."' AND rp.date_to >= '".$curr_date_to."')
						) AND
						rp.is_default = 0";
						
			$room_info = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
			if($room_info[1] > 0){
				$arr_week_price = $room_info[0];
				
				// calculate total sum, according to week day prices
				$start = $current_date = strtotime($curr_date_from); 
				$end = strtotime($curr_date_to); 
				while($current_date < $end) {
					// take default weekday price if weekday price is empty
					if(empty($arr_week_price[strtolower(date('D', $current_date))])){
						if($debug) echo "-".$arr_week_default_price[strtolower(date('D', $current_date))];	
						$total_price += $arr_week_default_price[strtolower(date('D', $current_date))];	
					}else{
						if($debug) echo "=".$arr_week_price[strtolower(date('D', $current_date))];
						$total_price += $arr_week_price[strtolower(date('D', $current_date))];
					}					
					$current_date = strtotime("+1 day", $current_date); 
				}				
			}else{
				// add efault (standard) price
				if($debug) echo ">".$arr_week_default_price[strtolower(date('D', strtotime($curr_date_from)))];
				$t_price = $arr_week_default_price[strtolower(date('D', strtotime($curr_date_from)))];
				if(!empty($t_price)) $total_price += $t_price;
				else $total_price += $room_default_price;
			}			
		}
        return $total_price;
	}

	/**
	 *	Returns room default price
	 *		@param $room_id
	 */
	private function GetRoomDefaultPrice($room_id)
	{
		$sql = "SELECT
					r.id,
					r.default_price,
					rp.mon,
					rp.tue,
					rp.wed,
					rp.thu,
					rp.fri,
					rp.sat,
					rp.sun,
					rp.sun,
					rp.is_default
				FROM ".TABLE_ROOMS." r
					INNER JOIN ".TABLE_ROOMS_PRICES." rp ON r.id = rp.room_id
				WHERE
					r.id = ".(int)$room_id." AND
					rp.is_default = 1";
					
		$room_info = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room_info[1] > 0){
			return $room_info[0]['mon'];
		}else{
			return $room_info[0]['default_price'];
		}
	}

	/**
	 *	Returns room week default price
	 *		@param $room_id
	 */
	private function GetRoomWeekDefaultPrice($room_id)
	{		
		$sql = "SELECT
					r.id,
					r.default_price,
					rp.mon,
					rp.tue,
					rp.wed,
					rp.thu,
					rp.fri,
					rp.sat,
					rp.sun,
					rp.sun,
					rp.is_default
				FROM ".TABLE_ROOMS." r
					INNER JOIN ".TABLE_ROOMS_PRICES." rp ON r.id = rp.room_id
				WHERE
					r.id = ".(int)$room_id." AND
					rp.is_default = 1";					
		$room_default_info = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room_default_info[1] > 0){
			return $room_default_info[0];
		}
		return array();
	}

	/**
	 *	Returns room availability for month
	 *		@param $arr_rooms
	 *		@param $year
	 *		@param $month
	 *		@param $day
	 */
	static public function GetRoomAvalibilityForWeek($arr_rooms, $year, $month, $day)
	{
		//echo "$year, $month, $day";
		$end_date = date("Y-m-d", strtotime("+7 day", strtotime($year."-".$month."-".$day)));
		$end_date = explode("-", $end_date);
		$year_end = $end_date["0"];
		$month_end = $end_date["1"];
		$day_end = $end_date["2"];
		
		$today = date("Ymd");
		$today_month = date("Ym");
				
		for($i=0; $i<count($arr_rooms); $i++){
			$arr_rooms[$i]['availability'] = array("01"=>0, "02"=>0, "03"=>0, "04"=>0, "05"=>0, "06"=>0, "07"=>0, "08"=>0, "09"=>0, "10"=>0, "11"=>0, "12"=>0, "13"=>0, "14"=>0, "15"=>0,
										           "16"=>0, "17"=>0, "18"=>0, "19"=>0, "20"=>0, "21"=>0, "22"=>0, "23"=>0, "24"=>0, "25"=>0, "26"=>0, "27"=>0, "28"=>0, "29"=>0, "30"=>0, "31"=>0);
			// exit if we in the past
			if($today_month > $year.$month) continue;

			// fill array with default availability
			// ------------------------------------
			$sql = "SELECT * FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE room_id = ".(int)$arr_rooms[$i]['id']." AND is_default = 1";
			$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
			$start = $current_date = strtotime($year."-".$month."-".$day); 
			$end = strtotime("+7 day", $current_date);
			while($current_date <= $end){
				$month_day = date("d", $current_date);
				$week_day = strtolower(date('D', $current_date));
				$end_month = strtolower(date('m', $current_date));
				if(isset($result[0][$week_day])){
					$arr_rooms[$i]['availability'][$month_day] = $result[0][$week_day];
				}else{
					$arr_rooms[$i]['availability'][$month_day] = "0";
				}
				$current_date = strtotime("+1 day", $current_date);
			}

			// fill array with pre-defined availability
			// ------------------------------------
			$sql = "SELECT ra.*
					FROM ".TABLE_ROOMS_AVAILABILITIES." ra
					WHERE
						room_id = ".(int)$arr_rooms[$i]['id']." AND
						(
							('".$year."-".$month."-".$day."' >= ra.date_from AND '".$year."-".$month."-".$day."' <= ra.date_to) OR
							('".$year_end."-".$month_end."-".$day_end."' <= ra.date_to AND '".$year_end."-".$month_end."-".$day_end."' >= ra.date_from)
						) AND
						ra.is_default = 0
					ORDER BY ra.date_from ASC";
			$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
			for($j=0; $j<$result[1]; $j++){
				$start = $current_date = strtotime($result[0][$j]['date_from']); 
				$end = strtotime($result[0][$j]['date_to']); 
				while($current_date <= $end){
					$month_day = date("d", $current_date);
					$week_day = strtolower(date('D', $current_date));												
					if(isset($result[0][$j][$week_day])){
						$arr_rooms[$i]['availability'][$month_day] = $result[0][$j][$week_day];
					}else{
						$arr_rooms[$i]['availability'][$month_day] = "0";
					}							
					$current_date = strtotime("+1 day", $current_date); 
				}				
			}
		}

		#echo "<pre>";
		#print_r($arr_rooms);
		#echo "</pre>";
				
		return $arr_rooms;
	}

	/**
	 *	Returns room availability for month
	 *		@param $arr_rooms
	 *		@param $year
	 *		@param $month
	 */
	static public function GetRoomAvalibilityForMonth($arr_rooms, $year, $month)
	{
		$today = date("Ymd");
		$today_month = date("Ym");
				
		for($i=0; $i<count($arr_rooms); $i++){
			$arr_rooms[$i]['availability'] = array("01"=>0, "02"=>0, "03"=>0, "04"=>0, "05"=>0, "06"=>0, "07"=>0, "08"=>0, "09"=>0, "10"=>0, "11"=>0, "12"=>0, "13"=>0, "14"=>0, "15"=>0,
										           "16"=>0, "17"=>0, "18"=>0, "19"=>0, "20"=>0, "21"=>0, "22"=>0, "23"=>0, "24"=>0, "25"=>0, "26"=>0, "27"=>0, "28"=>0, "29"=>0, "30"=>0, "31"=>0);

			// exit if we in the past
			if($today_month > $year.$month) continue;

			// fill array with default availability
			// ------------------------------------
			$sql = "SELECT * FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE room_id = ".(int)$arr_rooms[$i]['id']." AND is_default = 1";
			$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
			$start = $current_date = strtotime($year."-".$month."-01"); 
			$end = strtotime($year."-".$month."-".self::GetMonthLastDay($month, $year));
			while($current_date <= $end){
				$month_day = date("d", $current_date);
				$week_day = strtolower(date('D', $current_date));
				if(isset($result[0][$week_day])){
					$arr_rooms[$i]['availability'][$month_day] = $result[0][$week_day];
				}else{
					$arr_rooms[$i]['availability'][$month_day] = "0";
				}
				$current_date = strtotime("+1 day", $current_date); 
			}

			// fill array with pre-defined availability
			// ------------------------------------
			$sql = "SELECT * FROM ".TABLE_ROOMS_AVAILABILITIES." WHERE room_id = ".(int)$arr_rooms[$i]['id']." AND (SUBSTRING(date_from, 1, 7) = '".$year."-".$month."' OR SUBSTRING(date_to, 1, 7) = '".$year."-".$month."') AND is_default = 0";
			$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
			for($j=0; $j<$result[1]; $j++){					
				$start = $current_date = strtotime($result[0][$j]['date_from']); 
				$end = strtotime($result[0][$j]['date_to']);
				while($current_date <= $end){
					if(date('m', $current_date) == $month){							
						$month_day = date("d", $current_date);
						$week_day = strtolower(date('D', $current_date));												
						if(isset($result[0][$j][$week_day])){
							$arr_rooms[$i]['availability'][$month_day] = $result[0][$j][$week_day];
						}else{
							$arr_rooms[$i]['availability'][$month_day] = "0";
						}							
					}
					$current_date = strtotime("+1 day", $current_date); 
				}
				
			}				
		}

		//echo "<pre>";
		//print_r($arr_rooms);
		//echo "</pre>";
				
		return $arr_rooms;
	}
	
	/**
	 *	Returns room week default availability 
	 *		@param $room_id
	 *		@param $checkin_date
	 *		@param $checkout_date
	 */
	private function CheckRoomWeekDefaultAvailability($room_id, $checkin_date, $checkout_date)	
	{
		// calculate total sum, according to week day prices
		$start = $current_date = strtotime($checkin_date); 
		$end = strtotime($checkout_date); 
		while($current_date < $end) {
			//echo "<br />".
			$sql = "SELECT
					ra.is_default,
					".strtolower(date('D', $current_date))." as cnt 
				FROM ".TABLE_ROOMS_AVAILABILITIES." ra
				WHERE
					ra.room_id = ".(int)$room_id." AND 
					((
						ra.date_from <= '".date('Y-m-d', $current_date)."' AND   
						ra.date_to >= '".date('Y-m-d', $current_date)."' AND  
						ra.is_default = 0
					) OR (
						ra.is_default = 1
					))
				ORDER BY ra.is_default ASC";
			$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
			for($i=0; $i<$result[1]; $i++){
				///echo $i." ";
				///echo "<br />Room: ".$room_id." ".$result[0][$i]['cnt'];
				if(!$result[0][$i]['is_default'] && $result[0][$i]['cnt'] <= 0){
					///echo $room_id."1 ";
					return false;					
				}else if(!$result[0][$i]['is_default'] && $result[0][$i]['cnt'] > 0){
					break;
				}
				if($result[0][$i]['is_default'] && $result[0][$i]['cnt'] <= 0){
					///echo $room_id."2 ";
					return false;
				}
			}
			if($result[1] <= 0) return false; 			
			
			$current_date = strtotime("+1 day", $current_date); 
		}
		return true;		
	}

	/**
	 *	Convert to decimal number with leading zero
	 *  	@param $number
	 */	
	private function ConvertToDecimal($number)
	{
		return (($number < 0) ? "-" : "").((abs($number) < 10) ? "0" : "").abs($number);
	}

	/**
	 *	Get room info
	 *	  	@param $room_id
	 *	  	@param $param
	 */
	static public function GetRoomInfo($room_id, $param)
	{
		$lang = Application::Get("lang");
		
		$sql = "SELECT
					r.id,
					r.room_count,
					r.max_adults,
					r.max_children, 
					r.default_price,
					r.room_icon,					
					r.room_picture_1,
					r.room_picture_2,
					r.room_picture_3,
					r.is_active,
					rd.room_type,
					rd.room_short_description,
					rd.room_long_description
				FROM ".TABLE_ROOMS." r
					INNER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON r.id = rd.room_id
				WHERE
					r.id = ".(int)$room_id." AND
					rd.language_id = '".$lang."'";

		$room_info = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($room_info[1] > 0){
			return isset($room_info[0][$param]) ? $room_info[0][$param] : "";
		}
		return "";
	}

	/**
	 *	Returns room types default price
	 */
	static public function GetRoomTypes()
	{
		$lang = Application::Get("lang");
		$output = "";
		
		$sql = "SELECT
					r.id,
					r.room_count,
					rd.room_type,
					'' as availability
				FROM ".TABLE_ROOMS." r 
					INNER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON r.id = rd.room_id
				WHERE
					rd.language_id = '".$lang."'";

		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);

		if($result[1] > 0){
			return $result[0];
		}else{
			return "";
		}
	}

	/**
	 *	Returns last day of month
	 *		@param $month
	 *		@param $year
	 */
	static public function GetMonthLastDay($month, $year)
	{
		if(empty($month)) {
		   $month = date('m');
		}
		if(empty($year)) {
		   $year = date('Y');
		}
		$result = strtotime("{$year}-{$month}-01");
		$result = strtotime('-1 second', strtotime('+1 month', $result));
		return date('d', $result);
	}
	
	/**
	 *	Draws search availability block
	 */
	static public function DrawSearchAvailabilityBlock()
	{
		$output = "<link rel='stylesheet' type='text/css' href='templates/".Application::Get("template")."/css/calendar.css' />							
		<form action='index.php?page=check_availability' id='reservation-form' name='reservation-form' method='post'>
		".draw_token_field(true)."
			<table cellspacing='2' border='0'>
			<tr>
				<td><label>"._CHECK_IN.":</label></td>
			</tr>
			<tr>
				<td nowrap>                                        
					<select id='checkin_day' name='checkin_monthday' class='checkin_day' onchange=\"checkDateOrder(this,'checkin_monthday','checkin_year_month','checkout_monthday','checkout_year_month');updateDaySelect(this);\">
						<option class='day prompt' value='0'>"._DAY."</option>";
						$selected_day = isset($_POST['checkin_monthday']) ? prepare_input($_POST['checkin_monthday']) : date("d");
						for($i=1; $i<=31; $i++){													
							$output .= "<option value='".$i."' ".(($selected_day == $i) ? "selected='selected'" : "").">".$i."</option>";
						}
					$output .= "</select>
					<select id='checkin_year_month' name='checkin_year_month' class='checkin_year_month' onchange=\"checkDateOrder(this,'checkin_monthday','checkin_year_month','checkout_monthday','checkout_year_month');updateDaySelect(this);\">
						<option class='month prompt' value='0'>"._MONTH."</option>";
						$selected_year_month = isset($_POST['checkin_year_month']) ? prepare_input($_POST['checkin_year_month']) : date("Y-n");
						for($i=0; $i<12; $i++){
							$cur_time = mktime(0, 0, 0, date("m")+$i, "1", date("Y"));
							$val = date("Y", $cur_time)."-".(int)date("m", $cur_time);
							$output .= "<option value='".$val."' ".(($selected_year_month == $val) ? "selected='selected'" : "").">".get_month_local(date("n", $cur_time))." '".date("y", $cur_time)."</option>";
						}
					$output .= "</select>
					<a class='calender' onclick=\"showCalendar(this, 'calendar', 'checkin');\" href='javascript:void(0);'><img title='"._PICK_DATE."' alt='calendar' src='templates/".Application::Get("template")."/images/button-calender.png' width='21' height='18' /></a>
				</td>
			</tr>
			<tr>
				<td><label>"._CHECK_OUT.":</label></td>
			</tr>
			<tr>
				<td nowrap>                                        
					<select id='checkout_monthday' name='checkout_monthday' class='checkout_day' onchange=\"checkDateOrder(this,'checkout_monthday','checkout_year_month');updateDaySelect(this);\">
						<option class='day prompt' selected value='0'>"._DAY."</option>";
						$checkout_selected_day = isset($_POST['checkout_monthday']) ? prepare_input($_POST['checkout_monthday']) : date("d");
						for($i=1; $i<=31; $i++){
							$output .= "<option value='".$i."' ".(($checkout_selected_day == $i) ? "selected='selected'" : "").">".$i."</option>";
						}
					$output .= "</select>
					<select id='checkout_year_month' name='checkout_year_month' class='checkout_year_month' onchange=\"checkDateOrder(this,'checkout_monthday','checkout_year_month');updateDaySelect(this);\">
						<option class='month prompt' selected value='0'>"._MONTH."</option>";
						$checkout_selected_year_month = isset($_POST['checkout_year_month']) ? prepare_input($_POST['checkout_year_month']) : date("Y-n");
						for($i=0; $i<12; $i++){
							$cur_time = mktime(0, 0, 0, date("m")+$i, "1", date("Y"));
							$val = date("Y", $cur_time)."-".(int)date("m", $cur_time);
							$output .= "<option value='".$val."' ".(($checkout_selected_year_month == $val) ? "selected='selected'" : "").">".get_month_local(date("n", $cur_time))." '".date("y", $cur_time)."</option>";
						}
					$output .= "</select>
					<a class='calender' onclick=\"showCalendar(this, 'calendar', 'checkout');\" href='javascript:void(0);'><img title='"._PICK_DATE."' alt='calendar' src='templates/".Application::Get("template")."/images/button-calender.png' width='21' height='18' /></a>
				</td>
			</tr>
			<tr><td nowrap height='5px'></td></tr>
			<tr><td nowrap>
				"._ADULTS.":
					<select class='max_occupation' name='max_adults' id='max_adults'>";
						$max_adults = isset($_POST['max_adults']) ? (int)$_POST['max_adults'] : "1";
						for($i=1; $i<9; $i++){
							$output .= "<option value='".$i."' ".(($max_adults == $i) ? "selected='selected'" : "").">".$i."&nbsp;</option>";
						}
					$output .= "</select>&nbsp;
				"._CHILDREN.":
					<select class='max_occupation' name='max_children' id='max_children'>";
						$max_children = isset($_POST['max_children']) ? (int)$_POST['max_children'] : "0";
						for($i=0; $i<4; $i++){
							$output .= "<option value='".$i."' ".(($max_children == $i) ? "selected='selected'" : "").">".$i."&nbsp;</option>";
						}
					$output .= "</select>
				</td>
			</tr>
			<tr><td nowrap height='5px'></td></tr>
			<tr>
				<td>
					<input class='button' type='button' onclick=\"document.getElementById('reservation-form').submit()\" value='"._CHECK_AVAILABILITY."' />
				</td>
			</tr>	
			</table>
		</form>
		<div id='calendar'></div>";
		
		echo $output;		
	}
	
	/**
	 *	Draws search availability footer scripts
	 */	
	static public function DrawSearchAvailabilityFooter()
	{
		$objBookingSettings = new ModulesSettings("booking");
		$minimum_nights = $objBookingSettings->GetSettings("minimum_nights");

		$output = "<script type='text/javascript' src='templates/".Application::Get("template")."/js/calendar.js'></script>\n";
		$output .= "<script type='text/javascript'>\n";
		$output .= "var calendar = new Object();";
		$output .= "var tr = new Object();";
		$output .= "tr.nextMonth = '"._NEXT."';";
		$output .= "tr.prevMonth = '"._PREVIOUS."';";
		$output .= "tr.closeCalendar = '"._CLOSE."';";
		$output .= "tr.icons = 'templates/".Application::Get("template")."/images/';";
		$output .= "tr.iconPrevMonth2 = '".((Application::Get("defined_alignment") == "left") ? "butPrevMonth2.gif" : "butNextMonth2.gif")."';";
		$output .= "tr.iconPrevMonth = '".((Application::Get("defined_alignment") == "left") ? "butPrevMonth.gif" : "butNextMonth.gif")."';";
		$output .= "tr.iconNextMonth2 = '".((Application::Get("defined_alignment") == "left") ? "butNextMonth2.gif" : "butPrevMonth2.gif")."';";
		$output .= "tr.iconNextMonth = '".((Application::Get("defined_alignment") == "left") ? "butNextMonth.gif" : "butPrevMonth.gif")."';";
		$output .= "tr.currentDay = '".date("d")."';";
		$output .= "var minimum_nights = '".(int)$minimum_nights."';";
		$output .= "var months = ['"._JANUARY."','"._FEBRUARY."','"._MARCH."','"._APRIL."','"._MAY."','"._JUNE."','"._JULY."','"._AUGUST."','"._SEPTEMBER."','"._OCTOBER."','"._NOVEMBER."','"._DECEMBER."'];";
		$output .= "var days = ['"._MON."','"._TUE."','"._WED."','"._THU."','"._FRI."','"._SAT."','"._SUN."'];\n";
			if(!isset($_POST['checkin_monthday']) && !isset($_POST['checkin_year_month'])){ 
				$output .= "checkDateOrder(document.getElementById('checkin_day'), 'checkin_monthday', 'checkin_year_month', 'checkout_monthday', 'checkout_year_month');";
			}			
		$output .= "</script>";
		
		echo $output;
	}
	
	/**
	 *	Draw information about rooms and services
	 */	
	static public function DrawRoomsInfo()
	{
		$lang = Application::Get("lang");
		
		$sql = "SELECT
				r.id,
				r.max_adults,
				r.max_children,
				r.room_count,
				r.default_price,
				r.room_icon,
				r.room_icon_thumb,
				r.room_picture_1,
				r.room_picture_1_thumb,
				r.room_picture_2,
				r.room_picture_2_thumb,
				r.room_picture_3,
				r.room_picture_3_thumb,
				r.priority_order,
				r.is_active,
				CONCAT('<a href=\"index.php?admin=room_prices&rid=', r.id,'\" title=\""._CLICK_TO_MANAGE."\">', '[ "._PRICES." ]', '</a>') as link_prices,
				CONCAT('<a href=\"index.php?admin=room_availability&rid=', r.id,'\" title=\""._CLICK_TO_MANAGE."\">', '[ "._AVAILABILITY." ]', '</a>') as link_room_availability,
				IF(is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as my_is_active,
				CONCAT('<a href=\"index.php?admin=room_description&room_id=', r.id, '\" title=\""._CLICK_TO_MANAGE."\">[ ', '"._DESCRIPTION."', ' ]</a>') as link_room_description,
				rd.room_type,
				rd.room_short_description,
				rd.room_long_description
			FROM ".TABLE_ROOMS." r
				LEFT OUTER JOIN ".TABLE_ROOMS_DESCRIPTION." rd ON r.id = rd.room_id
			WHERE
				is_active = 1 AND 
				rd.language_id = '".$lang."'
			ORDER BY priority_order ASC";
		
		//echo $sql;
		$output = "";
		
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		for($i=0; $i<$result[1]; $i++){
			//print_r($result[0][$i]);
		#$room_type 		  = isset($room_info['loc_room_type']) ? $room_info['loc_room_type'] : "";
		#$room_long_description = isset($room_info['loc_room_long_description']) ? $room_info['loc_room_long_description'] : "";
		#$room_count       = isset($room_info['room_count']) ? $room_info['room_count'] : "";
		#$max_adults       = isset($room_info['max_adults']) ? $room_info['max_adults'] : "";
		#$max_children  	  = isset($room_info['max_children']) ? $room_info['max_children'] : "";
		#$default_price    = isset($room_info['default_price']) ? $room_info['default_price'] : "";
		#$room_picture_1	  = isset($room_info['room_picture_1']) ? $room_info['room_picture_1'] : "";
		#$room_picture_2	  = isset($room_info['room_picture_2']) ? $room_info['room_picture_2'] : "";
		#$room_picture_3	  = isset($room_info['room_picture_3']) ? $room_info['room_picture_3'] : "";
		#$room_picture_1_thumb = isset($room_info['room_picture_1_thumb']) ? $room_info['room_picture_1_thumb'] : "";
		#$room_picture_2_thumb = isset($room_info['room_picture_2_thumb']) ? $room_info['room_picture_2_thumb'] : "";
		#$room_picture_3_thumb = isset($room_info['room_picture_3_thumb']) ? $room_info['room_picture_3_thumb'] : "";
			$room_icon        = isset($result[0][$i]['room_icon']) ? $result[0][$i]['room_icon'] : "";
		    $is_active = (isset($result[0][$i]['is_active']) && $result[0][$i]['is_active'] == 1) ? _AVAILABLE : _NOT_AVAILABLE;		
	
			if($i > 0) $output .= "<div class='line-hor no_margin'></div>";

			$output .= "<table border='0'>";
			$output .= "<tr valign='top'>
							<td><h4><a href='index.php?page=room_description&room_id=".$result[0][$i]['id']."'>".$result[0][$i]['room_type']."</a></h4></td>
							<td rowspan='6'><img class='room_icon' src='images/rooms_icons/".$room_icon."' width='165px' alt='' /></td>
						</tr>";			
			$output .= "<tr><td>".$result[0][$i]['room_short_description']."</td></tr>";
			$output .= "<tr><td><b>"._COUNT.":</b> ".$result[0][$i]['room_count']."</td></tr>";
			$output .= "<tr><td><b>"._MAX_ADULTS.":</b> ".$result[0][$i]['max_adults']."</td></tr>";
			$output .= "<tr><td><b>"._MAX_CHILDREN.":</b> ".$result[0][$i]['max_children']."</td></tr>";
			//$output .= "<tr><td><b>"._DEFAULT_PRICE.":</b> ".Currencies::PriceFormat($default_price)."</td></tr>";
			$output .= "<tr><td><b>"._AVAILABILITY.":</b> ".$is_active."</td></tr>";
			$output .= "</tr>";
			$output .= "</table>";
			
		}	

		return $output;
	}
	
}
?>