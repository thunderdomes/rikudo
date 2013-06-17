<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(Modules::IsModuleInstalled("booking")){
	$objBookingSettings = new ModulesSettings("booking");
	$is_active = $objBookingSettings->GetSettings("is_active");
	$collect_credit_card = $objBookingSettings->GetSettings("online_collect_credit_card");

	if($is_active == "yes"){	
	
		if($payment_method == "paypal"){
			$title_desc = _PAYPAL_ORDER;
		}else if($payment_method == "2co"){
			$title_desc = _2CO_ORDER;
		}else{			
			$title_desc = _ONLINE_ORDER;
		}
				
		draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>"",$title_desc=>"")));
		
		draw_content_start();
		draw_reservation_bar("payment");

		// test mode alert
		if(Modules::IsModuleInstalled("booking")){
			$objBookingSettings = new ModulesSettings("booking");
			if($objBookingSettings->GetSettings("mode") == "TEST MODE"){
				draw_important_message(_TEST_MODE_ALERT_SHORT);
			}        
		}
		
		if($task == "do_booking"){
			$result = $objReservation->DoReservation($payment_method, $additional_info, $extras, $pre_payment);			
			echo $objReservation->error;
			if($result == true){
				$objReservation->DrawReservation($payment_method, $additional_info, $extras, $pre_payment);
			}
		}else if($task == "place_order"){
			if(@$objLogin->IsLoggedInAsAdmin()){
				// if admin makes this reservation
				if($cc_params['cc_number'] != ""){
					$result = check_credit_card($cc_params);
				}else{
					$result = "";
					$cc_params['cc_type'] 	       = "";
					$cc_params['cc_number'] 	   = "";
					$cc_params['cc_expires_month'] = "";
					$cc_params['cc_expires_year']  = "";
					$cc_params['cc_cvv_code']      = "";					
				}
				$place_booking = ($result != "") ? false : true;
			}else{
				$result = check_credit_card($cc_params);
				$place_booking = ($collect_credit_card == "yes" && $result != "") ? false : true;
			}
			if($place_booking){
				$objReservation->PlaceBooking($additional_info, $cc_params);	
				echo $objReservation->message."<br />";					
			}else{					
				draw_important_message($result);
				$objReservation->DrawReservation($payment_method, $additional_info, $extras, $pre_payment);
			}
		}else{
			draw_important_message(_WRONG_PARAMETER_PASSED);
		}
		draw_content_end();		
	}else{
		draw_important_message(_NOT_AUTHORIZED);
	}	
}else{
    draw_important_message(_NOT_AUTHORIZED);
}
?>