<?php

/**
 *	Class Bookings
 *  -------------- 
 *  Description : encapsulates bookings properties
 *	Usage       : HotelSite ONLY
 *  Updated	    : 20.06.2011
 *	Written by  : ApPHP
 *
 *	PUBLIC:						STATIC:					PRIVATE:
 *  -----------					-----------				-----------
 *  __construct             	RemoveExpired           GetBookingRoomsList 
 *  __destruct                                          
 *  DrawBookingDescription
 *  BeforeDeleteRecord
 *  AfterDeleteRecord
 *  CleanCreditCardInfo
 *  UpdatePaymentDate
 *  DrawBookingInvoice
 *  
 **/


class Bookings extends MicroGrid {
	
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
	
	private $page;
	private $user_id;
	private $rooms_amount;
	private $booking_number;
	private $booking_status;
	private $booking_client_id;
	private $booking_is_admin_reserv;
	private $fieldDateFormat;
	
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct($user_id = "") {
		
		$this->SetRunningTime();

		$this->params = array();		
		if(isset($_POST['status']))   $this->params['status'] = prepare_input($_POST['status']);
		if(isset($_POST['additional_payment'])) $this->params['additional_payment'] = prepare_input($_POST['additional_payment']);
		if(isset($_POST['additional_info'])) $this->params['additional_info'] = prepare_input($_POST['additional_info']);

		#// for checkboxes 
		#if(isset($_POST['parameter4']))   $this->params['parameter4'] = $_POST['parameter4']; else $this->params['parameter4'] = "0";

		$this->params['language_id'] = MicroGrid::GetParameter('language_id');
		$rid = MicroGrid::GetParameter('rid');
	
		$this->primaryKey 	= "id";
		$this->tableName 	= TABLE_BOOKINGS;
		$this->dataSet 		= array();
		$this->error 		= "";
		$this->booking_number = "";
		$this->rooms_amount = "";
		$this->booking_status = "";
		$this->booking_client_id = "";
		$this->booking_is_admin_reserv = "0";
		$arr_statuses = array("0"=>_PREPARING, "1"=>_RESERVED, "2"=>_COMPLETED, "3"=>_REFUNDED);
		

		if($user_id != ""){
			$this->user_id = $user_id;
			$this->page = "client=my_bookings";
			$this->actions   = array("add"=>false, "edit"=>false, "details"=>false, "delete"=>false);
			$arr_statuses_filter = array("1"=>_RESERVED, "2"=>_COMPLETED, "3"=>_REFUNDED);
			$arr_statuses_edit = array("1"=>_RESERVED, "2"=>_COMPLETED);
			$arr_statuses_edit_completed = array("2"=>_COMPLETED, "3"=>_REFUNDED);
		}else{
			$this->user_id = "";
			$this->page = "admin=mod_booking_bookings";
			$this->actions   = array("add"=>false, "edit"=>true, "details"=>false, "delete"=>false);
			$arr_statuses_filter = array("0"=>_PREPARING, "1"=>_RESERVED, "2"=>_COMPLETED, "3"=>_REFUNDED);
			$arr_statuses_edit = array("1"=>_RESERVED, "2"=>_COMPLETED, "3"=>_REFUNDED);
			$arr_statuses_edit_completed = array("2"=>_COMPLETED, "3"=>_REFUNDED);
		}
		$this->actionIcons  = true;
		$this->allowRefresh = true;
		$this->formActionURL = "index.php?".$this->page;


		$this->allowLanguages = false;
		$this->languageId  	= ""; // ($this->params['language_id'] != "") ? $this->params['language_id'] : Languages::GetDefaultLang();

		$this->WHERE_CLAUSE = ""; 
		if($this->user_id != ""){
			$this->WHERE_CLAUSE = "WHERE ".$this->tableName.".is_admin_reservation = 0 AND
				                         ".$this->tableName.".status <> 0 AND
			                             ".$this->tableName.".client_id = ".$this->user_id;
		}
		$this->ORDER_CLAUSE = "ORDER BY ".$this->tableName.".created_date DESC"; // ORDER BY date_created DESC
		
		$this->isAlterColorsAllowed = true;

		$this->isPagingAllowed = true;
		$this->pageSize = 20;

		$this->isSortingAllowed = true;

		$this->isFilteringAllowed = true;
		// define filtering fields
		$this->arrFilteringFields = array(
			_BOOKING_NUMBER => array("table"=>$this->tableName, "field"=>"booking_number", "type"=>"text", "sign"=>"like%", "width"=>"100px"),
			//_DATE 	  => array("table"=>$this->tableName, "field"=>"payment_date", "type"=>"text", "sign"=>"like%", "width"=>"60px"),
			//"Caption_2"  => array("table"=>"", "field"=>"", "type"=>"text", "sign"=>"=|like%|%like|%like%", "width"=>"80px"),
		);
		if($this->user_id == ""){
			$this->arrFilteringFields[_CLIENT] = array("table"=>TABLE_CLIENTS, "field"=>"user_name", "type"=>"text", "sign"=>"like%", "width"=>"100px");
		}
		$this->arrFilteringFields[_STATUS ] = array("table"=>$this->tableName, "field"=>"status", "type"=>"dropdownlist", "source"=>$arr_statuses_filter, "sign"=>"=", "width"=>"");
			
		global $objSettings;
		$default_currency_info = Currencies::GetDefaultCurrencyInfo();
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$this->fieldDateFormat = "M d, Y";
			$sqlFieldDateFormat = "%b %d, %Y";
		}else{
			$this->fieldDateFormat = "d M, Y";
			$sqlFieldDateFormat = "%d %b, %Y";
		}

		// prepare languages array		
		/// $total_languages = Languages::GetAllActive();
		/// $arr_languages      = array();
		/// foreach($total_languages[0] as $key => $val){
		/// 	$arr_languages[$val['abbreviation']] = $val['lang_name'];
		/// }

		//---------------------------------------------------------------------- 
		// VIEW MODE
		//---------------------------------------------------------------------- 
		$this->VIEW_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".booking_number,
								".$this->tableName.".booking_description,
								".$this->tableName.".additional_info,
								".$this->tableName.".order_price,
								".$this->tableName.".vat_fee,
								".$this->tableName.".vat_percent,
								".$this->tableName.".payment_sum,
								".$this->tableName.".currency,
								".$this->tableName.".client_id,
								".$this->tableName.".transaction_number,
								(SELECT GROUP_CONCAT(DATE_FORMAT(checkin, '".$sqlFieldDateFormat."'), ' - ' ,DATE_FORMAT(checkout, '".$sqlFieldDateFormat."') SEPARATOR '<br>') FROM ".TABLE_BOOKINGS_ROOMS." br WHERE br.booking_number = ".$this->tableName.".booking_number) as mod_checkin_checkout,
								DATE_FORMAT(".$this->tableName.".created_date, '".$sqlFieldDateFormat."') as created_date_formated,
								DATE_FORMAT(".$this->tableName.".payment_date, '".$sqlFieldDateFormat."') as payment_date_formated,
								".$this->tableName.".payment_type,
								".$this->tableName.".status,
								CASE
									WHEN ".$this->tableName.".payment_type = 0 THEN '"._ONLINE."'
									WHEN ".$this->tableName.".payment_type = 1 THEN '"._PAYPAL."'
									WHEN ".$this->tableName.".payment_type = 2 THEN '"._CREDIT_CARD."'
									WHEN ".$this->tableName.".payment_type = 3 THEN '2CO'
									ELSE '"._UNKNOWN."'
								END as mod_payment_type,
								CASE
									WHEN ".$this->tableName.".status = 0 THEN '<font color=#000096>"._PREPARING."</font>'
									WHEN ".$this->tableName.".status = 1 THEN '<font color=#969600>"._RESERVED."</font>'
									WHEN ".$this->tableName.".status = 2 THEN '<font color=#009600>"._COMPLETED."</font>'
									WHEN ".$this->tableName.".status = 3 THEN '<font color=#960000>"._REFUNDED."</font>'
									ELSE '"._UNKNOWN."'
								END as m_status,
								IF(".$this->tableName.".is_admin_reservation = 0, CONCAT('<a href=\"javascript:void(0)\" onclick=\"javascript:appGoTo(\'admin=mod_clients_management\', \'&mg_action=details&mg_rid=', ".TABLE_CLIENTS.".id, '\')\">', IF(".TABLE_CLIENTS.".user_name != '', ".TABLE_CLIENTS.".user_name, '[ "._WITHOUT_ACCOUNT." ]'), '</a>'), '"._ADMIN."') as client_name,
								".TABLE_CURRENCIES.".symbol,
								CASE
									WHEN ".TABLE_CURRENCIES.".symbol_placement  = 'left' THEN
										CONCAT(".TABLE_CURRENCIES.".symbol, FORMAT(".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment, 2))
					                ELSE
										CONCAT(FORMAT(".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment, 2), ".TABLE_CURRENCIES.".symbol)
								END as tp_w_currency,
								(".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment) as tp_wo_currency,
								IF(".$this->tableName.".status = 2, CONCAT('<a href=\"javascript:void(\'invoice\')\" onclick=\"javascript:__mgDoPostBack(\'".$this->tableName."\', \'invoice\', \'', ".$this->tableName.".".$this->primaryKey.", '\')\">[ ', '"._INVOICE."', ' ]</a>'), '<span class=lightgray>"._INVOICE."</span>') as link_order_invoice,
								CONCAT('<a href=\"javascript:void(\'description\')\" onclick=\"javascript:__mgDoPostBack(\'".$this->tableName."\', \'description\', \'', ".$this->tableName.".".$this->primaryKey.", '\')\">[ ', '"._DESCRIPTION."', ' ]</a>') as link_order_description,
								IF(".$this->tableName.".status = 1, CONCAT('<a href=\"javascript:void(0);\" title=\""._BUTTON_CANCEL."\" onclick=\"javascript:__mgDoPostBack(\'".TABLE_BOOKINGS."\', \'delete\', \'', ".$this->tableName.".".$this->primaryKey.", '\');\">[ "._BUTTON_CANCEL." ]</a>'), '') as link_cust_order_cancel,
								CONCAT('<a href=\"javascript:void(0);\" title=\""._BUTTON_CANCEL."\" onclick=\"javascript:__mgDoPostBack(\'".TABLE_BOOKINGS."\', \'delete\', \'', ".$this->tableName.".".$this->primaryKey.", '\');\">[ "._BUTTON_CANCEL." ]</a>') as link_admin_order_cancel
							FROM ".$this->tableName."
								INNER JOIN ".TABLE_CURRENCIES." ON ".$this->tableName.".currency = ".TABLE_CURRENCIES.".code
								LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".$this->tableName.".client_id = ".TABLE_CLIENTS.".id
							";		

							//".$this->tableName.".coupon_number,
							//".$this->tableName.".discount_campaign_id,

		// define view mode fields
		if($this->user_id != ""){
			$this->arrViewModeFields = array(
				//"created_date_formated"  => array("title"=>_DATE_CREATED, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
				"booking_number"    	 => array("title"=>_BOOKING_NUMBER, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
				"mod_checkin_checkout"   => array("title"=>_CHECK_IN." - "._CHECK_OUT, "type"=>"label", "align"=>"left", "width"=>"100px", "height"=>"", "maxlength"=>""),
				"mod_payment_type"    	 => array("title"=>_METHOD, "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>""),				
				"tp_w_currency"   		 => array("title"=>_PAYMENT_SUM, "type"=>"label", "align"=>"right", "width"=>"", "height"=>"", "maxlength"=>"", "sort_by"=>"tp_wo_currency", "sort_type"=>"numeric"),
				"m_status" 		  		 => array("title"=>_STATUS, "type"=>"label", "align"=>"center", "width"=>"90px", "height"=>"", "maxlength"=>""),
				"link_order_description" => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>"", "nowrap"=>"nowrap"),
				"link_order_invoice"     => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"75px", "height"=>"", "maxlength"=>"", "nowrap"=>"nowrap"),
				"link_cust_order_cancel" => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>"", "nowrap"=>"nowrap"),
			);			
		}else{
			$this->arrViewModeFields = array(
				"created_date_formated"   => array("title"=>_DATE_CREATED, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
				"booking_number"    	  => array("title"=>_BOOKING_NUMBER, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
				"mod_checkin_checkout"    => array("title"=>_CHECK_IN." - "._CHECK_OUT, "type"=>"label", "align"=>"left", "width"=>"", "height"=>"", "maxlength"=>""),
				"mod_payment_type"    	  => array("title"=>_METHOD, "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>""),				
				"client_name"   		  => array("title"=>_CLIENT, "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>""),
				"tp_w_currency"   		  => array("title"=>_PAYMENT_SUM, "type"=>"label", "align"=>"right", "width"=>"", "height"=>"", "maxlength"=>"", "sort_by"=>"tp_wo_currency", "sort_type"=>"numeric"),
				"m_status" 		  		  => array("title"=>_STATUS, "type"=>"label", "align"=>"center", "width"=>"90px", "height"=>"", "maxlength"=>""),
				"link_order_description"  => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>""),
				"link_order_invoice"      => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>""),
				"link_admin_order_cancel" => array("title"=>"", "type"=>"label", "align"=>"center", "width"=>"", "height"=>"", "maxlength"=>""),
			);						
		}
		
		//---------------------------------------------------------------------- 
		// ADD MODE
		//---------------------------------------------------------------------- 
		// define add mode fields
		$this->arrAddModeFields = array(
			
		);

		//---------------------------------------------------------------------- 
		// EDIT MODE
		//---------------------------------------------------------------------- 
		$this->EDIT_MODE_SQL = "SELECT
								".$this->tableName.".".$this->primaryKey.",
								".$this->tableName.".booking_number,
								".$this->tableName.".booking_number as booking_number_label,
								".$this->tableName.".booking_description,
								".$this->tableName.".additional_info,
								".$this->tableName.".order_price,
								IF(".$this->tableName.".pre_payment = 'first night', '"._FIRST_NIGHT."', CONCAT(".$this->tableName.".pre_payment, '%')) as mod_pre_payment,
								CASE
									WHEN ".$this->tableName.".payment_type = 0 THEN '"._ONLINE."'
									WHEN ".$this->tableName.".payment_type = 1 THEN '"._PAYPAL."'
									WHEN ".$this->tableName.".payment_type = 2 THEN '"._CREDIT_CARD."'
									WHEN ".$this->tableName.".payment_type = 3 THEN '2CO'
									ELSE '"._UNKNOWN."'
								END as mod_payment_type,
								CASE
									WHEN ".TABLE_CURRENCIES.".symbol_placement = 'left' THEN
										CONCAT(".TABLE_CURRENCIES.".symbol, ".$this->tableName.".payment_sum)
					                ELSE
										CONCAT(".$this->tableName.".payment_sum, ".TABLE_CURRENCIES.".symbol)
								END as mod_payment_sum,
								IF(((".$this->tableName.".order_price - (".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment)) > 0),
								    (".$this->tableName.".order_price - (".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment)),
									0) as mod_have_to_pay,
								".$this->tableName.".additional_payment,
								".$this->tableName.".vat_fee,
								".$this->tableName.".vat_percent,
								".$this->tableName.".payment_sum,
								".$this->tableName.".currency,
								".$this->tableName.".client_id,								
								".$this->tableName.".cc_type,
								IF(
									LENGTH(AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."')) = 4,
									CONCAT('...', AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."'), ' ("._CLEANED.")'),
									AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."')
								) as m_cc_number,								
								".$this->tableName.".cc_cvv_code,
								".$this->tableName.".cc_expires_month,
								".$this->tableName.".cc_expires_year,
								IF(".$this->tableName.".cc_expires_month != '', CONCAT(".$this->tableName.".cc_expires_month, '/', ".$this->tableName.".cc_expires_year), '') as m_cc_expires_date,
								".$this->tableName.".transaction_number,
								".$this->tableName.".payment_date,
								DATE_FORMAT(".$this->tableName.".payment_date, '".$sqlFieldDateFormat."') as payment_date_formated,								
								".$this->tableName.".payment_type,
								".$this->tableName.".status
							FROM ".$this->tableName."
								INNER JOIN ".TABLE_CURRENCIES." ON ".$this->tableName.".currency = ".TABLE_CURRENCIES.".code
								LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".$this->tableName.".client_id = ".TABLE_CLIENTS.".id
							";
		if($this->user_id != ""){
			$WHERE_CLAUSE = "WHERE ".$this->tableName.".is_admin_reservation = 0 AND
								   ".$this->tableName.".client_id = ".$this->user_id." AND
			                       ".$this->tableName.".".$this->primaryKey." = _RID_";
			$this->EDIT_MODE_SQL = $this->EDIT_MODE_SQL." WHERE TRUE = FALSE";					   
		}else{
			$WHERE_CLAUSE = "WHERE ".$this->tableName.".".$this->primaryKey." = _RID_";
			$this->EDIT_MODE_SQL = $this->EDIT_MODE_SQL.$WHERE_CLAUSE;
		}		

		// prepare trigger
		$sql = "SELECT
					status,
					IF(TRIM(cc_number) = '' OR LENGTH(AES_DECRYPT(cc_number, '".PASSWORDS_ENCRYPT_KEY."')) <= 4, 'hide', 'show') as cc_number_trigger
				FROM ".$this->tableName." WHERE id = ".(int)$rid;
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){
			$cc_number_trigger = $result[0]['cc_number_trigger'];
			$status_trigger = $result[0]['status'];
		}else{
			$cc_number_trigger = "hide";
			$status_trigger = "0";
		}		

		// define edit mode fields
		$this->arrEditModeFields = array(
			"booking_number_label" => array("title"=>_BOOKING_NUMBER, "type"=>"label"),
			"booking_number"     => array("title"=>"", "type"=>"hidden", "required"=>false, "default"=>""),
			"client_id"          => array("title"=>"", "type"=>"hidden", "required"=>false, "default"=>""),
			"mod_payment_type"   => array("title"=>_PAYMENT_TYPE, "type"=>"label"),
			"mod_payment_sum"    => array("title"=>_PAYMENT_SUM, "type"=>"label"),
			"mod_pre_payment"    => array("title"=>_PRE_PAYMENT, "type"=>"label"),
			"additional_payment" => array("title"=>_ADDITIONAL_PAYMENT, "type"=>"textbox",  "width"=>"100px", "required"=>false, "readonly"=>(($status_trigger == "1" || $status_trigger == "2") ? false : true), "maxlength"=>"10", "default"=>"", "validation_type"=>"float|positive", "unique"=>false, "visible"=>true),
			"mod_have_to_pay"    => array("title"=>_PAYMENT_REQUIRED, "type"=>"label"),
			"status"  			 => array("title"=>_STATUS, "type"=>"enum", "width"=>"110px", "required"=>true, "readonly"=>(($status_trigger >= "2") ? true : false), "source"=>$arr_statuses_edit),
		);
		if($this->user_id != ""){
			$this->arrEditModeFields["status"] = array("title"=>_STATUS, "type"=>"enum", "width"=>"110px", "required"=>true, "readonly"=>(($status_trigger >= "2") ? true : false), "source"=>$arr_statuses_edit);
		}else{
			$this->arrEditModeFields["status"]      = array("title"=>_STATUS, "type"=>"enum", "width"=>"110px", "required"=>true, "readonly"=>false, "source"=>(($status_trigger >= "2") ? $arr_statuses_edit_completed : $arr_statuses_edit));
			$this->arrEditModeFields["cc_type"] 	= array("title"=>_CREDIT_CARD_TYPE, "type"=>"label");
			$this->arrEditModeFields["m_cc_number"] = array("title"=>_CREDIT_CARD_NUMBER, "type"=>"label", "post_html"=>(($cc_number_trigger == "show") ? "&nbsp;[ <a href='javascript:void(0);' onclick='if(confirm(\""._PERFORM_OPERATION_COMMON_ALERT."\")) __mgDoPostBack(\"".$this->tableName."\", \"clean_credit_card\", \"".$rid."\")'>"._REMOVE."</a> ]" : ""));
			$this->arrEditModeFields["m_cc_expires_date"] = array("title"=>_CREDIT_CARD_EXPIRES, "type"=>"label");
			$this->arrEditModeFields["cc_cvv_code"] = array("title"=>_CVV_CODE, "type"=>"label");
			$this->arrEditModeFields["additional_info"] = array("title"=>_ADDITIONAL_INFO, "type"=>"textarea", "width"=>"390px", "height"=>"90px", "editor_type"=>"simple", "readonly"=>false, "default"=>"", "required"=>false, "validation_type"=>"", "unique"=>false);
		}

		//---------------------------------------------------------------------- 
		// DETAILS MODE
		//----------------------------------------------------------------------		
		$this->DETAILS_MODE_SQL = $this->VIEW_MODE_SQL.$WHERE_CLAUSE;

		$this->arrDetailsModeFields = array(

			"booking_number"  	 => array("title"=>_BOOKING_NUMBER, "type"=>"label"),
			"booking_description"  => array("title"=>_DESCRIPTION, "type"=>"label"),
			"order_price"  		 => array("title"=>_ORDER_PRICE, "type"=>"label"),
			"vat_fee"  		     => array("title"=>_VAT, "type"=>"label"),
			"vat_percent"  		 => array("title"=>_VAT_PERCENT, "type"=>"label"),
			"payment_sum"  		 => array("title"=>_PAYMENT_SUM, "type"=>"label"),
			"currency"  		 => array("title"=>_CURRENCY, "type"=>"label"),
			"client_name"        => array("title"=>_CLIENT, "type"=>"label"),
			"transaction_number" => array("title"=>_TRANSACTION, "type"=>"label"),
			"payment_date_formated" => array("title"=>_DATE, "type"=>"label"),
			"mod_payment_type"   => array("title"=>_PAYED_BY, "type"=>"label"),
			//"coupon_number"  	 => array("title"=>"", "type"=>"label"),
			//"discount_campaign_id" => array("title"=>"", "type"=>"label"),
			"m_status"  	     => array("title"=>_STATUS, "type"=>"label"),
			"additional_info"    => array("title"=>_ADDITIONAL_INFO, "type"=>"label"),

			// "parameter1"  => array("title"=>"", "type"=>"label"),
			// "parameter2"  => array("title"=>"", "type"=>"label"),
			// "parameter3"  => array("title"=>"", "type"=>"label"),
			// "parameter4"  => array("title"=>"", "type"=>"image", "target"=>"uploaded/"),
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
	 *	Draws order invoice
	 */
	public function DrawBookingInvoice($rid)
	{
		global $objSiteDescription;
		global $objSettings;
		
		$output = "";
		$oid = isset($rid) ? (int)$rid : "0";
		$language_id = Languages::GetDefaultLang();
		
		$sql = "SELECT
					".$this->tableName.".".$this->primaryKey.",
					".$this->tableName.".booking_number,
					".$this->tableName.".booking_description,
					".$this->tableName.".additional_info,
					".$this->tableName.".discount_fee,
					".$this->tableName.".order_price,
					".$this->tableName.".vat_fee,
					".$this->tableName.".vat_percent,
					".$this->tableName.".payment_sum,
					".$this->tableName.".pre_payment,
					IF(".$this->tableName.".pre_payment = 'first night', '"._FIRST_NIGHT."', CONCAT(".$this->tableName.".pre_payment, '%')) as mod_pre_payment,
					".$this->tableName.".additional_payment,
					".$this->tableName.".extras,
					".$this->tableName.".cc_type,
					".$this->tableName.".cc_expires_month,
					".$this->tableName.".cc_expires_year,
					".$this->tableName.".cc_cvv_code, 					
					".$this->tableName.".currency,
					".$this->tableName.".client_id,
					".$this->tableName.".transaction_number,
					".$this->tableName.".payment_date,
					".$this->tableName.".is_admin_reservation,
					DATE_FORMAT(".$this->tableName.".created_date, '".(($this->fieldDateFormat == "M d, Y") ? "%b %d, %Y %h:%i %p" : "%d %b %Y %h:%i %p")."') as created_date_formated,
					DATE_FORMAT(".$this->tableName.".payment_date, '".(($this->fieldDateFormat == "M d, Y") ? "%b %d, %Y %h:%i %p" : "%d %b %Y %h:%i %p")."') as payment_date_formated,
					".$this->tableName.".payment_type,
					CASE
						WHEN ".$this->tableName.".payment_type = 0 THEN '"._ONLINE_ORDER."'
						WHEN ".$this->tableName.".payment_type = 1 THEN '"._PAYPAL."'
						WHEN ".$this->tableName.".payment_type = 2 THEN '"._CREDIT_CARD."'
						WHEN ".$this->tableName.".payment_type = 3 THEN '2CO'
						ELSE '"._UNKNOWN."'
					END as mod_payment_type,
					CASE
						WHEN ".$this->tableName.".status = 0 THEN '<font color=#0000a3>"._PREPARING."</font>'
						WHEN ".$this->tableName.".status = 1 THEN '<font color=#a3a300>"._RESERVED."</font>'
						WHEN ".$this->tableName.".status = 2 THEN '<font color=#00a300>"._COMPLETED."</font>'
						WHEN ".$this->tableName.".status = 3 THEN '<font color=#a30000>"._REFUNDED."</font>'
						ELSE '"._UNKNOWN."'
					END as m_status,
					IF(".$this->tableName.".is_admin_reservation = 0, IF(".TABLE_CLIENTS.".user_name != '', ".TABLE_CLIENTS.".user_name, '[ "._WITHOUT_ACCOUNT." ]'), '"._ADMIN."') as client_name,
					".TABLE_CURRENCIES.".symbol,
					".TABLE_CURRENCIES.".symbol_placement,
					CASE
						WHEN ".TABLE_CURRENCIES.".symbol_placement  = 'left' THEN
							CONCAT(".TABLE_CURRENCIES.".symbol, ".$this->tableName.".additional_payment)
						ELSE
							CONCAT(".$this->tableName.".additional_payment, ".TABLE_CURRENCIES.".symbol)
					END as tp_w_additional,
					CONCAT('<a href=\"index.php?".$this->page."&mg_action=description&oid=', ".$this->tableName.".".$this->primaryKey.", '\">', '"._DESCRIPTION."', '</a>') as link_order_description,
					".TABLE_CLIENTS.".first_name,
					".TABLE_CLIENTS.".last_name,					
					".TABLE_CLIENTS.".email as client_email,
					".TABLE_CLIENTS.".company as client_company,
					".TABLE_CLIENTS.".b_address,
					".TABLE_CLIENTS.".b_address_2,
					".TABLE_CLIENTS.".b_city,
					".TABLE_CLIENTS.".b_state,
					".TABLE_CLIENTS.".b_zipcode, 
					".TABLE_COUNTRIES.".name as country_name,
					".TABLE_CAMPAIGNS.".campaign_name,
					".TABLE_CAMPAIGNS.".discount_percent 
				FROM ".$this->tableName."
					INNER JOIN ".TABLE_CURRENCIES." ON ".$this->tableName.".currency = ".TABLE_CURRENCIES.".code
					LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".$this->tableName.".client_id = ".TABLE_CLIENTS.".id
					LEFT OUTER JOIN ".TABLE_COUNTRIES." ON ".TABLE_CLIENTS.".b_country = ".TABLE_COUNTRIES.".abbrv 
					LEFT OUTER JOIN ".TABLE_CAMPAIGNS." ON ".$this->tableName.".discount_campaign_id = ".TABLE_CAMPAIGNS.".id
				WHERE
					".$this->tableName.".".$this->primaryKey." = ".(int)$oid."";

				if($this->user_id != ""){
					$sql .= " AND ".$this->tableName.".is_admin_reservation = 0 AND ".$this->tableName.".client_id = ".(int)$this->user_id;
				}
					
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){

			$part = "<table width='100%' border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<td valign='top'>						
						<h3>"._CLIENT_DETAILS.":</h3>";
						if($result[0]['is_admin_reservation'] == "1"){
							$part .= _ADMIN."<br />";
						}else{
							$part .= _FIRST_NAME.": ".$result[0]['first_name']."<br />";
							$part .= _LAST_NAME.": ".$result[0]['last_name']."<br />";
							$part .= _EMAIL_ADDRESS.": ".$result[0]['client_email']."<br />";
							$part .= _COMPANY.": ".$result[0]['client_company']."<br />";
							$part .= _ADDRESS.": ".$result[0]['b_address']." ".$result[0]['b_address_2']."<br />";
							$part .= $result[0]['b_city']." ".$result[0]['b_state']."<br />";
							$part .= $result[0]['country_name']." ".$result[0]['b_zipcode']."";						
						}
			$hotel_info = Hotel::GetHotelFullInfo();
			$part .= "</td>
					<td valign='top' align='right'>
						<h3>".$hotel_info["name"]."</h3>
						"._ADDRESS.": ".$hotel_info["address"]."<br />
						"._PHONE.": ".$hotel_info["phone"]."<br />
						"._FAX.": ".$hotel_info["fax"]."<br />
						"._EMAIL_ADDRESS.": ".$objSettings->GetParameter("admin_email")."<br />
						"._DATE_CREATED.": ".format_datetime($result[0]['payment_date'])."<br />
					</td>
				</tr>
				<tr><td colspan='2' nowrap height='10px'></td></tr>
				<tr>
					<td colspan='2'>";						
						$part .= "<table width='100%' border='0' cellspacing='0' cellpadding='3' style='border:1px solid #d1d2d3'>";
						$part .= "<tr style='background-color:#e1e2e3;font-weight:bold;font-size:13px;'><th align='left' colspan='2'>&nbsp;<b>"._BOOKING_DETAILS."</b></th></tr>";
						$part .= "<tr><td width='25%'>&nbsp;"._BOOKING_NUMBER.": </td><td>".$result[0]['booking_number']."</td></tr>";
						$part .= "<tr><td>&nbsp;"._DESCRIPTION.": </td><td>".$result[0]['booking_description']."</td></tr>";
						$part .= "</table><br />";									
						
						$part .= "<table width='100%' border='0' cellspacing='0' cellpadding='3' style='border:1px solid #d1d2d3'>";
						$part .= "<tr style='background-color:#e1e2e3;font-weight:bold;font-size:13px;'><th align='left' colspan='2'>&nbsp;<b>"._PAYMENT_DETAILS."</b></th></tr>";
						$part .= "<tr><td width='25%'>&nbsp;"._TRANSACTION.": </td><td>".$result[0]['transaction_number']."</td></tr>";
						$part .= "<tr><td>&nbsp;"._DATE_PAYMENT.": </td><td>".$result[0]['payment_date_formated']."</td></tr>";
						$part .= "<tr><td>&nbsp;"._PAYMENT_TYPE.": </td><td>".$result[0]['mod_payment_type']."</td></tr>";
						if($result[0]['campaign_name'] != "") $part .= "<tr><td>&nbsp;"._DISCOUNT.": </td><td>- ".Currencies::PriceFormat($result[0]['discount_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['discount_percent']."% - ".$result[0]['campaign_name'].")</td></tr>";
						$part .= "<tr><td>&nbsp;"._BOOKING_PRICE.(($result[0]['campaign_name'] != "") ? " ("._AFTER_DISCOUNT.")" : "").": </td><td>".Currencies::PriceFormat($result[0]['order_price'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
						$part .= "<tr><td>&nbsp;"._VAT.": </td><td>".Currencies::PriceFormat($result[0]['vat_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['vat_percent']."%)</td></tr>";
						$part .= "<tr><td>&nbsp;"._PAYMENT_SUM.": </td><td>".Currencies::PriceFormat($result[0]['order_price'] + $result[0]['vat_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";						
						if($result[0]['pre_payment'] == "first night"){
							$part .= "<tr><td>&nbsp;"._PRE_PAYMENT."</td><td>".Currencies::PriceFormat($result[0]['payment_sum'], $result[0]['symbol'], $result[0]['symbol_placement'])." ("._FIRST_NIGHT.")</td></tr>";
						}else if($result[0]['pre_payment'] != "" && $result[0]['pre_payment'] > 0 && $result[0]['pre_payment'] < 100){
							$part .= "<tr><td>&nbsp;"._PRE_PAYMENT."</td><td>".Currencies::PriceFormat($result[0]['payment_sum'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['pre_payment']."%)</td></tr>";
						}else{
							$part .= "<tr><td>&nbsp;"._PRE_PAYMENT."</td><td>"._FULL_PRICE."</td></tr>";
						}
						$part .= "<tr><td>&nbsp;"._ADDITIONAL_PAYMENT.": </td><td>".Currencies::PriceFormat($result[0]['additional_payment'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
						$part .= "<tr><td>&nbsp;"._TOTAL.": </td><td>".Currencies::PriceFormat($result[0]['payment_sum'] + $result[0]['additional_payment'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
						$part .= "</table><br />";															
				$part .= "</td>
				</tr>
				</table>";

			$content = @file_get_contents("html/templates/invoice.tpl");
			if($content){
				$content = str_replace("_TOP_PART_", $part, $content);
				$content = str_replace("_ROOMS_LIST_", $this->GetBookingRoomsList($oid, $language_id), $content);
				$content = str_replace("_EXTRAS_LIST_", Extras::GetExtrasList(unserialize($result[0]['extras']), $result[0]['currency']), $content);
				$content = str_replace("_YOUR_COMPANY_NAME_", $objSiteDescription->GetParameter("header_text"), $content);
				$content = str_replace("_ADMIN_EMAIL_", $objSettings->GetParameter("admin_email"), $content);
			}
		}
		$output .= "<div id='divInvoiceContent'>".$content."</div>";
		$output .= "<table width='100%' border='0'>";
		$output .= "<tr><td colspan='2'>&nbsp;</tr>";
		$output .= "<tr>";
		$output .= "  <td colspan='2' align='left'><input type='button' class='mgrid_button' name='btnBack' value='"._BUTTON_BACK."' onclick='javascript:document.location.href=\"index.php?".$this->page."\";'></td>";
		$output .= "</tr>";			
		$output .= "</table>";			
		
		echo $output;
	}
	
	/**
	 * Draws Booking Description
	 */
	public function DrawBookingDescription($rid)
	{
		global $objLogin;
		
		$output = "";
		$oid = isset($rid) ? (int)$rid : "0";
		$language_id = Application::Get("lang");

		$objBookingSettings  = new ModulesSettings("booking");
		$collect_credit_card = $objBookingSettings->GetSettings("online_collect_credit_card");
		
		$sql = "SELECT
					".$this->tableName.".".$this->primaryKey.",
					".$this->tableName.".booking_number,
					".$this->tableName.".booking_description,
					".$this->tableName.".additional_info,
					".$this->tableName.".discount_fee,
					".$this->tableName.".order_price,
					".$this->tableName.".vat_fee,
					".$this->tableName.".vat_percent,
					".$this->tableName.".payment_sum,
					".$this->tableName.".pre_payment,
					".$this->tableName.".additional_payment,
					".$this->tableName.".extras,
					CASE
						WHEN ".$this->tableName.".pre_payment = 'first night'
							THEN (".$this->tableName.".order_price - (".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment))
						WHEN (".$this->tableName.".pre_payment > 1 AND ".$this->tableName.".pre_payment < 100)
							THEN (".$this->tableName.".order_price - (".$this->tableName.".payment_sum + ".$this->tableName.".additional_payment))
						ELSE '0'
					END mod_have_to_pay,													
					".$this->tableName.".cc_type,
					IF(
						LENGTH(AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."')) = 4,
						CONCAT('...', AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."')),
						AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."')
					) as cc_number,								
					CONCAT('...', SUBSTRING(AES_DECRYPT(cc_number, '".PASSWORDS_ENCRYPT_KEY."'), -4)) as cc_number_for_client,								
					IF(
						LENGTH(AES_DECRYPT(".$this->tableName.".cc_number, '".PASSWORDS_ENCRYPT_KEY."')) = 4,
						' ("._CLEANED.")',
						''
					) as cc_number_cleaned,								
					".$this->tableName.".cc_expires_month,
					".$this->tableName.".cc_expires_year,
					".$this->tableName.".cc_cvv_code, 
					".$this->tableName.".currency,
					".$this->tableName.".client_id,
					".$this->tableName.".transaction_number,
					".$this->tableName.".payment_date,
					DATE_FORMAT(".$this->tableName.".created_date, '".(($this->fieldDateFormat == "M d, Y") ? "%b %d, %Y %h:%i %p" : "%d %b %Y %h:%i %p")."') as created_date_formated,
					DATE_FORMAT(".$this->tableName.".payment_date, '".(($this->fieldDateFormat == "M d, Y") ? "%b %d, %Y %h:%i %p" : "%d %b %Y %h:%i %p")."') as payment_date_formated,
					".$this->tableName.".payment_type,
					CASE
						WHEN ".$this->tableName.".payment_type = 0 THEN '"._ONLINE_ORDER."'
						WHEN ".$this->tableName.".payment_type = 1 THEN '"._PAYPAL."'
						WHEN ".$this->tableName.".payment_type = 2 THEN '"._CREDIT_CARD."'
						WHEN ".$this->tableName.".payment_type = 3 THEN '2CO'
						ELSE '"._UNKNOWN."'
					END as mod_payment_type,
					CASE
						WHEN ".$this->tableName.".status = 0 THEN '<font color=#0000a3>"._PREPARING."</font>'
						WHEN ".$this->tableName.".status = 1 THEN '<font color=#a3a300>"._RESERVED."</font>'
						WHEN ".$this->tableName.".status = 2 THEN '<font color=#00a300>"._COMPLETED."</font>'
						WHEN ".$this->tableName.".status = 3 THEN '<font color=#a30000>"._REFUNDED."</font>'
						ELSE '"._UNKNOWN."'
					END as m_status,
					IF(".$this->tableName.".is_admin_reservation = 0, IF(".TABLE_CLIENTS.".user_name != '', ".TABLE_CLIENTS.".user_name, '[ "._WITHOUT_ACCOUNT." ]'), '"._ADMIN."') as client_name,
					".TABLE_CURRENCIES.".symbol,
					".TABLE_CURRENCIES.".symbol_placement,
					CASE
						WHEN ".TABLE_CURRENCIES.".symbol_placement  = 'left' THEN
							CONCAT(".TABLE_CURRENCIES.".symbol, ".$this->tableName.".additional_payment)
						ELSE
							CONCAT(".$this->tableName.".additional_payment, ".TABLE_CURRENCIES.".symbol)
					END as tp_w_additional,
					CONCAT('<a href=\"index.php?".$this->page."&mg_action=description&oid=', ".$this->tableName.".".$this->primaryKey.", '\">', '"._DESCRIPTION."', '</a>') as link_order_description,
					".TABLE_CAMPAIGNS.".campaign_name,
					".TABLE_CAMPAIGNS.".discount_percent 
				FROM ".$this->tableName."
					INNER JOIN ".TABLE_CURRENCIES." ON ".$this->tableName.".currency = ".TABLE_CURRENCIES.".code
					LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".$this->tableName.".client_id = ".TABLE_CLIENTS.".id
					LEFT OUTER JOIN ".TABLE_CAMPAIGNS." ON ".$this->tableName.".discount_campaign_id = ".TABLE_CAMPAIGNS.".id
				WHERE
					".$this->tableName.".".$this->primaryKey." = ".(int)$oid."";
				if($this->user_id != ""){
					$sql .= " AND ".$this->tableName.".is_admin_reservation = 0 AND ".$this->tableName.".client_id = ".(int)$this->user_id;
				}
					
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){
			$output .= "<table width='100%' border='0'>";
			$output .= "<tr><td width='25%'>"._BOOKING_NUMBER.": </td><td>".$result[0]['booking_number']."</td></tr>";
			$output .= "<tr><td>"._DESCRIPTION.": </td><td>".$result[0]['booking_description']."</td></tr>";
			$output .= "<tr><td>"._TRANSACTION.": </td><td>".$result[0]['transaction_number']."</td></tr>";
			$output .= "<tr><td>"._DATE_CREATED.": </td><td>".$result[0]['created_date_formated']."</td></tr>";
			$output .= "<tr><td>"._DATE_PAYMENT.": </td><td>".$result[0]['payment_date_formated']."</td></tr>";
			if($this->user_id == "") $output .= "<tr><td>"._CLIENT.": </td><td>".$result[0]['client_name']."</td></tr>";
			$output .= "<tr><td>"._PAYMENT_TYPE.": </td><td>".$result[0]['mod_payment_type']."</td></tr>";
			if($result[0]['payment_type'] == "0" && $collect_credit_card == "yes"){
				$output .= "<tr><td>"._CREDIT_CARD_TYPE.": </td><td>".$result[0]['cc_type']."</td></tr>";
				if($this->user_id == ""){
					$output .= "<tr><td>"._CREDIT_CARD_NUMBER.": </td><td>".$result[0]['cc_number'].$result[0]['cc_number_cleaned']."</td></tr>";
					$output .= "<tr><td>"._CREDIT_CARD_EXPIRES.": </td><td>".(($result[0]['cc_expires_month'] != "") ? $result[0]['cc_expires_month']."/".$result[0]['cc_expires_year'] : "")."</td></tr>";
					$output .= "<tr><td>"._CVV_CODE.": </td><td>".$result[0]['cc_cvv_code']."</td></tr>";				
				}else{
					$output .= "<tr><td>"._CREDIT_CARD_NUMBER.": </td><td>".$result[0]['cc_number_for_client']."</td></tr>";
				}
			}
			if($result[0]['campaign_name'] != "") $output .= "<tr><td>"._DISCOUNT.": </td><td>- ".Currencies::PriceFormat($result[0]['discount_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['discount_percent']."% - ".$result[0]['campaign_name'].")</td></tr>";
			$output .= "<tr><td>"._BOOKING_PRICE.(($result[0]['campaign_name'] != "") ? " ("._AFTER_DISCOUNT.")" : "").": </td><td>".Currencies::PriceFormat($result[0]['order_price'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
			$output .= "<tr><td>"._VAT.": </td><td>".Currencies::PriceFormat($result[0]['vat_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['vat_percent']."%)</td></tr>";

			if($result[0]['pre_payment'] == "first night"){
				$output .= "<tr><td>"._PAYMENT_SUM.": </td><td>".Currencies::PriceFormat($result[0]['order_price'] + $result[0]['vat_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
				$output .= "<tr><td>"._PRE_PAYMENT.": </td><td>".Currencies::PriceFormat($result[0]['payment_sum'], $result[0]['symbol'], $result[0]['symbol_placement'])." ("._PARTIAL_PRICE." - "._FIRST_NIGHT.")</td></tr>";
				if($result[0]['additional_payment'] != 0) $output .= "<tr><td>"._ADDITIONAL_PAYMENT.": </td><td>".$result[0]['tp_w_additional']."</td></tr>";
				if($result[0]['mod_have_to_pay'] > 0) $output .= "<tr><td style='color:#a60000'>"._PAYMENT_REQUIRED.": </td><td style='color:#a60000'>".Currencies::PriceFormat($result[0]['mod_have_to_pay'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";						
			}else if($result[0]['pre_payment'] != "" && $result[0]['pre_payment'] > 0 && $result[0]['pre_payment'] < 100){
				$output .= "<tr><td>"._PAYMENT_SUM.": </td><td>".Currencies::PriceFormat($result[0]['order_price'] + $result[0]['vat_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
				$output .= "<tr><td>"._PRE_PAYMENT.": </td><td>".Currencies::PriceFormat($result[0]['payment_sum'], $result[0]['symbol'], $result[0]['symbol_placement'])." ("._PARTIAL_PRICE." - ".$result[0]['pre_payment']."%)</td></tr>";
				if($result[0]['additional_payment'] != 0) $output .= "<tr><td>"._ADDITIONAL_PAYMENT.": </td><td>".$result[0]['tp_w_additional']."</td></tr>";
				if($result[0]['mod_have_to_pay'] > 0) $output .= "<tr><td style='color:#a60000'>"._PAYMENT_REQUIRED.": </td><td style='color:#a60000'>".Currencies::PriceFormat($result[0]['mod_have_to_pay'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";						
			}else{
				$output .= "<tr><td>"._PAYMENT_SUM.": </td><td>".Currencies::PriceFormat($result[0]['payment_sum'], $result[0]['symbol'], $result[0]['symbol_placement'])."</td></tr>";
				if($result[0]['additional_payment'] != 0) $output .= "<tr><td>"._ADDITIONAL_PAYMENT.": </td><td>".$result[0]['tp_w_additional']."</td></tr>";
				$output .= "<tr><td>"._PRE_PAYMENT.": </td><td>"._FULL_PRICE."</td></tr>";				
			}			
			$output .= "<tr><td>"._STATUS.": </td><td>".$result[0]['m_status']."</td></tr>";
			if($result[0]['additional_info'] != "") $output .= "<tr><td>"._ADDITIONAL_INFO.": </td><td>".$result[0]['additional_info']."</td></tr>";
			$output .= "<tr><td colspan='2'>&nbsp;</tr>";
			$output .= "</table>";
			
			$extras = unserialize($result[0]['extras']);
			if(is_array($extras) && count($extras) > 0){				
				$output .= Extras::GetExtrasList($extras, $result[0]['currency']);				
			}
		}else{
			$output .= draw_important_message(_WRONG_PARAMETER_PASSED, false);
		}

		$output .= $this->GetBookingRoomsList($oid, $language_id);
		
		$output .= "<table width='100%' border='0'>";
		$output .= "<tr><td colspan='2'>&nbsp;</tr>";
		$output .= "<tr>";
		$output .= "  <td colspan='2' align='left'><input type='button' class='mgrid_button' name='btnBack' value='"._BUTTON_BACK."' onclick='javascript:document.location.href=\"index.php?".$this->page."\";'></td>";
		$output .= "</tr>";			
		$output .= "</table>";			
		
		echo $output;
	}

	/**
	 * Before-Delete record
	 */
    public function BeforeDeleteRecord()
	{
		$oid = MicroGrid::GetParameter('rid');
		$sql = "SELECT booking_number, rooms_amount, client_id, is_admin_reservation, status FROM ".TABLE_BOOKINGS." WHERE id = ".(int)$oid;		
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
		if($result[1] > 0){
			$this->booking_number = $result[0]['booking_number'];
			$this->rooms_amount = $result[0]['rooms_amount'];
			$this->booking_status = $result[0]['status'];
			$this->booking_client_id = $result[0]['client_id'];
			$this->booking_is_admin_reserv = (int)$result[0]['is_admin_reservation'];
			return true;
		}
		return false;
	}
	
	/**
	 *	After-Delete record
	 */	
	public function AfterDeleteRecord()
	{
		global $objLogin;
		
		$sql = "DELETE FROM ".TABLE_BOOKINGS_ROOMS." WHERE booking_number = '".encode_text($this->booking_number)."'";
		if($this->user_id != ""){
			$sql .= " AND ".$this->tableName.".is_admin_reservation = 0 AND ".$this->tableName.".client_id = ".(int)$this->user_id;
		}		
		if(!database_void_query($sql)){ /* echo "error!"; */ }	 

		// update client orders/rooms amount
		if($objLogin->IsLoggedIn() && ($this->booking_status > 0) && ($this->booking_is_admin_reserv == "0")){
			$sql = "UPDATE ".TABLE_CLIENTS." SET
						orders_count = IF(orders_count > 0, orders_count - 1, orders_count),
						rooms_count = IF(rooms_count > 0, rooms_count - ".(int)$this->rooms_amount.", rooms_count)
					WHERE id = ".(int)$this->booking_client_id;
			database_void_query($sql);
		}
	}
	
	/**
	 *	Update Payment Date
	 * 		@param $rid
	 */
	public function UpdatePaymentDate($rid)
	{
		$sql = "UPDATE ".$this->tableName."
				SET payment_date = '".date("Y-m-d H:i:s")."'
				WHERE
					".$this->primaryKey." = ".(int)$rid." AND 
					status = 2 AND
					(payment_date = '' OR payment_date = '0000-00-00')";
		database_void_query($sql);		
	}
		
	/**
	 *	Cleans credit card info
	 * 		@param $rid
	 */
	public function CleanCreditCardInfo($rid)
	{
		$sql = "UPDATE ".$this->tableName."
				SET
					cc_number = AES_ENCRYPT(SUBSTRING(AES_DECRYPT(cc_number, '".PASSWORDS_ENCRYPT_KEY."'), -4), '".PASSWORDS_ENCRYPT_KEY."'),
					cc_cvv_code = '',
					cc_expires_month = '',
					cc_expires_year = ''
				WHERE ".$this->primaryKey." = ".(int)$rid;
		return database_void_query($sql);		
	}
	
	/**
	 * Returns Rooms list for booking
	 */
	private function GetBookingRoomsList($oid, $language_id)
	{
		$output = "";
		
        // display list of rooms in order		
		$sql = "SELECT
					".TABLE_BOOKINGS_ROOMS.".booking_number,
					".TABLE_BOOKINGS_ROOMS.".rooms,
					".TABLE_BOOKINGS_ROOMS.".checkin,
					".TABLE_BOOKINGS_ROOMS.".checkout,
					".TABLE_ROOMS.".max_adults,
					".TABLE_ROOMS.".max_children,					
					".TABLE_ROOMS_DESCRIPTION.".room_type,
					".TABLE_BOOKINGS_ROOMS.".price,
					CASE
						WHEN ".TABLE_CURRENCIES.".symbol_placement = 'left' THEN
							CONCAT(".TABLE_CURRENCIES.".symbol, ".TABLE_BOOKINGS_ROOMS.".price)
						ELSE
							CONCAT(".TABLE_BOOKINGS_ROOMS.".price, ".TABLE_CURRENCIES.".symbol)
					END as rp_w_currency
				FROM ".TABLE_BOOKINGS_ROOMS."
					INNER JOIN ".$this->tableName." ON ".TABLE_BOOKINGS_ROOMS.".booking_number = ".$this->tableName.".booking_number
					INNER JOIN ".TABLE_ROOMS." ON ".TABLE_BOOKINGS_ROOMS.".room_id = ".TABLE_ROOMS.".id
					INNER JOIN ".TABLE_ROOMS_DESCRIPTION." ON ".TABLE_ROOMS.".id = ".TABLE_ROOMS_DESCRIPTION.".room_id 
					LEFT OUTER JOIN ".TABLE_CURRENCIES." ON ".$this->tableName.".currency = ".TABLE_CURRENCIES.".code
					LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".$this->tableName.".client_id = ".TABLE_CLIENTS.".id
				WHERE
					".TABLE_ROOMS_DESCRIPTION.".language_id = '".encode_text($language_id)."' AND 
					".$this->tableName.".".$this->primaryKey." = ".(int)$oid." ";
				if($this->user_id != ""){
					$sql .= " AND ".$this->tableName.".is_admin_reservation = 0 AND ".$this->tableName.".client_id = ".(int)$this->user_id;
				}

		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS, FETCH_ASSOC);

		if($result[1] > 0){
			$reservations_total = 0;

			$output .= "<h4>"._RESERVATION_DETAILS."</h4>";					
			$output .= "<table width='100%' border='0' cellspacing='0' cellpadding='3' style='border:1px solid #d1d2d3'>";
			$output .= "<tr style='background-color:#e1e2e3;font-weight:bold;font-size:13px;'>
				<th align='center'> # </th>
				<th align='left'>"._ROOM_TYPE."</th>
				<th align='left'>"._CHECK_IN."</th>
				<th align='left'>"._CHECK_OUT."</th>
				<th align='center'>"._ROOMS."</th>
				<th align='center'>"._ADULTS."</th>
				<th align='center'>"._CHILDREN."</th>
				<th align='right'>"._PRICE."</th>
				<th width='5px' nowrap>&nbsp;</th>
			</tr>";	
			for($i=0; $i < $result[1]; $i++){
				$output .= "<tr>";
				$output .= " <td align='center' width='40px'>".($i+1).".</td>";
				$output .= " <td align='left'>".$result[0][$i]['room_type']."</td>";
				$output .= " <td align='left'>".format_datetime($result[0][$i]['checkin'], $this->fieldDateFormat)."</td>";
				$output .= " <td align='left'>".format_datetime($result[0][$i]['checkout'], $this->fieldDateFormat)."</td>";
				$output .= " <td align='center'>".$result[0][$i]['rooms']."</td>";
				$output .= " <td align='center'>".$result[0][$i]['max_adults']."</td>";
				$output .= " <td align='center'>".$result[0][$i]['max_children']."</td>";
				$output .= " <td align='right'>".$result[0][$i]['rp_w_currency']."</td>";
				$output .= " <td></td>";
				$output .= "</tr>";
				$reservations_total += $result[0][$i]['price'];
			}
			if($reservations_total > 0){
				$output .= "<tr>";
				$output .= "<td colspan='6'></td>";
				$output .= "<td colspan='2' align='right'><span style='background-color:#e1e2e3;'>&nbsp;<b>"._TOTAL.": &nbsp;&nbsp;&nbsp;".Currencies::PriceFormat($reservations_total)."</b>&nbsp;</span></td>";
				$output .= " <td></td>";
				$output .= "</tr>";					
			}
			$output .= "</table>";			
		}
		
		
		return $output;
	}
	
	//==========================================================================
    // Static Methods
	//==========================================================================
	/**
	 * Remove expired 'Preparing' bookings
	 */
	static public function RemoveExpired()
	{
		$objBookingSettings  = new ModulesSettings("booking");
		$preparing_orders_timeout = (int)$objBookingSettings->GetSettings("preparing_orders_timeout");

		if($preparing_orders_timeout > 0){
			$sql = "DELETE FROM ".TABLE_BOOKINGS."
					WHERE status = 0 AND
						  TIMESTAMPDIFF(HOUR, created_date, '".date("Y-m-d H:i:s")."') > ".(int)$preparing_orders_timeout;
			return database_void_query($sql);
		}
	}
	
}
?>