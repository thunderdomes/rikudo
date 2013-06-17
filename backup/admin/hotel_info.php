<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------
	
if(@$objLogin->IsLoggedInAsAdmin()){
	
	$grid = "first";
	if(isset($_POST['name']) && isset($_POST['address']) && isset($_POST['description'])){
		$grid = "second";
	}

	$prefix 	= MicroGrid::GetParameter('prefix');
	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$operation_field = MicroGrid::GetParameter('operation_field');
	
	////////////////////////////////////////////////////////////////////////////
	// HOTEL INFO
	////////////////////////////////////////////////////////////////////////////
	$rid      = "1"; //MicroGrid::GetParameter('rid');
	$mode     = "edit";
	$msg 	  = "";
	$action_h = "";

	$objHotel = new Hotel();

    if($prefix == "htl_"){
		$action_h = $action;
		if($action == "" || $action=="edit"){
			$mode = "edit";
		}else if($action=="update"){
			if($objHotel->UpdateRecord($rid)){
				$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
				$mode = "edit";
			}else{
				$msg = draw_important_message($objHotel->error, false);
				$mode = "edit";
			}		
		}else if($action=="cancel_edit"){				
			$mode = "edit";
		}		
	}

	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_HOTEL_INFO=>"",ucfirst($action_h)=>"")));	
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	$objHotel->DrawEditMode($rid, "", "", array("reset"=>true, "cancel"=>false));		
	draw_content_end();
	

	////////////////////////////////////////////////////////////////////////////
	// HOTEL DESCRIPTION
	////////////////////////////////////////////////////////////////////////////
	$objHotelDescr = new HotelDescription();

	$mode = "view";
	$rid  = $objHotelDescr->GetParameter('rid');
    $msg  = "";
	$action_hd = "";
	
	if($prefix == ""){
		$action_hd = $action;
		if($action=="add"){		
			$mode = "add";
		}else if($action=="edit"){
			$mode = "edit";
		}else if($action=="update"){
			if($objHotelDescr->UpdateRecord($rid)){
				$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
				$mode = "view";
			}else{
				$msg = draw_important_message($objHotelDescr->error, false);
				$mode = "edit";
			}		
		}else if($action=="details"){		
			$mode = "details";		
		}else if($action=="cancel_add"){		
			$mode = "view";		
		}else if($action=="cancel_edit"){				
			$mode = "view";
		}		
	}	
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_HOTEL_DESCRIPTION=>"",ucfirst($action_hd)=>"")));
    	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == "view"){		
		$objHotelDescr->DrawViewMode();	
	}else if($mode == "add"){		
		$objHotelDescr->DrawAddMode();		
	}else if($mode == "edit"){		
		$objHotelDescr->DrawEditMode($rid, $operation, $operation_field);		
	}else if($mode == "details"){		
		$objHotelDescr->DrawDetailsMode($rid);		
	}
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>