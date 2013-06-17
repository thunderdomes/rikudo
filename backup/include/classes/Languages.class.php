<?php

/**
 *	Class Languages (has differences)
 *  -------------- 
 *  Description : encapsulates languages properties
 *	Usage       : MicroCMS, HotelSite, ShoppingCart, BusinessDirectory
 *	Updated	    : 07.06.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.1
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct				GetAll					CopyDataToNewLang
 *	__destruct				GetDefaultLang			DeleteDataOfLang
 *	DrawLanguagesBar		LanguageExists          IsAlphaNum
 *	GetParameter            GetUsedToBox
 *	DrawLanguages           GetLanguageName
 *	LanguageAdd             GetAllActive 
 *	LanguageUpdate          GetAllActiveSelectBox 
 *	LanguageDelete
 *	LanguageMove
 *
 *	ChangeLog:
 *	---------
 *  1.0.1
 *  	- fixed problem with wrong swith of language
 *  	-
 *  	-
 *  	-
 *  	-
 *	
 **/

class Languages {

	private $id;
	protected $language;
	public $error;
	
	private $languages_count; 
	
	//==========================================================================
    // Class Constructor
	// 		@param $id
	//==========================================================================
	function __construct($id = "")
	{
		$this->id = $id;
		if($this->id != ""){
			$sql = "SELECT
						id, lang_name, abbreviation, lang_dir, is_default, icon_image, used_on, lang_order, is_active,
						IF(is_default = 1, '<span class=yes>"._YES."</span>', '"._NO."') as is_default_verb,
						IF(is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as is_active_verb
					FROM ".TABLE_LANGUAGES." WHERE id = '".(int)$this->id."'";
			$this->language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
		}else{
			$this->language['lang_name'] = "";
			$this->language['lang_order'] = "";
			$this->language['lang_dir'] = "";
			$this->language['abbreviation'] = "";
			$this->language['used_on'] = "";
			$this->language['is_default'] = "";
			$this->language['is_active'] = "";
			$this->language['icon_image'] = "";
		}
		
	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }


	//==========================================================================
    // Static Methods
	//==========================================================================	
	/**
	 *	Returns default language
	 */
	static public function GetDefaultLang()
	{
		$def_language = "en";
		$sql = "SELECT abbreviation FROM ".TABLE_LANGUAGES." WHERE is_default = 1";
		if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$def_language = $language['abbreviation'];					
		}
		return $def_language;
	}

	/**
	 *	Checks if language exists
	 *		@param $lang_abbrev
	 */
	static public function LanguageExists($lang_abbrev)
	{
		global $objLogin;
		
		if($objLogin->IsLoggedInAs('owner','mainadmin','admin')){
			$used_on = " AND (used_on = 'global' || used_on = 'back-end')";				
		}else{
			$used_on = " AND (used_on = 'global' || used_on = 'front-end')";
		}
		
		$sql = "SELECT abbreviation
				FROM ".TABLE_LANGUAGES."
				WHERE abbreviation = '".$lang_abbrev."' ".$used_on." AND is_active = 1";
		if(database_query($sql, ROWS_ONLY) > 0){
			return true;
		}
		return false;
	}

	/**
	 *	Returns language direction
	 *		@param $lang_abbrev
	 */
	static public function GetLanguageDirection($lang_abbrev = "")
	{
		$lang_dir = "ltr";
		$sql = "SELECT lang_dir FROM ".TABLE_LANGUAGES." WHERE abbreviation = '".$lang_abbrev."'";
		if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$lang_dir = $language['lang_dir'];					
		}
		return $lang_dir;
	}
	
	/**
	 *  Draws 'Used To' select box 
	 *  	@param $selected_item
	 */
	static public function GetUsedToBox($selected_item = "", $is_default = false)
	{
		$output  = "<select class='form_text' name='used_on' id='used_on'>";
		$output .= "<option value='global' ".(($selected_item == "global") ? "selected='selected'" : "").">"._GLOBAL."</option>";
		$output .= "<option value='front-end' ".(($selected_item == "front-end") ? "selected='selected'" : "").">Front-End</option>";
		if(!$is_default) $output .= "<option value='back-end' ".(($selected_item == "back-end") ? "selected='selected'" : "").">Back-End</option>";
		
		return $output;
	}
	
	/**
	 *	Returns language name
	 *		@param $lang_abbrev
	 */
	static public function GetLanguageName($lang_abbrev = "")
	{
		$lang_name = "English";
		$sql = "SELECT lang_name FROM ".DB_PREFIX."languages WHERE abbreviation = '".$lang_abbrev."'";
		if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$lang_name = $language['lang_name'];					
		}
		return $lang_name;
	}

	/**
	 *	Returns all array of all active languages 
	 */
	static public function GetAllActive()
	{		
		global $objLogin;
		
		if($objLogin->IsLoggedInAs('owner','mainadmin','admin')){
			$used_on = " AND (used_on = 'global' || used_on = 'back-end')";				
		}else{
			$used_on = " AND (used_on = 'global' || used_on = 'front-end')";
		}
		
		$sql = "SELECT
					id, lang_name, abbreviation, lang_dir, is_default, icon_image, used_on, lang_order, is_active,
					IF(is_default = 1, '<font color=#00a600>"._YES."</font>', '"._NO."') as is_default_verb,
					IF(is_active = 1, '<font color=#00a600>"._YES."</font>', '<font color=#a60000>"._NO."</font>') as is_active_verb
				FROM ".TABLE_LANGUAGES."
				WHERE is_active = 1 ".$used_on."
				ORDER BY lang_order ASC";			
		
		return database_query($sql, DATA_AND_ROWS);
	}

	/**
	 *	Return select box with all active languages
	 */
	static public function GetAllActiveSelectBox($exclude_lang = "")
	{
		$output = "<select name='selLanguages' id='selLanguages'>";
		$total_languages = Languages::GetAllActive();		
		foreach($total_languages[0] as $key => $val){
			if($exclude_lang != ""){
				if($exclude_lang != $val['abbreviation']){
					$output .= "<option value='".$val['abbreviation']."'>".$val['lang_name']." &raquo; ".strtoupper($exclude_lang)."</option>";
				}
			}else{
				$output .= "<option value='".$val['abbreviation']."'>".$val['lang_name']."</option>";
			}
		}
		$output .= "</select>";
		return $output;		
	}	

	/***
	 *	Returns array of all languages 
	 *		@param $order - order clause
	 *		@param $join_table - join tables
	 *		@param $where
	 **/
	static public function GetAll($order = " lang_order ASC", $join_table = "", $where = "")
	{		
		$where_clause = ($where != "") ? " AND ".$where : "";
		
		// Build ORDER BY CLAUSE
		if($order=="")$order_clause = "";
		else $order_clause = "ORDER BY $order";		

		// Build JOIN clause
		$join_clause = "";
		$join_select_fields = "";
		
		$sql = "SELECT
					id, lang_name, abbreviation, lang_dir, is_default, icon_image, used_on, lang_order, is_active,
					IF(is_default = 1, '<span class=yes>"._YES."</span>', '"._NO."') as is_default_verb,
					IF(is_active = 1, '<span class=yes>"._YES."</span>', '<span class=no>"._NO."</span>') as is_active_verb
				FROM ".TABLE_LANGUAGES."
					$join_clause
					WHERE 1=1 $where_clause
				$order_clause";			
		
		return database_query($sql, DATA_AND_ROWS);
	}


	//==========================================================================
    // Public Methods
	//==========================================================================
	/**
	 *	Returns languages count
	 */
	public function GetLanguagesCount()
	{
		$sql = "SELECT COUNT(*) as cnt FROM ".TABLE_LANGUAGES." WHERE is_active = 1";
		$language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);        
		return $language['cnt'];
	}

	/**
	 *	Draws languages bar
	 */
	public function DrawLanguagesBar()
	{
		global $objLogin;
		
		$lang = Application::Get("lang");		
		$allow_opacity = true;
		$opacity = "";
		
		$all_languages = Languages::GetAll(" lang_order ASC", "", "is_active = 1 AND (used_on = 'global' OR used_on = 'front-end')");
		if($all_languages > 0){
			for($i=0; $i < $all_languages[1]; $i++){
				$url = get_page_url();
				if(!@$objLogin->IsLoggedInAsAdmin()){
					// prevent wrong re-loading for some problematic cases
					#-------------------------------------------
					# for HotelSite
					#-------------------------------------------
					 $url = str_replace(array("page=booking_payment"), "page=booking_checkout", $url);
					 $url = str_replace(array("page=check_availability"), "page=index", $url);				
					#-------------------------------------------
					# for ShoppingCart
					#-------------------------------------------
					# $url = str_replace(array("&act=add", "&act=remove"), "", $url);
					# $url = str_replace(array("page=order_proccess"), "page=checkout", $url);
					# $url = str_replace(array("page=search"), "page=index", $url);				

					if(preg_match("/lang=".$lang."/i", $url)){
						$url = str_replace("lang=".$lang, "lang=".$all_languages[0][$i]['abbreviation'], $url);						
					}else{
						$url .= (preg_match("/\?/", $url) ? "&" : "?")."lang=".$all_languages[0][$i]['abbreviation'];						
					}
				}
				if($allow_opacity = true){
					if($lang == $all_languages[0][$i]['abbreviation']){
						$opacity = " class='opacity_on'";
					}else{
						$opacity = " class='opacity' onmouseover='opacity_onmouseover(this)' onmouseout='opacity_onmouseout(this)'";
					}
				}
				echo "<a href='".$url."' title='".decode_text($all_languages[0][$i]['lang_name'])."'>".(file_exists("images/flags/".$all_languages[0][$i]['icon_image']) ? "<img src=\"images/flags/".$all_languages[0][$i]['icon_image']."\" border=\"0\" title=\"".decode_text($all_languages[0][$i]['lang_name'])."\" alt=\"".decode_text($all_languages[0][$i]['lang_name'])."\"".$opacity."/>" : $all_languages[0][$i]['abbreviation'])."</a> ";
			}
		}
	}

	/**
	 *	Returns language parameter
	 *		@param $param
	 */
	public function GetParameter($param = "")
	{
		if(isset($this->language[$param])){
			return $this->language[$param];
		}else{
			return "";
		}
	}

	/**
	 *	Draw table of languages
	 */
	public function DrawLanguages()
	{
		#echo "<pre>";
		#print_r($this->language);
		#echo "</pre>";
		$all_languages = Languages::GetAll();
		
		if($all_languages > 0){
			echo "<script type='text/javascript'>
			<!--
				function confirmDelete(lid, lo){
					if(!confirm('"._LANG_DELETE_WARNING."')){
						false;
					}else{
						document.location.href = 'index.php?admin=languages&act=delete&lid='+lid+'&lo='+lo;
					}			
				}
			//-->
			</script>";
			
			echo "<table width='99%' border='0' cellspacing='0' cellpadding='2' class='main_text'>
			      <tr><td align='left'><input class='form_button' type='button' name='btnAddNewLang' value='"._LANGUAGE_ADD_NEW."' onclick=\"javascript:document.location.href='index.php?admin=languages_add'\"></td></tr>
				  <tr><td></td></tr>
			      </table>";
			
			echo "<table width='99%' border='0' cellspacing='0' cellpadding='2' class='main_text'>
				<tr>
					<th align='".Application::Get("defined_left")."'>"._LANGUAGE_NAME."</th>
					<th width='12%'>"._ICON_IMAGE."</th>
					<th width='12%'>"._HDR_TEXT_DIRECTION."</th>
					<th width='11%'>"._ORDER."</th>
					<th width='13%'>"._CHANGE_ORDER."</th>
					<th width='10%'>"._USED_ON."</th>
					<th width='10%'>"._IS_DEFAULT."</th>
					<th width='10%'>"._ACTIVE."</th>
					<th width='7%'>"._ACTIONS."</th>
				</tr>
				<tr><td colspan='9' height='3px' nowrap='nowrap'>".draw_line("no_margin_line", IMAGE_DIRECTORY, false)."</td></tr>";
		
				for($i=0; $i < $all_languages[1]; $i++){
					echo "<tr ".highlight(0)." onmouseover='oldColor=this.style.backgroundColor;this.style.backgroundColor=\"#e1e1e1\";' onmouseout='this.style.backgroundColor=oldColor'>
							<td align='".Application::Get("defined_left")."'>".$all_languages[0][$i]['lang_name']." (".strtoupper($all_languages[0][$i]['abbreviation']).")</td>
							<td align='center'>".(file_exists("images/flags/".$all_languages[0][$i]['icon_image']) ? "<img src='images/flags/".$all_languages[0][$i]['icon_image']."' border='0' />" : "No Image")."</td>
							<td align='center'>".strtoupper($all_languages[0][$i]['lang_dir'])."</td>							
							<td align='center'>".(int)$all_languages[0][$i]['lang_order']."</td>
							<td align='center'>
								<a href='index.php?admin=languages&act=move&lid=".$all_languages[0][$i]['id']."&lo=".$all_languages[0][$i]['lang_order']."&dir=up'>"._UP."</a>/<a href='index.php?admin=languages&act=move&lid=".$all_languages[0][$i]['id']."&lo=".$all_languages[0][$i]['lang_order']."&dir=down'>"._DOWN."</a>
							</td>
							<td align='center'>".ucfirst($all_languages[0][$i]['used_on'])."</td>
							<td align='center'>".$all_languages[0][$i]['is_default_verb']."</td>
							<td align='center'>".$all_languages[0][$i]['is_active_verb']."</td>
							<td align='center' nowrap>
								<a href='index.php?admin=languages_edit&lid=".$all_languages[0][$i]['id']."' title='"._CLICK_TO_EDIT."'>"._EDIT_WORD."</a>&nbsp;".get_divider()."&nbsp;<a href='javascript:confirmDelete(\"".$all_languages[0][$i]['id']."\", \"".$all_languages[0][$i]['lang_order']."\");'>"._DELETE_WORD."</a>
							</td>
						</tr>";
				}
			echo "</table>";
		}
	}

	/**
	 *	Adds new language
	 *		@param $param - array of parameters
	 */
	public function LanguageAdd($params = array())
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		// Get input parameters
		if(!isset($params['lang_name'])) 	$params['lang_name'] = "";
		if(!isset($params['lang_dir']))     $params['lang_dir'] = "";
		if(!isset($params['lang_order'])) 	$params['lang_order'] = "";
		if(!isset($params['abbreviation'])) $params['abbreviation'] = "";
		if(!isset($params['icon_image'])) 	$params['icon_image'] = "";
		if(!isset($params['used_on'])) 	    $params['used_on'] = "global";
		if(!isset($params['is_default'])) 	$params['is_default'] = "0";
		if(!isset($params['is_active'])) 	$params['is_active'] = "0";

		// Prevent creating of empty records in our 'languages' table
		if($params['lang_name'] == ""){
			$this->error = _LANG_NAME_EMPTY;
			return false;
		}else if(!$this->IsAlphaNum($params['lang_name'])){
			$this->error = str_replace("_FIELD_", _LANGUAGE_NAME, _FIELD_MUST_BE_ALPHA_NUMERIC);
			return false;
		}else if($params['abbreviation'] == ""){
			$this->error = _LANG_ABBREV_EMPTY;
			return false;			
		}else if(strlen($params['abbreviation']) < 2){
			$msg_text = str_replace("_FIELD_", _ABBREVIATION, _FIELD_MIN_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "2", $msg_text);
			$this->error = $msg_text;
			return false;			
		}else if(!$this->IsAlphaNum($params['abbreviation'])){
			$this->error = str_replace("_FIELD_", _ABBREVIATION, _FIELD_MUST_BE_ALPHA_NUMERIC);
			return false;			
		}

		// check if such language already exists
		$sql = "SELECT * FROM ".TABLE_LANGUAGES." WHERE lang_name = '".encode_text($params['lang_name'])."' OR abbreviation = '".encode_text($params['abbreviation'])."'";
		if(database_query($sql, ROWS_ONLY) > 0){				
			$this->error = _LANG_NAME_EXISTS;
			return false;			
		}

		$m = Languages::GetAll();
		$max_order = (int)($m[1]+1);

		$sql = "INSERT INTO ".TABLE_LANGUAGES." (
					lang_name,
					abbreviation,
					lang_dir,
					icon_image,
					lang_order,
					used_on,
					is_default,
					is_active
				)VALUES(
					'".ucfirst(encode_text($params['lang_name']))."',
				    '".strtolower(encode_text($params['abbreviation']))."',
					'".$params['lang_dir']."',
					'".$params['icon_image']."',
					".$max_order.",
					'".$params['used_on']."',
					".(int)$params['is_default'].",
					".(int)$params['is_active']."
				)";
		if(!database_void_query($sql)){
			$this->error = _TRY_LATER;
			return false;
		}else{			
			$sql = "SELECT * FROM ".TABLE_LANGUAGES." WHERE lang_order = ".$max_order;                    
			if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){                        
			    $this->id = $language['id'];
			    $this->LanguageUpdate($params);

				// define previous default language
				$sql = "SELECT * FROM ".TABLE_LANGUAGES." WHERE is_default = 1 AND id != ".$language['id'];
				$previous_default = "en";				
				if($pd_language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
					$previous_default = $pd_language['abbreviation'];					
				}
				// clone data from default language 
				$this->CopyDataToNewLang($previous_default, $language['abbreviation']);				
				
				// set default  = 0 for other languages
				if($params['is_default'] == "1"){
					$sql = "UPDATE ".TABLE_LANGUAGES." SET is_default = 0 WHERE id != ".$language['id'];                    
					database_void_query($sql);					
				}				
			}
			return true;
		}
	}

	/**
	 *	Updates language
	 *		@param $param - array of parameters
	 */
	public function LanguageUpdate($params = array())
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		// Get input parameters
		if(!isset($params['id'])) 			$params['id'] = "0";
		if(!isset($params['lang_name'])) 	$params['lang_name'] = "";
		if(!isset($params['lang_dir']))     $params['lang_dir'] = "";
		if(!isset($params['lang_order'])) 	$params['lang_order'] = "";
		if(!isset($params['abbreviation'])) $params['abbreviation'] = "";
		if(!isset($params['icon_image'])) 	$params['icon_image'] = "";
		if(!isset($params['used_on'])) 	    $params['used_on'] = "global";
		if(!isset($params['is_default'])) 	$params['is_default'] = "0";

		// Prevent creating of empty records in our 'languages' table
		if($params['lang_name'] == ""){
			$this->error = _LANG_NAME_EMPTY;
			return false;
		}else if($params['abbreviation'] == ""){
			$this->error = _LANG_ABBREV_EMPTY;
			return false;			
		}

		// check if such language already exists
		$sql = "SELECT *
				FROM ".TABLE_LANGUAGES."
				WHERE
					(lang_name = '".encode_text($params['lang_name'])."' OR abbreviation = '".encode_text($params['abbreviation'])."') AND
					id != ".(int)$params['id'];
		if(database_query($sql, ROWS_ONLY) > 0){				
			$this->error = _LANG_NAME_EXISTS;
			return false;			
		}
		
		$sql = "SELECT MIN(lang_order) as min_order, MAX(lang_order) as max_order FROM ".TABLE_LANGUAGES;
		if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$min_order = $language['min_order'];
			$max_order = $language['max_order'];	

			// insert language with new priority order in languages list
			$sql = "SELECT lang_order FROM ".TABLE_LANGUAGES." WHERE id = ".(int)$this->id;
			if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
				$sql_down = "UPDATE ".TABLE_LANGUAGES." SET lang_order = lang_order - 1 WHERE id <> ".(int)$this->id." AND lang_order <= ".$params['lang_order']." AND lang_order > ".$language['lang_order'];
				$sql_up   = "UPDATE ".TABLE_LANGUAGES." SET lang_order = lang_order + 1 WHERE id <> ".(int)$this->id." AND lang_order >= ".$params['lang_order']." AND lang_order < ".$language['lang_order'];

				if($language['lang_order'] != $params['lang_order']){
					if($params['lang_order'] == $min_order){
						$sql = $sql_up;
					}else if($params['lang_order'] == $max_order){
						$sql = $sql_down;
					}else{
						if($language['lang_order'] < $params['lang_order']) $sql = $sql_down;
						else $sql = $sql_up;
					}
					$result = database_void_query($sql);
				}
				
				$sql = "UPDATE ".TABLE_LANGUAGES." SET
							lang_name = '".ucfirst(encode_text($params['lang_name']))."',
							lang_dir  = '".encode_text($params['lang_dir'])."',
							lang_order = '".encode_text($params['lang_order'])."',
							is_active = '".(int)$params['is_active']."', 
							abbreviation = '".strtolower(encode_text($params['abbreviation']))."',
							icon_image = '".encode_text($params['icon_image'])."',
							used_on = '".encode_text($params['used_on'])."'
						WHERE id = ".(int)$this->id;
				$result = database_void_query($sql);
				
				// set defaulr language
                if($params['is_default'] == "1"){					
					$sql = "UPDATE ".TABLE_LANGUAGES." SET is_active = 1, is_default = IF(id = ".(int)$this->id.", 1, 0)";
					$result = database_void_query($sql);
				}				
			}
		}
	
		if($result){
			return true;
		}else{
			$this->error = _TRY_LATER;
			return false;
		}				
	}

	/**
	 *	Delete language 
	 *		@param $lang_id - language ID
	 *		@param $lang_order
	 *		Returns: boolean
	 */
	public function LanguageDelete($lang_id="0", $lang_order="0")
	{
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		$all_languages = Languages::GetAll();
		$languages_count = $all_languages[1];

		if($languages_count <= 1){
			$this->error = _LANG_DELETE_LAST_ERROR;			
			return false;						
		}else{
			// define language's abbreviation
			$sql = "SELECT * FROM ".TABLE_LANGUAGES." WHERE id = ".(int)$lang_id;
			$language_abbrev = "";				
			if($d_language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
				$language_abbrev = $d_language['abbreviation'];					
			}
					
			$sql = "DELETE FROM ".TABLE_LANGUAGES." WHERE id = ".(int)$lang_id."";
			if(database_void_query($sql)){
				$sql = "UPDATE ".TABLE_LANGUAGES." SET lang_order = lang_order - 1 WHERE lang_order > ".(int)$lang_order."";
				if(database_void_query($sql) >= 0){
					// there is the last
					if($languages_count == 2){ 
						$sql = "UPDATE ".TABLE_LANGUAGES." SET is_default = 1";
						database_void_query($sql);					
					}
					$this->DeleteDataOfLang($language_abbrev);					
					return true;    
				}
			}
			$this->error = _LANG_NOT_DELETED;			
			return false;			
		}		
	}
	
	/**
	 *	Moves language (changes priority order)
	 *		@param $lang_id
	 *		@param $dir - direction
	 *		@param $lang_order  - menu order
	 */
	public function LanguageMove($lang_id, $dir = "", $lang_order = "")
	{		
		// Block operation in demo mode
		if(strtolower(SITE_MODE) == "demo"){ 
			$this->error = _OPERATION_BLOCKED;
			return false;
		}

		if(($dir == "") || ($lang_order == "")) return false;		

		$sql = "SELECT * FROM ".TABLE_LANGUAGES."
				WHERE id <> '".(int)$lang_id."' AND lang_order ".(($dir == "up") ? "<" : ">")." ".(int)$lang_order."
				ORDER BY lang_order ".(($dir == "up") ? "DESC" : "ASC");
        if($language = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$sql = "UPDATE ".TABLE_LANGUAGES." SET lang_order = '".$lang_order."' WHERE id = ".(int)$language['id']."";
			if(database_void_query($sql)){
				$sql = "UPDATE ".TABLE_LANGUAGES." SET lang_order = '".$language['lang_order']."' WHERE id = ".(int)$lang_id."";				
				if(!database_void_query($sql)){
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

	//==========================================================================
    // Private Methods
	//==========================================================================	
	/**
	 * Alpha Numeric Validation
	 * 		@param $field
	 */
	private function IsAlphaNum($field = "")
	{
		if(!preg_match("/\'|\"/", $field)){
		   return true;
		}else{
		   return false;
		}		
	}

	/**
	 *	Copies default data for new language
	 *		@param $from_abbrev
	 *		@param $to_abbrev
	 */
	private function CopyDataToNewLang($from_abbrev, $to_abbrev)
	{
		// clone data for Menus table
		$sql = "INSERT INTO ".TABLE_MENUS." (language_id, menu_code, menu_name, menu_placement, menu_order, access_level)
						        (SELECT '".$to_abbrev."', menu_code, menu_name, menu_placement, menu_order, access_level FROM ".TABLE_MENUS." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);					

		// clone data for Vocabulary table
		$sql = "INSERT INTO ".TABLE_VOCABULARY." (language_id, key_value, key_text)
			                         (SELECT '".$to_abbrev."', key_value, key_text FROM ".TABLE_VOCABULARY." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);					

		// clone data for Static Pages table
		$sql = "INSERT INTO ".TABLE_PAGES." (language_id, page_code, content_type, link_url, link_target, page_key, page_title, page_text, menu_id, menu_link, tag_title, tag_keywords, tag_description, comments_allowed, date_created, date_updated, is_home, is_removed, is_published, is_system_page, system_page, show_in_search, status_changed, access_level, priority_order)
			                    (SELECT '".$to_abbrev."', page_code, content_type, link_url, link_target, page_key, page_title, page_text, menu_id, menu_link, tag_title, tag_keywords, tag_description, comments_allowed, date_created, date_updated, is_home, is_removed, is_published, is_system_page, system_page, show_in_search, status_changed, access_level, priority_order FROM ".TABLE_PAGES." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);					

		// clone data for News table
		$sql = "INSERT INTO ".TABLE_NEWS." (language_id, type, header_text, body_text, date_created)
							   (SELECT '".$to_abbrev."', type, header_text, body_text, date_created FROM ".TABLE_NEWS." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);

		// clone data for Email Templates table
		$sql = "INSERT INTO ".TABLE_EMAIL_TEMPLATES." (language_id, template_code, template_name, template_subject, template_content, is_system_template)
					                      (SELECT '".$to_abbrev."', template_code, template_name, template_subject, template_content, is_system_template FROM ".TABLE_EMAIL_TEMPLATES." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);

		// clone data for Gallery Albums Description table
		$sql = "INSERT INTO ".TABLE_GALLERY_ALBUMS_DESCRIPTION." (gallery_album_id, language_id, name, description)
						                             (SELECT gallery_album_id, '".$to_abbrev."', name, description FROM ".TABLE_GALLERY_ALBUMS_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);

		// clone data for Gallery Album Items Description table
		$sql = "INSERT INTO ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." (gallery_album_item_id, language_id, item_text)
						                                  (SELECT gallery_album_item_id, '".$to_abbrev."', item_text FROM ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);

		// clone data for Banners Description table
		$sql = "INSERT INTO ".TABLE_BANNERS_DESCRIPTION." (banner_id, language_id, image_text)
						                      (SELECT banner_id, '".$to_abbrev."', image_text FROM ".TABLE_BANNERS_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);

		// clone data for Site Description table
		$sql = "INSERT INTO ".TABLE_SITE_DESCRIPTION." (language_id, header_text, slogan_text, footer_text, tag_title, tag_description, tag_keywords)
						                   (SELECT '".$to_abbrev."', header_text, slogan_text, footer_text, tag_title, tag_description, tag_keywords FROM ".TABLE_SITE_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);

        #-------------------------------------------
		# for BusinessDirectory
		#-------------------------------------------
		#// clone data for Categories Description table
		#$sql = "INSERT INTO ".TABLE_CATEGORIES_DESCRIPTION." (category_id, language_id, name, description)
		#				                         (SELECT category_id, '".$to_abbrev."', name, description FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		#database_void_query($sql);
		#
		#// clone data for Listings Description table
		#$sql = "INSERT INTO ".TABLE_LISTINGS_DESCRIPTION." (listing_id, language_id, business_name, business_address, business_description)
		#				                       (SELECT listing_id, '".$to_abbrev."', business_name, business_address, business_description FROM ".TABLE_LISTINGS_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		#database_void_query($sql);

        #-------------------------------------------
		# for HotelSite
		#-------------------------------------------
		// clone data from Hotel Description table
		$sql = "INSERT INTO ".TABLE_HOTEL_DESCRIPTION." (hotel_id, language_id, name, address, description)
				                               (SELECT hotel_id, '".$to_abbrev."', name, address, description FROM ".TABLE_HOTEL_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);
		
		// clone data from Rooms Description table
		$sql = "INSERT INTO ".TABLE_ROOMS_DESCRIPTION." (room_id, language_id, room_type, room_short_description, room_long_description)
				                               (SELECT room_id, '".$to_abbrev."', room_type, room_short_description, room_long_description FROM ".TABLE_ROOMS_DESCRIPTION." WHERE language_id = '".$from_abbrev."')";
		database_void_query($sql);
		
		// copy default messages.inc.php for new language
		$source_file = "include/messages.".$from_abbrev.".inc.php";
		$desfination_file = "include/messages.".$to_abbrev.".inc.php";
		@copy($source_file, $desfination_file);
	}
	
	/**
	 *	Delete data of language
	 *		@param $lang_abbrev
	 */
	private function DeleteDataOfLang($lang_abbrev)
	{	
		$sql = "DELETE FROM ".TABLE_MENUS." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_VOCABULARY." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_PAGES." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_NEWS." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_EMAIL_TEMPLATES." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_GALLERY_ALBUMS_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_GALLERY_ALBUM_ITEMS_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_BANNERS_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_SITE_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);

        #-------------------------------------------
		# for BusinessDirectory
		#-------------------------------------------
		#$sql = "DELETE FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		#database_void_query($sql);					
		#$sql = "DELETE FROM ".TABLE_LISTINGS_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		#database_void_query($sql);

        #-------------------------------------------
		# for HotelSite
		#-------------------------------------------
		$sql = "DELETE FROM ".TABLE_HOTEL_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					
		$sql = "DELETE FROM ".TABLE_ROOMS_DESCRIPTION." WHERE language_id = '".$lang_abbrev."'";
		database_void_query($sql);					

		// delete language file
		@unlink("include/messages.".$lang_abbrev.".inc.php");
	}
	
}
?>