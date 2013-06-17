<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$operation_field = MicroGrid::GetParameter('operation_field');
	$rid    	= MicroGrid::GetParameter('rid');
	$mode   	= "view";
	$msg 		= "";
	
	$objModules = new Modules();

	if($action=="add"){		
		$mode = "view";
	}else if($action=="create"){
		$mode = "view";
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objModules->UpdateRecord($rid)){
			$mst_text = ($objModules->error != "") ? $objModules->error : _UPDATING_OPERATION_COMPLETED;
			$msg = draw_success_message($mst_text, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objModules->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		$mode = "view";
	}else if($action=="details"){		
		$mode = "view";
	}else if($action=="cancel_add"){		
		$mode = "view";		
	}else if($action=="cancel_edit"){				
		$mode = "view";
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_MODULES=>"",_MODULES_MANAGEMENT=>"",ucfirst($action)=>"")));
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	if($objModules->modulesCount <= 0){
		$msg = draw_important_message(_MODULES_NOT_FOUND, false);
	}
	echo $msg;

	draw_content_start();	
	if($mode == "view"){		
		$objModules->DrawModules();
	}else if($mode == "add"){		
		$objModules->DrawAddMode();		
	}else if($mode == "edit"){		
		$objModules->DrawEditMode($rid);		
	}else if($mode == "details"){		
		$objModules->DrawDetailsMode($rid);		
	}
	draw_content_end();
	
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>