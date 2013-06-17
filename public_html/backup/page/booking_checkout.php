<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(Modules::IsModuleInstalled("booking")){
	$objBookingSettings = new ModulesSettings("booking");
	if($objBookingSettings->GetSettings("is_active") == "yes"){

		$m   = isset($_REQUEST['m']) ? prepare_input($_REQUEST['m']) : "";
		$act = isset($_POST['act']) ? prepare_input($_POST['act']) : "";
		$payment_method = isset($_POST['payment_method']) ? prepare_input($_POST['payment_method']) : ""; 
		$msg = "";		

		draw_content_start();
		draw_reservation_bar("reservation");
		
		// test mode alert
		if(Modules::IsModuleInstalled("booking")){
			$objBookingSettings = new ModulesSettings("booking");
			if($objBookingSettings->GetSettings("mode") == "TEST MODE"){
				draw_important_message(_TEST_MODE_ALERT_SHORT);
			}        
		}
		
		if($m == "1"){
			draw_success_message(_ACCOUNT_WAS_CREATED);			
		}else if($m == "2"){
			draw_success_message(_ACCOUNT_WAS_UPDATED);			
		}

		if($msg != "") echo $msg;	
		
		$objReservation->ShowCheckoutInfo();
		draw_content_end();
    }
}
?>	