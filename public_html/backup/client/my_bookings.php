<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------


if(@$objLogin->IsLoggedInAsClient() && Modules::IsModuleInstalled("booking")) {

	$objCartSettings = new ModulesSettings("booking");
	if($objCartSettings->GetSettings("is_active") == "yes"){
		
		$action 	= MicroGrid::GetParameter('action');
		$operation 	= MicroGrid::GetParameter('opearation');
		$operation_field = MicroGrid::GetParameter('operation_field');
		$rid    	= MicroGrid::GetParameter('rid');
		$mode   	= "view";
		$msg 		= "";
		
		$objBookings = new Bookings(@$objLogin->GetLoggedID());
		
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
		}
		
		// Start main content
		draw_title_bar(
			prepare_breadcrumbs(array(_BOOKINGS=>"",_BOOKINGS_MANAGEMENT=>"",ucfirst($action)=>"")),
			(($mode == "invoice") ? "<a href='javascript:appInvoicePreview();'>"._PRINT."</a>" : "")
		);
			
		//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
		echo $msg;
	
		//draw_content_start();
		echo "<div style='color:#000000;background-color:#fffff1; border:1px solid #c1c13a;padding:15px;'>";
		if($mode == "view"){		
			$objBookings->DrawViewMode();	
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
		//draw_content_end();
		echo "</div>";
		
	}else{
		draw_important_message(_NOT_AUTHORIZED);
	}
}else{
	draw_important_message(_NOT_AUTHORIZED);
}

?>