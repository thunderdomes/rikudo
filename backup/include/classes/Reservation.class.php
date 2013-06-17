<?php

/***
 *	Class Reservation
 *  -------------- 
 *  Description : encapsulates Booking properties
 *  Updated	    : 22.05.2011
 *	Written by  : ApPHP
 *	
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct										GetVatPercent 
 *  __destruct                                      
 *  AddToReservation
 *  RemoveReservation
 *  ShowReservationInfo
 *  ShowCheckoutInfo
 *  EmptyCart
 *  GetCartItems
 *  IsCartEmpty
 *  PlaceBooking
 *  DoReservation
 *  SendOrderEmail
 *  DrawReservation
 *  
 **/

class Reservation {

	public $arrReservation;
	public $error;
	public $message;

	private $fieldDateFormat;
	private $cartItems;
	private $roomsCount;
	private $cartTotalSum;
	private $firstNightSum;
	private $cartAdditionalInfo;
	private $currentClientID;
	private $selectedUser;
	private $vatPercent;
	private $discountPercent;
	private $discountCampaignID;
	private $lang;
	private $paypal_form_type;
	private $paypal_form_fields;
	private $paypal_form_fields_count;
	private $first_night_possible; 


	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{
		global $objSettings;
		global $objLogin;

		if(!@$objLogin->IsLoggedIn()){
			$this->currentClientID = isset($_SESSION['current_client_id']) ? (int)$_SESSION['current_client_id'] : 0;
			$this->selectedUser = "client";
		}else if($objLogin->IsLoggedInAsClient()){
			$this->currentClientID = @$objLogin->GetLoggedID();
			$this->selectedUser = "client";			
		}else{
			if(isset($_SESSION['sel_current_client_id']) && (int)$_SESSION['sel_current_client_id'] != 0){
				$this->currentClientID = (int)$_SESSION['sel_current_client_id'];
				$this->selectedUser = "client";
			}else{
				$this->currentClientID = @$objLogin->GetLoggedID();
				$this->selectedUser = "admin";
			}
		}

		// preapre currency info
		if(Application::Get("currency_code") == ""){
			$this->currencyCode = Currencies::GetDefaultCurrency();
			$this->currencyRate = "1";
		}else{
			//$default_currency_info = Currencies::GetDefaultCurrencyInfo();
			$this->currencyCode = Application::Get("currency_code");
			$this->currencyRate = Application::Get("currency_rate");
		}		

		// prepare discount info
		$campaign_info = Campaigns::GetCampaignInfo();
		if($campaign_info["id"] != ""){
			$this->discountCampaignID = $campaign_info["id"];
			$this->discountPercent = $campaign_info["discount_percent"];			
		}else{
			$this->discountCampaignID = "";
			$this->discountPercent = "0";
		}

		// prepare VAT percent
		$this->vatPercent = $this->GetVatPercent();

		// preapre datetime format
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			$this->fieldDateFormat = "M d, Y";
		}else{
			$this->fieldDateFormat = "d M, Y";
		}
		
		$this->lang = Application::Get("lang");

		$this->arrReservation = &$_SESSION['reservation'];
		$this->cartItems = 0;
		$this->roomsCount = 0;
		$this->cartTotalSum = 0;
		$this->firstNightSum = 0;
		$this->first_night_possible = false;
		$this->paypal_form_type = "multiple"; // single | multiple
		$this->paypal_form_fields = ""; 

		if(count($this->arrReservation) > 0){
			$paypal_form_fields_count = 0;
			foreach($this->arrReservation as $key => $val){
				$this->cartItems += 1;
				$this->roomsCount += $val['rooms'];
				$this->cartTotalSum += ($val['price'] / $this->currencyRate);
				$this->firstNightSum += ($val['price'] / $val['nights']);
				if($val['nights'] > 1) $this->first_night_possible = true;
				
				if($this->paypal_form_type == "multiple"){
					$this->paypal_form_fields_count++;					
					$this->paypal_form_fields .= "
						<input type='hidden' name='item_name_".$this->paypal_form_fields_count."' value='"._ROOM_TYPE.": ".$val['room_type']."'>
						<input type='hidden' name='quantity_".$this->paypal_form_fields_count."' value='".$val['rooms']."'>
						<input type='hidden' name='amount_".$this->paypal_form_fields_count."' value='".number_format((($val['price'] / $this->currencyRate) / $val['rooms']), "2", ".", ",")."'>";
				}
			}
		}
		$this->cartTotalSum = number_format($this->cartTotalSum, 2, ".", "");

		$this->message = "";
		$this->error = "";
		
		//echo "<pre>";
		//print_r($this->arrReservation);
		//echo "</pre>";
	}

	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/**
	 * Add room to reservation
	 * 		@param $room_id
	 * 		@param $from_date
	 * 		@param $to_date
	 * 		@param $nights
	 * 		@param $rooms
	 * 		@param $price
	 */	
	public function AddToReservation($room_id, $from_date, $to_date, $nights, $rooms, $price)
	{
		if (!empty($room_id)){			
			if(isset($this->arrReservation[$room_id])){
				// add new info for this room
				$this->arrReservation[$room_id]["from_date"] = $from_date;
				$this->arrReservation[$room_id]["to_date"] 	 = $to_date;
				$this->arrReservation[$room_id]["nights"] 	 = $nights;
				$this->arrReservation[$room_id]["rooms"] 	 = $rooms;
				$this->arrReservation[$room_id]["price"] 	 = $price;
				$this->arrReservation[$room_id]["room_type"] = Rooms::GetRoomInfo($room_id, "room_type");
			}else{
				// just add new room
				$this->arrReservation[$room_id] = array(
					"from_date" => $from_date,
					"to_date"   => $to_date,
					"nights"    => $nights,
					"rooms"     => $rooms,
					"price"     => $price,
					"room_type" => Rooms::GetRoomInfo($room_id, "room_type")
				);
			}			
			$this->error = draw_success_message(_ROOM_WAS_ADDED, false);
		}else{
			$this->error = draw_important_message(_WRONG_PARAMETER_PASSED, false);
		}
	}

	/**
	 * Remove room from the reservation cart
	 * 		@param $room_id
	 */
	public function RemoveReservation($room_id)
	{
		if((int)$room_id > 0){
			if(isset($this->arrReservation[$room_id]) && $this->arrReservation[$room_id] > 0){
				unset($this->arrReservation[$room_id]);
				$this->error = draw_message(_ROOM_WAS_REMOVED, false);
			}else{
				$this->error = draw_important_message(_ROOM_NOT_FOUND, false);
			}
		}else{
			$this->error = draw_important_message(_ROOM_NOT_FOUND, false);
		}
	}	

    /** 
	 * Show Reservation Cart on the screen
	 */	
	public function ShowReservationInfo()
	{
		global $objLogin;

		if(count($this->arrReservation)>0)
		{
			echo "<table class='reservation_cart' border='0' width='100%' align='center' cellspacing='0' cellpadding='5'>
			<tr class='header'>
				<th class='left' width='30px'>&nbsp;</th>
				<th width='40px'>&nbsp;</th>
				<th align='left'>"._ROOM_TYPE."</th>
				<th align='center'>"._FROM."</th>
				<th align='center'>"._TO."</th>
				<th width='40px' align='center'>&nbsp;"._ADULTS."&nbsp;</th>
				<th width='40px' align='center'>&nbsp;"._ROOMS."&nbsp;</th>
				<th width='40px' align='center'>&nbsp;"._NIGHTS."&nbsp;</th>								
				<th width='65px' align='center'>"._PER_NIGHT."</th>
				<th class='right' align='right'>"._PRICE."</th>
			</tr>
			<tr><td colspan='10' nowrap height='5px'></td></tr>";

			$order_price=0;
			$objRoom = new Rooms();
			foreach($this->arrReservation as $key => $val)
			{
				$sql = "SELECT
							".TABLE_ROOMS.".id,
							".TABLE_ROOMS.".room_type,
							".TABLE_ROOMS.".room_icon,
							".TABLE_ROOMS.".max_adults,
							".TABLE_ROOMS.".max_children, 
							".TABLE_ROOMS.".default_price as price,
							".TABLE_ROOMS_DESCRIPTION.".room_type as loc_room_type,
							".TABLE_ROOMS_DESCRIPTION.".room_short_description as loc_room_short_description
						FROM ".TABLE_ROOMS."
							INNER JOIN ".TABLE_ROOMS_DESCRIPTION." ON ".TABLE_ROOMS.".id = ".TABLE_ROOMS_DESCRIPTION.".room_id
						WHERE
							".TABLE_ROOMS.".id = ".$key." AND
							".TABLE_ROOMS_DESCRIPTION.".language_id = '".$this->lang."'";
							
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($result[1] > 0){			
					$room_icon = ($result[0]['room_icon'] != "") ? $result[0]['room_icon'] : "no_image.png";
					echo "<tr>
							<td align='center'><a href='index.php?page=booking&act=remove&rid=".$key."'><img src='images/remove.gif' width='16' height='16' border='0' title='"._REMOVE_ROOM_FROM_CART."' alt='' /></a></td>
							<td><img src='images/rooms_icons/".$room_icon."' alt='' width='32px' height='32px' /></td>							
							<td>&nbsp;<b><a href='index.php?page=room_description&room_id=".$result[0]['id']."' title='"._CLICK_TO_SEE_DESCR."'>".$result[0]['loc_room_type']."</a></b></td>
							<td align='center'>".format_datetime($val['from_date'], $this->fieldDateFormat)."</td>
							<td align='center'>".format_datetime($val['to_date'], $this->fieldDateFormat)."</td>							
							<td align='center'>".$result[0]['max_adults']."</td>
							<td align='center'>".$val['rooms']."</td>
							<td align='center'>".$val['nights']."</td>
							<td align='center'>".Currencies::PriceFormat(($val['price'] / $this->currencyRate) / $val['nights'])."</td>
							<td align='right'>".Currencies::PriceFormat($val['price'] / $this->currencyRate)."&nbsp;<a class='price_link' href='javascript:void(0);' onclick=\"javascript:appToggleElement('row_prices_".$key."')\" title='"._CLICK_TO_SEE_PRICES."'>(+)</a></td>
						</tr>
						<tr><td colspan='10' nowrap height='3px'></td></tr>";
					echo "<tr>
							<td colspan='10' align='right'><span id='row_prices_".$key."' style='margin:5px 5px 10px 5px;display:none;'>".$objRoom->GetRoomPricesTable($key)."</span></td>
						</tr>";
					$order_price += ($val['price'] / $this->currencyRate);					
				}
			}
			
			echo "<tr><td colspan='10' nowrap height='15px'></td></tr>";
			
			// calculate discount			
			$discount_value = ($order_price * ($this->discountPercent / 100));
			$order_price -= $discount_value;
			
			// calculate percent
			$vat_cost = ($order_price * ($this->vatPercent / 100));
			$cart_total = $order_price + $vat_cost;
			
			echo "<tr> 
					<td colspan='5'></td>
					<td class='td left' colspan='4'><b>"._VAT.": (".$this->vatPercent."%)</b></td>
					<td class='td right' align='right'><b>".Currencies::PriceFormat($vat_cost)."</b></td>
				</tr>";
			if($this->discountCampaignID != ""){
				echo "<tr>
						<td colspan='5'></td>
						<td class='td left' colspan='4'><b><span style='color:#a60000'>"._DISCOUNT.": (".$this->discountPercent."%)</span></b></td>
						<td class='td right' align='right'><b><span style='color:#a60000'>- ".Currencies::PriceFormat($discount_value)."</span></b></td>
					</tr>";				
			}				
			echo "<tr class='footer'>
					<td colspan='5'></td>
					<td class='td left' colspan='4'><b>"._TOTAL.":</b></td>
					<td class='td right' align='right'><b>".Currencies::PriceFormat($cart_total)."</b></td>
				</tr>
				<tr><td colspan='10' nowrap height='15px'></td></tr>
				<tr>
					<td colspan='5'></td>
					<td colspan='4' align='left'>";						
					echo "</td>
					<td align='right'>
						<input type='button' class='form_button' onclick='javascript:appGoTo(\"page=booking_details\")' value='"._BOOK."' />
					</td>
				</tr>
				</table>";
		}else{
			draw_message(_RESERVATION_CART_IS_EMPTY_ALERT, true, true);
		}
	}

    /** 
	 * Show checkout info
	 */	
	public function ShowCheckoutInfo()
	{
		global $objLogin;
		
		$objBookingSettings     = new ModulesSettings("booking");
		$default_payment_system = $objBookingSettings->GetSettings("default_payment_system");
		$pre_payment            = $objBookingSettings->GetSettings("pre_payment");
		$payment_method_online  = $objBookingSettings->GetSettings("payment_method_online");
		$payment_method_paypal  = $objBookingSettings->GetSettings("payment_method_paypal");
		$payment_method_2co     = $objBookingSettings->GetSettings("payment_method_2co");
		$payment_method         = isset($_POST['payment_method']) ? prepare_input($_POST['payment_method']) : $default_payment_system;
		$payment_method_cnt	    = ($payment_method_online === 'yes')+($payment_method_paypal === 'yes')+($payment_method_2co === 'yes');

		$find_user = isset($_GET['cl']) ? prepare_input($_GET['cl']) : "";
		$cid 	   = isset($_GET['cid']) ? prepare_input($_GET['cid']) : "";
		if($cid != ""){
			if($cid != "admin"){
				$this->currentClientID = $_SESSION['sel_current_client_id'] = $cid;
				$this->selectedUser = "client";			
			}else{
				$this->currentClientID = @$objLogin->GetLoggedID();
				$this->selectedUser = "admin";
				unset($_SESSION['sel_current_client_id']);
			}
		}
						    
		if(@$objLogin->IsLoggedInAsAdmin() && $this->selectedUser == "admin"){
			$table_name = TABLE_ACCOUNTS;
			$sql="SELECT
					".$table_name.".*
				  FROM ".$table_name."
				  WHERE ".$table_name.".id = ".(int)$this->currentClientID;
		}else{
			$table_name = TABLE_CLIENTS;
			$sql="SELECT
					".$table_name.".*,
					".TABLE_COUNTRIES.".name as country_name,
					".TABLE_COUNTRIES.".vat_value
				  FROM ".$table_name."
					 JOIN ".TABLE_COUNTRIES." ON ".$table_name.".b_country = ".TABLE_COUNTRIES.".abbrv
				  WHERE ".$table_name.".id = ".(int)$this->currentClientID;				  
		}
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] <= 0){
			draw_message(_RESERVATION_CART_IS_EMPTY_ALERT, true, true);
			return false;
		}

		if(count($this->arrReservation)>0)
		{
			echo "<form id='checkout-form' action='index.php?page=booking_payment' method='post'>
			<input type='hidden' name='task' value='do_booking' />
			<input type='hidden' name='selected_user' value='".$this->selectedUser."' />
			".draw_token_field(true)."
			
			<table class='reservation_cart' border='0' width='99%' align='center' cellspacing='0' cellpadding='5'>
			<tr>
				<td colspan='2'><h4>"._BILLING_DETAILS." &nbsp;";
					if(@$objLogin->IsLoggedIn()){
						if(@$objLogin->IsLoggedInAsClient()){
							echo "<a style='font-size:13px;' href='javascript:void(0);' onclick='javascript:appGoTo(\"client=my_account\")'>["._EDIT_WORD."]</a>	";
						}else if(@$objLogin->IsLoggedInAsAdmin()){
						
							echo "<br>"._CHANGE_CLIENT.": 
							<input type='text' id='find_user' name='find_user' value='' size='10' />
							<input type='button' class='button' value='"._SEARCH."' onclick='javascript:appGoTo(\"page=booking_checkout&cl=\"+jQuery(\"#find_user\").val())' />
							<select name='sel_client' id='sel_client'>";
								if($find_user == ""){
									if($this->selectedUser == "admin"){
										echo "<option value='admin'>".$result[0]['first_name']." ".$result[0]['last_name']." (".$result[0]['user_name'].")</option>";										
									}else{
										echo "<option value='".$result[0]['id']."'>ID:".$result[0]['id']." ".$result[0]['first_name']." ".$result[0]['last_name']." (".(($result[0]['user_name'] != "") ? $result[0]['user_name'] : _WITHOUT_ACCOUNT).")"."</option>";										
									}
								}else{
									$objClients = new Clients();
									$result_clients = $objClients->GetAllClients(" AND (last_name like '".$find_user."%' OR first_name like '".$find_user."%' OR user_name like '".$find_user."%') ");
									if($result_clients[1] > 0){
										for($i = 0; $i < $result_clients[1]; $i++){
											echo "<option value='".$result_clients[0][$i]['id']."'>ID:".$result_clients[0][$i]['id']." ".$result_clients[0][$i]['first_name']." ".$result_clients[0][$i]['last_name']." (".(($result_clients[0][$i]['user_name'] != "") ? $result_clients[0][$i]['user_name'] : _WITHOUT_ACCOUNT).")"."</option>";
										}								
									}else{
										echo "<option value='admin'>".$result[0]['first_name']." ".$result[0]['last_name']." (".$result[0]['user_name'].")</option>";
									}
								}								
							echo "</select> ";
							if($find_user != "") echo "<input type='button' class='button' value='"._APPLY."' onclick='javascript:appGoTo(\"page=booking_checkout&cid=\"+jQuery(\"#sel_client\").val())'/> ";
							echo "<input type='button' class='button' value='"._SET_ADMIN."' onclick='javascript:appGoTo(\"page=booking_checkout&cid=admin\")'/>";
							if($find_user != "" && $result_clients[1] == 0) echo " "._NO_CLIENT_FOUND;
						}
					}else{
						echo "<a style='font-size:13px;' href='javascript:void(0);' onclick='javascript:appGoTo(\"page=booking_details\", \"&m=edit\")'>["._EDIT_WORD."]</a>	";
					}
					echo "</h4>
				</td>
			</tr>
			<tr>
				<td style='padding-left:10px;'>
					"._FIRST_NAME.": ".$result[0]['first_name']."<br />
					"._LAST_NAME.": ".$result[0]['last_name']."<br />";				
					if(!@$objLogin->IsLoggedInAsAdmin()){					
						echo _ADDRESS.": ".$result[0]['b_address']."<br />";
						echo _ADDRESS_2.": ".$result[0]['b_address_2']."<br />";
						echo _CITY.": ".$result[0]['b_city']."<br />";
						echo _STATE.": ".$result[0]['b_state']."<br />";
						echo _COUNTRY.": ".$result[0]['country_name']."<br />";
						echo _ZIP_CODE.": ".$result[0]['b_zipcode']."<br />";
					}				
				echo "</td>
				<td></td>
			</tr>
			</table><br />";

			echo "<table class='reservation_cart' border='0' width='99%' align='center' cellspacing='0' cellpadding='5'>
			<tr>
				<td colspan='8'><h4>"._RESERVATION_DETAILS."</h4></td>
			</tr>
			<tr class='header'>
				<th class='left' width='40px'>&nbsp;</td>
				<th align='left'>"._ROOM_TYPE."</td>
				<th align='center'>"._FROM."</td>
				<th align='center'>"._TO."</td>
				<th width='60px' align='center'>"._NIGHTS."</td>								
				<th width='70px' align='center'>"._ADULTS."</td>
				<th width='50px' align='center'>"._ROOMS."</td>
				<th class='right' width='90px' align='right'>"._PRICE."</td>
			</tr>
			<tr><td colspan='8' nowrap height='5px'></td></tr>";
			
			$order_price=0;
			foreach ($this->arrReservation as $key => $val)
			{
				$sql = "SELECT
							".TABLE_ROOMS.".id,
							".TABLE_ROOMS.".room_type,
							".TABLE_ROOMS.".room_icon,
							".TABLE_ROOMS.".max_adults,
							".TABLE_ROOMS.".max_children, 
							".TABLE_ROOMS.".default_price as price,
							".TABLE_ROOMS_DESCRIPTION.".room_type as loc_room_type,
							".TABLE_ROOMS_DESCRIPTION.".room_short_description as loc_room_short_description
						FROM ".TABLE_ROOMS."
							INNER JOIN ".TABLE_ROOMS_DESCRIPTION." ON ".TABLE_ROOMS.".id = ".TABLE_ROOMS_DESCRIPTION.".room_id
						WHERE
							".TABLE_ROOMS.".id = ".$key." AND
							".TABLE_ROOMS_DESCRIPTION.".language_id = '".$this->lang."'";
							
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($result[1] > 0){
					$room_icon = ($result[0]['room_icon'] != "") ? $result[0]['room_icon'] : "no_image.png";
					echo "<tr>
							<td><img src='images/rooms_icons/".$room_icon."' alt='' width='32px' height='32px' /></td>							
							<td>&nbsp;<b><a href='index.php?page=room_description&room_id=".$result[0]['id']."' title='"._CLICK_TO_SEE_DESCR."'>".$result[0]['loc_room_type']."</a></b></td>
							<td align='center'>".format_datetime($val['from_date'], $this->fieldDateFormat)."</td>
							<td align='center'>".format_datetime($val['to_date'], $this->fieldDateFormat)."</td>							
							<td align='center'>".$val['nights']."</td>
							<td align='center'>".$result[0]['max_adults']."</td>
							<td align='center'>".$val['rooms']."</td>
							<td align='right'>".Currencies::PriceFormat($val['price'] / $this->currencyRate)."&nbsp;</td>
						</tr>
						<tr><td colspan='8' nowrap height='3px'></td></tr>";
					$order_price += ($val['price'] / $this->currencyRate);					
				}
			}
			
			echo "<tr><td colspan='9' nowrap height='15px'></td></tr>";
			
			// calculate discount			
			$discount_value = ($order_price * ($this->discountPercent / 100));
			$order_price -= $discount_value;
			
			// calculate percent
			$vat_cost = ($order_price * ($this->vatPercent / 100));
			$cart_total = $order_price + $vat_cost;

			echo "<tr>
					<td colspan='4'></td>
					<td class='td left' colspan='3'><b>"._VAT.": (".$this->vatPercent."%)</b></td>
					<td class='td right' align='right'><b>".Currencies::PriceFormat($vat_cost)."</b></td>
				 </tr>";
			if($this->discountCampaignID != ""){
				echo "<tr>
						<td colspan='4'></td>
						<td class='td left' colspan='3'><b><span style='color:#a60000'>"._DISCOUNT.": (".$this->discountPercent."%)</span></b></td>
						<td class='td right' align='right'><b><span style='color:#a60000'>- ".Currencies::PriceFormat($discount_value)."</span></b></td>
					</tr>";				
			}
			echo "<tr><td colspan='8' nowrap height='5px'></td></tr>
				    <tr class='footer'>
					<td colspan='4'></td>
					<td class='td left' colspan='3'><b>"._TOTAL.":</b></td>
					<td class='td right' align='right'><b>".Currencies::PriceFormat($cart_total)."</b></td>
				 </tr>";

			// PAYMENT DETAILS
			// ------------------------------------------------------------
			echo "<tr><td colspan='8' nowrap height='12px'></td></tr>";
			echo "<tr><td colspan='8'><h4>"._PAYMENT_DETAILS."</h4></td></tr>";
			echo "<tr><td colspan='8'>";
			echo "<table width='340px' border='0'><tr>";			
				if($payment_method_cnt > 1){
					echo "<td>"._PAYMENT_METHOD.": </td><td> 
					<select name='payment_method' id='payment_method'>";
						if($payment_method_online == "yes"){
							echo "<option value='online' ".(($payment_method == "online") ? "selected='selected'" : "").">"._ONLINE_ORDER."</option>";	
						}							
						if($payment_method_paypal == "yes"){
							echo "<option value='paypal' ".(($payment_method == "paypal") ? "selected='selected'" : "").">"._PAYPAL."</option>";	
						}							
						if($payment_method_2co == "yes"){
							echo "<option value='2co' ".(($payment_method == "2co") ? "selected='selected'" : "").">2CO</option>";	
						}							
					echo "</select>";
				}else{
					echo "<td></td><td>";
					echo "<input type='hidden' name='payment_method' id='payment_method' value='".$payment_method."'>";
				}			
			echo "</td></tr>";
			echo "<tr>";
					echo "<td>"._PAYMENT_TYPE.": </td>";
					echo "<td>";
					echo "<input type='radio' name='pre_payment' id='pre_payment_full' value='100' checked='checked' /> <label for='pre_payment_full'>"._FULL_PRICE."</label> &nbsp;&nbsp;";
					if($pre_payment == "first night" && $this->first_night_possible){
						echo "<input type='radio' name='pre_payment' id='pre_payment_partially' value='".$pre_payment."' /> <label for='pre_payment_partially'>"._FIRST_NIGHT."</label>";						
					}else if($pre_payment > "0" && $pre_payment < "100"){
						echo "<input type='radio' name='pre_payment' id='pre_payment_partially' value='".$pre_payment."' /> <label for='pre_payment_partially'>"._PRE_PAYMENT." (".$pre_payment."%)</label>";
					}
					echo "</td>";
			echo "</tr>";			
			echo "</table></td></tr>";								
			
			// EXTRAS
			// ------------------------------------------------------------
			$extras = Extras::GetAllExtras();
			if($extras[1]){
				echo "<tr><td colspan='8' nowrap height='15px'></td></tr>";
				echo "<tr><td colspan='8'><h4>"._EXTRAS."</h4></td></tr>";				
				echo "<tr><td colspan='8'><table width='340px'>";				
				for($i=0; $i<$extras[1]; $i++){
					echo "<tr>";
					echo "<td><input type='checkbox' id='extras_".$extras[0][$i]['id']."' name='extras[]' value='".$extras[0][$i]['id']."'></td>";
					echo "<td>&nbsp;</td>";
					echo "<td nowrap><label for='extras_".$extras[0][$i]['id']."'>".$extras[0][$i]['name']."</label> <span class='help' title='".$extras[0][$i]['description']."'>[?]</span></td>";
					echo "<td>&nbsp;</td>";
					echo "<td align='right'>".Currencies::PriceFormat($extras[0][$i]['price'] / $this->currencyRate)."</td>";					
					echo "<td>&nbsp;</td>";
					echo "<td>".draw_numbers_select_field("extras_amount_".$extras[0][$i]['id'], "", "0", "10", 1, "extras_ddl", true)."</td>";
					echo "</tr>";
				}
				echo "</table></td></tr>";								
			}
			
			echo   "<tr><td colspan='8' nowrap height='15px'></td></tr>
			      <tr valign='middle'>
					<td colspan='8' nowrap height='15px'>
						<h4 style='cursor:pointer;' onclick=\"appToggleElement('additional_info')\">"._ADDITIONAL_INFO." +</h4>
						<textarea name='additional_info' id='additional_info' style='display:none;width:100%;height:75px'></textarea>
					</td>
				  </tr>
				  <tr><td colspan='8' nowrap height='5px'></td></tr>
			      <tr valign='middle'>
					<td colspan='5' align='right'></td>
					<td align='right' colspan='3'>
						<input class='button' type='submit' value='"._SUBMIT_BOOKING."' /> 
					</td>
				</tr>";
			echo "</table>";
			echo "</form>";
		}else{
			draw_message(_RESERVATION_CART_IS_EMPTY_ALERT, true, true);
		}
	}	

	/**
	 * Empty Reservation Cart
	 */
	public function EmptyCart()
	{
		$this->arrReservation=array();
		unset($_SESSION['current_client_id']);
	}
	
	/**
	 * Returns amount of items in Reservation Cart
	 */
	public function GetCartItems()
	{
		return $this->cartItems;
	}	
	
	/**
	 * Checks if cart is empty 
	 */
	public function IsCartEmpty()
	{
		return ($this->cartItems > 0) ? false : true;
	}	

	/**
	 * Draw reservation info
	 * 		@param $payment_method
	 * 		@param $additional_info
	 */
	public function DrawReservation($payment_method, $additional_info, $extras = array(), $pre_payment = "")
	{
		global $objLogin;

		$cc_type 		  = isset($_POST['cc_type']) ? prepare_input($_POST['cc_type']) : "";
		$cc_number 		  = isset($_POST['cc_number']) ? prepare_input($_POST['cc_number']) : "";
		$cc_expires_month = isset($_POST['cc_expires_month']) ? prepare_input($_POST['cc_expires_month']) : "01";
		$cc_expires_year  = isset($_POST['cc_expires_year']) ? prepare_input($_POST['cc_expires_year']) : date("Y");
		$cc_cvv_code 	  = isset($_POST['cc_cvv_code']) ? prepare_input($_POST['cc_cvv_code']) : "";

		$objBookingSettings  = new ModulesSettings("booking");
		$paypal_email        = $objBookingSettings->GetSettings("paypal_email");
		$collect_credit_card = $objBookingSettings->GetSettings("online_collect_credit_card");
		$two_checkout_vendor = $objBookingSettings->GetSettings("two_checkout_vendor");
		$mode                = $objBookingSettings->GetSettings("mode");
		
		// prepare clients info 
		$sql="SELECT *
			  FROM ".TABLE_CLIENTS."
			  WHERE id = ".(int)$this->currentClientID;
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		$client_info = array();
		$client_info['first_name'] = isset($result[0]['first_name']) ? $result[0]['first_name'] : "";
		$client_info['last_name'] = isset($result[0]['last_name']) ? $result[0]['last_name'] : "";
		$client_info['address1'] = isset($result[0]['b_address']) ? $result[0]['b_address'] : "";
		$client_info['address2'] = isset($result[0]['b_address2']) ? $result[0]['b_address2'] : "";
		$client_info['city'] = isset($result[0]['b_city']) ? $result[0]['b_city'] : "";
		$client_info['state'] = isset($result[0]['b_state']) ? $result[0]['b_state'] : "";
		$client_info['zip'] = isset($result[0]['b_zipcode']) ? $result[0]['b_zipcode'] : "";
		$client_info['country'] = isset($result[0]['b_country']) ? $result[0]['b_country'] : "";
		$client_info['email'] = isset($result[0]['email']) ? $result[0]['email'] : "";
		
		// check if prepared booking exists and replace it		
		$sql="SELECT id, booking_number
			  FROM ".TABLE_BOOKINGS."
			  WHERE
					client_id = ".(int)$this->currentClientID." AND
					status = 0 AND  
					is_admin_reservation = ".(($this->selectedUser == "admin") ? "1" : "0")."
			  ORDER BY id DESC";	
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$booking_number = $result[0]['booking_number'];
		}else{
			///echo $sql.mysql_error();
			echo draw_important_message(_ORDER_ERROR, false);
			return false;
		}
		
		$order_price = $this->cartTotalSum;
		$order_price_wo_discount = $this->cartTotalSum;
		$order_price_wo_extras = $this->cartTotalSum;

        // prepare extras
		$extras_hidden = "";
		$extras_text = "";
		$extras_sub_total = "0";
		if(count($extras) > 0){			
			$extras_text_header = "<tr><td colspan='3' nowrap height='10px'></td></tr>
				  <tr><td colspan='3'><h4>"._EXTRAS."</h4></td></tr>
				  <tr><td colspan='3'>";				
				$extras_text_middle = "";
				foreach($extras as $key => $val){
					$extr = Extras::GetExtrasInfo($val);
					$extr_amount = isset($_POST['extras_amount_'.$val]) ? $_POST['extras_amount_'.$val] : "0";
					if($extr_amount){
						$extras_sub_total += ($extr["price"] / $this->currencyRate) * $extr_amount;
						$extras_text_middle .= "<tr><td nowrap>".$extr["name"]."&nbsp;</td>";
						$extras_text_middle .= "<td> : </td>";
						$extras_text_middle .= "<td> ".Currencies::PriceFormat($extr["price"] / $this->currencyRate)." x ".$extr_amount;										
						$extras_text_middle .= "<input type='hidden' name='extras[]' value='".$val."' />\n";
						$extras_text_middle .= "<input type='hidden' name='extras_amount_".$val."' value='".$extr_amount."' />\n";
						$extras_text_middle .= "</td></tr>";
					}
				}
			$extras_text_footer  = "<tr><td>"._EXTRAS_SUBTOTAL." </td><td> : </td><td> <b>".Currencies::PriceFormat($extras_sub_total)."</b></td></tr>";								  
			$extras_text_footer .= "</td></tr>";			

            if($extras_sub_total > 0){
				$extras_text = $extras_text_header.$extras_text_middle.$extras_text_footer;
				$order_price += $extras_sub_total;
				$order_price_wo_discount += $extras_sub_total;				
			}
		}


		// calculate discount			
		$discount_value = ($order_price * ($this->discountPercent / 100));
		$order_price -= $discount_value;

		// calculate VAT			
		$vat_cost = (round($order_price, 2) * ($this->vatPercent / 100));
		$cart_total_sum = round($order_price, 2) + $vat_cost;
		
		if($pre_payment == "first night"){
			$is_prepayment = true;			
			$cart_total_sum = $this->firstNightSum;
			$prepayment_text = _FIRST_NIGHT;
		}else if(($pre_payment != "") && (int)$pre_payment > 0 && (int)$pre_payment < 100){
			$is_prepayment = true;			
			$cart_total_sum = ($cart_total_sum * ($pre_payment / 100));
			$prepayment_text = $pre_payment."%";
		}else{
			$prepayment_text = "";
			$is_prepayment = false;
		}

		if($payment_method == "online"){
			echo "
				<form id='frmOnlineOrder' action='index.php?page=booking_payment' method='post'>
				<input type='hidden' name='task' value='place_order' />
				<input type='hidden' name='payment_method' value='".$payment_method."' />
				<input type='hidden' name='additional_info' value='".$additional_info."' />
				<input type='hidden' name='pre_payment' value='".$pre_payment."' />
				".draw_token_field(true)."
				".$extras_hidden."
				
					<table border='0' width='98%' align='center'>
						<tr><td width='15%'>"._BOOKING_DATE." </td><td width='2%'> : </td><td> ".format_datetime(date("Y-m-d H:i:s"), $this->fieldDateFormat)."</td></tr>						
						<tr><td>"._ROOMS." </td><td> : </td><td> ".(int)$this->roomsCount."</td></tr>
						<tr><td width='15%'>"._BOOKING_PRICE." </td><td width='2%'> : </td><td> <b>".Currencies::PriceFormat($order_price_wo_extras)."</b></td></tr>";

						if(count($extras) > 0) echo $extras_text;

						echo "<tr><td colspan='3' nowrap height='10px'></td></tr>";
						echo "<tr><td colspan='3'><h4>"._TOTAL."</h4></td></tr>";
						echo "<tr><td>"._VAT." (".$this->vatPercent."%) </td><td> : </td><td> ".Currencies::PriceFormat($vat_cost)."</td></tr>";
						echo (($discount_value != "") ? "<tr><td>"._DISCOUNT." </td><td> : </td><td> - ".Currencies::PriceFormat($discount_value)." (".$this->discountPercent."%)</td></tr>" : "");
						if($is_prepayment){
							echo "<tr><td>"._PAYMENT_SUM." </td><td> : </td><td> <b>".Currencies::PriceFormat($order_price + $vat_cost)."</b></td></tr>";
							echo "<tr><td>"._PRE_PAYMENT."</td><td> : </td> <td>".Currencies::PriceFormat($cart_total_sum)." (".$prepayment_text.")</td></tr>";
						}else{
							echo "<tr><td>"._PAYMENT_SUM." </td><td> : </td><td> <b>".Currencies::PriceFormat($cart_total_sum)."</b></td></tr>";
							echo "<tr><td>"._PRE_PAYMENT."</td><td> : </td> <td>"._FULL_PRICE."</td></tr>";
						}

						if($additional_info != ""){
							echo "<tr><td colspan='3' nowrap height='10px'></td></tr>
								  <tr><td colspan='3'><h4>"._ADDITIONAL_INFO."</h4>".$additional_info."</td></tr>";							
						}

						echo "<tr><td colspan='3' nowrap height='10px'></td></tr>";
						if($collect_credit_card == "yes"){
							echo "<tr><td colspan='3'>";
							echo "<table border='0' id='ccDetailsPanel'>
								<tr><td><h4>"._CREDIT_CARD."</h4></td></tr>
								<tr><td>"._CREDIT_CARD_TYPE.": </td>
									<td>&nbsp;</td>
									<td>
										<select name='cc_type'>
											<option value='Visa' ".(($cc_type == "Visa") ? "selected='selected'" : "").">Visa</option>
											<option value='MasterCard' ".(($cc_type == "MasterCard") ? "selected='selected'" : "").">MasterCard</option>
											<option value='American Express' ".(($cc_type == "American Express") ? "selected='selected'" : "").">American Express</option>
											<option value='Discover' ".(($cc_type == "Discover") ? "selected='selected'" : "").">Discover</option> 
										</select>
									</td>
								</tr>
								<tr>
									<td>"._CREDIT_CARD_NUMBER.": </td>
									<td></td>
									<td><input type='text' name='cc_number' size='20' maxlength='20' value='".$cc_number."' autocomplete='off' /></td>
								</tr>
								<tr><td>"._CREDIT_CARD_EXPIRES.": </td>
									<td></td>
									<td>
										".draw_months_select_box('cc_expires_month', $cc_expires_month, 'cc_month', false, false)."
										".draw_years_select_box('cc_expires_year', $cc_expires_year, 'cc_year', false)."
									</td>
								</tr>
								<tr><td>"._CVV_CODE.": </td>
									<td></td>
									<td><input type='text' name='cc_cvv_code' size='4' maxlength='4' value='".$cc_cvv_code."' autocomplete='off' /> <a href='javascript:appOpenPopup(\"html/cvv_description.html\")'>What is CVV [?]</a></td>
								</tr>
								</table>";					
							echo "</td></tr>";
							echo "<tr><td colspan='3' nowrap height='10px'></td></tr>";
						}
						echo "
						<tr>
							<td colspan='3'>
								<table width='99%' border='0'>
								<tr>
									<td width='120px' align='left'><input type='button' class='form_button' value='"._BUTTON_CANCEL."' name='btnCancel' onclick='javascript:appGoTo(\"page=booking_details\")' /></td>
									<td align='left'><input type='submit' class='form_button' value='"._PLACE_ORDER."' name='btnSubmit' /></td>
								</tr>
								</table>
							</td>
						</tr>	
					</table>
				</form>";				
		
		}else if($payment_method == "paypal"){							
		
			$base_url = get_base_url();
			$objBookingSettings = new ModulesSettings("booking");
			$paypal_email = $objBookingSettings->GetSettings("paypal_email");
			echo "<table border='0' width='98%' align='center'>
					<tr><td width='15%'>"._BOOKING_DATE." </td><td width='2%'> : </td><td> ".format_datetime(date("Y-m-d H:i:s"), $this->fieldDateFormat)."</td></tr>
					<tr><td>"._ROOMS." </td><td> : </td><td> ".(int)$this->roomsCount."</td></tr>
					<tr><td width='15%'>"._BOOKING_PRICE." </td><td width='2%'> : </td><td> <b>".Currencies::PriceFormat($order_price_wo_extras)."</b></td></tr>";
					
					if(count($extras) > 0) echo $extras_text;

			echo "<tr><td colspan='3' nowrap height='10px'></td></tr>";
			echo "<tr><td colspan='3'><h4>"._TOTAL."</h4></td></tr>";
			echo "<tr><td>"._VAT." (".$this->vatPercent."%) </td><td> : </td><td> ".Currencies::PriceFormat($vat_cost)."</td></tr>";
			echo (($discount_value != "") ? "<tr><td>"._DISCOUNT." </td><td> : </td><td> - ".Currencies::PriceFormat($discount_value)." (".$this->discountPercent."%)</td></tr>" : "");
					if($is_prepayment){
						echo "<tr><td>"._PAYMENT_SUM." </td><td> : </td><td> <b>".Currencies::PriceFormat($order_price + $vat_cost)."</b></td></tr>";
						echo "<tr><td>"._PRE_PAYMENT."</td><td> : </td> <td>".Currencies::PriceFormat($cart_total_sum)." (".$prepayment_text.")</td></tr>";
					}else{
						echo "<tr><td>"._PAYMENT_SUM." </td><td> : </td><td> <b>".Currencies::PriceFormat($cart_total_sum)."</b></td></tr>";
						echo "<tr><td>"._PRE_PAYMENT."</td><td> : </td> <td>"._FULL_PRICE."</td></tr>";
					}
			echo "<tr><td colspan='4'>&nbsp;</td></tr>
					<tr><td colspan='4'>
							".(($additional_info != "") ? "<h4>"._ADDITIONAL_INFO."</h4><p>".$additional_info."</p>" : "")."							
						</td>
					</tr>
					<tr><td colspan='3'>";
					if($mode == "TEST MODE"){
	 				   echo "<br /><form action='index.php?page=booking_notify_paypal' method='post' name='payform'>
									<input type='hidden' name='txn_id' value='TEST_".random_string(8)."' />
									<input type='hidden' name='payer_status' value='verified' />
									<input type='hidden' name='mc_gross' value='".round($cart_total_sum, 2)."' />
									<input type='hidden' name='custom' value='".$booking_number."' />
									".draw_token_field(true)."									
									"._PAYPAL_NOTICE."<br /><br />
									<table width='99%' border='0'>
									<tr>
										<td width='120px' align='left'><input type='button' class='form_button' value='"._BUTTON_CANCEL."' name='btnCancel' onclick='javascript:appGoTo(\"page=booking_details\")' /></td>
										<td align='left'><input type='image' style='border:0px' src='images/ppc_icons/btn_pp_paynow.gif' title='"._BOOK_NOW."' value='Go To Payment' name='btnSubmit' /></td>
									</tr>
									</table>
								</form>";
					}else{
						echo "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' name='payform'>
								<input type='hidden' name='business' value='".$paypal_email."'>";
                                if($this->paypal_form_type == "multiple" && !$is_prepayment && !$discount_value){
                                    echo "<input type='hidden' name='cmd' value='_cart'>\n";
                                    echo "<input type='hidden' name='upload' value='1'>\n";
                                    echo $this->paypal_form_fields;
									if($extras_sub_total > 0){
										$this->paypal_form_fields_count++;					
										echo "<input type='hidden' name='item_name_".$this->paypal_form_fields_count."' value='"._EXTRAS."'>\n";
										echo "<input type='hidden' name='quantity_".$this->paypal_form_fields_count."' value='1'>\n";
										echo "<input type='hidden' name='amount_".$this->paypal_form_fields_count."' value='".number_format($extras_sub_total, "2", ".", ",")."'>\n";
									}
									if($vat_cost > 0){
										$this->paypal_form_fields_count++;					
										echo "<input type='hidden' name='item_name_".$this->paypal_form_fields_count."' value='"._VAT."'>\n";
										echo "<input type='hidden' name='quantity_".$this->paypal_form_fields_count."' value='1'>\n";
										echo "<input type='hidden' name='amount_".$this->paypal_form_fields_count."' value='".number_format($vat_cost, "2", ".", ",")."'>\n";
									}									
                                }else{
                                    echo "<input type='hidden' name='cmd' value='_xclick'>";
                                    echo "<input type='hidden' name='item_name' value='Rooms Reservation'>";
                                    echo "<input type='hidden' name='item_number' value='002'>";
                                    echo "<input type='hidden' name='amount' value='".round($cart_total_sum, 2)."'>";                                
                                }                        
						  echo "<input type='hidden' name='custom' value='".$booking_number."'>
								<input type='hidden' name='lc' value='US'>
								<input type='hidden' name='currency_code' value='".$this->currencyCode."'>
								<input type='hidden' name='cn' value=''>
								<input type='hidden' name='no_shipping' value='1'>
								<input type='hidden' name='rm' value='1'>								

								<input type='hidden' name='address1' value='".$client_info['address1']."'>
								<input type='hidden' name='address2' value='".$client_info['address2']."'>
								<input type='hidden' name='city' value='".$client_info['city']."'>
								<input type='hidden' name='country' value='".$client_info['country']."'>
								<input type='hidden' name='first_name' value='".$client_info['first_name']."'>
								<input type='hidden' name='last_name' value='".$client_info['last_name']."'>						
								<input type='hidden' name='state' value='".$client_info['state']."'>
								<input type='hidden' name='zip' value='".$client_info['zip']."'>
								<input type='hidden' name='email' value='".$client_info['email']."'>
																
								<input type='hidden' name='notify' value='".$base_url."index.php?page=booking_notify_paypal'>
								<input type='hidden' name='return' value='".$base_url."index.php?page=booking_return'>
								<input type='hidden' name='cancel_return' value='".$base_url."index.php?page=booking_cancel'>
								<input type='hidden' name='bn' value='PP-BuyNowBF'>
								"._PAYPAL_NOTICE."<br /><br />
								<table width='99%' border='0'>
								<tr>
									<td align='left' width='120px'><input type='button' class='form_button' value='"._BUTTON_CANCEL."' name='btnCancel' onclick='javascript:appGoTo(\"page=booking_details\")' /></td>
									<td align='left'><input type='image' style='border:0px' src='images/ppc_icons/btn_pp_paynow.gif' title='"._BOOK_NOW."' value='Go To Payment' name='btnSubmit' /></td>
								</tr>
								</table>
							</form>";						
					}
						echo "</td>
					</tr>	
				</table>";				
		
		}else if($payment_method == "2co"){				

			$base_url = get_base_url();
			$objBookingSettings = new ModulesSettings("booking");
			$two_checkout_vendor = $objBookingSettings->GetSettings("two_checkout_vendor");
			
			echo "<table border='0' width='98%' align='center'>
					<tr><td width='15%'>"._BOOKING_DATE." </td><td width='2%'> : </td><td> ".format_datetime(date("Y-m-d H:i:s"), $this->fieldDateFormat)."</td></tr>
					<tr><td>"._ROOMS." </td><td> : </td><td> ".(int)$this->roomsCount."</td></tr>
					<tr><td width='15%'>"._BOOKING_PRICE." </td><td width='2%'> : </td><td> <b>".Currencies::PriceFormat($order_price_wo_extras)."</b></td></tr>";

					if(count($extras) > 0) echo $extras_text;

			echo "<tr><td colspan='3' nowrap height='10px'></td></tr>";
			echo "<tr><td colspan='3'><h4>"._TOTAL."</h4></td></tr>";
			echo (($discount_value != "") ? "<tr><td>"._DISCOUNT." </td><td> : </td><td> - ".Currencies::PriceFormat($discount_value)." (".$this->discountPercent."%)</td></tr>" : "")."
					<tr><td>"._VAT." (".$this->vatPercent."%) </td><td> : </td><td> ".Currencies::PriceFormat($vat_cost)."</td></tr>";
					if($is_prepayment){
						echo "<tr><td>"._PAYMENT_SUM." </td><td> : </td><td> <b>".Currencies::PriceFormat($order_price + $vat_cost)."</b></td></tr>";
						echo "<tr><td>"._PRE_PAYMENT."</td><td> : </td> <td>".Currencies::PriceFormat($cart_total_sum)." (".$prepayment_text.")</td></tr>";
					}else{
						echo "<tr><td>"._PAYMENT_SUM." </td><td> : </td><td> <b>".Currencies::PriceFormat($cart_total_sum)."</b></td></tr>";
						echo "<tr><td>"._PRE_PAYMENT."</td><td> : </td> <td>"._FULL_PRICE."</td></tr>";
					}
			echo "<tr><td colspan='4'>&nbsp;</td></tr>
					<tr><td colspan='4'>
							".(($additional_info != "") ? "<h4>"._ADDITIONAL_INFO."</h4><p>".$additional_info."</p>" : "")."
						</td>
					</tr>
					<tr><td colspan='3'>";
						if($mode == "TEST MODE"){
						   echo "<br /><form action='index.php?page=booking_notify_2co' method='post'>
								<input type='hidden' name='order_number' value='TEST_".random_string(8)."'>
								<input type='hidden' name='pay_method' value='2CO'>
								<input type='hidden' name='total' value='".round($cart_total_sum, 2)."'>
								<input type='hidden' name='custom' value='".$booking_number."'>
								".draw_token_field(true)."								
								"._2CO_NOTICE."<br /><br />
								<table width='99%' border='0'>
								<tr>
									<td width='120px' align='left'><input type='button' class='form_button' value='"._BUTTON_CANCEL."' name='btnCancel' onclick='javascript:appGoTo(\"page=booking_details\")' /></td>
									<td align='left'><input type='image' style='border:0px' src='images/ppc_icons/btn_2co_buynow.gif' title='"._BOOK_NOW."' value='Go To Payment' name='btnSubmit' /></td>
								</tr>
								</table>
							</form>";							
						}else{						
							echo "<form action='https://www.2checkout.com/checkout/purchase' method='post'>
								<input type='hidden' name='sid' value='".$two_checkout_vendor."'>
								<input type='hidden' name='cart_order_id' value='Rooms Reservation'>
								<input type='hidden' name='total' value='".round($cart_total_sum * $this->currencyRate, 2)."'>
								
								<input type='hidden' name='x_Receipt_Link_URL' value='".$base_url."index.php?page=booking_notify_2co'>
								<input type='hidden' name='return_url' value='".$base_url."index.php?page=booking_return'>
								<input type='hidden' name='tco_currency' value='".$this->currencyCode."'>
								<input type='hidden' name='custom' value='".$booking_number."'>
	
								"._2CO_NOTICE."<br /><br />
								<table width='99%' border='0'>
								<tr>
									<td width='120px' align='left'><input type='button' class='form_button' value='"._BUTTON_CANCEL."' name='btnCancel' onclick='javascript:appGoTo(\"page=booking_details\")' /></td>
									<td align='left'><input type='image' style='border:0px' src='images/ppc_icons/btn_2co_buynow.gif' title='"._BOOK_NOW."' value='Go To Payment' name='btnSubmit' /></td>
								</tr>
								</table>
							</form>";						
						}
						echo "</td>
					</tr>	
				</table>";				
		}
			
	}

	/**
	 * Place order
	 * 		@param $payment_type
	 */
	public function PlaceBooking($additional_info = "", $cc_params = array())
	{
		global $objLogin;
		$additional_info = substr_by_word($additional_info, 1024);
		
        if(SITE_MODE == "demo"){
           $this->message = draw_important_message(_OPERATION_BLOCKED, false);
		   return false;
        }
		
		// check if prepared booking exists
		$sql = "SELECT id, booking_number
				FROM ".TABLE_BOOKINGS."
				WHERE client_id = ".(int)$this->currentClientID." AND
					  is_admin_reservation = ".(($this->selectedUser == "admin") ? "1" : "0")." AND
					  status = 0
				ORDER BY id DESC";

		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$booking_number = $result[0]['booking_number'];
			
			$sql = "UPDATE ".TABLE_BOOKINGS."
					SET
						status_changed = '".date("Y-m-d H:i:s")."',
						additional_info = '".$additional_info."',
						cc_type = '".$cc_params['cc_type']."',
						cc_number = AES_ENCRYPT('".$cc_params['cc_number']."', '".PASSWORDS_ENCRYPT_KEY."'),
						cc_expires_month = '".$cc_params['cc_expires_month']."',
						cc_expires_year = '".$cc_params['cc_expires_year']."',
						cc_cvv_code = '".$cc_params['cc_cvv_code']."',
						status = '1'
					WHERE booking_number = '".$booking_number."'";
			database_void_query($sql);

			// update client bookings/rooms amount
			$sql = "UPDATE ".TABLE_CLIENTS." SET 
						orders_count = orders_count + 1,
						rooms_count = rooms_count + ".$this->roomsCount."
					WHERE id = ".(int)$this->currentClientID;
			database_void_query($sql);					
			if(!$objLogin->IsLoggedIn()){
				// clear selected user ID for non-registered visitors
				unset($_SESSION['sel_current_client_id']);					
			}

			if($this->SendOrderEmail($booking_number, "accepted", $this->currentClientID)){
				$this->message = draw_success_message(_ORDER_PLACED_MSG, false);
			}else{
				$this->message = draw_success_message(_ORDER_SEND_MAIL_ERROR, false);					
			}
		}else{
			$this->message = draw_success_message(_ORDER_SEND_MAIL_ERROR, false);					
		}

		$this->EmptyCart();		
	}	

	/**
	 * Makes reservation
	 * 		@param $payment_type
	 * 		@param $additional_info
	 */
	public function DoReservation($payment_type = "", $additional_info = "", $extras = array(), $pre_payment = "")
	{
		global $objLogin;
		
        if(SITE_MODE == "demo"){
           $this->error = draw_important_message(_OPERATION_BLOCKED, false);
		   return false;
        }
		
		$booking_accepted = false;
		$booking_number = strtoupper(random_string(10));
		$additional_info = substr_by_word($additional_info, 1024);

		$order_price = $this->cartTotalSum;

		// calculate extras
		$extras_sub_total = "0";
		$extras_info = array();
		if(count($extras) > 0){				
			foreach($extras as $key => $val){
				$extr = Extras::GetExtrasInfo($val);
				$extr_amount = isset($_POST['extras_amount_'.$val]) ? $_POST['extras_amount_'.$val] : "0";
				if($extr_amount){
					$extras_sub_total += $extr["price"] * $extr_amount;
					$extras_info[$val] = $extr_amount;
				}
			}
			$order_price += $extras_sub_total;
		}

		// calculate discount			
		$discount_value = ($order_price * ($this->discountPercent / 100));
		$order_price -= $discount_value;

		// calculate VAT			
		$vat_cost = (round($order_price, 2) * ($this->vatPercent / 100));
		$cart_total_sum = $order_price + $vat_cost;

		if($pre_payment == "first night"){
			$cart_total_sum = $this->firstNightSum;	
		}else if(($pre_payment != "") && (int)$pre_payment > 0 && (int)$pre_payment < 100){
			$cart_total_sum = ($cart_total_sum * ($pre_payment / 100));	
		}			

		if($this->cartItems > 0){
            // add order to database
			if($payment_type == "online" || $payment_type == "paypal" || $payment_type == "2co")
			{				
				if($payment_type == "paypal"){
					$payed_by = "1";
					$status = "0";
				}else if($payment_type == "2co"){
					$payed_by = "3";
					$status = "0";
				}else{
					$payed_by = "0";
					$status = "0";
				}				

				// check if prepared booking exists and replace it
				$sql = "SELECT id, booking_number
						FROM ".TABLE_BOOKINGS."
						WHERE client_id = ".(int)$this->currentClientID." AND
							  is_admin_reservation = ".(($this->selectedUser == "admin") ? "1" : "0")." AND
							  status = 0
						ORDER BY id DESC";
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($result[1] > 0){
					$booking_number = $result[0]['booking_number'];
					// booking exists - replace it with new					
					$sql = "DELETE FROM ".TABLE_BOOKINGS_ROOMS." WHERE booking_number = '".$booking_number."'";		
					if(!database_void_query($sql)){ /* echo "error!"; */ }
					
					$sql = "UPDATE ".TABLE_BOOKINGS." SET ";
					$sql_end = " WHERE booking_number = '".$booking_number."'";
				}else{
					$booking_number = strtoupper(random_string(10));
					$sql = "INSERT INTO ".TABLE_BOOKINGS." SET booking_number = '".$booking_number."',";
					$sql_end = "";
				}

				$sql .= "booking_description = '"._ROOMS_RESERVATION."',
						order_price = ".$order_price.",
						pre_payment = '".$pre_payment."',
						discount_fee = ".$discount_value.",
						vat_fee = ".$vat_cost.",
						vat_percent = ".$this->vatPercent.",
						payment_sum = ".$cart_total_sum.",
						currency = '".$this->currencyCode."',
						rooms_amount = ".(int)$this->roomsCount.",						
						client_id = ".(int)$this->currentClientID.",
						is_admin_reservation = ".(($this->selectedUser == "admin") ? "1" : "0").",
						transaction_number = '',
						created_date = '".date("Y-m-d H:i:s")."',
						payment_type = ".$payed_by.",
						coupon_number = '',
						discount_campaign_id = ".(int)$this->discountCampaignID.",
						additional_info = '".$additional_info."',
						extras = '".serialize($extras_info)."',
						cc_type = '', 
						cc_number = '', 
						cc_expires_month = '', 
						cc_expires_year = '', 
						cc_cvv_code = '',
						status = ".(int)$status;
				$sql .= $sql_end;
				
                // handle booking details
				if(database_void_query($sql)){
					$sql = "INSERT INTO ".TABLE_BOOKINGS_ROOMS."
								(id, booking_number, room_id, checkin, checkout, rooms, price)
							VALUES ";
					$items_count = 0;
					foreach($this->arrReservation as $key => $val){					
						$sql .= ($items_count++ > 0) ? "," : "";
						$sql .= "(NULL, '".$booking_number."', ".(int)$key.", '".$val['from_date']."', '".$val['to_date']."', ".(int)$val['rooms'].", ".($val['price'] / $this->currencyRate).")";
					}
					if(database_void_query($sql)){
						$booking_accepted = true;						
					}else{
						///echo mysql_error();
						$this->error = draw_important_message(_ORDER_ERROR, false);
					}
				}else{
					///echo mysql_error();
					$this->error = draw_important_message(_ORDER_ERROR, false);
				}
			}else{
				///echo mysql_error();
				$this->error = draw_important_message(_ORDER_ERROR, false);
			}
		}else{
			$this->error = draw_message(_RESERVATION_CART_IS_EMPTY_ALERT, false, true);
		}
		
		return $booking_accepted;		
	}	

	/**
	 * Sends order email
	 * 		@param booking_number
	 * 		@param $order_type
	 * 		@param $client_id
	 */
	public function SendOrderEmail($booking_number, $order_type = "accepted", $client_id = "")
	{		
		global $objSettings;
		
		$lang = Application::Get("lang");
	
		// send email to client
		$sql = "SELECT
			".TABLE_BOOKINGS.".id,
			".TABLE_BOOKINGS.".booking_number,
			".TABLE_BOOKINGS.".booking_description,
			".TABLE_BOOKINGS.".order_price,
			".TABLE_BOOKINGS.".discount_fee,
			".TABLE_BOOKINGS.".vat_fee,
			".TABLE_BOOKINGS.".vat_percent,
			".TABLE_BOOKINGS.".payment_sum,
			".TABLE_BOOKINGS.".currency,
			".TABLE_BOOKINGS.".rooms_amount,
			".TABLE_BOOKINGS.".client_id,
			".TABLE_BOOKINGS.".transaction_number,
			".TABLE_BOOKINGS.".created_date,
			".TABLE_BOOKINGS.".payment_date,
			".TABLE_BOOKINGS.".payment_type,
			".TABLE_BOOKINGS.".status,
			".TABLE_BOOKINGS.".email_sent,
			".TABLE_BOOKINGS.".additional_info,
			".TABLE_BOOKINGS.".extras,
			CASE
				WHEN ".TABLE_BOOKINGS.".payment_type = 0 THEN '"._ONLINE_ORDER."'
				WHEN ".TABLE_BOOKINGS.".payment_type = 1 THEN '"._PAYPAL."'
				WHEN ".TABLE_BOOKINGS.".payment_type = 2 THEN '"._CREDIT_CARD."'
				WHEN ".TABLE_BOOKINGS.".payment_type = 3 THEN '2CO'
				ELSE '"._UNKNOWN."'
			END as m_payment_type,
			".TABLE_CLIENTS.".first_name,
			".TABLE_CLIENTS.".last_name,
			".TABLE_CLIENTS.".user_name as client_name,
			".TABLE_CLIENTS.".email,
			".TABLE_CLIENTS.".b_address,
			".TABLE_CLIENTS.".b_address_2,
			".TABLE_CLIENTS.".b_city,
			".TABLE_CLIENTS.".b_state,
			".TABLE_CLIENTS.".b_country,
			".TABLE_CLIENTS.".b_zipcode,
			".TABLE_CLIENTS.".phone,
			".TABLE_CURRENCIES.".symbol,
			".TABLE_CURRENCIES.".symbol_placement,
			".TABLE_CAMPAIGNS.".campaign_name,
			".TABLE_CAMPAIGNS.".discount_percent 
		FROM ".TABLE_BOOKINGS."
			INNER JOIN ".TABLE_CURRENCIES." ON ".TABLE_BOOKINGS.".currency = ".TABLE_CURRENCIES.".code
			LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".TABLE_BOOKINGS.".client_id = ".TABLE_CLIENTS.".id
			LEFT OUTER JOIN ".TABLE_CAMPAIGNS." ON ".TABLE_BOOKINGS.".discount_campaign_id = ".TABLE_CAMPAIGNS.".id
		WHERE
			".TABLE_BOOKINGS.".client_id = ".$client_id." AND
			".TABLE_BOOKINGS.".booking_number = '".$booking_number."'";
		
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){					
			
			$recipient = $result[0]['email'];
			$first_name = $result[0]['first_name'];
			$last_name = $result[0]['last_name'];
			$email_sent = $result[0]['email_sent'];
			
			$personal_information  = "<b>"._PERSONAL_INFORMATION.":</b>";
			$personal_information .= "<br />-----------------------------<br />";
			$personal_information .= _FIRST_NAME." : ".$result[0]['first_name']."<br />";
			$personal_information .= _LAST_NAME." : ".$result[0]['last_name']."<br />";
			$personal_information .= _EMAIL_ADDRESS." : ".$result[0]['email']."<br />";

			$billing_information  = "<b>"._BILLING_DETAILS.":</b>";
			$billing_information .= "<br />-----------------------------<br />";
			$billing_information .= _ADDRESS." : ".$result[0]['b_address']." ".$result[0]['b_address_2']."<br />";
			$billing_information .= _CITY." : ".$result[0]['b_city']."<br />";
			$billing_information .= _STATE." : ".$result[0]['b_state']."<br />";
			$billing_information .= _COUNTRY." : ".$result[0]['b_country']."<br />";
			$billing_information .= _ZIP_CODE." : ".$result[0]['b_zipcode']."<br />";
			$billing_information .= _PHONE." : ".$result[0]['phone']."<br />";
	
			$booking_details  = _BOOKING_DESCRIPTION.": ".$result[0]['booking_description']."<br />";
			$booking_details .= _CURRENCY.": ".$result[0]['currency']."<br />";
			$booking_details .= _CREATED_DATE.": ".format_datetime($result[0]['created_date'], $this->fieldDateFormat." H:i:s")."<br />";
			$booking_details .= _PAYMENT_DATE.": ".format_datetime($result[0]['payment_date'], $this->fieldDateFormat." H:i:s")."<br />";
			$booking_details .= _PAYMENT_METHOD.": ".$result[0]['m_payment_type']."<br />";
			$booking_details .= _ROOMS.": ".$result[0]['rooms_amount']."<br />";
			$booking_details .= (($result[0]['campaign_name'] != "") ? _DISCOUNT.": - ".Currencies::PriceFormat($result[0]['discount_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['discount_percent']."% - ".$result[0]['campaign_name'].")" : "")."<br />";
			$booking_details .= _BOOKING_PRICE.(($result[0]['campaign_name'] != "") ? " ("._AFTER_DISCOUNT.")" : "").": ".Currencies::PriceFormat($result[0]['order_price'], $result[0]['symbol'], $result[0]['symbol_placement'])."<br />";
			$booking_details .= _VAT.": ".Currencies::PriceFormat($result[0]['vat_fee'], $result[0]['symbol'], $result[0]['symbol_placement'])." (".$result[0]['vat_percent']."%)<br />";
			$booking_details .= _PAYMENT_SUM.": ".Currencies::PriceFormat($result[0]['payment_sum'], $result[0]['symbol'], $result[0]['symbol_placement'])."<br />";
			if($result[0]['additional_info'] != "") $booking_details .= _ADDITIONAL_INFO.": ".nl2br($result[0]['additional_info'])."<br />";
			$booking_details .= "<br />";

			// display list of extras in order
			// -----------------------------------------------------------------------------
			$extras = unserialize($result[0]['extras']);
			if(is_array($extras) && count($extras) > 0){				
				$booking_details .= Extras::GetExtrasList($extras, $result[0]['currency'], "email");
			}

			// display list of rooms in order
			// -----------------------------------------------------------------------------
			$booking_details .= "<b>"._RESERVATION_DETAILS.":</b>";
			$booking_details .= "<br />-----------------------------<br />";
			$sql = "SELECT
						".TABLE_BOOKINGS_ROOMS.".booking_number,
						".TABLE_BOOKINGS_ROOMS.".rooms,
						".TABLE_BOOKINGS_ROOMS.".checkin,
						".TABLE_BOOKINGS_ROOMS.".checkout,
						".TABLE_ROOMS.".max_adults,
						".TABLE_ROOMS.".max_children,
						".TABLE_ROOMS_DESCRIPTION.".room_type,
						CASE
							WHEN ".TABLE_CURRENCIES.".symbol_placement  = 'left' THEN
								CONCAT(".TABLE_CURRENCIES.".symbol, ".TABLE_BOOKINGS_ROOMS.".price)
							ELSE
								CONCAT(".TABLE_BOOKINGS_ROOMS.".price, ".TABLE_CURRENCIES.".symbol)
						END as tp_w_currency
					FROM ".TABLE_BOOKINGS."
						INNER JOIN ".TABLE_BOOKINGS_ROOMS." ON ".TABLE_BOOKINGS.".booking_number = ".TABLE_BOOKINGS_ROOMS.".booking_number
						INNER JOIN ".TABLE_ROOMS." ON ".TABLE_BOOKINGS_ROOMS.".room_id = ".TABLE_ROOMS.".id
						INNER JOIN ".TABLE_ROOMS_DESCRIPTION." ON ".TABLE_ROOMS.".id = ".TABLE_ROOMS_DESCRIPTION.".room_id 
						LEFT OUTER JOIN ".TABLE_CURRENCIES." ON ".TABLE_BOOKINGS.".currency = ".TABLE_CURRENCIES.".code
						LEFT OUTER JOIN ".TABLE_CLIENTS." ON ".TABLE_BOOKINGS.".client_id = ".TABLE_CLIENTS.".id
					WHERE
						".TABLE_ROOMS_DESCRIPTION.".language_id = '".$this->lang."' AND 
						".TABLE_BOOKINGS.".booking_number = '".$result[0]['booking_number']."' ";
	
			$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS, FETCH_ASSOC);
	        //echo "----------".mysql_error();
			if($result[1] > 0){
				$booking_details .= "<table style='border:1px' cellspacing='2'>";
				$booking_details .= "<tr>";
				$booking_details .= "<th align='center'>#</th>";
				$booking_details .= "<th align='left'>"._ROOM_TYPE."</th>";
				$booking_details .= "<th align='left'>"._CHECK_IN."</th>";
				$booking_details .= "<th align='left'>"._CHECK_OUT."</th>";
				$booking_details .= "<th align='center'>"._ROOMS."</th>";
				$booking_details .= "<th align='center'>"._ADULTS."</th>";
				$booking_details .= "<th align='center'>"._CHILDREN."</th>";
				$booking_details .= "<th align='right'>"._PRICE."</th>";
				$booking_details .= "</tr>";
				for($i=0; $i < $result[1]; $i++){
					$booking_details .= "<tr>";
					$booking_details .= "<td align='center' width='40px'>".($i+1).".</td>";
					$booking_details .= "<td align='left'>".$result[0][$i]['room_type']."</td>";
					$booking_details .= "<td align='left'>".format_datetime($result[0][$i]['checkin'], $this->fieldDateFormat)."</td>";
					$booking_details .= "<td align='left'>".format_datetime($result[0][$i]['checkout'], $this->fieldDateFormat)."</td>";
					$booking_details .= "<td align='center'>".$result[0][$i]['rooms']."</td>";
					$booking_details .= "<td align='center'>".$result[0][$i]['max_adults']."</td>";
					$booking_details .= "<td align='center'>".$result[0][$i]['max_children']."</td>";
					$booking_details .= "<td align='right'>".$result[0][$i]['tp_w_currency']."</td>";
					$booking_details .= "</tr>";	
				}
				$booking_details .= "</table>";			
			}
			$objBookingSettings = new ModulesSettings("booking");
			$send_order_copy_to_admin =  $objBookingSettings->GetSettings("send_order_copy_to_admin");
			////////////////////////////////////////////////////////////
			$sender = $objSettings->GetParameter("admin_email");
			///$recipient = $result[0]['email'];

			$objEmailTemplates = new EmailTemplates();
			if($order_type == "completed"){
				
				// exit if email was already sent				
				if($email_sent == "1") return true;				

				$email_template = $objEmailTemplates->GetTemplate("order_paid", $lang);
				$subject 	 	= $email_template["template_subject"];
				$body_middle 	= $email_template["template_content"];
			}else{
				$email_template = $objEmailTemplates->GetTemplate("order_accepted_online", $lang);
				$subject 	 	= $email_template["template_subject"];
				$body_middle 	= $email_template["template_content"];
			}
			
			$body_middle = str_replace("{FIRST NAME}", $first_name, $body_middle);
			$body_middle = str_replace("{LAST NAME}", $last_name, $body_middle);
			$body_middle = str_replace("{BOOKING NUMBER}", "#".$booking_number, $body_middle);
			$body_middle = str_replace("{BOOKING DETAILS}", $booking_details, $body_middle);
			$body_middle = str_replace("{PERSONAL INFORMATION}", $personal_information, $body_middle);
			$body_middle = str_replace("{BILLING INFORMATION}", $billing_information, $body_middle);

			$body   = "<div style='direction:".Application::Get("lang_dir")."'>";
			$body  .= $body_middle;
			$body  .= "</div>";
			
			$text_version = strip_tags($body);
			$html_version = nl2br($body);

			if($this->selectedUser == "client"){
				$objEmail = new Email($recipient, $sender, $subject);                     
				$objEmail->textOnly = false;
				$objEmail->content = $html_version;
				$objEmail->Send();				
			}

			if($send_order_copy_to_admin == "yes"){
				$objEmail = new Email($sender, $sender, $subject." (admin copy)");                     
				$objEmail->textOnly = false;
				$objEmail->content = $html_version;
				$objEmail->Send();
			}

			if($order_type == "completed" && !$email_sent){
				// exit if email was already sent
				$sql = "UPDATE ".TABLE_BOOKINGS." SET email_sent = 1 WHERE booking_number = '".$booking_number."'";
				database_void_query($sql);					
			}			
			////////////////////////////////////////////////////////////
			return true;
		}
		return false;
	}
	
	/**
	 * Returns VAT percent
	 */
	private function GetVatPercent()
	{
		$sql="SELECT
				cl.*,
				count.name as country_name,
				count.vat_value
		  	  FROM ".TABLE_CLIENTS." cl
				INNER JOIN ".TABLE_COUNTRIES." count ON cl.b_country = count.abbrv
			  WHERE cl.id = ".(int)$this->currentClientID;
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$vat_percent = isset($result[0]['vat_value']) ? $result[0]['vat_value'] : "0";
		}else{
			$objBookingSettings = new ModulesSettings("booking");
			$vat_percent =  $objBookingSettings->GetSettings("vat_value");
		}
		
		return $vat_percent;		
	}	

}