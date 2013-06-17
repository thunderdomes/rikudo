<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin") && Modules::IsModuleInstalled("clients")){	

	$action = MicroGrid::GetParameter('action');
	$rid    = MicroGrid::GetParameter('rid');
	$mode   = "view";
	$msg    = "";
	
	$objClientsSettings = new ModulesSettings("clients");
	
	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objClientsSettings->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objClientsSettings->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objClientsSettings->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objClientsSettings->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		if($objClientsSettings->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objClientsSettings->error, false);
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
	draw_title_bar(prepare_breadcrumbs(array(_MODULES=>"",_CLIENTS_SETTINGS=>"",ucfirst($action)=>"")));
    echo "<br />";
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();
	if($mode == "view"){		
		$objClientsSettings->DrawViewMode();	
	}else if($mode == "add"){		
		$objClientsSettings->DrawAddMode();		
	}else if($mode == "edit"){		
		$objClientsSettings->DrawEditMode($rid);		
	}else if($mode == "details"){ 
		$objClientsSettings->DrawDetailsMode($rid);		
	}
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>