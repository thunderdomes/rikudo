<?php

/**
 * 	Class GalleryAlbums
 *  -------------- 
 *  Description : encapsulates gallery albums properties
 *	Usage       : MicroCMS, MicroBlog, HotelSite, ShoppingCart, BusinessDirectory 
 *  Updated	    : 02.06.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.6
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct             SetLibraries()
 *	__destruct
 *	SetSQLs
 *	GetAlbumInfo
 *	DrawAlbum
 *	DrawGallery
 *	AfterInsertRecord
 *	AfterUpdateRecord
 *	BeforeDeleteRecord
 *	AfterDeleteRecord
 *	
 *  1.0.6
 *  	- changed DrawAlbum
 *  	-
 *  	-
 *  	-
 *  	-
 *  1.0.5
 *      - wrong text for video items on drawing album
 *      - fixed error on showing empty video album
 *      - fixed error - text for DIV wrapper was not showing for images
 *      - added params for youtube link - &amp;hd=1&amp;autoplay=1
 *      - added check for video album icon      
 *  1.0.4
 *  	- added SetLibraries() method
 *  	- added video gallery with videobox
 *  	- fixed error with roxbox <> video albums
 *  	- added drawing thumbnail for video fields
 *  	- when album is empty or inactive DrawAlbum() returns draws empty string
 *  1.0.3
 *      - adding possibility to edit name, description in Edit Mode for all languages
 *      - added framesets for add/edit/detail modes
 *      - added new table gallery_albums_description 
 *      - added possibility to define wrapper type: table or div
 *      - added video gallery with rokbox
 *  1.0.2
 * 		- added "movable"=>true for priority_order
 * 		- added check for installed module in DrawGallery()
 * 		- added album_type field
 * 		  	INSERT INTO `microcms`.`mcms_vocabulary` (`id` ,`language_id` ,`key_value` ,`key_text` )VALUES (NULL , 'en', '_VIDEO', 'Video'), (NULL , 'es', '_VIDEO', 'Video'), (NULL , 'de', '_VIDEO', 'Video');
 * 		- improved DrawAlbum() method
 * 		- added "album_code" in View Mode
 **/


class GalleryAlbums extends MicroGrid {
	
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
	private $curAlbumCode = "";
	private $curAlbumType = "";
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{		
		global $objLogin;

		$this->SetRunningTime();

		$this->params = array();		
		if(isset($_POST['album_code']))     $this->params['album_code'] = prepare_input($_POST['album_code']);
		if(isset($_POST['album_type']))     $this->params['album_type'] = prepare_input($_POST['album_type']);
		if(isset($_POST['priority_order'])) $this->params['priority_order'] = prepare_input($_POST['priority_order']);
		if(isset($_POST['is_active']))      $this->params['is_active'] = prepare_input($_POST['is_active']); else $this->params['is_active'] = "0";
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_GALLERY_ALBUMS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->formActionURL = "index.php?admin=mod_gallery_management";
		$this->actions      = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons  = true;
		$this->allowRefresh = true;

		$this->allowLanguages = false;
		$this->languageId  	=  @$objLogin->GetPreferredLang();
		$this->WHERE_CLAUSE = ""; // WHERE...
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
			$this->arrLanguages[$val['abbreviation']] = array("lang_name"=>$val['lang_name'], "album_name"=>self::GetParameter('name_'.$val['abbreviation'], false), "album_description"=>self::GetParameter('description_'.$val['abbreviation'], false));
		}
		$sql = "SELECT id, gallery_album_id, language_id, name, description FROM ".TABLE_GALLERY_ALBUMS_DESCRIPTION." WHERE gallery_album_id = '".self::GetParameter("rid")."'";
		$albums_description = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		$sql_album_description = "";
		for($i=0; $i<$albums_description[1]; $i++){
			$sql_album_description .= "'".mysql_real_escape_string($albums_description[0][$i]['name'])."' as name_".$albums_description[0][$i]['language_id'].",";
			$sql_album_description .= "'".mysql_real_escape_string($albums_description[0][$i]['description'])."' as description_".$albums_description[0][$i]['language_id'].",";
		}
		
		// prepare album types array		
		$arr_album_types = array("images"=>_IMAGES, "video"=>_VIDEO);

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT
									ga.".$this->primaryKey.",
		                            ga.album_code,
									ga.album_type,
									UCASE(ga.album_code) as mod_album_code,
									CONCAT(UCASE(SUBSTRING(ga.album_type, 1, 1)),LCASE(SUBSTRING(ga.album_type, 2))) as mod_album_type,
									gad.name,
									gad.description,
									ga.priority_order,
									IF(ga.is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as is_active,
									CONCAT('<a href=\"index.php?admin=mod_gallery_upload_items&album=', album_code, '\">"._UPLOAD."</a> (', (SELECT COUNT(*) as cnt FROM ".TABLE_GALLERY_ALBUM_ITEMS." gi WHERE gi.album_code = ga.album_code) , ')') as link_upload_items
								FROM (".$this->tableName." ga	
									LEFT OUTER JOIN ".TABLE_GALLERY_ALBUMS_DESCRIPTION." gad ON ga.id = gad.gallery_album_id AND gad.language_id = '".$this->languageId."')";		
		// define view mode fields
		$this->arrViewModeFields = array(
			"name"  			 => array("title"=>_ALBUM_NAME, "type"=>"label", "align"=>"left", "width"=>"15%"),
			"description"    	 => array("title"=>_DESCRIPTION, "type"=>"label", "align"=>"left", "width"=>""),
			"mod_album_code"  	 => array("title"=>_ALBUM_CODE, "type"=>"label", "align"=>"center", "width"=>"12%"),
			"mod_album_type"     => array("title"=>_TYPE, "type"=>"label", "align"=>"center", "width"=>"8%"),
			"is_active"      	 => array("title"=>_ACTIVE, "type"=>"label", "align"=>"center", "width"=>"8%"),
			"priority_order" 	 => array("title"=>_ORDER, "type"=>"label", "align"=>"center", "width"=>"8%", "movable"=>true),
			"link_upload_items"  => array("title"=>_ITEMS, "type"=>"label", "align"=>"center", "width"=>"12%"),
		);
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
			"separator_general"   =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"album_code"     => array("title"=>"",           "type"=>"hidden",   "required"=>true, "readonly"=>false, "default"=>random_string(8)),
				"album_type"     => array("title"=>_TYPE,        "type"=>"enum",     "required"=>true, "readonly"=>false, "source"=>$arr_album_types),
				"priority_order" => array("title"=>_ORDER,     "type"=>"textbox",  "width"=>"50px", "maxlength"=>"3", "required"=>true, "readonly"=>false, "validation_type"=>"numeric"),
				"is_active"      => array("title"=>_ACTIVE,      "type"=>"checkbox", "readonly"=>false, "true_value"=>"1", "false_value"=>"0", "default"=>"1"),
			)
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		// define edit mode fields
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								UCASE(".$this->tableName.".album_code) as album_code,
								".$this->tableName.".album_type,
								CONCAT(UCASE(SUBSTRING(".$this->tableName.".album_type, 1, 1)),LCASE(SUBSTRING(".$this->tableName.".album_type, 2))) as mod_album_type,
								".$sql_album_description."
								".$this->tableName.".priority_order,
								".$this->tableName.".is_active,
								IF(".$this->tableName.".is_active, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as mod_is_active
							FROM ".$this->tableName."
							WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";		
		// define edit mode fields
		$this->arrEditModeFields = array(
			"separator_general"   =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"album_code"     => array("title"=>_CODE,  "type"=>"label"),
				"album_type"   => array("title"=>_TYPE,    "type"=>"enum",     "required"=>true, "readonly"=>false, "source"=>$arr_album_types),
				"priority_order" => array("title"=>_ORDER, "type"=>"textbox",  "width"=>"50px", "maxlength"=>"3", "required"=>true, "readonly"=>false, "validation_type"=>"numeric"),
				"is_active"    => array("title"=>_ACTIVE,  "type"=>"checkbox", "readonly"=>false, "true_value"=>"1", "false_value"=>"0"),
			)
		);

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------
		$this->DETAILS_MODE_SQL = $this->EDIT_MODE_SQL;
		$this->arrDetailsModeFields = array(
			"separator_general"   =>array(
				"separator_info" => array("legend"=>_GENERAL),
				"album_code"     => array("title"=>_CODE,    "type"=>"label"),
				"mod_album_type" => array("title"=>_TYPE,    "type"=>"label"),
				"priority_order" => array("title"=>_ORDER,   "type"=>"label"),
				"mod_is_active"  => array("title"=>_ACTIVE,  "type"=>"label"),
			)
		);

		foreach($this->arrLanguages as $key => $val){
		    $this->arrAddModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['lang_name']),
				"name_".$key => array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>$val['album_name'], "validation_type"=>""),
				"description_".$key => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"410px", "height"=>"90px", "required"=>false, "readonly"=>false, "default"=>$val['album_description'], "validation_type"=>"", "validation_maxlength"=>"255")
			);
		    $this->arrEditModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['lang_name']),
				"name_".$key => array("title"=>_NAME, "type"=>"textbox",  "width"=>"210px", "required"=>true, "readonly"=>false, "default"=>$val['album_name'], "validation_type"=>""),
				"description_".$key => array("title"=>_DESCRIPTION, "type"=>"textarea", "width"=>"410px", "height"=>"90px", "required"=>false, "readonly"=>false, "default"=>$val['album_description'], "validation_type"=>"", "validation_maxlength"=>"255")
			);
		    $this->arrDetailsModeFields["separator_".$key] = array(
				"separator_info" => array("legend"=>$val['lang_name']),
				"name_".$key => array("title"=>_NAME, "type"=>"label"),
				"description_".$key => array("title"=>_DESCRIPTION, "type"=>"label")
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
	 *	Set system SQLs
	 *		@param $key
	 *		@param $msg
	 */	
	public function SetSQLs($key, $msg)
	{
		if($this->debug) $this->arrSQLs[$key] = $msg;					
	}

	/**
	 *	Returns album info
	 *		@param $album_code
	 */
	public function GetAlbumInfo($album_code)
	{
		$sql = "SELECT
					ga.id,
					ga.album_code,
					ga.album_type,
					ga.priority_order,
					ga.is_active, 
					gad.name,
					gad.description
				FROM ".$this->tableName." ga	
					LEFT OUTER JOIN ".TABLE_GALLERY_ALBUMS_DESCRIPTION." gad ON ga.id = gad.gallery_album_id
				WHERE
					ga.album_code='".$album_code."' AND 
					gad.language_id = '".$this->languageId."'";		
		return database_query($sql, DATA_ONLY);
	}
	
	/**
	 *	Draws album
	 *		@param $album_code
	 */
	public function DrawAlbum($album_code = "", $return = false)
	{		
		$lang = Application::Get("lang");
		
		$output = "";

		$objGallerySettings = new ModulesSettings("gallery");
		$icon_width  	 = $objGallerySettings->GetSettings("album_icon_width");
		$icon_height 	 = $objGallerySettings->GetSettings("album_icon_height");
		$albums_per_line = $objGallerySettings->GetSettings("albums_per_line");
		$wrapper 		 = $objGallerySettings->GetSettings("wrapper");
		$image_gallery_type = $objGallerySettings->GetSettings("image_gallery_type");
		$image_gallery_rel = ($image_gallery_type == "lytebox") ? "lyteshow" : "rokbox";
		$video_gallery_type = $objGallerySettings->GetSettings("video_gallery_type");
		$video_gallery_rel = ($video_gallery_type == "videobox") ? "vidbox" : "rokbox";
		
		$sql = "SELECT
					gad.name as album_name,
					gad.description,
					IF(gai.item_file_thumb != '', gai.item_file_thumb, gai.item_file) as mod_item_file_thumb,
					ga.album_code,
					ga.album_type,
					gai.item_file,
					gai.item_file_thumb,
					gaid.item_text					
				FROM ".TABLE_GALLERY_ALBUMS." ga
					INNER JOIN ".TABLE_GALLERY_ALBUM_ITEMS." gai ON ga.album_code = gai.album_code
					LEFT OUTER JOIN ".TABLE_GALLERY_ALBUMS_DESCRIPTION." gad ON ga.id = gad.gallery_album_id AND gad.language_id = '".$lang."'
					LEFT OUTER JOIN ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." gaid ON gai.id = gaid.gallery_album_item_id AND gaid.language_id = '".$lang."'
				WHERE
					ga.album_code = '".$album_code."' AND
					ga.is_active = 1 AND
					gai.item_file != '' AND
					gai.is_active = 1
				ORDER BY ga.priority_order ASC";

		$result_items = database_query($sql, DATA_AND_ROWS);
		
		if($result_items[1] > 0){			
			$output .= draw_title_bar(_ALBUM.": ".$result_items[0][0]['album_name'], "", false);

			$output .= ($wrapper == "table") ? "<table border='0' cellspacing='5'><tr>" : "<div style='padding:5px 0px'>";
			for($i=0; $i<$result_items[1]; $i++){
				$additional_params = (preg_match("/youtube/i", $result_items[0][$i]['item_file'])) ? "&amp;hd=1&amp;autoplay=1" : "";
				if($wrapper == "table"){
					if(($i != 0) && ($i % $albums_per_line == 0)) $output .= "</tr><tr>";
					$output .= "<td valign='top'>";
					if($result_items[0][$i]['album_type'] == "video"){
						$output .= $result_items[0][$i]['item_text']."<br>";
						$output .= "<a href='".$result_items[0][$i]['item_file'].$additional_params."' rel='".$video_gallery_rel."[720 480](album".$result_items[0][$i]['album_code'].")' title=''>";
						$output .= "<img src='".(($result_items[0][$i]['item_file_thumb'] != "") ? $result_items[0][$i]['item_file_thumb'] : "images/modules_icons/gallery/video.png")."' alt='' title='' border='0' />";
						$output .= "</a>";
					}else{
						$output .= "<a href='images/gallery/".$result_items[0][$i]['item_file']."' rel='".$image_gallery_rel."[720 480](album_".$result_items[0][$i]['album_name'].")' title='".$result_items[0][$i]['item_text']."'><img src='images/gallery/".$result_items[0][$i]['mod_item_file_thumb']."' width='".$icon_width."' ".(($icon_height != "px") ? "height='".$icon_height."'" : "")." style='margin:5px 2px;' alt='' title='' border='' /></a>";
						$output .= "<div style='padding-left:3px;width:".$icon_width.";white-space:wrap;'>".($i+1).". ".substr_by_word($result_items[0][$i]['item_text'], 40, true)."</div>";
					}
					$output .= "</td>";					
				}else{
					if(($i != 0) && ($i % $albums_per_line == 0)) $output .= "<br>";
					if($result_items[0][$i]['album_type'] == "video"){
						$output .= $result_items[0][$i]['item_text']."<br>";
						$output .= "<a href='".$result_items[0][$i]['item_file'].$additional_params."' rel='".$video_gallery_rel."[720 480](album_".$result_items[0][$i]['album_name'].")' title=''><img src='images/modules_icons/gallery/video.png' alt='' title='' border='0' /></a>";
						$output .= "<br><br>";
					}else{
						$output .= "<div style='float:".Application::Get("defined_alignment").";margin-right:5px;width:".$icon_width.";white-space:wrap;'>";
						$output .= "<a href='images/gallery/".$result_items[0][$i]['item_file']."' rel='".$image_gallery_rel."[720 480](album_".$result_items[0][$i]['album_name'].")' title='".$result_items[0][$i]['item_text']."'><img src='images/gallery/".$result_items[0][$i]['mod_item_file_thumb']."' width='".$icon_width."' ".(($icon_height != "px") ? "height='".$icon_height."'" : "")." style='margin:5px;' alt='' title='' border='0' /></a><br />";
						$output .= "&nbsp; ".($i+1).". ".substr_by_word($result_items[0][$i]['item_text'], 40, true);
						$output .= "</div>";
					}
				}
			}
			$output .= ($wrapper == "table") ? "</tr></table>" : "</div>";
		}else{
			$output .= draw_title_bar(_ALBUM.": ".$album_code." ("._EMPTY.")", "", false)."<br />";
		}
	
		if($return)	return $output;
		else echo $output;		
	}
	
	/**
	 *	Draws gallery
	 */
	public function DrawGallery()
	{
		$lang = Application::Get("lang");	
		$output = "";
		
		if(!Modules::IsModuleInstalled("gallery")) return $output;

		$objGallerySettings = new ModulesSettings("gallery");
		$icon_width  = $objGallerySettings->GetSettings("album_icon_width");
		$icon_height = $objGallerySettings->GetSettings("album_icon_height");
		$albums_per_line = $objGallerySettings->GetSettings("albums_per_line");
		$image_gallery_type = $objGallerySettings->GetSettings("image_gallery_type");
		$image_gallery_rel = ($image_gallery_type == "lytebox") ? "lyteshow" : "rokbox";

		$sql = "SELECT
					ga.id,
					ga.album_code,
					ga.album_type,
					gad.name,
					gad.description
				FROM ".TABLE_GALLERY_ALBUMS." ga	
					LEFT OUTER JOIN ".TABLE_GALLERY_ALBUMS_DESCRIPTION." gad ON ga.id = gad.gallery_album_id AND gad.language_id = '".$lang."'
				WHERE
					ga.is_active = 1
				ORDER BY ga.priority_order ASC";
		$result = database_query($sql, DATA_AND_ROWS);
		if($result[1] > 0){
			$output .= "<table class='gallery_table' border='0' cellspacing='5'>";
			$output .= "<tr>";		
			for($i=0; $i<$result[1]; $i++){
				if(($i != 0) && ($i % $albums_per_line == 0)){
					$output .= "</tr><tr>";	
				}
				$output .= "<td valign='top' align='center'>";	
				$sql = "SELECT
							gai.item_file,
							gai.item_file_thumb,
							IF(gai.item_file_thumb != '', gai.item_file_thumb, gai.item_file) as mod_item_file_thumb, 
							gaid.item_text
						FROM ".TABLE_GALLERY_ALBUM_ITEMS." gai
							INNER JOIN ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." gaid ON gai.id = gaid.gallery_album_item_id AND gaid.language_id = '".$lang."'
						WHERE
							gai.item_file != '' AND
							gai.album_code = '".$result[0][$i]['album_code']."' AND
							gai.is_active = 1 
						ORDER BY gai.priority_order ASC";
				$result_items = database_query($sql, DATA_AND_ROWS);
				$gallery_icon = "";
				$gallery_links = "";
				$video_gallery_thumb = "images/modules_icons/gallery/video_album.png";
				for($j=0; $j<$result_items[1]; $j++){
					if($result[0][$i]['album_type'] == "images"){
						if($gallery_icon == "" && $result_items[0][$j]['mod_item_file_thumb'] != ""){
							$gallery_icon .= "<a href='images/gallery/".$result_items[0][$j]['item_file']."' rel='".$image_gallery_rel."[720 480](gallery_album_".$result[0][$i]['name'].")' title='".$result_items[0][$j]['item_text']."'><img src='images/gallery/".$result_items[0][$j]['mod_item_file_thumb']."' width='".$icon_width."' ".(($icon_height != "px") ? "height='".$icon_height."'" : "")." alt='' title='' border='0' /></a>";
						}else{
							$gallery_links .= "<a href='images/gallery/".$result_items[0][$j]['item_file']."' rel='".$image_gallery_rel."[720 480](gallery_album_".$result[0][$i]['name'].")' title='".$result_items[0][$j]['item_text']."'></a>";
						}						
					}else if($result[0][$i]['album_type'] == "video"){
						if($result_items[0][$j]['item_file_thumb'] != "" && @file_exists($video_gallery_thumb)) $video_gallery_thumb = $result_items[0][$j]['item_file_thumb']; 
					}
					
				}
				$output .= $gallery_icon;
				$output .= $gallery_links;
				if($result[0][$i]['album_type'] == "video"){
					$output .= "<img src='".$video_gallery_thumb."' width='".$icon_width."' ".(($icon_height != "px") ? "height='".$icon_height."'" : "")." alt='' title='' border='0' />";
					if($j == 0){
						$output .= "<br /><span title='".$result[0][$i]['description']."'><b>".$result[0][$i]['name']."</b> (".$result_items[1].")</span>";					
					}else{
						$output .= "<br /><a href='index.php?page=gallery&acode=".$result[0][$i]['album_code']."'><span title='".$result[0][$i]['description']."'><b>".$result[0][$i]['name']."</b> (".$result_items[1].")</span></a>";					
					}					
				}else{
					if($j == 0){
						$output .= "<img src='images/gallery/no_image.png' width='".$icon_width."' ".(($icon_height != "px") ? "height='".$icon_height."'" : "")." alt='' title='' border='0' />";
						$output .= "<br /><span title='".$result[0][$i]['description']."'><b>".$result[0][$i]['name']."</b> (".$result_items[1].")</span>";
					}else{
						$output .= "<br /><a href='index.php?page=gallery&acode=".$result[0][$i]['album_code']."'><span title='".$result[0][$i]['description']."'><b>".$result[0][$i]['name']."</b> (".$result_items[1].")</span></a>";					
					}					
				}
				$output .= "</td>";
			}
			$output .= "</tr>";
			$output .= "</table>";
		}
		return $output;
	}

	////////////////////////////////////////////////////////////////////
	// POST METHODS
	///////////////////////////////////////////////////////////////////
	
	/**
	 * After-Addition - add album descriptions to description table
	 */
	public function AfterInsertRecord()
	{
		$sql = "INSERT INTO ".TABLE_GALLERY_ALBUMS_DESCRIPTION."(id, gallery_album_id, language_id, name, description) VALUES ";
		$count = 0;
		foreach($this->arrLanguages as $key => $val){
			if($count > 0) $sql .= ",";
			$sql .= "(NULL, ".$this->lastInsertId.", '".$key."', '".mysql_real_escape_string($val['album_name'])."', '".mysql_real_escape_string($val['album_description'])."')";
			$count++;
		}
		if(database_void_query($sql)){
			return true;
		}else{
			return false;
		}
	}	

	/**
	 * After-Updating - update album descriptions to description table
	 */
	public function AfterUpdateRecord()
	{
		foreach($this->arrLanguages as $key => $val){
			$sql = "UPDATE ".TABLE_GALLERY_ALBUMS_DESCRIPTION."
					SET name = '".mysql_real_escape_string($val['album_name'])."',
						description = '".mysql_real_escape_string($val['album_description'])."'
					WHERE gallery_album_id = ".$this->curRecordId." AND language_id = '".$key."'";
			database_void_query($sql);
			//echo mysql_error();
		}
	}	

	/**
	 * Before-Deleting - delete album descriptions from description table
	 */
	public function BeforeDeleteRecord()
	{
		$sql = "SELECT id, album_code, album_type, priority_order, is_active  
				FROM ".TABLE_GALLERY_ALBUMS."
				WHERE id = ".$this->curRecordId;
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);		
		if($result[1] > 0){
			$this->curAlbumCode = $result[0]['album_code'];
			$this->curAlbumType = $result[0]['album_type'];
		}
		return true;
	}

	/**
	 * After-Deleting - delete album descriptions from description table
	 */
	public function AfterDeleteRecord()
	{
		$sql = "DELETE FROM ".TABLE_GALLERY_ALBUMS_DESCRIPTION." WHERE gallery_album_id = ".(int)$this->curRecordId;
		database_void_query($sql);
		
		if($this->curAlbumCode != ""){
			$sql = "SELECT id, album_code, item_file, item_file_thumb, priority_order, is_active 
					FROM ".TABLE_GALLERY_ALBUM_ITEMS."
					WHERE album_code = '".$this->curAlbumCode."'";
			$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
			if($result[1] > 0){
				for($i=0; $i < $result[1]; $i++){
					if($this->curAlbumType == "images"){					
						unlink("images/gallery/".$result[0][$i]['item_file']);
						unlink("images/gallery/".$result[0][$i]['item_file_thumb']);
					}
					$sql = "DELETE FROM ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." WHERE gallery_album_item_id = ".(int)$result[0][$i]['id'];
					database_void_query($sql);						
				}
				
				$sql = "DELETE FROM ".TABLE_GALLERY_ALBUM_ITEMS." WHERE album_code = '".$this->curAlbumCode."'";
				database_void_query($sql);
				
				return true;	
			}			
		}
		return false;			
	}
	
	/**
	 * Include style and javascript files
	 */
	static public function SetLibraries()
	{
		if(!Modules::IsModuleInstalled("gallery")) return false;

		$objGallerySettings = new ModulesSettings("gallery");
		$image_gallery_type = $objGallerySettings->GetSettings("image_gallery_type");
		$video_gallery_type = $objGallerySettings->GetSettings("video_gallery_type");
		$output = "";

		if($image_gallery_type == "lytebox"){
			$output .= "<!-- LyteBox v3.22 Author: Markus F. Hay Website: http://www.dolem.com/lytebox -->\n";
			$output .= "<link rel='stylesheet' href='modules/lytebox/css/lytebox.css' type='text/css' media='screen' />\n";
			$output .= "<script type='text/javascript' language='javascript' src='modules/lytebox/js/lytebox.js'></script>\n";
		}
		
		if($image_gallery_type == "rokbox" || $video_gallery_type == "rokbox" || $video_gallery_type == "videobox"){
			$output .= "<script type='text/javascript' src='js/mootools.js'></script>\n";
		}

		if($image_gallery_type == "rokbox" || $video_gallery_type == "rokbox"){
			$output .= "<!-- RokBox -->\n";
			$output .= "<link rel='stylesheet' href='modules/rokbox/themes/dark/rokbox-style.css' type='text/css' />\n";		
			$output .= "<link rel='stylesheet' href='modules/rokbox/themes/dark/rokbox-style-ie8.css' type='text/css' />\n";
			$output .= "<script type='text/javascript' src='modules/rokbox/rokbox.js'></script>\n";
			$output .= "<script type='text/javascript' src='modules/rokbox/rokbox-config.js'></script>\n";
		}
		
		if($video_gallery_type == "videobox"){
			$output .= "<!-- VideoBox -->\n";
			$output .= "<link rel='stylesheet' href='modules/videobox/css/videobox.css' type='text/css' />\n";		
			$output .= "<script type='text/javascript' src='modules/videobox/js/swfobject.js'></script>\n";
			$output .= "<script type='text/javascript' src='modules/videobox/js/videobox.js'></script>\n";
		}

		return $output;
	}

}
?>