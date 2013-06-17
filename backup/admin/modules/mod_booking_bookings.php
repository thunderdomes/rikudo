<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin() && Modules::IsModuleInstalled("booking")){

	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$title_action = $action;
	$operation_field = MicroGrid::GetParameter('operation_field');
	$rid    	= MicroGrid::GetParameter('rid');

	$booking_status  = MicroGrid::GetParameter('status', false);
	$booking_number  = MicroGrid::GetParameter('booking_number', false);
	$client_id       = MicroGrid::GetParameter('client_id', false);

	$mode   	= "view";
	$msg 		= "";
	
	$objBookings = new Bookings();
	
	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objBookings->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objBookings->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objBookings->UpdateRecord($rid)){
			if($booking_status == "2"){
				$objBookings->UpdatePaymentDate($rid);
				// send email to client
				$objReservation = new Reservation();
				$objReservation->SendOrderEmail($booking_number, "completed", $client_id);
			}
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objBookings->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		if($objBookings->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objBookings->error, false);
		}
		$mode = "view";
	}else if($action=="details"){		
		$mode = "details";		
	}else if($action=="cancel_add"){		
		$mode = "view";		
	}else if($action=="cancel_edit"){				
		$mode = "view";
	}else if($action=="description"){				
		$mode = "description";
	}else if($action=="invoice"){				
		$mode = "invoice";
	}else if($action=="clean_credit_card"){				
		if($objBookings->CleanCreditCardInfo($rid)){
			$msg = draw_success_message(_OPERATION_COMMON_COMPLETED, false);
		}else{
			$msg = draw_important_message($objBookings->error, false);
		}
		$mode = "view";
		$title_action = "Clean";
	}
	
	// Start main content
	draw_title_bar(
		prepare_breadcrumbs(array(_BOOKINGS=>"",_BOOKINGS=>"",ucfirst($title_action)=>"")),
		(($mode == "invoice") ? "<a href='javascript:appInvoicePreview();'><img src='images/printer.png' alt='' /> "._PRINT."</a>" : "")
	);
    	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == "view"){
		$objBookings->DrawViewMode();
		
		echo "<fieldset class='instructions' style='margin-top:10px;'>
				<legend>Legend: </legend>
				<div style='padding:10px;'>
					<font color=#0000a3>"._PREPARING."</font> - "._LEGEND_PREPARING."<br>
					<font color=#a3a300>"._RESERVED."</font> - "._LEGEND_RESERVED."<br>
					<font color=#00a300>"._COMPLETED."</font> - "._LEGEND_COMPLETED."<br>
					<font color=#a30000>"._REFUNDED."</font> - "._LEGEND_REFUNDED."<br>
				</div>
			</fieldset>";
		
	}else if($mode == "add"){		
		$objBookings->DrawAddMode();		
	}else if($mode == "edit"){		
		$objBookings->DrawEditMode($rid, $operation, $operation_field);		
	}else if($mode == "details"){		
		$objBookings->DrawDetailsMode($rid);		
	}else if($mode == "description"){		
		$objBookings->DrawBookingDescription($rid);		
	}else if($mode == "invoice"){		
		$objBookings->DrawBookingInvoice($rid);		
	}	
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>