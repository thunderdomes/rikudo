<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(Modules::IsModuleInstalled("booking")){
	$objBookingSettings = new ModulesSettings("booking");
	if($objBookingSettings->GetSettings("is_active") == "yes"){
			
		$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : "0";
		$from_date = isset($_POST['from_date']) ? prepare_input($_POST['from_date']) : "";
		$to_date = isset($_POST['to_date']) ? prepare_input($_POST['to_date']) : "";
		$nights  = isset($_POST['nights']) ? (int)$_POST['nights'] : "";
		$available_rooms  = isset($_POST['available_rooms']) ? prepare_input($_POST['available_rooms']) : "";
		$available_rooms_parts = explode("-", $available_rooms);
		$rooms  = isset($available_rooms_parts[0]) ? (int)$available_rooms_parts[0] : "";
		$price  = isset($available_rooms_parts[1]) ? (float)$available_rooms_parts[1] : "";
		
		$objReservation = new Reservation();
		
		$act   = isset($_GET['act']) ? prepare_input($_GET['act']) : "";
		$rid   = isset($_GET['rid']) ? (int)$_GET['rid'] : "";
		
		if($act == 'remove'){
			$objReservation->RemoveReservation($rid);
		}else{
			$objReservation->AddToReservation($room_id, $from_date, $to_date, $nights, $rooms, $price);				
		}
		
		if(@$objLogin->IsLoggedInAsAdmin()) draw_title_bar(prepare_breadcrumbs(array(_BOOKING=>"")));
		
		draw_content_start();
		draw_reservation_bar("selected_rooms");		

		// test mode alert
		if(Modules::IsModuleInstalled("booking")){
			$objBookingSettings = new ModulesSettings("booking");
			if($objBookingSettings->GetSettings("mode") == "TEST MODE"){
				draw_important_message(_TEST_MODE_ALERT_SHORT);
			}        
		}

		Campaigns::DrawCampaignBanner();

		$objReservation->ShowReservationInfo();
		draw_content_end();
	}else{
		draw_important_message(_NOT_AUTHORIZED);	
	}
}else{
	draw_important_message(_NOT_AUTHORIZED);
}	
?>