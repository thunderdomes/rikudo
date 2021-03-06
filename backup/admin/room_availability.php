<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------
	
// Draw title bar
$rid  = isset($_REQUEST['rid']) ? (int)$_REQUEST['rid'] : "";
$room_type  = Rooms::GetRoomInfo($rid, "room_type");

if(@$objLogin->IsLoggedInAsAdmin() && Modules::IsModuleInstalled("booking")){
	
	draw_title_bar(
		prepare_breadcrumbs(array(_ROOMS_MANAGEMENT=>"",$room_type=>"",_AVAILABILITY=>"")),
		"<a href='index.php?admin=mod_rooms_management'>"._BUTTON_BACK."</a>"
	);

	$task = isset($_REQUEST['task']) ? prepare_input($_REQUEST['task']) : "";
	$rpid  = isset($_POST['rpid']) ? (int)$_POST['rpid'] : "";
	
	$objRoom = new Rooms();

	if($task == "add_new"){
		if($objRoom->AddRoomAvailability($rid)){
			draw_success_message(_ROOM_PRICES_WERE_ADDED);	
		}else{
			draw_important_message($objRoom->error);
		}		
	}else if($task == "update"){
		if($objRoom->UpdateRoomAvailability($rid)){
			draw_success_message(_CHANGES_WERE_SAVED);	
		}else{
			draw_important_message($objRoom->error);
		}		
	}else if($task == "delete"){
		if($objRoom->DeleteRoomAvailability($rpid)){
			draw_success_message(_RECORD_WAS_DELETED_COMMON);	
		}else{
			draw_important_message($objRoom->error);
		}				
	}else if($task == "refresh"){
		unset($_POST);
	}
	//echo $msg;

	if(is_integer($rid) && $rid != ""){
		draw_content_start();
		$objRoom->DrawRoomAvailabilitiesForm($rid);			
		draw_content_end();		
	}else{
		draw_important_message(_WRONG_PARAMETER_PASSED);
	}
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>