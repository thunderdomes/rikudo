<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------
	
if(@$objLogin->IsLoggedInAs("owner","mainadmin") && Modules::IsModuleInstalled("clients")){
	
	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$operation_field = MicroGrid::GetParameter('operation_field');
	$rid    	= MicroGrid::GetParameter('rid');
	$email		= MicroGrid::GetParameter('email', false);
	$mode   	= "view";
	$msg 		= "";
	
	$objClients = new Clients();

	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objClients->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objClients->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objClients->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objClients->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		if($objClients->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objClients->error, false);
		}
		$mode = "view";
	}else if($action=="details"){		
		$mode = "details";		
	}else if($action=="cancel_add"){		
		$mode = "view";		
	}else if($action=="cancel_edit"){				
		$mode = "view";
	}else if($action=="reactivate"){
		if($objClients->Reactivate($email)){
			$msg = draw_success_message(_EMAIL_SUCCESSFULLY_SENT, false);
		}else{
			$msg = draw_important_message($objClients->error, false);
		}		
		$mode = "view";
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>"",_CLIENTS_MANAGEMENT=>"",ucfirst($action)=>"")));
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == "view"){
		$objClients->DrawOperationLinks("<a href='index.php?admin=mod_clients_groups'>[ "._CLIENT_GROUPS." ]</a>");			
		$objClients->DrawViewMode();	
	}else if($mode == "add"){		
		$objClients->DrawAddMode();		
	}else if($mode == "edit"){		
		$objClients->DrawEditMode($rid);		
	}else if($mode == "details"){		
		$objClients->DrawDetailsMode($rid);		
	}
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>