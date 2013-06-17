<?php

/***
 *	Class Pages
 *  -------------- 
 *  Description : encapsulates pages properties
 *  Updated	    : 24.03.2011
 *	Written by  : ApPHP
 *	
 *	PUBLIC:				  	STATIC:				 		PRIVATE:
 * 	------------------	  	---------------     		---------------
 *	__construct			  	GetAll				   		GetMenuIdByLang
 *	__destruct            	DrawPageAccessSelectBox 	GetDataEncoded
 *	DrawTitle               UpdateMetaTags
 *	DrawText                GetPageId
 *	GetParameter
 *	GetTitle
 *	GetMenuId
 *	GetMenuLink
 *	GetKey
 *	GetId
 *	GetText
 *	PageUpdate
 *	PageCreate
 *	PageDelete
 *	MoveToTrash
 *	PageRestore
 *	CheckAccessRights
 *	CacheAllowed
 *	GetMaxOrder
 *	
 **/

class Pages {

	protected $page_key;
	protected $page_id;
	protected $page;
	protected $languageId;
	
	public $error;
	public $focusOnField;
	
	//==========================================================================
    // Class Constructor
	//		@param $page_id
	//		@param $is_active
	// 		@param $lang_id	
	//==========================================================================
	function __construct($page_id = "", $is_active = false, $lang_id = "")
	{
		$this->focusOnField = "page_title";
		$lang = ($lang_id != "") ? $lang_id : Application::Get("lang");
		
		if($page_id == "home" || $page_id == "public_home"){				
			$this->languageId = ($lang != "") ? $lang : Languages::GetDefaultLang();
		}else{
			$this->languageId = (isset($_REQUEST['language_id']) && $_REQUEST['language_id'] != "") ? prepare_input($_REQUEST['language_id']) : Languages::GetDefaultLang();
		}
		
		if($page_id != ""){
			if($page_id == "home" || $page_id == "public_home"){				
				$sql_home = "SELECT
							".TABLE_PAGES.".*,
							".TABLE_LANGUAGES.".lang_name as language_name
						FROM ".TABLE_PAGES."
							LEFT OUTER JOIN ".TABLE_LANGUAGES." ON ".TABLE_PAGES.".language_id = ".TABLE_LANGUAGES.".abbreviation
						WHERE
							".TABLE_PAGES.".is_home = 1 AND
							".TABLE_PAGES.".language_id = '".$this->languageId."'";
				$this->page = database_query($sql_home, DATA_ONLY, FIRST_ROW_ONLY);
				if(empty($this->page)){
					// create Home Page
					$sql = "INSERT INTO ".TABLE_PAGES."(
							id, page_code, language_id, content_type,
							link_url, link_target, page_key, page_title, page_text,
							menu_id, menu_link, tag_title, tag_keywords, tag_description, 
							comments_allowed, show_in_search, date_created, date_updated, finish_publishing,
							is_home, is_removed, is_published, is_system_page, system_page,
							status_changed, access_level, priority_order
						)VALUES(
							NULL, '".random_string()."', '".$this->languageId."', 'article',
							'', '', '', 'Home', '',
							'', '', '', '', '', 
							0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00',
							0, 0, 0, 0, '',
							'0000-00-00 00:00:00', 'public', 0
						)";
					if(database_void_query($sql)){
						$this->page = database_query($sql_home, DATA_ONLY, FIRST_ROW_ONLY);
					}
				}
			}else if($page_id != "" && !is_numeric($page_id)){
				$sql_home = "SELECT
							".TABLE_PAGES.".*,
							".TABLE_LANGUAGES.".lang_name as language_name
						FROM ".TABLE_PAGES."
							LEFT OUTER JOIN ".TABLE_LANGUAGES." ON ".TABLE_PAGES.".language_id = ".TABLE_LANGUAGES.".abbreviation
						WHERE
							".TABLE_PAGES.".is_system_page = 1 AND
							".TABLE_LANGUAGES.".abbreviation = '".$lang."' AND
							".TABLE_PAGES.".system_page = '".$page_id."' AND
							".TABLE_PAGES.".is_published = 1 ";
				$this->page = database_query($sql_home, DATA_ONLY, FIRST_ROW_ONLY);
			}else{
				$sql = "SELECT
							".TABLE_PAGES.".*,
							".TABLE_LANGUAGES.".lang_name as language_name,
							".TABLE_MENUS.".access_level as menu_access_level
						FROM ".TABLE_PAGES."
							LEFT OUTER JOIN ".TABLE_LANGUAGES." ON ".TABLE_PAGES.".language_id = ".TABLE_LANGUAGES.".abbreviation
							LEFT OUTER JOIN ".TABLE_MENUS." ON ".TABLE_PAGES.".menu_id = ".TABLE_MENUS.".id
						WHERE
							".TABLE_PAGES.".id = '".(int)$page_id."'";
						if($is_active){
							$sql .= " AND ".TABLE_PAGES.".is_removed = 0 ";
							$sql .= " AND ".TABLE_PAGES.".is_published = 1 ";
						}
				$this->page = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
			}
			if(empty($this->page)){
				$this->page_id = "";
			}else{
				$this->page_id = $page_id;				
			}			
		}else{
			$this->page_id = $page_id;
			$this->page['id'] = "";
			$this->page['page_code'] = "";
			$this->page['language_id'] = "";
			$this->page['content_type'] = "article";
			$this->page['link_url'] = "";
			$this->page['link_target'] = "";
			$this->page['page_key'] = "";
			$this->page['page_title'] = "";
			$this->page['page_text'] = "";
			$this->page['menu_id'] = "0";
			$this->page['menu_link'] = "";
			$this->page['tag_title'] = "";
			$this->page['tag_keywords'] = "";
			$this->page['tag_description'] = "";
			$this->page['comments_allowed'] = "0";
			$this->page['date_created'] = "";
			$this->page['date_updated'] = "";
			$this->page['finish_publishing'] = "";
			$this->page['show_in_search'] = "1";
			$this->page['is_home'] = "0";
			$this->page['is_removed'] = "0";
			$this->page['is_published'] = "0";
			$this->page['is_system_page'] = "0";
			$this->page['language_name'] = "";
			$this->page['language_id'] = "";			
			$this->page['access_level'] = "0";
			$this->page['priority_order'] = "0";
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
	 * Draw page title
	 * 		@param $additional_text
	 */
	public function DrawTitle($additional_text = "")
	{
		if(isset($this->page['page_title'])){
			$page_title = decode_text($this->page['page_title']);
			draw_title_bar($page_title, $additional_text);
		}
	}

	/**
	 * Draw page text
	 */
	public function DrawText()
	{	
		$objGallery = new GalleryAlbums();
		$objContactUs = new ContactUs();
		$objHotel = new Hotel();
		$replace_needles = 1;
		$module_page = false;		
	
		// dont show this page if it was expired
		if($this->page['finish_publishing'] != '0000-00-00' && date("Y-m-d") > $this->page['finish_publishing']){
			draw_important_message(_PAGE_EXPIRED);
			return false;				
		}

		if($this->page['content_type'] == "article" && isset($this->page['page_text'])){
			$page_text = decode_text($this->page['page_text'], false);			
			
			echo "<div class='pages_contents'>";				
				if(preg_match("/{module:gallery}/i", $page_text)){
					$module_page = true;
					$page_text = @preg_replace("/{module:gallery}/i", $objGallery->DrawGallery(), $page_text, 1);
				}
				if(preg_match_all("/{module:album=(.*?)}/i", $page_text, $matches)){
					$module_page = true;
					if(is_array($matches[1])){
						foreach($matches[1] as $key => $val){
							$val = @preg_replace("/[^A-Za-z0-9]/i", "", $val);
							$page_text = @preg_replace("/{module:album=".$val."}/i", $objGallery->DrawAlbum($val, false), $page_text, 1);			
						}						
					}
				}
				if(preg_match("/{module:contact_us}/i", $page_text)){
					$module_page = true;
					$page_text = @preg_replace("/{module:contact_us}/i", $objContactUs->DrawContactUsForm(), $page_text, 1);			
				}
				if(preg_match("/{module:about_us}/i", $page_text)){
					$module_page = true;
					$page_text = @preg_replace("/{module:about_us}/i", $objHotel->DrawAboutUs(), $page_text, 1);
				}
				if(preg_match("/{module:rooms}/i", $page_text)){
					$module_page = true;
					$page_text = @preg_replace("/{module:rooms}/i", Rooms::DrawRoomsInfo(), $page_text, 1);
				}
				
				if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) $page_text = stripslashes($page_text);
				echo $page_text;
				
				// draw comments form
				if(!$this->page['is_home'] && !$module_page){
					if(Modules::IsModuleInstalled("comments")){
						$objCommentsSettings = new ModulesSettings("comments");
						$comments_allow	= $objCommentsSettings->GetSettings("comments_allow");
						if($comments_allow == "yes" && $this->page['comments_allowed']){
							$objComments = new Comments();
							echo $objComments->DrawArticleComments($this->page['id'], $objCommentsSettings);
						}
					}
				}
			echo "</div>";
			
		}else if($this->page['content_type'] == "link" && isset($this->page['link_url'])){
			$link_url = decode_text($this->page['link_url']);
			echo "<div class='pages_contents'>";
			echo "<a href='".$link_url."'>".$link_url."</a>";
			echo "</div>";			
		}
	}
	
	/**
	 *	Return parameter
	 *		@param $param
	 */
	public function GetParameter($param = "")
	{
		$meta_array = array("tag_title", "tag_keywords", "tag_description");
		if(isset($this->page[$param])){
			if(in_array($param, $meta_array)){
				return htmlspecialchars($this->page[$param]);	
			}else{
				return $this->page[$param];	
			}			
		}else{
			return "";
		}
	}

	/**
	 * Return page title
	 */
	public function GetTitle()
	{
		if(isset($this->page['page_title'])){
			$page_title = decode_text($this->page['page_title']);
			return $page_title;
		}else return "";
	}	

	/**
	 * Returns menu ID
	 */
	public function GetMenuId()
	{
		if(isset($this->page['menu_id'])) return $this->page['menu_id'];
		return "";
	}

	/**
	 * Returns menu link
	 */
	public function GetMenuLink()
	{
		if(isset($this->page['menu_link'])){
			$menu_link = decode_text($this->page['menu_link']);
			return $menu_link;
		}else return "";
	}
	
	/**
	 * Returns page key
	 */
	public function GetKey()
	{
		return $this->page_key;
	}

	/**
	 * Returns page ID
	 */
	public function GetId()
	{
		return $this->page_id;
	}
	
	/**
	 * Returns page text
	 */
	public function GetText()
	{
		if(isset($this->page['page_text'])){
			return decode_text($this->page['page_text'], false);
		}else return "";
	}
	
	/**
	 * Return maximal order of pages
	 */
	public function GetMaxOrder($language_id = "")
	{
		$sql = "SELECT MAX(priority_order) as max_order FROM ".TABLE_PAGES." WHERE is_removed = 0 AND language_id = '".$language_id."'";
		$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
		return (isset($result['max_order']) && $result['max_order'] < 9999) ? $result['max_order'] : "9998";
	}

	/**
	 * Return record set of current page
	 *		@param $lang_id
	 *		@param $is_removed
	 */
	static public function GetAll($lang_id = "", $is_removed = "")
	{		
		$where_clause = "";
		$order_clause = "ORDER BY ".TABLE_MENUS.".menu_order ASC";

		if($lang_id != "") $where_clause .= "AND ".TABLE_PAGES.".language_id = '".$lang_id."' ";
		if($is_removed == "removed"){
			$where_clause .= "AND ".TABLE_PAGES.".is_removed = 1 ";
			$order_clause = " ORDER BY ".TABLE_PAGES.".status_changed DESC, ".TABLE_MENUS.".menu_order ASC";
		}
		else $where_clause .= "AND ".TABLE_PAGES.".is_removed != 1 ";

		$sql = "SELECT
					".TABLE_PAGES.".*,
					".TABLE_MENUS.".menu_name,
					".TABLE_LANGUAGES.".lang_name as language_name
				FROM ".TABLE_PAGES."
					LEFT OUTER JOIN ".TABLE_MENUS." ON ".TABLE_PAGES.".menu_id=".TABLE_MENUS.".id
					LEFT OUTER JOIN ".TABLE_LANGUAGES." ON ".TABLE_PAGES.".language_id = ".TABLE_LANGUAGES.".abbreviation
				WHERE
					".TABLE_PAGES.".is_home != 1
					".$where_clause."
					".$order_clause."";				
		return database_query($sql, DATA_AND_ROWS);
	}	
	
	/**
	 * Updates current page
	 *		@param $params - set of fields
	 */
	public function PageUpdate($params = array())
	{
		if(isset($this->page['id'])){
			if(strtolower(SITE_MODE) == "demo"){
				$this->error = _OPERATION_BLOCKED;
				return false;
			}else{				
			
				// Get input parameters
				if(isset($params['id']))	         $this->page['id'] = $params['id'];
				if(isset($params['content_type']))	 $this->page['content_type'] = $params['content_type'];
				if(isset($params['link_url']))	 	 $this->page['link_url'] = $params['link_url'];
				if(isset($params['link_target']))	 $this->page['link_target'] = $params['link_target'];
				if(isset($params['page_title']))	 $this->page['page_title'] = $params['page_title'];
				if(isset($params['page_key'])) 		 $this->page['page_key'] = $params['page_key'];
				if(isset($params['page_text']))		 $this->page['page_text'] = $params['page_text'];
				if(isset($params['menu_id']))		 $this->page['menu_id'] = $params['menu_id'];
				if(isset($params['menu_link']))		 $this->page['menu_link'] = $params['menu_link'];
				if(isset($params['is_published']))	 $this->page['is_published'] = $params['is_published'];
				if(isset($params['comments_allowed'])) $this->page['comments_allowed'] = $params['comments_allowed'];
				if(isset($params['show_in_search'])) $this->page['show_in_search'] = $params['show_in_search'];
				if(isset($params['date_updated']))   $this->page['date_updated'] = $params['date_updated'];
				if(isset($params['finish_publishing']))$this->page['finish_publishing'] = $params['finish_publishing'];
				if(isset($params['priority_order'])) $this->page['priority_order'] = $params['priority_order'];
				if(isset($params['access_level']))   $this->page['access_level'] = $params['access_level'];

				if(isset($params['tag_title']))   	 $this->page['tag_title'] = $params['tag_title'];
				if(isset($params['tag_keywords']))   $this->page['tag_keywords'] = $params['tag_keywords'];
				if(isset($params['tag_description']))$this->page['tag_description'] = $params['tag_description'];		
				
				// Menu link cannot be more then 30 characters
				if(($this->page_id != "home" && $this->page_id != "public_home") && (strlen($this->page['menu_link']) > 30)) {
					$this->error = _PAGE_LINK_TOO_LONG;
					return false;
				}else if($this->page['page_title'] == ""){
					$this->error = _PAGE_HEADER_EMPTY;
					return false;			
				}else if($this->page['content_type'] == "link" && $this->page['link_url'] == ""){
					$this->error = str_replace("_FIELD_", _LINK, _FIELD_CANNOT_BE_EMPTY);
					$this->focusOnField = "link_url";
					return false;					
				}else if(!check_integer($this->page['priority_order']) || $this->page['priority_order'] < 0){
					$this->error = str_replace("_FIELD_", _ORDER, _FIELD_MUST_BE_NUMERIC_POSITIVE);
					$this->focusOnField = "priority_order";
					return false;
				}else if(strlen($this->page['tag_title']) > 255){
					$msg_text = str_replace("_FIELD_", "TITLE", _FIELD_LENGTH_ALERT);
					$msg_text = str_replace("_LENGTH_", "255", $msg_text);
					$this->error = $msg_text;
					return false;
				}else if(strlen($this->page["tag_keywords"]) > 512){
					$msg_text = str_replace("_FIELD_", "KEYWORDS", _FIELD_LENGTH_ALERT);
					$msg_text = str_replace("_LENGTH_", "512", $msg_text);
					$this->error = $msg_text;
					return false;
				}else if(strlen($this->page["tag_description"]) > 512){
					$msg_text = str_replace("_FIELD_", "DESCRIPTION", _FIELD_LENGTH_ALERT);
					$msg_text = str_replace("_LENGTH_", "512", $msg_text);
					$this->error = $msg_text;
					return false;
				}

				$sql = "UPDATE ".TABLE_PAGES."
						SET
							content_type = '".$this->page['content_type']."',
							link_url     = '".encode_text($this->page['link_url'])."',
							link_target  = '".encode_text($this->page['link_target'])."',
							page_title 	 = '".encode_text($this->page['page_title'])."',
							page_key 	 = '".$this->page['page_key']."',
							page_text 	 = '".encode_text($this->page['page_text'])."',
							menu_id 	 = ".(int)$this->page['menu_id'].",
							menu_link    = '".encode_text($this->page['menu_link'])."',
							tag_title    = '".encode_text($this->page['tag_title'])."',
							tag_keywords = '".encode_text($this->page['tag_keywords'])."',
							tag_description = '".encode_text($this->page['tag_description'])."',
							comments_allowed = ".(int)$this->page['comments_allowed'].",
							show_in_search = ".(int)$this->page['show_in_search'].",
							date_updated = '".date("Y-m-d H:i:s")."',
							finish_publishing = '".$this->page['finish_publishing']."',
							is_published = ".(int)$this->page['is_published'].",
							access_level = '".$this->page['access_level']."',
							priority_order = ".(int)$this->page['priority_order']."							
						WHERE id = '".(int)$this->page['id']."'";						
				if(database_void_query($sql)){
					return true;
				}else{
					$this->error = _TRY_LATER;
					return false;
				}				
			}
		}else{
			$this->error = _PAGE_UNKNOWN;
			return false;
		}
	}
	
	/**
	 * Creates new page
	 *		@param $params - set of fields
	 *		@param $copy_to_other_langs
	 */
	public function PageCreate($params = array(), $copy_to_other_langs = "yes")
	{
		// Get input parameters
		if(isset($params['content_type']))	$this->page['content_type'] = $params['content_type'];
		if(isset($params['link_url']))		$this->page['link_url'] = $params['link_url'];
		if(isset($params['link_target']))	$this->page['link_target'] = $params['link_target'];
		if(isset($params['page_title']))	$this->page['page_title'] = $params['page_title'];
		if(isset($params['page_key']))		$this->page['page_key'] = $params['page_key'];
		if(isset($params['page_text']))		$this->page['page_text'] = $params['page_text'];
		if(isset($params['menu_id']))		$this->page['menu_id'] = $params['menu_id'];
		if(isset($params['menu_link']))		$this->page['menu_link'] = $params['menu_link'];
		if(isset($params['is_published']))  $this->page['is_published'] = $params['is_published'];
		if(isset($params['language_id'])) 	$this->page['language_id'] = $params['language_id'];
		if(isset($params['comments_allowed'])) $this->page['comments_allowed'] = $params['comments_allowed'];
		if(isset($params['show_in_search']))   $this->page['show_in_search'] = $params['show_in_search'];
		if(isset($params['priority_order']))   $this->page['priority_order'] = $params['priority_order'];
		if(isset($params['access_level']))     $this->page['access_level'] = $params['access_level'];
		if(isset($params['finish_publishing']))$this->page['finish_publishing'] = $params['finish_publishing'];

		if(isset($params['tag_title']))   	 $this->page['tag_title'] = $params['tag_title'];
		if(isset($params['tag_keywords']))   $this->page['tag_keywords'] = $params['tag_keywords'];
		if(isset($params['tag_description']))$this->page['tag_description'] = $params['tag_description'];		

		// Menu link cannot be more then 30 characters
		if (strlen($this->page['menu_link']) > 30) {
			$this->error = _PAGE_LINK_TOO_LONG;
			return false;
		}else if($this->page['page_title'] == ""){
			$this->error = _PAGE_HEADER_EMPTY;
			return false;			
		}else if($this->page['content_type'] == "link" && $this->page['link_url'] == ""){
			$this->error = str_replace("_FIELD_", _LINK, _FIELD_CANNOT_BE_EMPTY);
			$this->focusOnField = "link_url";
			return false;					
		}else if(!check_integer($this->page['priority_order']) || $this->page['priority_order'] < 0){
			$this->error = str_replace("_FIELD_", _ORDER, _FIELD_MUST_BE_NUMERIC_POSITIVE);
			$this->focusOnField = "priority_order";
			return false;			
		}else if(strlen($this->page['tag_title']) > 255){
			$msg_text = str_replace("_FIELD_", "TITLE", _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "255", $msg_text);
			$this->error = $msg_text;
			return false;
		}else if(strlen($this->page['tag_keywords']) > 512){
			$msg_text = str_replace("_FIELD_", "KEYWORDS", _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "512", $msg_text);
			$this->error = $msg_text;
			return false;
		}else if(strlen($this->page['tag_description']) > 512){
			$msg_text = str_replace("_FIELD_", "DESCRIPTION", _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "512", $msg_text);
			$this->error = $msg_text;
			return false;
		}
		
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}else{				
			if($copy_to_other_langs == "yes"){
				$total_languages = Languages::GetAllActive();
			}else{
				$total_languages = Languages::GetAll(" lang_order ASC", "", "abbreviation='".$this->page['language_id']."'");
			}
			$page_code = random_string();
			for($i = 0; $i < $total_languages[1]; $i++){					
				// Create new record
				$sql = "INSERT INTO ".TABLE_PAGES."(
						id,
						page_code,
						language_id,
						content_type,
						link_url,
						link_target,
						page_key,
						page_title,
						page_text,
						menu_id,
						menu_link,
						tag_title,
						tag_keywords,
						tag_description,
						comments_allowed,
						show_in_search,
						date_created,
						date_updated,
						finish_publishing,
						is_published,
						is_system_page,
						system_page,
						status_changed,
						access_level,
						priority_order
					)VALUES(
						NULL,
						'".$page_code."',
						'".$total_languages[0][$i]['abbreviation']."',
						'".$this->page['content_type']."',
						'".encode_text($this->page['link_url'])."',
						'".$this->page['link_target']."',
						'',
						'".encode_text($this->page['page_title'])."',
						'".encode_text($this->page['page_text'])."',
						".(int)$this->GetMenuIdByLang($this->page['menu_id'], $total_languages[0][$i]['abbreviation']).",
						'".encode_text($this->page['menu_link'])."',
						'".encode_text($this->page['tag_title'])."',
						'".encode_text($this->page['tag_keywords'])."',
						'".encode_text($this->page['tag_description'])."',
						".(int)$this->page['comments_allowed'].",
						".(int)$this->page['show_in_search'].",
						'".date("Y-m-d H:i:s")."',
						'0000-00-00 00:00:00',
						'".$this->page['finish_publishing']."',
						".(int)$this->page['is_published'].",
						0,
						'',
						'0000-00-00 00:00:00',
						'".$this->page['access_level']."',
						".(int)$this->page['priority_order']."
					)";
				if(database_void_query($sql)){				
					// Update page_key
					$last_insert_id = mysql_insert_id();
					$sql = "UPDATE ".TABLE_PAGES."
							SET page_key='".$this->page['page_key']."'
							WHERE id=".(int)$last_insert_id;
					if(database_void_query($sql)){
						// ok
					}else{
						$this->error = _TRY_LATER;
						return false;
					}
				}else{
					$this->error = _TRY_LATER;
					return false;
				}					
			}
			return true;				
		}					
	}

	/**
	 * Delete a page
	 *		@param $page_id
	 */
	public function PageDelete($page_id = "")
	{
		// check that the page exists
		if($this->page_id != ""){
			// Delete page
			if(strtolower(SITE_MODE) == "demo"){
				$this->error = _OPERATION_BLOCKED;
				return false;
			}else{
				$sql = "DELETE FROM ".TABLE_PAGES." WHERE id = '".(int)$page_id."'";
				database_void_query($sql);

				$sql = "DELETE FROM ".TABLE_COMMENTS." WHERE article_id = '".(int)$page_id."'";
				database_void_query($sql);
				return true;
			}
		}
		$this->error = _TRY_LATER;
		return false;
	}

	/**
	 * Move a page to the trash
	 */
	public function MoveToTrash()
	{
		// check that the page exists
		if($this->page_id != ""){
			// Delete page
			if(strtolower(SITE_MODE) == "demo"){
				$this->error = _OPERATION_BLOCKED;
				return false;
			}else{
				$sql = "UPDATE ".TABLE_PAGES." SET is_removed = 1, is_published = 0, status_changed = '".date("Y-m-d H:i:s")."' WHERE id = '".(int)$this->page_id."'";
				if(database_void_query($sql)){
					return true;
				}
			}
		}
		$this->error = _TRY_LATER;
		return false;
	}

	/**
	 * Restore a page from the trash (PageRestore)
	 *		@param $page_id
	 */
	public function PageRestore($page_id = "")
	{
		// check that the page exists
		if($this->page_id != ""){
			// Delete page
			if(strtolower(SITE_MODE) == "demo"){
				$this->error = _OPERATION_BLOCKED;
				return false;
			}else{
				$sql = "UPDATE ".TABLE_PAGES." SET is_removed = 0 WHERE id = '".(int)$page_id."'";
				if(database_void_query($sql)) return true;				
			}
		}
		$this->error = _TRY_LATER;
		return false;
	}

	/**
	 * Returns menu id by language
	 *		@param $menu_id
	 *		@param $lang
	 */
	private function GetMenuIdByLang($menu_id = "0", $lang = "en")
	{
		$sql = "SELECT menu_code
				FROM ".TABLE_MENUS."
				WHERE id = '".$menu_id."'";
		$menu = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
		if(!empty($menu)){
			$sql = "SELECT id
					FROM ".TABLE_MENUS."
					WHERE
						menu_code = '".$menu['menu_code']."' AND
						language_id = '".$lang."'";
			$menu_id = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
			if(!empty($menu_id)){
				return $menu_id['id'];
			}
		}
		return "0";
	}

	/**
	 * Returns data encoded
	 *		@param $string
	 */
	private function GetDataEncoded($string = "")
	{
		return $string;
	}	

	/**
	 *	Draw pages accessible dropdown menu
	 *		@param $access_level
	 */
	static public function DrawPageAccessSelectBox($access_level = "all")
	{
		echo "<select name='access_level' id='access_level'>";
			echo "<option value='public' ".(($access_level == "public") ? " selected='selected'" : "").">"._PUBLIC."</option>";
			echo "<option value='registered' ".(($access_level == "registered") ? " selected='selected'" : "").">"._REGISTERED."</option>";
		echo "</select>";		
	}	
	
	/**
	 *	Update META tags for pages
	 *		@param $params
	 *		@param $lang_id
	 */
	static public function UpdateMetaTags($params, $lang_id = "")
	{
		$tag_title 		 = isset($params['tag_title']) ? $params['tag_title'] : "";
		$tag_keywords 	 = isset($params['tag_keywords']) ? $params['tag_keywords'] : "";
		$tag_description = isset($params['tag_description']) ? $params['tag_description'] : "";
		
		$sql = "UPDATE ".TABLE_PAGES." 
				SET
					tag_title = '".mysql_real_escape_string($tag_title)."',
					tag_keywords = '".mysql_real_escape_string($tag_keywords)."',
					tag_description = '".mysql_real_escape_string($tag_description)."'					
				WHERE language_id = '".$lang_id."'";
		if(database_void_query($sql)){
			return true;
		}else{
			///$this->error = _TRY_LATER;
			return false;
		}
	}	

	/**
	 *	Return page id for spesific language
	 *		@param $pid
	 *		@param $lang
	 **/
	static public function GetPageId($pid = "", $lang = "")
	{
		if($pid != "" && $lang != ""){
			$sql = "SELECT id
					FROM ".TABLE_PAGES."
					WHERE language_id = '".$lang."' AND 
						  page_code = (SELECT page_code FROM ".TABLE_PAGES." WHERE id = ".(int)$pid.")";
			$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
			return isset($result['id']) ? $result['id'] : "";			
		}else{
			return "";	
		}		
	}

	/**
	 *	Check if this page is accessible 
	 *		@param $is_logged
	 */
	public function CheckAccessRights($is_logged)
	{		
		if(isset($this->page['menu_access_level']) && $this->page['menu_access_level'] == "registered" && !$is_logged){
			return false;
		}else if(isset($this->page['access_level']) && $this->page['access_level'] == "registered" && !$is_logged){
			return false;
		}
		return true;
	}	

	/**
	 *	Checks if the page may be cached
	 */
	public function CacheAllowed()
	{
		if(count($this->page) <= 0) return false;
		if(preg_match("/{module:contact_us}/i", $this->page['page_text']) || $this->page['comments_allowed']){
			return false;
		}
		return true;
	}	
	
}
?>