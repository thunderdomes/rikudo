<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin()){

	$room_id 	= isset($_GET['room_id']) ? (int)$_GET['room_id'] : "0";
	$room_type  = Rooms::GetRoomInfo($room_id, "room_type");

	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$operation_field = MicroGrid::GetParameter('operation_field');
	$rid    	= MicroGrid::GetParameter('rid');
	$mode   	= "view";
	$msg 		= "";
	
	$objRoomsDescr = new RoomsDescription();
	
	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		#if($objRoomsDescr->AddRecord()){
		#	$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
		#	$mode = "view";
		#}else{
		#	$msg = draw_important_message($objRoomsDescr->error, false);
		#	$mode = "add";
		#}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objRoomsDescr->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objRoomsDescr->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		#if($objRoomsDescr->DeleteRecord($rid)){
		#	$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		#}else{
		#	$msg = draw_important_message($objRoomsDescr->error, false);
		#}
		#$mode = "view";
	}else if($action=="details"){		
		$mode = "details";		
	}else if($action=="cancel_add"){		
		$mode = "view";		
	}else if($action=="cancel_edit"){				
		$mode = "view";
	}
	
	// Start main content
	draw_title_bar(
		prepare_breadcrumbs(array(_ROOMS_MANAGEMENT=>"",$room_type=>"",_DESCRIPTION=>"")),
		"<a href='index.php?admin=mod_rooms_management'>"._BUTTON_BACK."</a>"
	);
    	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == "view"){		
		$objRoomsDescr->DrawViewMode();	
	}else if($mode == "add"){		
		$objRoomsDescr->DrawAddMode();		
	}else if($mode == "edit"){		
		$objRoomsDescr->DrawEditMode($rid, $operation, $operation_field);		
	}else if($mode == "details"){		
		$objRoomsDescr->DrawDetailsMode($rid);		
	}
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>