<?php

/**
 *	Class MicroGrid
 *  -------------- 
 *  Description : encapsulates grid operations & properties
 *  Usage       : MicroCMS, MicroBlog, HotelSite, ShoppingCart, BusinessDirectory
 *  Updated	    : 03.06.2011
 *	Written by  : ApPHP
 *	Version     : 1.5.2
 *
 * 	PUBLIC:					PROTECTED			PRIVATE					STATIC
 *  -----------------		
 *	__construct				IsEmail				GetDataEncodedText   	GetParameter
 *	__destruct				IsNumeric			GetDataDecodedText		GetRandomString
 *	GetAll					IsFloat				GetDataEncoded
 *	GetInfoByID				IsAlpha				GetDataDecoded
 *	GetRecord				IsAlphaNumeric		FieldsValidation
 *	AddRecord				IsText				ValidateField
 *	UpdateRecord			IsPassword			CryptPasswordValue
 *	DeleteRecord			IsIpAddress			UncryptPasswordValue
 *	BeforeInsertRecord		IsInteger			FindUniqueFields
 *  BeforeEditRecord        ResizeImage         ResizeImage
 *	BeforeUpdateRecord		IncludeJSFunctions	RemoveFileImage
 *	BeforeDeleteRecord		DrawErrors			ParamEmpty 
 *	AfterInsertRecord		DrawWarnings
 *	AfterUpdateRecord		SetSQLs
 *	AfterDeleteRecord		DrawSQLs
 *	AfterDetailsMode        DrawPostInfo
 *	DrawViewMode			GetOSName
 *	DrawAddMode				DeleteImages
 *	DrawEditMode			GetFormattedMicrotime
 *	DrawDetailsMode			SetRunningTime
 *	DrawFieldByType			DrawRunningTime
 *	DrawOperationLinks		ResizeImage	
 *							
 *
 *  1.5.3
 *      - replaces all with Application::foo()
 *      - fixed bug on updating classes with unique prefix
 *      - fixed height issue for wysiwyg editors
 *      - add draw_token_field() tpo prevent csrt attaks
 *      -
 *  1.5.2
 *  	- for label fields 0000-00-00 date formatting returning value is empty string
 *  	- added new validation type : validation_minlength : _FIELD_VALUE_MINIMUM
 *  	- fixed but with log update for IE - changed button type from "submit" into "button"
 *  	- improved encoding for strings
 *  	- fixed bug with drawing empty "date" field in details mode
 *  1.5.1
 *      - added new type - "link" for View Mode
 *      - improved "link" type - allowed using of {field_name} in "href" attribute
 *      - renamed Pre/Post function with Before/After, added BeforeEditRecord
 *      - unexpected $this->params[$v_key]
 *      - added new field type for details mode : "object"
 *  1.5.0
 *  	- added ParamEmpty() method
 *  	- added [ Reset ] for non-required datetime fields
 *  	- added new attribute for datetime fields : min_year|max_year
 *  	- bug fixed with $val['separator_info']['legend']
 *  	- added automatical text direction for textarea and textboxes 
 *	
 **/

class MicroGrid {
		
	protected $debug = false;
		
	protected $tableName;
	protected $primaryKey;
	protected $dataSet;
	protected $lastInsertId;
	
	protected $formActionURL;
	protected $params;
	protected $actions;
	protected $actionIcons;
	protected $errorField;
	protected $arrErrors;
	protected $arrWarnings;
	protected $arrSQLs;

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
	protected $result;
	
	protected $isHtmlEncoding;
	
	protected $isAlterColorsAllowed;
	
	protected $isPagingAllowed;
	protected $pageSize;
	
	protected $isSortingAllowed;
	
	protected $isFilteringAllowed;
	protected $arrFilteringFields;
	
	protected $arrImagesFields;
	
	private $startTime;
	private $operation_links;
	
	//TODO
	// - add try catch + error
	// returns errors
	
	protected $uPrefix;
	
	public $error;
	
	//==========================================================================
    // Class Constructor
	//		@param $id
	//==========================================================================
	function __construct($id = "")
	{
		$this->params = array();

		$this->isHtmlEncoding = false;
		
		$this->uPrefix          = "";
		
		$this->primaryKey       = "";
		$this->tableName        = "";
		$this->formActionURL    = "";
		
		$this->languageId  	    = "";
		$this->allowLanguages   = false;
		$this->allowRefresh     = false;

		$this->VIEW_MODE_SQL    = "";
		$this->EDIT_MODE_SQL    = "";
		$this->DETAILS_MODE_SQL = "";
		$this->WHERE_CLAUSE     = "";
		$this->ORDER_CLAUSE     = "";	
		$this->LIMIT_CLAUSE     = "";
		
		$this->arrViewModeFields    = array();
		$this->arrAddModeFields     = array();
		$this->arrEditModeFields    = array();
		$this->arrDetailsModeFields = array();
		$this->arrFilterModeFields  = array();
		
		$this->actions = array("add"=>true, "edit"=>true, "details"=>true, "delete"=>true);
		$this->actionIcons = false;
		
		$this->dataSet = array();
		$this->lastInsertId = "";
		$this->curRecordId = "";
		$this->arrErrors = array();
		$this->arrWarnings = array();
		$this->arrSQLs = array();
		$this->error = "";
		$this->errorField = "";
		
		$this->isAlterColorsAllowed = true;
		
		$this->isPagingAllowed = true;
		$this->pageSize = 20;
		
		$this->isSortingAllowed = true;

		$this->isFilteringAllowed = true;
		$this->arrFilteringFields = array();
		
		$this->arrImagesFields = array();
		
	}
	
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }


	/***
	 *	Returns DataSet array
	 *		@param $order_clause
	 *		@param $limit_clause
	 */
	public function GetAll($order_clause = "", $limit_clause = "")
	{
		$sql = $this->VIEW_MODE_SQL." ".$this->WHERE_CLAUSE." ".$order_clause." ".$limit_clause;
		if($this->debug) $this->arrSQLs['select_get_all'] = $sql;					
		return database_query($sql, DATA_AND_ROWS);
	}

	/***
	 *	Returns info by ID
	 *		@param $key
	 */
	public function GetInfoByID($key = "")
	{
		$sql = "SELECT * FROM ".$this->tableName." WHERE ".$this->primaryKey."=".(int)$key." LIMIT 0, 1";
		return database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
	}
	
	/***
	 *	Returns one record
	 *		@param $sql
	 */
	public function GetRecord($sql = "")
	{
		return database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
	}
	
	/***************************************************************************
	 *
	 *	ADD NEW RECORD
	 *	
	 **************************************************************************/
	public function AddRecord()
	{		
		//----------------------------------------------------------------------
		// block if this is a demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}

		//----------------------------------------------------------------------
		// F5 validation
		if(!$this->F5Validation()){
			return true;
		}		

		//----------------------------------------------------------------------
		// fields data validation
		if(!$this->FieldsValidation($this->arrAddModeFields)){
			return false;
		}		

		//----------------------------------------------------------------------
		// pre addition check
		if(!$this->BeforeInsertRecord()){
			return false;
		}

		//----------------------------------------------------------------------
		// check for unique fields
		if($this->FindUniqueFields("add")){
			return false;
		}

		//----------------------------------------------------------------------
		// prepare (handle) uploaded files
		$arrUploadedFiles = $this->UploadFileImage("add");
		
		//----------------------------------------------------------------------
		// prepare INSERT SQL
		$sql = "INSERT INTO `".$this->tableName."`(";
			$sql .= $this->primaryKey;
			foreach($this->params as $key => $val){
				if(array_key_exists($key, $this->arrAddModeFields)){
					$sql .= ", `".$key."`";
				}else{
					foreach($this->arrAddModeFields as $v_key => $v_val){
						if(array_key_exists($key, $v_val)){
							$sql .= ", `".$key."`";
						}							
					}
				}
			}
			foreach($arrUploadedFiles as $key => $val){
				$sql .= ", `".$key."`";
			}
		$sql .= ") VALUES (";
			$sql .= "NULL";
			foreach($this->params as $key => $val){
				if(array_key_exists($key, $this->arrAddModeFields)){
					if($this->arrAddModeFields[$key]['type'] == "password"){
						$password_value = $this->CryptPasswordValue($key, $this->arrAddModeFields[$key], $val);
						$sql .= ", ".$password_value;
					}else{
						$sql .= ", '".mysql_real_escape_string($val)."'";
					}										
				}else{
					foreach($this->arrAddModeFields as $v_key => $v_val){
						if(array_key_exists($key, $v_val)){
							if($v_val[$key]['type'] == "password"){
								$password_value = $this->CryptPasswordValue($key, $v_val[$key], $val);
								$sql .= ", ".$password_value;
							}else{
								$sql .= ", '".mysql_real_escape_string($val)."'";
							}					
						}							
					}
				}
			}
			foreach($arrUploadedFiles as $key => $val){
				$sql .= ", '".$val."'";
			}
		$sql .= ")";
		
		if($this->debug) $this->arrSQLs['insert_sql'] = $sql;
		if(!database_void_query($sql)){
			if(isset($_SESSION)) $_SESSION[$this->uPrefix.'_operation_code'] = ""; 
			if($this->debug) $this->arrErrors['insert_sql'] = mysql_error();			
			$this->error = _TRY_LATER;
			return false;
		}else{
			if(isset($_SESSION)) $_SESSION[$this->uPrefix.'_operation_code'] = self::GetParameter('operation_code'); 
			$this->lastInsertId = mysql_insert_id();
			$this->AfterInsertRecord();
			return true;
		}		
	}

	/***************************************************************************
	 *
	 *	UPDATE RECORD
	 *	
	 **************************************************************************/
	public function UpdateRecord($rid = "0")
	{		
		$this->curRecordId = $rid;

		//----------------------------------------------------------------------
		// block if this is a demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}		
			
		//----------------------------------------------------------------------
		// check if we work with valid record
		if($this->curRecordId == "0" || $this->curRecordId == "" || !is_numeric($this->curRecordId)){
			$this->error = _WRONG_PARAMETER_PASSED;
			return false;
		}		

		//----------------------------------------------------------------------
		// F5 validation
		if(!$this->F5Validation()){
			return true;
		}		

		//----------------------------------------------------------------------
		// fields data validation
		if(!$this->FieldsValidation($this->arrEditModeFields)){
			return false;
		}		

		//----------------------------------------------------------------------
		// pre updating check
		if(!$this->BeforeUpdateRecord()){
			return false;
		}

		//----------------------------------------------------------------------
		// check for unique fields
		if($this->FindUniqueFields("edit", $this->curRecordId)){
			return false;
		}

		//----------------------------------------------------------------------
		// prepare (handle) uploaded files
		$arrUploadedFiles = $this->UploadFileImage("edit");
		
		//----------------------------------------------------------------------
		// update
		$sql = "UPDATE `".$this->tableName."` SET ";
			$fields_count = 0;
			foreach($this->params as $key => $val){
				if(array_key_exists($key, $this->arrEditModeFields)){
					if($fields_count++ > 0) $sql .= ",";
					if($this->arrEditModeFields[$key]['type'] == "password"){
						$password_value = $this->CryptPasswordValue($key, $this->arrEditModeFields[$key], $val);
						$sql .= "`".$key."` = ".$password_value;
					}else{
						$sql .= "`".$key."` = '".mysql_real_escape_string($val)."'";					
					}
				}else{
					foreach($this->arrEditModeFields as $v_key => $v_val){
						if(array_key_exists($key, $v_val)){
							if($fields_count++ > 0) $sql .= ",";
							if($v_val[$key]['type'] == "password"){
								$password_value = $this->CryptPasswordValue($key, $v_val[$key], $val);
								$sql .= "`".$key."` = ".$password_value;					
							}else{
								$sql .= "`".$key."` = '".mysql_real_escape_string($val)."'";					
							}					
						}							
					}
				}
			}				
			foreach($arrUploadedFiles as $key => $val){
				if($fields_count > 0) $sql .= ",";
				$sql .= "`".$key."` = '".mysql_real_escape_string($val)."'";					
			}
		$sql .= " WHERE `".$this->primaryKey."`=".(int)$this->curRecordId;
		
		if($this->debug) $this->arrSQLs['update_sql'] = $sql;
		if(database_void_query($sql) < 0){
			if(isset($_SESSION)) $_SESSION[$this->uPrefix.'_operation_code'] = ""; 
			if($this->debug) $this->arrErrors['update_sql'] = mysql_error();						
			$this->error = _TRY_LATER;
			return false;
		}else{
			if(isset($_SESSION)) $_SESSION[$this->uPrefix.'_operation_code'] = self::GetParameter('operation_code'); 
			$this->AfterUpdateRecord();
			return true;
		}	
		
	}

	/***************************************************************************
	 *
	 *	DELETE RECORD
	 *	
	 **************************************************************************/
	public function DeleteRecord($rid = "")
	{
		$this->curRecordId = $rid;

		//----------------------------------------------------------------------
		// check if rid is not empty
		if($this->curRecordId == ""){				
			$this->error = _WRONG_PARAMETER_PASSED;
			return false;
		}
		
		//----------------------------------------------------------------------
		// block if this is a demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}
		
		//----------------------------------------------------------------------
		// F5 validation
		if(!$this->F5Validation()){
			return false;
		}		

		//----------------------------------------------------------------------
		// pre deleting check
		if(!$this->BeforeDeleteRecord()){
			return false;
		}

		$this->PrepareImagesArray($this->curRecordId);

		//----------------------------------------------------------------------
		// delete
		$sql = "DELETE FROM ".$this->tableName." WHERE ".$this->primaryKey." = ".(int)$this->curRecordId."";
		if(database_void_query($sql) > 0){
			if(isset($_SESSION)) $_SESSION[$this->uPrefix.'_operation_code'] = self::GetParameter('operation_code'); 
			$this->DeleteImages();
			if($this->debug) $this->arrSQLs['delete_sql'] = $sql;
			$this->AfterDeleteRecord();
			return true;
		}else{
			if(isset($_SESSION)) $_SESSION[$this->uPrefix.'_operation_code'] = ""; 			
			if($this->debug) $this->arrErrors['delete_sql'] = $sql."<br>".mysql_error();
			$this->error = _TRY_LATER;
			return false;
		}
	}
	
	/**
	 *	"Before"-operation methods
	 */
	public function BeforeInsertRecord()
	{
		return true;
	}

	public function BeforeEditRecord()
	{
		// $this->curRecordId - currently editing record
		// $this->result - current record info
	}
	
	public function BeforeUpdateRecord()
	{
		return true;
	}

	public function BeforeDeleteRecord()
	{
		return true;
	}

	/**
	 *	"After"-operation methods
	 */
	public function AfterInsertRecord()
	{
		// $this->lastInsertId - currently inserted record
	}

	public function AfterUpdateRecord()
	{
		// $this->curRecordId - currently updated record
	}

	public function AfterDeleteRecord()
	{
		// $this->curRecordId - currently deleted record
	}

	public function AfterDetailsMode()
	{
		// $this->curRecordId - currently viewed record
	}

	/***********************************************************************
	 *
	 *	Draw View Mode
	 *	
	 ***********************************************************************/
	public function DrawViewMode()
	{
		$this->IncludeJSFunctions();		
		
		$sorting_fields  = self::GetParameter('sorting_fields');
		$sorting_types   = self::GetParameter('sorting_types');
		$page 			 = self::GetParameter('page');
		$total_pages	 = $page;

		$rid 		     = self::GetParameter('rid');
		$operation 		 = self::GetParameter('operation');
		$operation_type  = self::GetParameter('operation_type');
		$operation_field = self::GetParameter('operation_field');
		
		$search_status   = self::GetParameter('search_status');
		
		$concat_sign 	 = (preg_match('/\?/', $this->formActionURL) ? "&" : "?");		
		$colspan 		 = count($this->arrViewModeFields)+1;
		$start_row 		 = 0;
		$total_records 	 = 0;
		$sort_by         = "";
		

		// prepare changing of language
		if($operation == "change_language" && $operation_type != ""){
			$this->languageId = $operation_type;
		}
			
		// prepare sorting data
		if($this->isSortingAllowed){
			if($operation == "sorting"){
				if($sorting_fields != ""){
					if(strtolower($sorting_types) == "asc") $sorting_types = "DESC";
					else $sorting_types = "ASC";
					
					$sort_type = isset($this->arrViewModeFields[$sorting_fields]['sort_type']) ? $this->arrViewModeFields[$sorting_fields]['sort_type'] : "string";
					$sort_by = isset($this->arrViewModeFields[$sorting_fields]['sort_by']) ? $this->arrViewModeFields[$sorting_fields]['sort_by'] : $sorting_fields;
					if($sort_type == "numeric"){
						$this->ORDER_CLAUSE = " ORDER BY ABS(".$sort_by.") ".$sorting_types." ";	
					}else{
						$this->ORDER_CLAUSE = " ORDER BY ".$sort_by." ".$sorting_types." ";	
					}					
				}else{
					$sorting_types = "ASC";
				}
			}else{
				if($sorting_fields != "" && $sorting_types != ""){
					$this->ORDER_CLAUSE = " ORDER BY ".$sorting_fields." ".$sorting_types." ";	
				}
			}
		}
		
		// prepare filtering data
		if($this->isFilteringAllowed){
			if($search_status == "active"){
				if($this->WHERE_CLAUSE == "") $this->WHERE_CLAUSE .= " WHERE 1=1 ";
				$count = 0;
				foreach($this->arrFilteringFields as $key => $val){					
					if(self::GetParameter("filter_by_".$val['table'].$val['field'], false) !== ""){
						$sign = "="; $sign_start = ""; $sign_end = "";
						if($val['sign'] == "="){
							$sign = "=";
						}else if($val['sign'] == "like%"){
							$sign = "LIKE";
							$sign_end = "%";
						}else if($val['sign'] == "%like"){
							$sign = "LIKE";
							$sign_start = "%";
						}else if($val['sign'] == "%like%"){
							$sign = "LIKE";
							$sign_start = "%";
							$sign_end = "%";
						}
						$key_value = self::GetParameter("filter_by_".$val['table'].$val['field'], false);
						if(isset($val['table']) && $val['table'] != "") $field_name = $val['table'].".".$val['field'];
						else $field_name = $val['field'];
						$this->WHERE_CLAUSE .= " AND ".$field_name." ".$sign." '".$sign_start.mysql_real_escape_string($key_value).$sign_end."' ";						
					}
				}
			}			
		}		

		// prepare paging data
		if($this->isPagingAllowed){
			if(!is_numeric($page) || (int)$page <= 0) $page = 1;
			$sql = $this->VIEW_MODE_SQL." ".$this->WHERE_CLAUSE;
			$total_records = (int)database_query($sql, ROWS_ONLY);
			if($this->debug){
				if(mysql_error() != ""){
					$this->arrErrors['filter_sql'] = $sql."<br>".mysql_error();		
				}else{
					$this->arrSQLs['filter_sql'] = $sql;		
				}
			}
			if($this->pageSize == 0) $this->pageSize = "10";
			$total_pages = (int)($total_records / $this->pageSize);
			// when you back from other languages where more pages than on current
			if($page > ($total_pages+1)) $page = 1; 
			if(($total_records % $this->pageSize) != 0) $total_pages++;
			$start_row = ($page - 1) * $this->pageSize;				
		}
		
		// check if there is move operation and perform it
		if($operation == "move"){			
			//----------------------------------------------------------------------
			// block if this is a demo mode
			if(strtolower(SITE_MODE) == "demo"){
				$this->error = _OPERATION_BLOCKED;
			}else{
				$operation_field_p = explode("#", $operation_field);
				$operation_field_p0 = explode("-", $operation_field_p[0]);
				$operation_field_p1 = explode("-", $operation_field_p[2]);
				$of_first 	= isset($operation_field_p0[0]) ? $operation_field_p0[0] : "";
				$of_second 	= isset($operation_field_p0[1]) ? $operation_field_p0[1] : "";
				$of_name 	= $operation_field_p[1];
				$of_first_value  = isset($operation_field_p1[0]) ? $operation_field_p1[0] : "";
				$of_second_value = isset($operation_field_p1[1]) ? $operation_field_p1[1] : "";
				
				if(($of_first_value != "") && ($of_second_value != "")){
					$sql = "UPDATE ".$this->tableName." SET ".$of_name." = '".$of_second_value."' WHERE ".$this->primaryKey." = '".$of_first."'";
					database_void_query($sql);
					if($this->debug) $this->arrSQLs['select_move_1'] = $sql;
					$sql = "UPDATE ".$this->tableName." SET ".$of_name." = '".$of_first_value."' WHERE ".$this->primaryKey." = '".$of_second."'";
					database_void_query($sql);
					if($this->debug) $this->arrSQLs['select_move_2'] = $sql;					
				}				
			}
		}		
		
		$arrRecords = $this->GetAll($this->ORDER_CLAUSE, "LIMIT ".$start_row.", ".(int)$this->pageSize);
		if($this->allowLanguages) $arrLanguages = Languages::GetAllActive();
		if(!$this->isPagingAllowed){
			$total_records = $arrRecords[1];
		}
		
		echo "<form name='frmMicroGrid_".$this->tableName."' id='frmMicroGrid_".$this->tableName."' action='".$this->formActionURL."' method='post'>\n";
		echo draw_hidden_field("mg_prefix", $this->uPrefix)."\n";
		echo draw_hidden_field("mg_action", "view")."\n";
		echo draw_hidden_field("mg_rid", "")."\n";
		echo draw_hidden_field("mg_sorting_fields", $sorting_fields)."\n";
		echo draw_hidden_field("mg_sorting_types", $sorting_types)."\n";
		echo draw_hidden_field("mg_page", $page)."\n";
		echo draw_hidden_field("mg_operation", $operation)."\n";
		echo draw_hidden_field("mg_operation_type", $operation_type)."\n";
		echo draw_hidden_field("mg_operation_field", $operation_field)."\n";
		echo draw_hidden_field("mg_search_status", $search_status)."\n";
		echo draw_hidden_field("mg_language_id", $this->languageId)."\n";
		echo draw_hidden_field("mg_operation_code", self::GetRandomString(20))."\n";
		echo draw_token_field()."\n";

		if($this->actions['add'] || $this->allowLanguages || $this->allowRefresh){
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>
				<tr>";
					echo "<td align='".Application::Get("defined_left")."' valign='middle'>";
					if($this->actions['add']) echo "<input class='mgrid_button' type='button' name='btnAddNew' value=\""._ADD_NEW."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"add\");'>";
					if($this->operation_links != "")  echo "&nbsp;&nbsp;&nbsp;".$this->operation_links;
					echo "</td>";
					
					echo "<td align='".Application::Get("defined_right")."'>";
					if($this->allowRefresh)	echo "<a href='javascript:void(\"refresh\");' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\");' title='"._REFRESH."'><img src='images/microgrid_icons/refresh.gif' alt='"._REFRESH."'></a>";
					echo "</td>";						
					
					if($this->allowLanguages){
						echo "<td align='".Application::Get("defined_right")."' width='80px'>";
						echo (($this->allowLanguages) ? get_languages_box("mg_language_id", $arrLanguages[0], "abbreviation", "lang_name", $this->languageId, "", "onchange='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", null, null, null, null, \"change_language\", this.value, \"language_id\");'") : "");
						echo "</td>";
					}
					echo "
				</tr>
				<tr><td nowrap height='10px'></td></tr>
			</table>";
		}

		if($this->isFilteringAllowed){
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>
				<tr>
					<td align='".Application::Get("defined_left")."'>";
						echo "<b>"._FILTER_BY."</b>: &nbsp;&nbsp;&nbsp;";
						foreach($this->arrFilteringFields as $key => $val){
							$filter_field_value = ($search_status == "active") ? self::GetParameter("filter_by_".$val['table'].$val['field'], false) : "";
							if($val['type'] == "text"){
								echo $key.":&nbsp;<input type='text' class='mgrid_text' name='filter_by_".$val['table'].$val['field']."' value=\"".$this->GetDataDecoded($filter_field_value)."\" style='width:".$val['width'].";'>&nbsp;&nbsp;&nbsp;";
							}else if($val['type'] == "dropdownlist"){
								if(is_array($val['source'])){
									echo $key.":&nbsp;<select class='mgrid_text' name='filter_by_".$val['table'].$val['field']."' style='width:".$val['width'].";'>";
									echo "<option value=''>-- "._SELECT." --</option>";	
									foreach($val['source'] as $key => $val){
										echo "<option ".(($filter_field_value !== "" && $filter_field_value == $key) ? " selected='selected'" : "")." value=\"".$this->GetDataDecoded($key)."\">".$val."</option>";	
									}
									echo "</select>&nbsp;&nbsp;&nbsp;";
								}								
							}
						}
						if(count($this->arrFilteringFields) > 0){
							echo "&nbsp;";
							if($search_status == "active") echo " <input type='button' class='mgrid_button' name='btnReset' value=\""._BUTTON_RESET."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", \"\", \"\", \"\", \"\", \"reset_filtering\");'>";
							echo " <input type='button' class='mgrid_button' name='btnSearch' value=\""._SEARCH."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", \"\", \"\", \"\", \"\", \"filtering\");'>";
						}
			echo "	</td>
				</tr>
				<tr><td nowrap height='10px'></td></tr>
			</table>";
		}
		
		// draw rows
		if($arrRecords[1] > 0){			
			echo "<table width='100%' border='".(($this->debug) ? "1" : "0")."' cellspacing='0' cellpadding='2' class='mgrid_table'>";
			// draw column headers
			echo "<tr>";
				foreach($this->arrViewModeFields as $key => $val){
					$width = isset($val['width']) ? " width='".$val['width']."'": "";
					if(isset($val['align']) && $val['align'] == "left" && Application::Get("defined_left") == "right"){
						$align = " align='right'";
					}else if(isset($val['align']) && $val['align'] == "right" && Application::Get("defined_right") == "left"){
						$align = " align='left'";
					}else if(isset($val['align'])){
						$align = " align='".$val['align']."'";
					}else{
						$align = "";	
					}					
					$visible = (isset($val['visible']) && $val['visible']!=="") ? $val['visible'] : true;
					$sortable = (isset($val['sortable']) && $val['sortable']!=="") ? $val['sortable'] : true;
					$th_class = ($key == $sort_by) ? " class='th_sorted'" : "";
					if($visible){
						echo "<th".$width.$align.$th_class.">";
							if($this->isSortingAllowed && $sortable){
								$field_sorting = "DESC";
								$sort_icon = "";
								if($key == $sorting_fields){
									if(strtolower($sorting_types) == "asc"){
										$sort_icon = " <img src='images/microgrid_icons/up.png' alt='' title='asc'>";
									}else if(strtolower($sorting_types) == "desc"){
										$sort_icon = " <img src='images/microgrid_icons/down.png' alt='' title='desc'>";
									}
									$field_sorting = $sorting_types;
								}
								echo "<a href='javascript:void(\"sort\");' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", \"\", \"".$key."\", \"".$field_sorting."\", \"".$page."\", \"sorting\");'><b>".$val['title']."</b></a>".$sort_icon;
							}else{
								echo "<label>".$val['title']."</label>";
							}
						echo "</th>";
					}					
				}				
			if($this->actions['details'] || $this->actions['edit'] || $this->actions['delete']){
				echo "<th width='8%'>"._ACTIONS."</th>";
			}
			echo "</tr>";
			echo "<tr><td colspan='".$colspan."' height='3px' nowrap='nowrap'>".draw_line("no_margin_line", IMAGE_DIRECTORY, false)."</td></tr>";
			for($i=0; $i<$arrRecords[1]; $i++){
				echo "<tr ".(($this->isAlterColorsAllowed) ? highlight(0) : "")." onmouseover='oldColor=this.style.backgroundColor;this.style.backgroundColor=\"#e7e7e7\";' onmouseout='this.style.backgroundColor=oldColor'>";
					foreach($this->arrViewModeFields as $key => $val){
						if(isset($val['align']) && $val['align'] == "left" && Application::Get("defined_left") == "right"){
							$align = " align='right'";
						}else if(isset($val['align']) && $val['align'] == "right" && Application::Get("defined_right") == "left"){
							$align = " align='left'";
						}else if(isset($val['align'])){
							$align = " align='".$val['align']."'";
						}else{
							$align = "";	
						}					
						$wrap    = isset($val['nowrap']) ? " nowrap='".$val['nowrap']."'": "";
						$visible = (isset($val['visible']) && $val['visible']!=="") ? $val['visible'] : true;
						$movable = (isset($val['movable']) && $val['movable']!=="") ? $val['movable'] : false;
						if(isset($arrRecords[0][$i][$key])){
							$field_value = $this->DrawFieldByType("view", $key, $val, $arrRecords[0][$i], false);								
						}else{
							if($this->debug) $this->arrWarnings['wrong_'.$key] = "Field <b>".$key."</b>: wrong definition in View mode or field has no value in SQL! Please check currefully your code.";
							$field_value = "";
						}
						if($visible){
							$move_link = "";
							if($movable){
								$move_prev_id  = $arrRecords[0][$i]['id']."-".(isset($arrRecords[0][$i-1]['id']) ? $arrRecords[0][$i-1]['id']."" : "")."#";
								$move_prev_id .= $key."#";
								$move_prev_id .= $arrRecords[0][$i][$key]."-".(isset($arrRecords[0][$i-1][$key]) ? $arrRecords[0][$i-1][$key]."" : "");							
								$move_next_id  = $arrRecords[0][$i]['id']."-".(isset($arrRecords[0][$i+1]['id']) ? $arrRecords[0][$i+1]['id']."" : "")."#";
								$move_next_id .= $key."#";
								$move_next_id .= $arrRecords[0][$i][$key]."-".(isset($arrRecords[0][$i+1][$key]) ? $arrRecords[0][$i+1][$key]."" : "");
								if(isset($arrRecords[0][$i-1]['id'])){
									$move_link .= " <a href='javascript:void(\"move|up\");' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", \"".$arrRecords[0][$i]['id']."\", \"\", \"\", \"\", \"move\", \"up\", \"".$move_prev_id."\")'>";
									$move_link .= ($this->actionIcons) ? "<img src='images/microgrid_icons/up.png' style='margin-bottom:2px' alt='' title='"._UP."'>" : _UP;
									$move_link .= "</a>";										
								}else{
									$move_link .= " <span style='width:11px;height:11px;'></span>";
								}
								if(isset($arrRecords[0][$i+1]['id'])){									
									$move_link .= "<a href='javascript:void(\"move|down\");' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", \"".$arrRecords[0][$i]['id']."\", \"\", \"\", \"\", \"move\", \"down\", \"".$move_next_id."\")'>";
									$move_link .= ($this->actionIcons) ? "<img src='images/microgrid_icons/down.png' style='margin-top:2px' alt='' title='"._DOWN."'>" : ((isset($arrRecords[0][$i-1]['id'])) ? "/" : "")._DOWN;
									$move_link .= "</a>";
								}else{
									$move_link .= "<span style='width:11px;height:11px;'></span>";
								}
							}
							echo "<td".$align.$wrap.">".$field_value.$move_link."</td>";
						}
					}				
					if($this->actions['details'] || $this->actions['edit'] || $this->actions['delete']){
						echo "<td align='center' nowrap>";
						if($this->actions['details']){
							echo "<a href='javascript:void(\"details|".$arrRecords[0][$i]['id']."\");' title='"._VIEW_WORD."' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"details\", \"".$arrRecords[0][$i]['id']."\");'>".(($this->actionIcons) ? "<img src='images/microgrid_icons/details.gif' title='"._VIEW_WORD."' alt='' border='0' style='margin:0px; padding:0px; ' height='16px'>" : _VIEW_WORD)."</a>";
						}				
						if($this->actions['edit']){
							if($this->actions['details']) echo "&nbsp;".(($this->actionIcons) ? "&nbsp;" : "").get_divider()."&nbsp"; 
							echo "<a href='javascript:void(\"edit|".$arrRecords[0][$i]['id']."\");' title='"._EDIT_WORD."' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"edit\", \"".$arrRecords[0][$i]['id']."\");'>".(($this->actionIcons) ? "<img src='images/microgrid_icons/edit.gif' title='"._EDIT_WORD."' alt='' border='0' style='margin:0px; padding:0px;' height='16px'>" : _EDIT_WORD)."</a>";
						}
						if($this->actions['delete']){
							if($this->actions['edit'] || $this->actions['details']) echo "&nbsp;".(($this->actionIcons) ? "&nbsp;" : "").get_divider()."&nbsp"; 
							echo "<a href='javascript:void(\"delete|".$arrRecords[0][$i]['id']."\");' title='"._DELETE_WORD."' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"delete\", \"".$arrRecords[0][$i]['id']."\");'>".(($this->actionIcons) ? "<img src='images/microgrid_icons/delete.gif' title='"._DELETE_WORD."' alt='' border='0' style='margin:0px; padding:0px;' height='16px'>" : _DELETE_WORD)."</a>";
						}
						echo "&nbsp;</td>";
					}
				echo "</tr>";
			}

			echo "<tr><td colspan='".$colspan."' height='15px' nowrap='nowrap'>".draw_line("no_margin_line", IMAGE_DIRECTORY, false)."</td></tr>";
			echo "</table>";
			
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>";
			echo "<tr>";
			echo "	<td>";
				if($this->isPagingAllowed){
					echo "<b>"._PAGES.":</b> ";
					for($i = 1; $i <= $total_pages; $i++){
						echo "<a class='pagging_link' href='javascript:void(\"paging\");' onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\", \"\", \"\", \"\", \"".$i."\", \"\");'>".(($i == $page) ? "<b>[".$i."]</b>" : $i)."</a> ";
					}				
				}			
			echo "	</td>";
			echo "	<td align='".Application::Get("defined_right")."'><b>"._TOTAL."</b>: ".$total_records."</td>";
			echo "</tr>";
			echo "</table>";
		}else{
			echo draw_message(_NO_RECORDS_FOUND, true, true, false, "width:100%");
			//if($this->debug) $this->arrSQLs['select'] = $this->VIEW_MODE_SQL." ".$this->WHERE_CLAUSE." ".$this->ORDER_CLAUSE." LIMIT ".$start_row.", ".(int)$this->pageSize;
		}
		
		echo "</form>";
		
		$this->DrawRunningTime();
		$this->DrawErrors();
		$this->DrawWarnings();
		$this->DrawSQLs();	
		$this->DrawPostInfo();	
	}

	/***********************************************************************
	 *
	 *	Draw Add Mode
	 *	
	 ***********************************************************************/
	public function DrawAddMode()
	{		
		$this->IncludeJSFunctions("add");		
		
		$sorting_fields  = self::GetParameter('sorting_fields');
		$sorting_types   = self::GetParameter('sorting_types');
		$page 			 = self::GetParameter('page');
		$operation 		 = self::GetParameter('operation');
		$operation_type  = self::GetParameter('operation_type');
		$operation_field = self::GetParameter('operation_field');
		$search_status   = self::GetParameter('search_status');
		// prepare language direction for textboxes, textareas etc..
		$language_dir    = @Languages::GetLanguageDirection($this->languageId);
		
		$first_field_focus = "";

		echo "<form name='frmMicroGrid_".$this->tableName."' id='frmMicroGrid_".$this->tableName."' action='".$this->formActionURL."' method='post' enctype='multipart/form-data'>\n";
		echo draw_hidden_field("mg_prefix", $this->uPrefix)."\n";
		echo draw_hidden_field("mg_action", "create")."\n";
		echo draw_hidden_field("mg_rid", "-1")."\n";
		echo draw_hidden_field("mg_sorting_fields", $sorting_fields)."\n";
		echo draw_hidden_field("mg_sorting_types", $sorting_types)."\n";
		echo draw_hidden_field("mg_page", $page)."\n";
		echo draw_hidden_field("mg_operation", $operation)."\n";
		echo draw_hidden_field("mg_operation_type", $operation_type)."\n";
		echo draw_hidden_field("mg_operation_field", $operation_field)."\n";
		echo draw_hidden_field("mg_search_status", $search_status)."\n";
		echo draw_hidden_field("mg_language_id", $this->languageId)."\n";
		echo draw_hidden_field("mg_operation_code", self::GetRandomString(20))."\n";
		echo draw_token_field()."\n";
		
		// draw hidden fields
		foreach($this->arrAddModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						if($v_val['type'] == "hidden"){
							draw_hidden_field($v_key, $v_val['default'])."\n";
						}				
					}
				}				
			}else{
				if($val['type'] == "hidden"){
					draw_hidden_field($key, $val['default'])."\n";
				}				
			}
		}				
		
		// draw Add Form
		echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
		echo "<tr><td colspan='2' height='1px' nowrap></td></tr>\n";
		foreach($this->arrAddModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				echo "</table><br>\n";
				echo "<fieldset style='padding:5px;margin-left:5px;margin-right:10px;'>";
				if(isset($val['separator_info']['legend'])) echo "<legend>".$val['separator_info']['legend']."</legend>";
				echo "\n<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info" && $v_val['type'] != "hidden"){						
						echo "<tr id='mg_row_".$v_key."'>\n";
						echo "  <td width='25%'><label for='".$v_key."'>".$v_val['title']."</label>".((isset($v_val['required']) && $v_val['required']) ? " <font color='#c13a3a'>*</font>" : "").":</td>\n";
						echo "  <td style='padding-left:6px;'>".$this->DrawFieldByType("add", $v_key, $v_val, $this->params, false, $language_dir)."</td>\n";
						echo "</tr>\n";
						if(empty($first_field_focus)) $first_field_focus = $v_key;
					}
				}
				echo "</table>\n";
				echo "</fieldset>\n";				
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
			}else{
				if($val['type'] != "hidden"){
					echo "<tr id='mg_row_".$key."'>\n";
					echo "  <td width='25%'><label for='".$key."'>".$val['title']."</label>".((isset($val['required']) && $val['required']) ? " <font color='#c13a3a'>*</font>" : "").":</td>\n";
					echo "  <td>".$this->DrawFieldByType("add", $key, $val, $this->params, false, $language_dir)."</td>\n";
					echo "</tr>\n";				
					if(empty($first_field_focus)) $first_field_focus = $key;
				}							
			}
		}				
		echo "<tr><td colspan='2' height='15px' nowrap></td></tr>\n";
		echo "<tr>\n
				<td colspan='2'>
					<input class='mgrid_button' type='button' name='subAddNewRecord' value=\""._BUTTON_CREATE."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"create\");'>
					<input class='mgrid_button' type='button' name='btnCancel' value=\""._BUTTON_CANCEL."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\");'>
				</td>
			  <tr>\n";		
		echo "</table><br>\n";
		echo "</form>\n";

		$focus_field = ($this->errorField != "") ? $this->errorField : $first_field_focus;
		if(!empty($focus_field)) echo "<script type='text/javascript'>__mgSetFocus('".$focus_field."');</script>";
			
		$this->DrawRunningTime();
		$this->DrawErrors();
		$this->DrawWarnings();
		$this->DrawSQLs();	
		$this->DrawPostInfo();	
	}

	
	/***********************************************************************
	 *
	 *	Draw Edit Mode
	 *	
	 ***********************************************************************/
	public function DrawEditMode($rid = "0", $operation = "", $operation_field = "", $buttons = array("reset"=>false, "cancel"=>true))
	{		
		$this->IncludeJSFunctions("edit");
		
		$this->curRecordId = $rid;
		
		$sorting_fields  = self::GetParameter('sorting_fields');
		$sorting_types   = self::GetParameter('sorting_types');
		$page 			 = self::GetParameter('page');
		$operation 		 = self::GetParameter('operation');
		$operation_type  = self::GetParameter('operation_type');
		$operation_field = self::GetParameter('operation_field');
		$search_status   = self::GetParameter('search_status');
		// prepare language direction for textboxes, textareas etc..		
		$language_dir    = @Languages::GetLanguageDirection($this->languageId);
		
		echo "\n<form name='frmMicroGrid_".$this->tableName."' id='frmMicroGrid_".$this->tableName."' action='".$this->formActionURL."' method='post' enctype='multipart/form-data'>\n";
		echo draw_hidden_field("mg_prefix", $this->uPrefix)."\n";
		echo draw_hidden_field("mg_action", "update")."\n";
		echo draw_hidden_field("mg_rid", $this->curRecordId)."\n";
		echo draw_hidden_field("mg_sorting_fields", $sorting_fields)."\n";
		echo draw_hidden_field("mg_sorting_types", $sorting_types)."\n";
		echo draw_hidden_field("mg_page", $page)."\n";
		echo draw_hidden_field("mg_operation", "")."\n";
		echo draw_hidden_field("mg_operation_type", "")."\n";
		echo draw_hidden_field("mg_operation_field", "")."\n";
		echo draw_hidden_field("mg_search_status", $search_status)."\n";
		echo draw_hidden_field("mg_language_id", $this->languageId)."\n";
		echo draw_hidden_field("mg_operation_code", self::GetRandomString(20))."\n";
		echo draw_token_field()."\n";
		
		// save filter (search) data for view mode
		if($this->isFilteringAllowed){
			foreach($this->arrFilteringFields as $key => $val){
				//if($val['type'] == "text"){
					$filter_field_value = ($search_status == "active") ? self::GetParameter("filter_by_".$val['table'].$val['field'], false) : "";
					echo draw_hidden_field("filter_by_".$val['table'].$val['field'], $filter_field_value)."\n";
				//}
			}
		}

		// 1. prepare password fields
		foreach($this->arrEditModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						// prepare password
						if($v_val['type'] == "password"){
							$password_field = $this->UncryptPasswordValue($v_key, $v_val);
							$this->EDIT_MODE_SQL = str_replace($this->tableName.".".$v_key, $password_field, $this->EDIT_MODE_SQL);
						}											
					}					
				}				
			}else{
				// prepare password
				if($val['type'] == "password"){
					$password_field = $this->UncryptPasswordValue($key, $val);
					$this->EDIT_MODE_SQL = str_replace($this->tableName.".".$key, $password_field, $this->EDIT_MODE_SQL);
				}
			}
		}						

		$this->EDIT_MODE_SQL = str_replace("_RID_", $this->curRecordId, $this->EDIT_MODE_SQL);
		$this->result = database_query($this->EDIT_MODE_SQL, DATA_AND_ROWS);
        if(!$this->result[1]) { echo _WRONG_PARAMETER_PASSED; return false; }
		if($this->debug) $this->arrSQLs['select_edit_mode'] = $this->EDIT_MODE_SQL;
		
		//----------------------------------------------------------------------
		// perform operations before drawing Edit Mode
		$this->BeforeEditRecord();

		// 1. draw hidden fields
		// 2. delete files/images
		foreach($this->arrEditModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						// delete file/image
						if($operation == "remove" && $operation_field != "" && ($v_key == $operation_field)){
							$this->RemoveFileImage($this->curRecordId, $operation_field, $v_val['target'], $this->result[0][0][$v_key]);
							$this->result[0][0][$v_key] = "";
						}
						// draw hidden field
						if($v_val['type'] == "hidden"){
							draw_hidden_field($v_key, (($v_val['default'] != "") ? $v_val['default'] : $this->result[0][0][$v_key]));
							echo "\n";
						}
					}					
				}				
			}else{
				// delete file/image
				if($operation == "remove" && $operation_field != "" && ($key == $operation_field)){
					$this->RemoveFileImage($this->curRecordId, $operation_field, $val['target'], $this->result[0][0][$key]);
					$this->result[0][0][$key] = "";
				}
				// draw hidden field
				if($val['type'] == "hidden"){
					draw_hidden_field($key, (($val['default'] != "") ? $val['default'] : $this->result[0][0][$key]));
					echo "\n";
				}
			}
		}								

		// draw Edit Form
		echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";
		$blocks_count = "0";
		foreach($this->arrEditModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				echo "</table>".(($blocks_count++ > 0) ? "<br>" : "")."\n";
				echo "<fieldset style='padding:5px;margin-left:5px;margin-right:10px;'>";
				if(isset($val['separator_info']['legend'])) echo "<legend>".$val['separator_info']['legend']."</legend>";
				echo "\n<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info" && $v_val['type'] != "hidden"){
						echo "<tr id='mg_row_".$v_key."'>\n";
						echo "<td width='25%'><label for='".$v_key."'>".$v_val['title']."</label>".((isset($v_val['required']) && $v_val['required']) ? " <font color='#c13a3a'>*</font>" : "").":</td>\n";
						if(!$this->ParamEmpty($v_key) && ($v_val['type'] != "checkbox")){
							echo "<td style='padding-left:6px;'>".$this->DrawFieldByType("edit", $v_key, $v_val, $this->params, false, $language_dir)."</td>\n";
						}else{
							echo "<td style='padding-left:6px;'>".$this->DrawFieldByType("edit", $v_key, $v_val ,$this->result[0][0], false, $language_dir)."</td>\n";
						}
						echo "</tr>\n";
					}
				}
				echo "</table>\n";
				echo "</fieldset>\n";				
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
			}else{
				if($val['type'] != "hidden"){
					echo "<tr id='mg_row_".$key."'>\n";
					echo "<td width='25%'><label for='".$key."'>".$val['title']."</label>".((isset($val['required']) && $val['required']) ? " <font color='#c13a3a'>*</font>" : "").":</td>\n";
					if(!$this->ParamEmpty($key) && ($val['type'] != "checkbox")){
						echo "<td>".$this->DrawFieldByType("edit", $key, $val, $this->params, false, $language_dir)."</td>\n";													
					}else{
						echo "<td>".$this->DrawFieldByType("edit", $key, $val, $this->result[0][0], false, $language_dir)."</td>\n";						
					}
					echo "</tr>\n";				
				}
			}
		}
		echo "<tr><td colspan='2' height='5px' nowrap></td></tr>";
		echo "<tr>
				<td colspan='2'>
					<input class='mgrid_button' type='button' name='subUpdateRecord' value=\""._BUTTON_UPDATE."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"update\");'>
					".(($buttons["reset"]) ? "<input class='mgrid_button' type='reset' name='btnReset' value=\""._BUTTON_RESET."\" / >" : "")."
					".(($buttons["cancel"]) ? "<input class='mgrid_button' type='button' name='btnCancel' value=\""._BUTTON_CANCEL."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\");'>" : "")."					
				</td>
			  <tr>\n";		
		echo "</table><br>\n";
		echo "</form>\n";

		if($this->errorField != "") echo "<script type='text/javascript'>__mgSetFocus('".$this->errorField."');</script>";	   
		
		$this->DrawRunningTime();
		$this->DrawErrors();
		$this->DrawWarnings();
		$this->DrawSQLs();	
		$this->DrawPostInfo();	
	}


	
	/***********************************************************************
	 *
	 *	Draw Details Mode
	 *	
	 ***********************************************************************/
	public function DrawDetailsMode($rid = "0")
	{		
		$this->IncludeJSFunctions();
		
		$this->curRecordId = $rid;
		
		$sorting_fields  = self::GetParameter('sorting_fields');
		$sorting_types   = self::GetParameter('sorting_types');
		$page 			 = self::GetParameter('page');
		$operation 		 = self::GetParameter('operation');
		$operation_type  = self::GetParameter('operation_type');
		$operation_field = self::GetParameter('operation_field');
		$search_status   = self::GetParameter('search_status');

		echo "\n<form name='frmMicroGrid_".$this->tableName."' id='frmMicroGrid_".$this->tableName."' action='".$this->formActionURL."' method='post' enctype='multipart/form-data'>\n";
		echo draw_hidden_field("mg_prefix", $this->uPrefix)."\n";
		echo draw_hidden_field("mg_action", "details")."\n";
		echo draw_hidden_field("mg_rid", $this->curRecordId)."\n";
		echo draw_hidden_field("mg_sorting_fields", $sorting_fields)."\n";
		echo draw_hidden_field("mg_sorting_types", $sorting_types)."\n";
		echo draw_hidden_field("mg_page", $page)."\n";
		// to prevent re-sorting on back to view mode $operation = ""
		echo draw_hidden_field("mg_operation", "")."\n";
		echo draw_hidden_field("mg_operation_type", $operation_type)."\n";
		echo draw_hidden_field("mg_operation_field", $operation_field)."\n";
		echo draw_hidden_field("mg_search_status", $search_status)."\n";
		echo draw_hidden_field("mg_language_id", $this->languageId)."\n";
		echo draw_token_field()."\n";
		
		// save filter (search) data for view mode
		if($this->isFilteringAllowed){
			foreach($this->arrFilteringFields as $key => $val){
				//if($val['type'] == "text"){
					$filter_field_value = ($search_status == "active") ? self::GetParameter("filter_by_".$val['table'].$val['field'], false) : "";
					echo draw_hidden_field("filter_by_".$val['table'].$val['field'], $filter_field_value)."\n";
				//}
			}
		}

		// ger result for edited row		
		$this->DETAILS_MODE_SQL = str_replace("_RID_", $this->curRecordId, $this->DETAILS_MODE_SQL);
		$this->result = database_query($this->DETAILS_MODE_SQL, DATA_AND_ROWS);
        if(!$this->result[1]) { echo _WRONG_PARAMETER_PASSED; return false; }
		if($this->debug) $this->arrSQLs['select_details'] = $this->DETAILS_MODE_SQL;
	
		// draw Details Form
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='2' class='mgrid_table'>\n";
		echo "<tr><td colspan='2' height='5px' nowrap></td></tr>";
		foreach($this->arrDetailsModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				echo "</table><br>\n";
				echo "<fieldset style='padding:5px;margin-left:5px;margin-right:10px;'>";
				if(isset($val['separator_info']['legend'])) echo "<legend>".$val['separator_info']['legend']."</legend>";
				echo "\n<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						echo "<tr>\n";
						echo "  <td width='27%'>".$v_val['title'].":</td>\n";
						echo "  <td style='padding-left:6px;'>".$this->DrawFieldByType("details", $v_key, $v_val, $this->result[0][0], false)."</td>\n";
						echo "</tr>\n";
					}
				}
				echo "</table>\n";
				echo "</fieldset>\n";				
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' class='mgrid_table'>\n";		
			}else{
				if($val['type'] != "hidden"){
					echo "<tr>\n";
					echo "  <td width='25%'>".ucfirst($val['title']).":</td>\n";
					echo "  <td>".$this->DrawFieldByType("details", $key, $val, $this->result[0][0], false)."</td>\n";
					echo "</tr>\n";				
				}			
			}
		}
		echo "<tr><td colspan='2' height='5px' nowrap></td></tr>";
		echo "<tr>
				<td colspan='2'>
					<input class='mgrid_button' type='button' name='btnBack' value=\""._BUTTON_BACK."\" onclick='javascript:__mgDoPostBack(\"".$this->tableName."\", \"view\");'> 
				</td>
			  <tr>\n";		
		echo "</table><br>\n";
		echo "</form>\n";
		
		$this->AfterDetailsMode();

		$this->DrawRunningTime();
		$this->DrawErrors();
		$this->DrawWarnings();
		$this->DrawSQLs();	
		$this->DrawPostInfo();	
	}

	/***
	 * Draw field by type
	 *		@param $link
	 **/	
	public function DrawOperationLinks($links)
	{
		$this->operation_links = $links;
	}

	/***
	 * Draw field by type
	 *		@param $field_name
	 *		@param $field_array - ['field'] => array("".....)
	 **/	
	public function DrawFieldByType($mode, $field_name, $field_array = array(), $params = array(), $draw = true, $language_dir = "ltr")
	{
		$output = "";
		
		if($field_name == "") return false;

		//print_r($field_array);
		$direction    = ($language_dir == "rtl" || $language_dir == "ltr") ? " dir='".$language_dir."'" : "";
		$rid 		  = isset($params[$this->primaryKey]) ? $params[$this->primaryKey] : "";
		$field_type   = isset($field_array['type']) ? $field_array['type'] : "";
		$source       = isset($field_array['source']) ? $field_array['source'] : "";
		$readonly     = (isset($field_array['readonly']) && $field_array['readonly'] === true) ? true : false;
		$default 	  = isset($field_array['default']) ? $field_array['default'] : "";
		$true_value   = isset($field_array['true_value']) ? $field_array['true_value'] : "1";
		$width        = isset($field_array['width']) ? $field_array['width'] : "";
		$height       = isset($field_array['height']) ? $field_array['height'] : "";
		$image_width  = isset($field_array['image_width']) ? $field_array['image_width'] : "120px";
		$image_height = isset($field_array['image_height']) ? $field_array['image_height'] : "90px";
		$target       = isset($field_array['target']) ? $field_array['target'] : "";
		$maxlength    = isset($field_array['maxlength']) ? $field_array['maxlength'] : "";
		$editor_type  = isset($field_array['editor_type']) ? $field_array['editor_type'] : "";							
		$no_image     = isset($field_array['no_image']) ? $field_array['no_image'] : "";
		$required 	  = isset($field_array['required']) ? $field_array['required'] : false;
		$pre_html 	  = isset($field_array['pre_html']) ? $field_array['pre_html'] : "";
		$post_html 	  = isset($field_array['post_html']) ? $field_array['post_html'] : "";
		$format 	  = isset($field_array['format']) ? $field_array['format'] : "";
		$tooltip 	  = isset($field_array['tooltip']) ? $field_array['tooltip'] : "";
		$min_year     = isset($field_array['min_year']) ? $field_array['min_year'] : "90";
		$max_year     = isset($field_array['max_year']) ? $field_array['max_year'] : "10";
		$href         = isset($field_array['href']) ? $field_array['href'] : "#";
		$target       = isset($field_array['target']) ? $field_array['target'] : "";
		$format_parameter = isset($field_array['format_parameter']) ? $field_array['format_parameter'] : "";
		$javascript_event = isset($field_array['javascript_event']) ? $field_array['javascript_event'] : "";
		$visible      = (isset($field_array['visible']) && $field_array['visible']!=="") ? $field_array['visible'] : true;
		
		$atr_readonly = ($readonly) ? " readonly='readonly'" : "";
		$atr_disabled = ($readonly) ? " disabled='disabled'" : "";
		$css_disabled = ($readonly) ? " mgrid_disabled" : "";
		$attr_maxlength = ($maxlength != "") ? " maxlength='".intval($maxlength)."'" : "";

		$field_value  = isset($params[$field_name]) ? $params[$field_name] : "";
		if($mode == "add" && $field_value == "") $field_value = $default;
		if($this->isHtmlEncoding) $field_value = $this->GetDataDecoded($field_value);

		if($mode == "view"){
			// View Mode
			switch($field_type){
				case "link":
					$target_str = ($target != "") ? " target='".$target."'" : "";
					$href_str = $href;
					if(preg_match_all("/{.*?}/i", $href, $matches)){
						foreach($matches[0] as $key => $val){
							$val = trim($val, "{}");
							if(isset($params[$val])) $href_str = str_replace("{".$val."}", $params[$val], $href);
						}
					}
					$output = "<a href='".$href_str."'".$target_str.">".$field_value."</a>";
					break;

				case "enum":
					if(is_array($source)){
						foreach($source as $key => $val){
							if($field_value == $key){
								$output = $val;
								break;
							}
						}							
					}
					break;			

				case "image":
				    if($field_value == "" && $no_image != "") $field_value = $no_image;
					$output = "<img src='".$target.$field_value."' title='".$field_value."' alt='' width='".$image_width."' height='".$image_height."'>";				
					break;
				
				default:
				case "label":
					$title = "";
					if($format == "strip_tags"){
						$field_value = strip_tags($field_value);
					}else if($format == "date" && $field_value != ""){
						if(empty($format_parameter)) $format_parameter = "Y-m-d H:i:s";
						$field_value = ((int)$field_value != 0) ? date($format_parameter, strtotime($field_value)) : "";
					}else if($format == "nl2br"){
						$field_value = nl2br($field_value);
					}
					if($maxlength != "" && $this->IsInteger($maxlength)){
						if(strlen($field_value) > $maxlength){
							$title = $field_value;
							$field_value = substr($field_value, 0, $maxlength)."...";						
						}
					}else if($tooltip != ""){
						$title = $tooltip;
					}
					$output = "<label class='mgrid_label' title='".$this->GetDataDecoded(strip_tags($title))."'>".$this->GetDataDecodedText($field_value)."</label>";
					$output .= $post_html;
					break;			
			}			
		}else{
			// Add/Edit/Detail Modes 
			switch($field_type){
				case "checkbox":
					$checked = "";				
					if($mode == "add" && $default == $true_value){
						$checked = " checked='checked'";
					}else{
						if($field_value == "1") $checked = " checked='checked'";
					}
					if($readonly){
						$output  = "<input type='checkbox' name='".$field_name."' id='".$field_name."' class='mgrid_checkbox' value='1'".$checked.$atr_disabled.">";						
						$output .= "<input type='hidden' name='".$field_name."' id='".$field_name."' value='1'>";
					}else{
						$output = "<input type='checkbox' name='".$field_name."' id='".$field_name."' class='mgrid_checkbox' value='1'".$checked.">";						
					}
					$output .= $post_html;
					break;
				case "date":
				case "datetime":
					if($mode != "details"){
						$lang = array();
						$lang['months'][1] = (defined('_JANUARY')) ? _JANUARY : "January";
						$lang['months'][2] = (defined('_FEBRUARY')) ? _FEBRUARY : "February";
						$lang['months'][3] = (defined('_MARCH')) ? _MARCH : "March";
						$lang['months'][4] = (defined('_APRIL')) ? _APRIL : "April";
						$lang['months'][5] = (defined('_MAY')) ? _MAY : "May";
						$lang['months'][6] = (defined('_JUNE')) ? _JUNE : "June";
						$lang['months'][7] = (defined('_JULY')) ? _JULY : "July";
						$lang['months'][8] = (defined('_AUGUST')) ? _AUGUST : "August";
						$lang['months'][9] = (defined('_SEPTEMBER')) ? _SEPTEMBER : "September";
						$lang['months'][10] = (defined('_OCTOBER')) ? _OCTOBER : "October";
						$lang['months'][11] = (defined('_NOVEMBER')) ? _NOVEMBER : "November";
						$lang['months'][12] = (defined('_DECEMBER')) ? _DECEMBER : "December";
						
						if($field_type == "datetime"){
							$datetime_format = "Y-m-d H:i:s";
							$datetime_empty_value = "0000-00-00 00:00:00";
						}else{
							$datetime_format = "Y-m-d";
							$datetime_empty_value = "0000-00-00";
						}
						$date_datetime_format = @date($datetime_format);
						
						$year = substr($field_value, 0, 4);
						$month = substr($field_value, 5, 2);
						$day = substr($field_value, 8, 2);
						if($field_type == "datetime"){
							$hour = substr($field_value, 11, 2);
							$minute = substr($field_value, 14, 2);
							$second = substr($field_value, 17, 2);							
						}						
						
						$arr_ret_date = array();
						$arr_ret_date["y"] = "<select name='".$field_name."__nc_year' id='".$field_name."__nc_year' onChange='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\")'><option value=''>"._YEAR."</option>"; for($i=@date("Y")-$min_year; $i<=@date("Y")+$max_year; $i++) { $arr_ret_date["y"] .= "<option value='".$i."'".(($year == $i) ? " selected='selected'" : "").">".$i."</option>"; }; $arr_ret_date["y"] .= "</select>";                            
						$arr_ret_date["m"] = "<select name='".$field_name."__nc_month' id='".$field_name."__nc_month' onChange='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\")'><option value=''>"._MONTH."</option>"; for($i=1; $i<=12; $i++) { $arr_ret_date["m"] .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($month == $i) ? " selected='selected'" : "").">".$lang['months'][$i]."</option>"; }; $arr_ret_date["m"] .= "</select>";
						$arr_ret_date["d"] = "<select name='".$field_name."__nc_day' id='".$field_name."__nc_day' onChange='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\")'><option value=''>"._DAY."</option>"; for($i=1; $i<=31; $i++) { $arr_ret_date["d"] .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($day == $i) ? " selected='selected'" : "").">".(($i < 10) ? "0".$i : $i)."</option>"; }; $arr_ret_date["d"] .= "</select>";

						$output  = $arr_ret_date[strtolower(substr($datetime_format, 0, 1))];
						$output .= $arr_ret_date[strtolower(substr($datetime_format, 2, 1))];
						$output .= $arr_ret_date[strtolower(substr($datetime_format, 4, 1))];

						if($field_type == "datetime"){
							$output .= " : ";
							$output .= "<select name='".$field_name."__nc_hour' id='".$field_name."__nc_hour' onChange='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\")'><option value=''>"._HOUR."</option>"; for($i=0; $i<=23; $i++) { $output .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($hour == $i) ? " selected='selected'" : "").">".(($i < 10) ? "0".$i : $i)."</option>"; }; $output .= "</select>";
							$output .= "<select name='".$field_name."__nc_minute' id='".$field_name."__nc_minute' onChange='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\")'><option value=''>"._MIN."</option>"; for($i=0; $i<=59; $i++) { $output .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($minute == $i) ? " selected='selected'" : "").">".(($i < 10) ? "0".$i : $i)."</option>"; }; $output .= "</select>";                    
							$output .= "<select name='".$field_name."__nc_second' id='".$field_name."__nc_second' onChange='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\")'><option value=''>"._SEC."</option>"; for($i=0; $i<=59; $i++) { $output .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($second == $i) ? " selected='selected'" : "").">".(($i < 10) ? "0".$i : $i)."</option>"; }; $output .= "</select>";
						}
						
						$output .= " <a href='javascript:void(\"date|set\");' onclick='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\", \"".@date($datetime_format)."\", \"".(@date("Y")-$min_year)."\", false)'>[ ".$date_datetime_format." ]</a>";
						if(!$required) $output .= " <a href='javascript:void(\"date|reset\");' onclick='setCalendarDate(\"frmMicroGrid_".$this->tableName."\", \"".$field_name."\", \"".$datetime_format."\", \"".$datetime_empty_value."\", \"1\", false)'>[ "._RESET." ]</a>";
						$output .= "<input style='width:0px;border:0px;margin:0px;padding:0px;' type='text' name='".$field_name."' id='".$field_name."' value=\"".$field_value."\">";					
					}else{
						if($format == "date"){
							if($field_value != "" && $field_value != "0000-00-00"){
								if(empty($format_parameter)) $format_parameter = "Y-m-d H:i:s";
								$field_value = date($format_parameter, strtotime($field_value));
							}else{
								$field_value = "";
							}
						}
						$output = "<label class='mgrid_label'>".$this->GetDataDecoded($field_value)."</label>";				
					}
					break;
				case "file":
				case "image":
					if(strtolower(SITE_MODE) == "demo") $atr_readonly = " disabled='disabled'";
				
					if(($mode == "edit" || $mode == "details")){
						//if(file_exists($target.$field_value)){						
						//}
						if($mode == "edit"){
							if($field_value != ""){
								$output = ($field_type == "file") ? $field_value : "<img src='".$target.$field_value."' title='".$field_value."' alt='' width='".$image_width."' height='".$image_height."'>";								
								if($required) $output .= "<input type='hidden' name='".$field_name."' id='".$field_name."' value=\"".$field_value."\">";
								if(strtolower(SITE_MODE) != "demo" && !$readonly) $output .= "<br><a href='".$this->formActionURL."&mg_prefix=".$this->uPrefix."&mg_action=edit&mg_rid=".$rid."&mg_operation=remove&mg_operation_field=".$field_name."'>["._DELETE_WORD."]</a>";
							}else{
								$output = "<input type='file' name='".$field_name."' id='".$field_name."' class='mgrid_file' ".$atr_readonly.">";		
							}
						}else if($mode == "details"){
							if($field_value == "" && $no_image != "") $field_value = $no_image;
							$output = ($field_type == "file") ? $field_value : "<img src='".$target.$field_value."' title='".$field_value."' alt='' width='".$image_width."' height='".$image_height."'>";								
						}
					}else{
						$output = "<input type='file' name='".$field_name."' id='".$field_name."' class='mgrid_file' ".$atr_readonly.">";
					}
					break;
				
				case "enum":
					if(is_array($source)){
						if($mode == "add" || $mode == "edit"){
							$output_start = "<select class='mgrid_select' name='".$field_name."'
													id='".$field_name."'
													".(($javascript_event!="") ? " ".$javascript_event : "")."
													style='".(($width!="")?"width:".$width.";":"")."'
													".(($readonly) ? "disabled='disabled'" : "").">";
							$output_options = "<option value=''>-- "._SELECT." --</option>";
							foreach($source as $key => $val){
								$output_options .= "<option value=\"".$key. "\" ";
								$output_options .= ($field_value == $key) ? "selected='selected' " : "";
								$output_options .= ">".$val."</option>";
							}
							$output = $output_start.$output_options."</select>";												
						}else{
							foreach($source as $key => $val){
								if($field_value == $key){
									$output = $val;
									break;
								}
							}							
						}
					}
					$output .= $post_html;
					break;			
				
				case "label":
					$title = "";
					if($format == "strip_tags"){
						$field_value = strip_tags($field_value);
					}else if($format == "date" && $field_value != ""){
						if(empty($format_parameter)) $format_parameter = "Y-m-d H:i:s";
						$field_value = ((int)$field_value != 0) ? date($format_parameter, strtotime($field_value)) : "";
					}else if($format == "nl2br"){
						$field_value = nl2br($field_value);
					}
					if($maxlength != "" && $this->IsInteger($maxlength)){
						if(strlen($field_value) > $maxlength){
							$title = $field_value;
							$field_value = substr($field_value, 0, 18)."...";						
						}
					}
					$output = $pre_html."<label class='mgrid_label' title='".strip_tags($title)."'>".$this->GetDataDecoded($field_value)."</label>".$post_html;			
					break;			

				case "object":
					if(!preg_match("/youtube/i", $field_value)){
						$output = "<object width='".$width."' height='".$height."'>
								   <param name='movie' value='".$field_value."'>
								   <embed src='".$field_value."' width='".$width."' height='".$height."'></embed>
								   </object>";														
					}else{
						$output = $field_value;
					}
					break;			

				case "password":
					if($mode == "add" || $mode == "edit"){
						$output = "<input class='mgrid_text' name='".$field_name."' id='".$field_name."' style='".(($width!="")?"width:".$width.";":"")."' value=\"".$this->GetDataDecoded($field_value)."\" ".$atr_readonly.$attr_maxlength.">";
					}else{
						$output = "<label class='mgrid_label'>*****</label>";				
					}				
					break;			
	
				case "textarea":
					$output = "";
					if($editor_type == "wysiwyg"){
						$wysiwyg_state = (isset($_COOKIE["wysiwyg_".$field_name."_mode"])) ? $_COOKIE["wysiwyg_".$field_name."_mode"] : "0";
						$output .= "<script type='text/javascript'>";
						$output .= "__mgAddListener(document, 'load', function() { toggleEditor('".$wysiwyg_state."','".$field_name."', '".$height."'); }, false); \n";
						$output .= "__mgAddListener(this, 'load', function() { toggleEditor('".$wysiwyg_state."','".$field_name."', '".$height."'); }, false); \n";					
						$output .= "</script>";						
						$output .= "[ <a id='lnk_0_".$field_name."' style='display:none;' href=\"javascript:toggleEditor('0','".$field_name."');\" title='Switch to Simple Mode'>"._SIMPLE."</a><a id='lnk_1_".$field_name."' href=\"javascript:toggleEditor('1','".$field_name."');\" title='Switch to Advanced Mode'>"._ADVANCED."</a> ]<br>";
					}
					$output .= $pre_html."<textarea class='mgrid_textarea' name='".$field_name."' id='".$field_name."' style='".(($width!="")?"width:".$width.";":" rows='7'").(($height!="")?"height:".$height.";":" cols='60'")."' ".$atr_disabled.$direction.">".$this->GetDataDecodedText($field_value)."</textarea>".$post_html;
					
					break;			

				default:
				case "textbox":
					$output = $pre_html."<input class='mgrid_text".$css_disabled."' name='".$field_name."' id='".$field_name."'  style='".(($width!="")?"width:".$width.";":"").(($visible == false)?"display:none;'":"")."' value=\"".$this->GetDataDecoded($field_value)."\" ".$atr_readonly.$attr_maxlength.$direction.">".$post_html;
					break;			
				
			}			
		}		
		
		if($draw) echo $output;
		else return $output;		
	}

	
	/***
	 * Get data encoded text
	 *		@param $string
	 **/
	private function GetDataEncodedText($string = "")
	{
		return $string;
	}
	
	
	/***
	 * Get data decoded text
	 *		@param $string
	 **/
	private function GetDataDecodedText($string = "")
	{
		$search  = array("\\\\","\\0","\\n","\\r","\Z");
		$replace = array("\\","\0","\n","\r","\x1a");
		return str_replace($search, $replace, $string);
	}	
	
	/***
	 * Get data encoded
	 *		@param $string
	 **/
	private function GetDataEncoded($string = "")
	{
		$search	 = array("\\","\0","\n","\r","\x1a","'",'"',"\'",'\"');
		$replace = array("\\\\","\\0","\\n","\\r","\Z","\'",'\"',"\\'",'\\"');
		return str_replace($search, $replace, $string);
	}	
	
	/***
	 * Get data decoded 
	 *		@param $string
	 **/
	private function GetDataDecoded($string = "")
	{
		$search  = array("\\\\","\\0","\\n","\\r","\Z","\'",'\"','"',"'");
		$replace = array("\\","\0","\n","\r","\x1a","\&#039;",'\&#034;',"&#034;","&#039;");
		return str_replace($search, $replace, $string);
	}

	////////////////////////////////////////////////////////////////////////////
	// Validation methods
	/***
	 * F5 validation procedure
	 **/
	private function F5Validation()
	{
		$operation_code = self::GetParameter('operation_code');
        if(isset($_SESSION[$this->uPrefix.'_operation_code']) && $operation_code != "" &&
			    ($_SESSION[$this->uPrefix.'_operation_code'] == $operation_code))
		{		
            $this->error = _OPERATION_WAS_ALREADY_COMPLETED;
			return false;		
		}
		return true;		
	}

	/***
	 * Fields validation procedure
	 * 		@param $array - validation array of fields
	 **/
	private function FieldsValidation($array)
	{		
		if(!is_array($array)) return false;
		
		foreach($array as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						if(!$this->ValidateField($v_key, $v_val)){
							$this->errorField = $v_key;
							return false;
						}						
					}
				}
			}else{
				if(!$this->ValidateField($key, $val)){
					$this->errorField = $key;
					return false;
				}										
			}
		}
		return true;
	}
	
	/***
	 * Validate field
	 **/
	private function ValidateField($key, $val)
	{
		$validation_type = isset($val['validation_type']) ? strtolower($val['validation_type']) : "";
		$validation_type_parts = explode("|", $validation_type);
		$validation_type = isset($validation_type_parts[0]) ? $validation_type_parts[0] : "";
		$validation_sub_type = isset($validation_type_parts[1]) ? $validation_type_parts[1] : "";

		$validation_maxlength = (isset($val['validation_maxlength']) && $this->IsInteger($val['validation_maxlength'])) ? $val['validation_maxlength'] : "";
		$validation_minlength = (isset($val['validation_minlength']) && $this->IsInteger($val['validation_minlength'])) ? $val['validation_minlength'] : "";
		$validation_maximum = (isset($val['validation_maximum']) && is_numeric($val['validation_maximum'])) ? $val['validation_maximum'] : "";
		$validation_minimum = (isset($val['validation_minimum']) && is_numeric($val['validation_minimum'])) ? $val['validation_minimum'] : "";
		
		$field_type = isset($val['type']) ? strtolower($val['type']) : "";
		$required = isset($val['required']) ? $val['required'] : false;
		$readonly = isset($val['readonly']) ? $val['readonly'] : false;

		// check image fields
		if($field_type == "image" || $field_type == "file"){
			$field_value = isset($_FILES[$key]['name']) ? $_FILES[$key]['name'] : "";
			if($field_value == "") $field_value = self::GetParameter($key, false); 
			if($required && !$field_value){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_CANNOT_BE_EMPTY);
				return false;
			}
			return true;
		}		

		if($required && isset($this->params[$key]) && $this->params[$key] === "" && !$readonly){ // 
			$this->error = str_replace("_FIELD_", $val['title'], _FIELD_CANNOT_BE_EMPTY);
			return false;
		}
		if(isset($this->params[$key]) && $this->params[$key] != ""){		
			if($validation_type == "email" && !$this->IsEmail($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_EMAIL);
				return false;					
			}else if($validation_type == "alpha" && !$this->IsAlpha($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_ALPHA);
				return false;					
			}else if($validation_type == "numeric"){
				if(!$this->IsNumeric($this->params[$key])){
					$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_NUMERIC);
					return false;
				}else if($validation_sub_type == "positive" && $this->params[$key] < 0){
					$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_NUMERIC_POSITIVE);
					return false;					
				}
			}else if($validation_type == "float"){
				if(!$this->IsFloat($this->params[$key])){
					$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_FLOAT);
					return false;
				}else if($validation_sub_type == "positive" && $this->params[$key] < 0){
					$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_FLOAT_POSITIVE);
					return false;
				}				
			}else if($validation_type == "alpha_numeric" && !$this->IsAlphaNumeric($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_ALPHA_NUMERIC);
				return false;					
			}else if($validation_type == "text" && !$this->IsText($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_TEXT);
				return false;					
			}else if($validation_type == "password" && !$this->IsPassword($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_PASSWORD);
				return false;					
			}else if($validation_type == "ip_address" && !$this->IsIpAddress($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_IP_ADDRESS);
				return false;					
			}else if($validation_type == "date" && !$this->IsDate($this->params[$key])){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MUST_BE_DATE);
				return false;					
			}
			
			// check maxlength
			if($validation_maxlength > 0 && strlen($this->params[$key]) > $validation_maxlength){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_LENGTH_EXCEEDED);
				$this->error = str_replace("_LENGTH_", $validation_maxlength, $this->error);
				return false;									
			}
			
			// check minlength
			if($validation_minlength > 0 && strlen($this->params[$key]) < $validation_minlength){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_MIN_LENGTH_ALERT);
				$this->error = str_replace("_LENGTH_", $validation_minlength, $this->error);
				return false;									
			}
			
			// check min value
			if($this->params[$key] < $validation_minimum){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_VALUE_MINIMUM);
				$this->error = str_replace("_MIN_", $validation_minimum, $this->error);
				return false;									
			}

			// check max value
			if($validation_maximum > 0 && ($this->params[$key] > $validation_maximum)){
				$this->error = str_replace("_FIELD_", $val['title'], _FIELD_VALUE_EXCEEDED);
				$this->error = str_replace("_MAX_", $validation_maximum, $this->error);
				return false;									
			}
		}		
		return true;
	}
	
	
	/***
	 * Email Validation
	 **/
	protected function IsEmail($field = "")
	{
		$strict = false;
		$regex = $strict ? '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' :  '/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';
		
		if(preg_match($regex, trim($field))) {
		   return true;
		} else {
		   return false;
		}		
	}

	/***
	 * Numeric Validation
	 **/
	protected function IsNumeric($field = "")
	{
		return ($field == strval(intval($field))) ? true : false;
	}

	/***
	 * Float Number Validation
	 **/
	protected function IsFloat($field = "")
	{
		return ($field == strval(floatval($field))) ? true : false;
	}

	/***
	 * Alpha Validation
	 **/
	protected function IsAlpha($field = "")
	{
		if(function_exists("ctype_alpha") && ctype_alpha($field)){
			return true;
		}else if(preg_match("/[A-Za-z]/",$field)){					
			return true;
		}else{
			return false;
		}		
	}

	/***
	 * Alpha Numeric Validation
	 **/
	protected function IsAlphaNumeric($field = "")
	{
		if(function_exists("ctype_alnum") && ctype_alnum($field)){
			return true;
		}else if(preg_match("/[A-Za-z0-9]/",$field)){					
			return true;
		}else{
			return false;
		}		
	}

	/***
	 * Text Validation
	 **/
	protected function IsText($field = "")
	{
		if($this->IsAlphaNumeric() || $field != ""){
			return true;
		}else{
			return false;
		}		
	}

	/***
	 * Password Validation
	 **/
	protected function IsPassword($field = "")
	{
		if(strlen($field) >= 6 && preg_match("/[A-Za-z0-9]/",$field)){
			return true;
		} else {
			return false;
		}		
	}
	
	/***
	 * IP Address Validation
	 **/
	protected function IsIpAddress($field = "")
	{
		// format of the ip address is matched
		if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$field)){
			// all the intger values are separated
			$parts=explode(".",$field);
			// check each part can range from 0-255
			foreach($parts as $ip_parts){
				//if number is not within range of 0-255
				if(intval($ip_parts)>255 || intval($ip_parts)<0) return false; 				
			}
			return true;
		}else{
			return false;
		}
	}

	/***
	 * Date Validation
	 **/
	protected function IsDate($field = "")
	{
        if($field == "0000-00-00") return true;
		$year  = (int)substr($field, 0, 4);
		$month = (int)substr($field, 5, 2);
		$day   = (int)substr($field, 8, 2);	
		if(checkdate($month, $day, $year)){		
			return true;
		}else{
			return false;
		}    
	}

	/***
	 * Integer Validation
	 **/
	protected function IsInteger($field = "")
	{
        if(is_numeric($field) === true){
            if((int)$field == $field){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
	
	
	/***
	 * Crypt password value
	 **/
	private function CryptPasswordValue($key, $field_array, $value)
	{
		$cryptography = $field_array['cryptography'];
		$cryptography_type = strtolower($field_array['cryptography_type']);
		$aes_password = $field_array['aes_password'];
        if($cryptography === true || strtolower($cryptography) == "true"){
            if($cryptography_type == "md5"){
                return "MD5('".$value."')";
            }else if($cryptography_type == "aes"){
                return "AES_ENCRYPT('".$value."', '".$aes_password."')";                
            }
        }
        return "'".$value."'";    
	}
	
	/***
	 * Uncrypt password value
	 **/
	private function UncryptPasswordValue($key, $field_array)
	{
		$output = $key;
		$cryptography = $field_array['cryptography'];
		$cryptography_type = strtolower($field_array['cryptography_type']);
		$aes_password = $field_array['aes_password'];
		if($cryptography === true || strtolower($cryptography) == "true"){
			if($cryptography_type == "aes"){
				$output = "AES_DECRYPT(".$key.", '".$aes_password."') as ".$key;    
			}
		}
        return $output;    
	}
	
	/***
	 * Check if there are unique fields
	 **/
	private function FindUniqueFields($mode = "add", $rid = "0")
	{
		foreach($this->params as $key => $val){
			$arrModeFields = ($mode == "add") ? $this->arrAddModeFields : $this->arrEditModeFields;
			$title = "";
			$fp_unique = false;
			if(array_key_exists($key, $arrModeFields)){
				$fp_unique = isset($arrModeFields[$key]["unique"]) ? $arrModeFields[$key]["unique"] : false;
				$title = isset($arrModeFields[$key]["title"]) ? $arrModeFields[$key]["title"] : "";
			}else{
				foreach($arrModeFields as $v_key => $v_val){
					if(array_key_exists($key, $v_val)){
						$fp_unique = isset($v_val[$key]["unique"]) ? $v_val[$key]["unique"] : false;
						$title = isset($v_val[$key]["title"]) ? $v_val[$key]["title"] : "";
					}
				}
			}			
			if($fp_unique === true || strtolower($fp_unique) == "true"){
				$sql = "SELECT COUNT(*) as cnt FROM `".$this->tableName."` WHERE ".$key."='".mysql_real_escape_string($val)."'";
				if($mode == "edit"){
					$sql .= " AND ".$this->primaryKey." != '".$rid."'";
				}
				$records = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
				if($records['cnt'] > 0){
					$this->error = str_replace("_FIELD_", $title, _FILED_UNIQUE_VALUE_ALERT);
					return true;					
				}
			}
		}
		return false;
	}
	
	/***
	 * Include JavaScript
	 **/
	protected function IncludeJSFunctions($mode = "")
	{
		echo "<script type='text/javascript' src='include/classes/js/microgrid.js'></script>\n";
		echo "<script type='text/javascript' src='include/classes/js/lang/en.js'></script>\n";

		// check for WYSIWYG Editor
		$include_wysiwyg_editor = false;
		if($mode == "add"){
			$arrModeFields = &$this->arrAddModeFields;
		}else if($mode == "edit"){
			$arrModeFields = &$this->arrEditModeFields;
		}
		if($mode == "add" || $mode == "edit"){
			foreach($arrModeFields as $key => $val){
				if($include_wysiwyg_editor == true) break;
				if(preg_match("/separator/i", $key) && is_array($val)){
					foreach($val as $v_key => $v_val){
						if($v_key != "separator_info"){
							$type = isset($v_val['type']) ? $v_val['type'] : "";
							$editor_type = isset($v_val['editor_type']) ? $v_val['editor_type'] : "";							
							if($type == "textarea" && $editor_type == "wysiwyg") $include_wysiwyg_editor = true;
						}					
					}				
				}else{
					$type = isset($val['type']) ? $val['type'] : "";
					$editor_type = isset($val['editor_type']) ? $val['editor_type'] : "";							
					if($type == "textarea" && $editor_type == "wysiwyg") $include_wysiwyg_editor = true;
				}
			}		
		}

		if($include_wysiwyg_editor == true){
			echo "<script type='text/javascript' src='modules/tinymce/tiny_mce.js'></script>";
			echo "<script type='text/javascript' src='include/classes/js/microgrid_tinymce.js'></script>";
		}

	}
	
	/**
	 *	Draw system errors
	 *
	*/	
	protected function DrawErrors($draw = true)
	{
		if(!$this->debug) return false;
		$output = "<br><div style='width:100%; text-align:left; color:#860000;'>";
		$output .= "Errors(".count($this->arrErrors)."):<br>------<br>";
		if(count($this->arrErrors) > 0){
			foreach($this->arrErrors as $key){
				$output .= "<span>* ".$key."</span><br>";
			}
		}
		$output .= "</div>";
		if($draw) echo $output;
		else return $output;		
	}
	
	/**
	 *	Draw system warnings
	 *
	*/	
	protected function DrawWarnings($draw = true)
	{
		if(!$this->debug) return false;
		$output = "<br><div style='width:100%; text-align:left; color:#cc9900;'>";
		$output .= "Warnings(".count($this->arrWarnings)."):<br>------<br>";
		if(count($this->arrWarnings) > 0){
			foreach($this->arrWarnings as $key){
				$output .= "<span>* ".$key."</span><br>";
			}
		}
		$output .= "</div>";
		if($draw) echo $output;
		else return $output;		
	}
	
	/**
	 *	Set system SQLs
	 *
	*/	
	protected function SetSQLs($key, $msg)
	{
		if($this->debug) $this->arrSQLs[$key] = $msg;					
	}

	/**
	 *	Draw system SQLs
	 *
	*/	
	protected function DrawSQLs($draw = true)
	{
		if(!$this->debug) return false;
		$output = "<br><div style='width:100%; text-align:left; color:#444444;'>";
		$output .= "SQL:<br>------<br>";
		if(count($this->arrSQLs) > 0){
			$output .= "<ol>";
			foreach($this->arrSQLs as $key){
				$output .= "<li style='margin-bottom:10px;'>".htmlentities($key)."</li>";
			}
			$output .= "</ol>";
		}
		$output .= "</div>";
		if($draw) echo $output;
		else return $output;		
	}

	/**
	 *	POST data
	*/	
	protected function DrawPostInfo($draw = true)
	{
		if(!$this->debug) return false;
		$output = "<br><div style='width:100%; text-align:left; color:#008600;'>";
		$output .= "POST:<br>------<br><pre>";
		$output .= print_r($_POST, true);
		$output .= "</pre>";
		$output .= "</div>";
		if($draw) echo $output;
		else return $output;		
	}
	
	/**
	 *	Upload image/file 
	*/	
	private function UploadFileImage($mode)
	{
		$arrUploadedFiles = array();
		
		$system = $this->GetOSName();		

		#if ($uploaded_size > 350000){echo "Your file is too large.<br>"; $ok=0;} 				
		#if ($uploaded_type =="text/php"){echo "No PHP files<br>";$ok=0;} 
		#if (!($uploaded_type=="image/gif")) {echo "You may only upload GIF files.<br>";$ok=0;} 				

		if($mode == "add"){
			$arrModeFields = &$this->arrAddModeFields;
		}else if($mode == "edit"){
			$arrModeFields = &$this->arrEditModeFields;
		}
		
		foreach($arrModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						if(isset($v_val['type']) && ($v_val['type'] == "image" || $v_val['type'] == "file")){
							if(isset($_FILES[$v_key]['name'])){
								$thumbnail_create = (isset($v_val['thumbnail_create']) && ($v_val['thumbnail_create'] === true || $v_val['thumbnail_create'] == "true")) ? true : false;
								$thumbnail_field  = (isset($v_val['thumbnail_field'])) ? $v_val['thumbnail_field'] : "";
								$thumbnail_width  = (isset($v_val['thumbnail_width'])) ? $v_val['thumbnail_width'] : "16px";
								$thumbnail_height = (isset($v_val['thumbnail_height'])) ? $v_val['thumbnail_height'] : "16px";

								if($system == "windows"){
									$target = str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']).$v_val['target'];
									$target = str_replace("/", "\\", $target);
									$target = str_replace("\\", "\\\\", $target);
								}else{
									$target = $v_val['target'];
								}								
                                $random_name = isset($v_val['random_name']) ? $v_val['random_name'] : false;
								$overwrite_image = isset($v_val['overwrite_image']) ? $v_val['overwrite_image'] : false;
								$image_name_pefix = isset($v_val['image_name_pefix']) ? $v_val['image_name_pefix'] : ""; 
								if($random_name == "true" || $random_name === true){									
									$target_file_name = basename($_FILES[$v_key]['name']);
									$ext = substr(strrchr($target_file_name, '.'), 1);
									$target_file_name = $image_name_pefix.self::GetRandomString(20).".".$ext;
								}else{
									$target_file_name = basename($_FILES[$v_key]['name']);
								}
								$target_full = $target.$target_file_name;
								if(!$overwrite_image && file_exists($target_full)){
									$target_file_ext = substr(strrchr($target_file_name, '.'), 1);
									$target_file_basename = str_replace(".".$target_file_ext, "", $target_file_name);
									$target_file_name = $target_file_basename."[1].".$target_file_ext;
									$target_full = $target.$target_file_name;
								}								
								if(move_uploaded_file($_FILES[$v_key]['tmp_name'], $target_full)){
									$arrUploadedFiles[$v_key] = $target_file_name;
									if($thumbnail_create){
										// create thumbnail
										$thumb_file_ext = substr(strrchr($target_file_name, '.'), 1);
										$thumb_file_name = str_replace(".".$thumb_file_ext, "", $target_file_name);
										$thumb_file_fullname = $thumb_file_name."_thumb.".$thumb_file_ext;								
										@copy($target_full, $target.$thumb_file_fullname);								
										$thumb_file_thumb_fullname = $this->ResizeImage($target, $thumb_file_fullname, $thumbnail_width, $thumbnail_height);
										if($thumbnail_field == $v_key){
											$arrUploadedFiles[$v_key] = $thumb_file_thumb_fullname;
											@unlink($target_full);
										}else if($thumbnail_field != ""){
											$arrUploadedFiles[$thumbnail_field] = $thumb_file_thumb_fullname;
										}
									}
									//echo "The file ". basename( $_FILES[$key]['name']). " has been uploaded";
								}else{
									//echo "Sorry, there was a problem uploading your file.";
								}
							}
						}
					}
				}
			}else{
				if(isset($val['type']) && ($val['type'] == "image" || $val['type'] == "file")){
					if(isset($_FILES[$key]['name'])){
						$thumbnail_create = (isset($val['thumbnail_create']) && ($val['thumbnail_create'] === true || $val['thumbnail_create'] == "true")) ? true : false;
						$thumbnail_field = (isset($val['thumbnail_field'])) ? $val['thumbnail_field'] : "";
						$thumbnail_width = (isset($val['thumbnail_width'])) ? $val['thumbnail_width'] : "16px";
						$thumbnail_height = (isset($val['thumbnail_height'])) ? $val['thumbnail_height'] : "16px";
						
						if($system == "windows"){
							$target = str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']).$val['target'];
							$target = str_replace("/", "\\", $target);
							$target = str_replace("\\", "\\\\", $target);
						}else{
							$target = $val['target'];
						}								
						$random_name = isset($val['random_name']) ? $val['random_name'] : false;
						$overwrite_image = isset($v_val['overwrite_image']) ? $v_val['overwrite_image'] : false;
						$image_name_pefix = isset($val['image_name_pefix']) ? $val['image_name_pefix'] : ""; 
						if($random_name == "true" || $random_name === true){									
							$target_file_name = basename($_FILES[$key]['name']);
							$ext = substr(strrchr($target_file_name, '.'), 1);
							$target_file_name = $image_name_pefix.self::GetRandomString(20).".".$ext;
						}else{
							$target_file_name = basename($_FILES[$key]['name']);
						}
						$target_full = $target.$target_file_name;
						if(!$overwrite_image && file_exists($target_full)){
							$target_file_ext = substr(strrchr($target_file_name, '.'), 1);
							$target_file_basename = str_replace(".".$target_file_ext, "", $target_file_name);
							$target_file_name = $target_file_basename."[1].".$target_file_ext;
							$target_full = $target.$target_file_name;
						}						
						if(move_uploaded_file($_FILES[$key]['tmp_name'], $target_full)){
							$arrUploadedFiles[$key] = $target_file_name;
							if($thumbnail_create){
								// create thumbnail
								$thumb_file_ext = substr(strrchr($target_file_name, '.'), 1);
								$thumb_file_name = str_replace(".".$thumb_file_ext, "", $target_file_name);
								$thumb_file_fullname = $thumb_file_name."_thumb.".$thumb_file_ext;								
								@copy($target_full, $target.$thumb_file_fullname);								
								$thumb_file_thumb_fullname = $this->ResizeImage($target, $thumb_file_fullname, $thumbnail_width, $thumbnail_height);
								if($thumbnail_field == $key){
									$arrUploadedFiles[$key] = $thumb_file_thumb_fullname;
									@unlink($target_full);									
								}else if($thumbnail_field != ""){
									$arrUploadedFiles[$thumbnail_field] = $thumb_file_thumb_fullname;
								}
							}
							//echo "The file ". basename( $_FILES[$key]['name']). " has been uploaded";
						}else{
							//echo "Sorry, there was a problem uploading your file.";
						}
					}
				}
			}
		}			
		
		return $arrUploadedFiles;
	}

	/**
	 *	Remove (delete) image/file and update table field
	*/	
	private function RemoveFileImage($rid, $operation_field, $target_path, $target_file)
	{
		//----------------------------------------------------------------------
		// block if this is a demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}

		@unlink($target_path.$target_file);
		$ext = substr($target_file,strrpos($target_file,".")+1);
		@unlink($target_path.str_replace(".".$ext, "_thumb.jpg", $target_file));
		$sql = "UPDATE ".$this->tableName." SET ".$operation_field." = '' WHERE ".$this->primaryKey." = ".$rid;
		database_void_query($sql);
		$sql = "UPDATE ".$this->tableName." SET ".$operation_field."_thumb = '' WHERE ".$this->primaryKey." = ".$rid;
		database_void_query($sql);
		if($this->debug) $this->arrSQLs['delete_image'] = $sql;					
	}
	
	/**
	 * Prepare images/files fields
	 **/
	protected function PrepareImagesArray($rid = "0")
	{
		$this->arrImagesFields = array();

		// prepare images/files fields
		foreach($this->arrEditModeFields as $key => $val){
			if(preg_match("/separator/i", $key) && is_array($val)){
				foreach($val as $v_key => $v_val){
					if($v_key != "separator_info"){						
						// prepare images
						if($v_val['type'] == "image" || $v_val['type'] == "file"){
							$sql = "SELECT ".$v_key." FROM ".$this->tableName." WHERE ".$this->primaryKey." = ".(int)$rid."";
							$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
							if(isset($result[$v_key])){
								$this->arrImagesFields[$v_key] = $v_val['target'].$result[$v_key];	
							}							
						}											
					}					
				}				
			}else{
				// prepare images
				if($val['type'] == "image" || $val['type'] == "file"){
					$sql = "SELECT ".$key." FROM ".$this->tableName." WHERE ".$this->primaryKey." = ".(int)$rid."";
					$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
					if(isset($result[$key])){
						$this->arrImagesFields[$key] = $val['target'].$result[$key];	
					}							
				}
			}
		}		
	}
	
	/**
	 * Delete images from images array
	 **/
	protected function DeleteImages()
	{		
		//----------------------------------------------------------------------
		// block if this is a demo mode
		if(strtolower(SITE_MODE) == "demo"){
			$this->error = _OPERATION_BLOCKED;
			return false;				
		}

		foreach($this->arrImagesFields as $key => $val){
			@unlink($val);
			@unlink(str_replace(".", "_thumb.", $val));
		}			
	}
	
	/**
	 *	Get formatted microtime
	*/	
    protected function GetFormattedMicrotime()
	{
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }    
	
	/**
	 *	Set running time
	*/	
    protected function SetRunningTime()
	{		
        if($this->debug){
            $this->startTime = $this->GetFormattedMicrotime();
        }        
    }    
		
	/**
	 *	Draw script running time
	*/	
    protected function DrawRunningTime()
	{
        // finish calculating running time of a script
        if($this->debug){
            $this->finishTime = $this->GetFormattedMicrotime();
            $output = "<br><div style='width:100%; text-align:left; color:#000047;'>";
			$output .= "Total running time: ".round((float)$this->finishTime - (float)$this->startTime, 6)." sec.";
			$output .= "</div>";
			echo $output;
        }        
    }    

    /**
     * Resize uploaded image
     */
    protected function ResizeImage($image_path, $image_name, $resize_width = "", $resize_height = "")
	{
        $image_path_name = $image_path.$image_name;        
        if(empty($image_path_name)){ // No Image?    
            echo "uploaded_file_not_image"; //$this->AddWarning("", "", $this->lang['uploaded_file_not_image']);
		}else if(!function_exists("imagecreatefromjpeg")){
			if($this->debug) $this->arrWarnings['wrong_foo'] =  "Function 'imagecreatefromjpeg' doesn't exists!";
			return $image_name;
        }else{ // An Image?
			if($image_path_name){
                $size   = getimagesize($image_path_name);
                $width  = $size[0];
                $height = $size[1];                
                $case = "";
                $curr_ext = strtolower(substr($image_path_name,strrpos($image_path_name,".")+1));
				$imagetype = (function_exists('exif_imagetype')) ? exif_imagetype($image_path_name) : "";	
                if($imagetype == "1" && $curr_ext != "gif") $ext = "gif";
                else if($imagetype == "3" && $curr_ext != "png") $ext = "png";
				else $ext = $curr_ext;
                switch($ext){
                    case 'png':
                        $iTmp = @imagecreatefrompng($image_path_name);
                        $case = "png";
                        break;
                    case 'gif':
                        $iTmp = @imagecreatefromgif($image_path_name);
                        $case = "gif";
                        break;                
                    case 'jpeg':            
                    case 'jpg':
                        $iTmp = @imagecreatefromjpeg($image_path_name);
                        $case = "jpg";
                        break;                
                }
				$image_name = str_replace(".".$curr_ext, ".jpg", strtolower($image_name));
				$image_path_name_new = $image_path.$image_name;        

				if($case != ""){
					if($resize_width != "" && $resize_height == ""){
						$new_width=$resize_width;
						$new_height = ($height/$width)*$new_width;                
					}else if($resize_width == "" && $resize_height != ""){
						$new_height = $resize_height;
						$new_width=($width/$height)*$new_height;
					}else if($resize_width != "" && $resize_height != ""){
						$new_width  = $resize_width;
						$new_height = $resize_height;                    
					}else{
						$new_width  = $width;  
						$new_height = $height;
					}
					$iOut = @imagecreatetruecolor(intval($new_width), intval($new_height));     
					@imagecopyresampled($iOut,$iTmp,0,0,0,0,intval($new_width), intval($new_height), $width, $height);
					@imagejpeg($iOut,$image_path_name_new,100);
					if($case != "jpg") @unlink($image_path_name);
				}
            }            
        }
		return $image_name;
    }
	
    /**
     * Returns Operating System name
     */
	protected function GetOSName()
	{
		// some possible outputs
		// Linux: Linux localhost 2.4.21-0.13mdk #1 Fri Mar 14 15:08:06 EST 2003 i686		
		// FreeBSD: FreeBSD localhost 3.2-RELEASE #15: Mon Dec 17 08:46:02 GMT 2001		
		// WINNT: Windows NT XN1 5.1 build 2600		
		// MAC: Darwin Ron-Cyriers-MacBook-Pro.local 10.6.0 Darwin Kernel Version 10.6.0: Wed Nov 10 18:13:17 PST 2010; root:xnu-1504.9.26~3/RELEASE_I386 i386
		$os_name = strtoupper(substr(PHP_OS, 0, 3));
		switch($os_name){
			case 'WIN':
				return "windows"; break;
			case 'LIN':
				return "linux"; break;
			case 'FRE':
				return "freebsd"; break;
			case 'DAR':
				return "mac"; break;
			default:
				return "windows"; break;
		}
	}
	
	/**
	 * Check if parameter value is empty
	 */
	private function ParamEmpty($key)
	{
		if(isset($this->params[$key]) && $this->params[$key] !== "" && $this->params[$key] != "0" && $this->params[$key] != "0000-00-00"){
			return false;
		}
		return true;	
	}
	
	////////////////////////////////////////////////////////////////////////////
	static public function GetParameter($param, $use_prefix = true, $u_prefix = "")
	{
		$prefix = ($use_prefix) ? "mg_" : "";
		$output = isset($_REQUEST[$u_prefix.$prefix.$param]) ? $_REQUEST[$u_prefix.$prefix.$param] : "";
		return $output;
	}
		
    static public function GetRandomString($length = 20)
	{
        $template_alpha = "abcdefghijklmnopqrstuvwxyz";
        $template_alphanumeric = "1234567890abcdefghijklmnopqrstuvwxyz";
        settype($template, "string");
        settype($length, "integer");
        settype($rndstring, "string");
        settype($a, "integer");
        settype($b, "integer");
        $b = rand(0, strlen($template_alpha) - 1);
        $rndstring .= $template_alpha[$b];        
        for ($a = 0; $a < $length-1; $a++) {
            $b = rand(0, strlen($template_alphanumeric) - 1);
            $rndstring .= $template_alphanumeric[$b];
        }       
        return $rndstring;       
    }	
		
}
?>