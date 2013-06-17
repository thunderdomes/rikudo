<?php

/***
 *	Class Vocabulary
 *  -------------- 
 *  Description : encapsulates vocabulary properties and methods
 *	Usage       : MicroCMS, HotelSite, ShoppingCart, BusinessDirectory
 *  Updated	    : 07.06.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.4
 *
 *	PUBLIC				  	STATIC				 	PRIVATE
 * 	------------------	  	---------------     	---------------
 *	__construct			                           	GetVocabulary
 *	__destruct                                     	GetVocabularySize
 *	GetFilterURL                                   	GetFieldsEncoded
 *	GetLanguageURL
 *	DrawRewriteButton
 *	DrawEditForm
 *	IsKeyUpdated
 *	UpdateKey
 *	DrawVocabulary
 *	RewriteVocabularyFile
 *	DrawUploadForm
 *	UploadAndUpdate
 *	
 *	1.0.4
 *	    - changed UploadAndUpdate
 *	    - added numbers to "filter by"
 *	    - added demo block for uploaded files
 *	    -
 *	    -
 *	1.0.3
 *		- added text direction for textarea
 *		- fixed bug for using '$' in constant text
 *		- added langIdByUrl to [ Upload from File ] link
 *		- changed appGoTo() parameters
 *		- added blocking of Translate button on click
 *	
 *	
 **/

class Vocabulary {

	public $error;
	public $updatedKeys;	
	
	protected $keys;
	protected $filterBy;
	protected $filterByUrl;
	protected $languageId;
	protected $langIdByUrl;
	protected $whereClause;
	protected $isKeyUpdated;
	protected $currentKey;

	private $vocabularySize;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{
		// get filter value
		$this->filterBy = isset($_REQUEST['filter_by']) ? prepare_input($_REQUEST['filter_by']) : "";
		$this->filterByUrl = ($this->filterBy != "") ? "&filter_by=".$this->filterBy : "";

		$this->languageId  = (isset($_REQUEST['language_id']) && $_REQUEST['language_id'] != "") ? prepare_input($_REQUEST['language_id']) : Languages::GetDefaultLang();
		$this->langIdByUrl = ($this->languageId != "") ? "&language_id=".$this->languageId : "";

		$this->whereClause  = "";
		$this->whereClause .= ($this->languageId != "") ? " AND language_id = '".$this->languageId."'" : "";		
		$this->whereClause .= ($this->filterBy != "") ? " AND key_value LIKE '_".$this->filterBy."%'" : "";		
		
		$this->isKeyUpdated = false;
		$this->vocabularySize = 0;
		$this->currentKey = "";
		$this->updatedKeys = "0";
	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/***
	 * Returns filter URL
	 */
	public function GetFilterURL()
	{
		return $this->filterByUrl;		
	}

	/***
	 * Returns language URL
	 */
	public function GetLanguageURL()
	{
		return $this->langIdByUrl;		
	}

	/***
	 * Returns vocabulary record set
	 *		@param $where_clause
	 */
	private function GetVocabulary($where_clause = "")
	{
		$sql = "SELECT * FROM ".TABLE_VOCABULARY." WHERE 1=1 ".$where_clause." ORDER BY key_value ASC";
		$this->keys = database_query($sql, DATA_ONLY, ALL_ROWS);
		$this->vocabularySize = count($this->keys);
	}
	
	/***
	 * Returns vocabulary size
	 *		@param $where_clause
	 */
	private function GetVocabularySize($where_clause = "")
	{
		$sql = "SELECT COUNT(*) FROM ".TABLE_VOCABULARY;
		$this->vocabularySize = database_query($sql, ROWS_ONLY);		
	}

	/***
	 * Draws rewrite button
	 */
	public function DrawRewriteButton()
	{
		global $objSettings;
		$total_languages = Languages::GetAllActive();
		
		$button_align_left  = (Application::Get("lang_dir") == "ltr") ? "text-align: left;" : "text-align: right;";
		$button_align_right = (Application::Get("lang_dir") == "ltr") ? "text-align: right;" : "text-align: left;";

		if($this->GetVocabularySize() <= 0){			
			echo "<form action='index.php?admin=vocabulary&filter_by=A' method='post'>";
			draw_hidden_field("submition_type", "2");
			draw_token_field();
			echo "<table align='center' width='100%' border='0' cellspacing='0' cellpadding='3' class='main_text'>
				  <tr valign='top' >					
					<td style='".$button_align_left."'>
						".get_languages_box("language_id", $total_languages[0], "abbreviation", "lang_name", $this->languageId, "", "onchange=\"document.location.href='index.php?admin=vocabulary".$this->filterByUrl."&language_id='+this.value\"")."
					</td>
					<td style='padding-right:10px;".$button_align_right."'>
						<a href='index.php?admin=vocabulary&act=upload_form".$this->langIdByUrl."'>[ "._UPLOAD_FROM_FILE." ]</a>&nbsp;&nbsp;&nbsp;
						<input class='form_button' type='submit' name='submit' value='".decode_text(_BUTTON_REWRITE)."'>
					</td>
				  </tr>
				  </table>";
			echo "</form>";			
		}
	}	
	
	/***
	 * Draws Upload Form
	 */
	public function DrawUploadForm()
	{
		$disabled = (strtolower(SITE_MODE) == "demo") ? " disabled='disabled'" : "";
		$total_languages = Languages::GetAll();
		
		echo "<form action='index.php?admin=vocabulary' method='post' enctype='multipart/form-data'>";
				draw_hidden_field("act", "upload_and_update");
				draw_token_field();
		echo "<table align='center' width='99%' border='0' cellspacing='0' cellpadding='3' class='main_text'>
		<tr valign='top'>
			<td width='200px'><b>"._SELECT_FILE_TO_UPLOAD."</b></td>
			<td><input class='form_text' name='lang_update_file' type='file'></td>
			<td align='right'>
				<input class='form_button' ".$disabled." type='submit' value='"._UPLOAD_AND_PROCCESS."' onclick='return confirm(\""._PERFORM_OPERATION_COMMON_ALERT."\");'>
				&nbsp;
				<a href='javascript:void(0);' onclick='javascript:appGoTo(\"admin=vocabulary\")'>[ "._BUTTON_CANCEL." ]</a>
			</td>
		</tr>
		<tr valign='top'>
			<td><b>"._SELECT_LANG_TO_UPDATE."</b></td>
			<td colspan='2'>".get_languages_box("language_id", $total_languages[0], "abbreviation", "lang_name", $this->languageId)."</td>
		</tr>
		<tr><td colspan='3'></td></tr>
		</table>
		</form>";			 		
	}

	/***
	 * Upload and Update Vocabulary 
	 */
	public function UploadAndUpdate($language_id = "")
	{
		// Block all operations in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}

		$lang 	     = (!empty($language_id)) ? $language_id : Application::Get("lang");
		$update_file = isset($_FILES['lang_update_file']) ? true : false;
		$file_type   = isset($_FILES['lang_update_file']['type']) ? $_FILES['lang_update_file']['type'] : "";
		$count       = 0;
		
		if($update_file && preg_match("/php/i", $file_type)){			
			$arr = file($_FILES['lang_update_file']['tmp_name']);
			
			foreach($arr as $key => $val){
				if(preg_match("/define/i", $val)){
					$val = str_replace(array("define(", ");", '"'), "", $val);
					$val = trim($val, "\r\n");
					$val_parts = explode(",", $val);
					
					$key_value = isset($val_parts[0]) ? trim($val_parts[0]) : "";
					$key_text = isset($val_parts[1]) ? $val_parts[1] : "";

					$sql = "UPDATE ".TABLE_VOCABULARY."
							SET key_text = '".$key_text."'
							WHERE key_value = '".$key_value."' AND language_id = '".$lang."'";
					if(database_void_query($sql, false, false)){					
						$count++;
					}
				}
			}
		}else{
			$this->error = _WRONG_FILE_TYPE;
			return false;
		}
		if($count > 0){
			$this->updatedKeys = $count;
			return true;
		}else{
			$this->error = _NO_RECORDS_UPDATED;
			return false;
		}		
	}	

	/***
	 * Draws Edit Form
	 *		@param $key 
	 */
	public function DrawEditForm($key = "0")
	{
		$total_languages = Languages::GetAll();
		$key_value = $key_text = "";
		$default_lang_name = "English";
		$default_lang_abbr = "en";
		$lang_to_dir  = Languages::GetLanguageDirection($this->languageId);
		$default_lang_text = "";		
		$align_left  = (Application::Get("lang_dir") == "ltr") ? "left" : "right";
		$align_right = (Application::Get("lang_dir") == "ltr") ? "right" : "left";		
	
		$sql = "SELECT * FROM ".TABLE_VOCABULARY." WHERE id = ".(int)$key."";
        if($row = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$key_value = $row['key_value'];
			$key_text = $row['key_text'];
			$this->currentKey = $key_value;
		}
		
		$sql = "SELECT * FROM ".TABLE_LANGUAGES." WHERE is_default = 1";
        if($row = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
			$default_lang_name = $row['lang_name'];
			$default_lang_abbr = $row['abbreviation'];

			$sql = "SELECT * FROM ".TABLE_VOCABULARY." WHERE key_value = '".$this->currentKey."' AND language_id = '".$default_lang_abbr."'";
			if($row = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY)){
				$default_lang_text = strip_tags($row['key_text'], "<b><i><u><br>");
			}
		}

		echo '<script src="http://www.google.com/jsapi" type="text/javascript"></script>';
	
		if($default_lang_abbr != $this->languageId){		
			echo "<script type='text/javascript'>
				google.load('language', '1');
				
				function GoAndTranslate(){";				
				// Block all operations in demo mode
				if(strtolower(SITE_MODE) == "demo"){
					echo "alert('"._OPERATION_BLOCKED."'); return false; ";
				}else{
					echo "// grabbing the text to translate					
					var text = $('#txt_key_value').val();
					$('#btnTranslate').attr('disabled', true); 
					if(text.indexOf(' ') <= 0) text = text.toLowerCase();
					$('#txt_message').html('');
					google.language.translate(text, '".$default_lang_abbr."', '".$this->languageId."', function(result) {
						var translated = document.getElementById('txt_key_value');
						if(result.translation){
							$('#txt_message').html('"._COMPLETED."!');
							translated.value = result.translation;
							$('#btnTranslate').attr('disabled', false); 
						}
					});";					
				}
				echo "}
				</script>";
		}
	
		echo "<form action='index.php?admin=vocabulary' method='post'>";
				draw_hidden_field("submition_type", "1");
				draw_hidden_field("key", $key);
				draw_hidden_field("filter_by", $this->filterBy);
				draw_hidden_field("language_id", $this->languageId);
				draw_token_field();
		  echo "<table align='center' width='99%' border='0' cellspacing='0' cellpadding='3' class='main_text'>
				<tr valign='top'>
					<td><b>"._EDIT_WORD."</b></td>
					<td><div id='txt_message' style='color:#00a600'></div></td>
					<td width='20px' nowrap></td>
					<td align='".$align_right."'>".get_languages_box("language_id", $total_languages[0], "abbreviation", "lang_name", $this->languageId, "", "disabled='disabled'")."</td>
				</tr>
				<tr valign='top'>
					<td align='".$align_right."' width='90px'>"._KEY.":</td>					
					<td align='".$align_left."' colspan='2'>
						".$key_value."
						<input type='hidden' name='txt_key' value='".$key_value."' />
					</td>
					<td></td>
				</tr>";
		   echo "<tr valign='top'>
					<td align='".$align_right."'>"._VALUE." <font color='#c13a3a'>*</font>:</td>
					<td align='".$align_left."'>
						<textarea dir='".$lang_to_dir."' style='width:100%;height:60px;overflow:auto;padding:3px;' name='txt_key_value' id='txt_key_value'>".decode_text($key_text)."</textarea>						
					</td>
					<td></td>
					<td align='right' width='240px'>";
					if($default_lang_abbr != $this->languageId){
						echo "<nobr>
							".$default_lang_name." &raquo; ".strtoupper($this->languageId)." &nbsp;
							<input class='form_button' type='button' id='btnTranslate' name='submit' style='width:150px' onclick='GoAndTranslate()' value='"._TRANSLATE_VIA_GOOGLE."' />
							<input class='form_button' type='reset' name='btnReset' title='"._RESET."' value='R' />
						</nobr><br /><br />";
					}
				echo "<input class='form_button' type='submit' name='submit' value='".decode_text(_BUTTON_UPDATE)."'>&nbsp;&nbsp;
					  <input class='form_button' type='button' onclick=\"document.location.href='index.php?admin=vocabulary".$this->langIdByUrl."'\" value='".decode_text(_BUTTON_CANCEL)."'>			
					</td>
				</tr>";
			if($default_lang_abbr != $this->languageId){
				echo "<tr valign='top'>
						<td align='".$align_right."' width='110px'>".$default_lang_name.":</td>
						<td align='".$align_left."'>".$default_lang_text."</td>
						<td colspan='2'></td>
					</tr>";
			}				
		   echo "<tr align='right'><td colspan='4'></td></tr>
				</table>
			 </form>";			 
	}	
	
	/***
	 * Checks if a key was updated
	 */
	public function IsKeyUpdated()
	{
		return $this->isKeyUpdated;
	}

	/***
	 * Updates vocabulary key
	 *		@param $key_value
	 *		@param $key_text
	 */
	public function UpdateKey($key_value = "", $key_text = "")
	{		
		// Block all operations in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}

		// Check input parameters
		if($key_text == ""){
			$this->error = _VOC_KEY_VALUE_EMPTY;
			return false;
		}
		
		$sql = "UPDATE ".TABLE_VOCABULARY."
				SET key_text = '".$this->GetFieldsEncoded($key_text)."'
				WHERE
					key_value = '".$key_value."' AND
					language_id = '".$this->languageId."'";
		if(database_void_query($sql)){
			$this->isKeyUpdated = true;
			return true;
		}else{
			$this->error = _TRY_LATER;
			return false;
		}            
	}			
	
	/***
	 * Draws vocabulary
	 * 		@param $key
	 */
	public function DrawVocabulary($key)
	{	
		$align_left  = (Application::Get("lang_dir") == "ltr") ? "left" : "right";
		$align_right = (Application::Get("lang_dir") == "ltr") ? "right" : "left";		

		$this->GetVocabulary($this->whereClause);
			
		echo "<table width='100%' align='center' border='0' cellspacing='0' cellpadding='2' class='main_text'>
			  <tr>
				<td>"._FILTER_BY.": ";
				echo "<a href='index.php?admin=vocabulary".$this->langIdByUrl."'>"._ALL."</a> - ";
				for($i=65; $i<91; $i++){
					if($this->filterBy == chr($i)) $chr_i = "<b><u>".chr($i)."</u></b>";
					else $chr_i = chr($i);
					echo "<a href='index.php?admin=vocabulary&filter_by=".chr($i).$this->langIdByUrl."'>".$chr_i."</a> ";
				}
				echo " - ";
				for($i=1; $i<=5; $i++){
					if($this->filterBy == $i) $chr_i = "<b><u>".$i."</u></b>";
					else $chr_i = $i;
					echo "<a href='index.php?admin=vocabulary&filter_by=".$i.$this->langIdByUrl."'>".$chr_i."</a> ";
				}
				echo "</td>
				<td width='7%' align='center' nowrap>
				"._TOTAL.": ".count($this->keys)."
				</td>
			  </tr>";
		echo "<tr align='center'><td colspan='2'>".draw_line("line_no_margin", IMAGE_DIRECTORY, false)."</td></tr>";	
		echo "</table>";

		if(!empty($this->keys)){							           
            echo "<table width='100%' align='center' border='0' cellspacing='0' cellpadding='3' class='main_text'>";
			echo "<tr>
					<th width='1%'>#</th>
					<th width='25%' align='".$align_left."'>"._KEY."</th>
					<th width='67%' align='".$align_left."'>"._VALUE."</th>
					<th width='7%'></th>";

			for($i=0; $i < $this->vocabularySize; $i++){
				// Prepare key_text for displaying
				$decoded_text = strip_tags(decode_text($this->keys[$i]['key_text']));
				if(strlen($decoded_text) > 90){
					$key_text = "<span style='cursor:help;' title='".$decoded_text."'>".substr_by_word($decoded_text, 95, true)."</span>";
				}else{
					$key_text = $decoded_text;
				}

				// Display vocabulary row
				if($this->keys[$i]['key_value'] == $this->currentKey){
					echo "<tr>";
					echo "<td align='".$align_right."' class='voc_row_edit_".$align_left."' nowrap='nowrap'>".($i+1).".</td>";
					echo "<td align='".$align_left."' class='voc_row_edit_middle' nowrap='nowrap'>".$this->keys[$i]['key_value']."</td>";
					echo "<td align='".$align_left."' class='voc_row_edit_middle'>".$key_text."</td>
					      <td align='center' class='voc_row_edit_".$align_right."'><a href='index.php?admin=vocabulary&key=".$this->keys[$i]['id']."&act=edit".$this->filterByUrl.$this->langIdByUrl."'>[ "._EDIT_WORD." ]</a></td>
					</tr>";
				}else if($this->keys[$i]['id'] == (int)$key){
					echo "<tr>";
					echo "<td align='".$align_right."' class='voc_row_update_".$align_left."' nowrap='nowrap'>".($i+1).".</td>";
					echo "<td align='".$align_left."' class='voc_row_update_middle' nowrap='nowrap'>".$this->keys[$i]['key_value']."</td>";
					echo "<td align='".$align_left."' class='voc_row_update_middle'>".$key_text."</td>
					      <td align='center' class='voc_row_update_".$align_right."'><a href='index.php?admin=vocabulary&key=".$this->keys[$i]['id']."&act=edit".$this->filterByUrl.$this->langIdByUrl."'>[ "._EDIT_WORD." ]</a></td>
					</tr>";					
				}else{
					echo "<tr ".highlight(0)." onmouseover='oldColor=this.style.backgroundColor;this.style.backgroundColor=\"#ededed\";' onmouseout='this.style.backgroundColor=oldColor'>";
					echo "<td align='".$align_right."' nowrap='nowrap'>".($i+1).".</td>";
					echo "<td align='".$align_left."' nowrap='nowrap'>".$this->keys[$i]['key_value']."</td>";
					echo "<td align='".$align_left."'>".$key_text."</td>
					  <td align='center'><a href='index.php?admin=vocabulary&key=".$this->keys[$i]['id']."&act=edit".$this->filterByUrl.$this->langIdByUrl."'>[ "._EDIT_WORD." ]</a></td>
					</tr>";				
				}
			}			
			echo "</table>";
		}else{
			draw_important_message(_VOC_NOT_FOUND);
		}			
	}

	/***
	 * Rewrites vocabulary file
	 * 		@param $all_languages
	 */
	public function RewriteVocabularyFile($all_languages = false)
	{	
		// Block all operations in demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}

		$this->GetVocabulary((!$all_languages) ? "AND language_id = '".$this->languageId."'" : "");
		
		$string_data ="<?php\n";		
		for($i=0; $i < $this->vocabularySize; $i++){
			$replace_from = array('"', "\\", "$");
			$replace_to   = array("&#034;", "\\\\", "&#36;");
			$key_text = str_replace($replace_from, $replace_to, $this->keys[$i]['key_text']); // double slashes
			$string_data .= 'define("'.$this->keys[$i]['key_value'].'","'.$key_text.'");'."\n";
		}		
		$string_data .= "\n?>";
		
		// Write data to the file
		if(!$all_languages){
			$voc_file ="include/messages.".$this->languageId.".inc.php";			
		}else{
			$voc_file ="include/messages.inc.php";
		}
		@chmod($voc_file, 0755);
		$fh = @fopen($voc_file, 'w');
		if(!$fh){
			$this->error = "Can not open vocabulary file: ".$voc_file;
		}else{
			@fwrite($fh, $string_data);
			@fclose($fh);						
		}
		@chmod($voc_file, 0644);
		
		return true;	
	}
	
	/***
	 * Returns encoded data 
	 *		@param $str
	 */
	private function GetFieldsEncoded($str = "")
	{
		$str = encode_text($str);
		$str = str_replace("<TITLE>", "&lt;TITLE&gt;", $str); // <TITLE>
		$str = str_replace("<META>", "&lt;META&gt;", $str);   // <META>
		$str = str_replace("<DESCRIPTION>", "&lt;DESCRIPTION&gt;", $str); // <DESCRIPTION>
		return $str;
	}	
}
?>