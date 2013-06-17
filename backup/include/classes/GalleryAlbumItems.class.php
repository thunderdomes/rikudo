<?php

/**
 * 	Class GalleryAlbumItems
 *  -------------- 
 *  Description : encapsulates gallery album items properties
 *	Usage       : MicroCMS, MicroBlog, HotelSite, ShoppingCart, BusinessDirectory 
 *  Updated	    : 04.05.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.3
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct
 *	__destruct
 *	AfterInsertRecord
 *	AfterUpdateRecord
 *	AfterDeleteRecord
 *
 *	1.0.3
 *		- added http:// for video links 
 *		- added automaticall addition of http:// for video links 
 *		- added thumbnail for video files
 *		- added tooltip for video file field
 *		-
 *	1.0.2
 *		- changed in Details Mode thumb with normal image
 *		- added "movable"=>true for priority_order
 *		- added possibility to add/edit language descriptions on one page
 *		- added image/video switch
 *		- renamed table from gallery_images into gallery_album_items
 *
 *	
 **/


class GalleryAlbumItems extends MicroGrid {
	
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
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{		
		global $objLogin;

		$this->SetRunningTime();

		$album  = MicroGrid::GetParameter('album', false);
		$objAlbums = new GalleryAlbums();
		$album_info = $objAlbums->GetAlbumInfo($album);	

		$this->params = array();		
		if(isset($_POST['album_code'])) $this->params['album_code'] = prepare_input($_POST['album_code']);
		if(isset($_POST['priority_order'])) $this->params['priority_order'] = prepare_input($_POST['priority_order']);
		if(isset($_POST['is_active']))  $this->params['is_active'] = prepare_input($_POST['is_active']); else $this->params['is_active'] = "0";
		if($album_info[0]['album_type'] == "video"){
			if(isset($_POST['item_file'])){
				$this->params['item_file'] = prepare_input($_POST['item_file']);
				if($this->params['item_file'] != "" && !preg_match("/^http:\/\/i/", $this->params['item_file'])) $this->params['item_file'] = "http://".$this->params['item_file'];
			}
			if(isset($_POST['item_file_thumb'])) $this->params['item_file_thumb'] = prepare_input($_POST['item_file_thumb'], false, "medium");
		}
		///$this->params['language_id'] 	= MicroGrid::GetParameter('language_id');

		$objGallerySettings = new ModulesSettings("gallery");
		$icon_width  = ($objGallerySettings->GetSettings("album_icon_width") != "") ? $objGallerySettings->GetSettings("album_icon_width") : "120px";
		$icon_height = ($objGallerySettings->GetSettings("album_icon_height") != "") ? $objGallerySettings->GetSettings("album_icon_height") : "90px";
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_GALLERY_ALBUM_ITEMS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_gallery_upload_items&album=".$album;
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->languageId  	=  @$objLogin->GetPreferredLang();
		$this->WHERE_CLAUSE = "WHERE album_code = '".$album."'";		
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

		// prepare languages array		
		$total_languages = Languages::GetAllActive();
		$arr_languages   = array();
		foreach($total_languages[0] as $key => $val){
			$this->arrLanguages[$val['abbreviation']] = array("lang_name"=>$val['lang_name'], "item_text"=>self::GetParameter('item_text_'.$val['abbreviation'], false));
		}
		$sql = "SELECT id, gallery_album_item_id, language_id, item_text FROM ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." WHERE gallery_album_item_id = '".self::GetParameter("rid")."'";
		$items_description = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		$sql_item_description = "";
		for($i=0; $i<$items_description[1]; $i++){
			$sql_item_description .= "'".mysql_real_escape_string($items_description[0][$i]['item_text'])."' as item_text_".$items_description[0][$i]['language_id'].",";
		}

		$help_tooltip = "<img class='help' src='images/question_mark.png' alt='' title='Ex.: http://www.youtube.com/watch?v=5VIV8nt2KkU - or - http://localhost/php-hotel-site/my_video.wmv'>";

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT
									gi.".$this->primaryKey.",
									gi.album_code,
									gi.item_file,
									gi.item_file_thumb,
									gi.priority_order,
									IF(gi.is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as mod_is_active,
									gid.item_text
								FROM (".$this->tableName." gi	
									LEFT OUTER JOIN ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." gid ON gi.id = gid.gallery_album_item_id AND gid.language_id = '".$this->languageId."')";		
		// define view mode fields
		if($album_info[0]['album_type'] == "video"){
			$this->arrViewModeFields["item_text"]     = array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"");
			$this->arrViewModeFields["item_file"] = array("title"=>_VIDEO, "type"=>"label", "align"=>"left", "width"=>"40%");
		}else{
			$this->arrViewModeFields["item_file_thumb"] = array("title"=>_IMAGE, "type"=>"image", "align"=>"left", "width"=>"60px", "sortable"=>false, "nowrap"=>"", "visible"=>"", "image_width"=>"50px", "image_height"=>"30px", "target"=>"images/gallery/", "no_image"=>"no_image.png");
			$this->arrViewModeFields["item_text"]     = array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"");			
		}
		$this->arrViewModeFields["item_text"]     = array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>"");
		$this->arrViewModeFields["priority_order"] = array("title"=>_ORDER, "type"=>"label", "align"=>"center", "width"=>"10%", "movable"=>true);
		$this->arrViewModeFields["mod_is_active"]  = array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"10%");

		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
			"separator_general"  =>array(
				"separator_info" => array("legend"=>_GENERAL),				
				"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"60px", "maxlength"=>"3", "required"=>true, "readonly"=>false, "validation_type"=>"numeric"),
				"is_active"      => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "default"=>"1", "true_value"=>"1", "false_value"=>"0"),
				"album_code"     => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>$album),
			)
		);
		if($album_info[0]['album_type'] == "video"){
			$this->arrAddModeFields["separator_general"]["item_file"]       = array("title"=>_VIDEO." ".$help_tooltip." (http://)", "type"=>"textbox",  "width"=>"370px", "maxlength"=>"255", "required"=>false, "readonly"=>false, "validation_type"=>"");
			$this->arrAddModeFields["separator_general"]["item_file_thumb"] = array("title"=>_THUMBNAIL." (http://)", "type"=>"textbox",  "width"=>"370px", "maxlength"=>"255", "required"=>false, "readonly"=>false, "validation_type"=>"");
		}else{
			$this->arrAddModeFields["separator_general"]["item_file"] = array("title"=>_IMAGE, "type"=>"image", "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"images/gallery/", "thumbnail_create"=>true, "thumbnail_field"=>"item_file_thumb", "thumbnail_width"=>$icon_width, "thumbnail_height"=>$icon_height);
		}

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->primaryKey.",
								album_code,
								item_file,
								item_file_thumb,
								priority_order,
								is_active,
								".$sql_item_description."
								IF(is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as mod_is_active
							FROM ".$this->tableName."
							WHERE ".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
			"separator_general"  =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"60px", "maxlength"=>"3", "required"=>true, "readonly"=>false, "validation_type"=>"numeric"),
				"is_active"      => array("title"=>_ACTIVE, "type"=>"checkbox", "readonly"=>false, "true_value"=>"1", "false_value"=>"0"),
				"album_code"     => array("title"=>"", "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>$album),
			)
		);
		if($album_info[0]['album_type'] == "video"){
			$this->arrEditModeFields["separator_general"]["item_file"] = array("title"=>_VIDEO." ".$help_tooltip." (http://) ", "type"=>"textbox",  "width"=>"370px", "maxlength"=>"255", "required"=>false, "readonly"=>false, "validation_type"=>"");
			$this->arrEditModeFields["separator_general"]["item_file_thumb"] = array("title"=>_THUMBNAIL." (http://)", "type"=>"textbox",  "width"=>"370px", "maxlength"=>"255", "required"=>false, "readonly"=>false, "validation_type"=>"");
		}else{
			$this->arrEditModeFields["separator_general"]["item_file"] = array("title"=>_IMAGE, "type"=>"image", "width"=>"210px", "required"=>true, "readonly"=>false, "target"=>"images/gallery/", "thumbnail_create"=>true, "thumbnail_field"=>"item_file_thumb", "thumbnail_width"=>$icon_width, "thumbnail_height"=>$icon_height);
		}

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(
			"separator_general"  =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"priority_order" => array("title"=>_ORDER, "type"=>"label"),
				"mod_is_active"   => array("title"=>_ACTIVE, "type"=>"label"),
			)
		);
		if($album_info[0]['album_type'] == "video"){
			$this->arrDetailsModeFields["separator_general"]["item_file"] = array("title"=>_VIDEO, "type"=>"object", "width"=>"240px", "height"=>"200px");
			$this->arrDetailsModeFields["separator_general"]["item_file_thumb"] = array("title"=>_THUMBNAIL, "type"=>"label");
		}else{
			$this->arrDetailsModeFields["separator_general"]["item_file"] = array("title"=>_IMAGE, "type"=>"image", "target"=>"images/gallery/", "no_image"=>"no_image.png");
		}


		foreach($this->arrLanguages as $key => $val){
			$this->arrAddModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['lang_name']),
				"item_text_".$key => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"370px", "height"=>"70px", "required"=>false, "validation_maxlength"=>"255", "readonly"=>false, "default"=>$val['item_text'], "validation_type"=>""),
			);
		    $this->arrEditModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['lang_name']),
				"item_text_".$key => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"370px", "height"=>"70px", "required"=>false, "validation_maxlength"=>"255", "readonly"=>false, "default"=>$val['item_text'], "validation_type"=>""),
			);
		    $this->arrDetailsModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['lang_name']),
				"item_text_".$key => array("title"=>_DESCRIPTION, "type"=>"label"),
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
	
	////////////////////////////////////////////////////////////////////
	// POST METHODS
	///////////////////////////////////////////////////////////////////
	
	/**
	 * After-Addition - add banner descriptions to description table
	 */
	public function AfterInsertRecord()
	{
		$sql = "INSERT INTO ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION."(id, gallery_album_item_id, language_id, item_text) VALUES ";
		$count = 0;
		foreach($this->arrLanguages as $key => $val){
			if($count > 0) $sql .= ",";
			$sql .= "(NULL, ".$this->lastInsertId.", '".$key."', '".mysql_real_escape_string($val['item_text'])."')";
			$count++;
		}
		if(database_void_query($sql)){
			return true;
		}else{
			//echo mysql_error();			
			return false;
		}
	}	

	/**
	 * After-Updating - update album item descriptions to description table
	 */
	public function AfterUpdateRecord()
	{
		foreach($this->arrLanguages as $key => $val){
			$sql = "UPDATE ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION."
					SET item_text = '".mysql_real_escape_string($val['item_text'])."'
					WHERE gallery_album_item_id = ".$this->curRecordId." AND language_id = '".$key."'";
			database_void_query($sql);
			//echo mysql_error();
		}
	}	

	/**
	 * After-Deleting - delete album altem descriptions from description table
	 */
	public function AfterDeleteRecord()
	{
		$sql = "DELETE FROM ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." WHERE gallery_album_item_id = ".$this->curRecordId;
		if(database_void_query($sql)){
			return true;
		}else{
			return false;
		}
	}
	
}
?>