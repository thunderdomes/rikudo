<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------
	
if(@$objLogin->IsLoggedInAs("owner","mainadmin")){
	
	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$operation_field = MicroGrid::GetParameter('operation_field');
	$rid    	= MicroGrid::GetParameter('rid');
	$mode   = "view";
	$msg 	= "";
	
	$objRooms = new Rooms();

	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objRooms->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objRooms->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objRooms->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objRooms->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		if($objRooms->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objRooms->error, false);
		}
		$mode = "view";
	}else if($action=="details"){		
		$mode = "details";		
	}else if($action=="cancel_add"){		
		$mode = "view";		
	}else if($action=="cancel_edit"){				
		$mode = "view";
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_HOTEL_MANAGEMENT=>"",_ROOMS_MANAGEMENT=>"",ucfirst($action)=>"")));
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == "view"){		
		$objRooms->DrawViewMode();	
	}else if($mode == "add"){		
		$objRooms->DrawAddMode();		
	}else if($mode == "edit"){		
		$objRooms->DrawEditMode($rid);		
	}else if($mode == "details"){		
		$objRooms->DrawDetailsMode($rid);		
	}
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>