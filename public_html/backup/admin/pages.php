<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$type = isset($_GET['type']) ? prepare_input($_GET['type']) : "";

if(@$objLogin->IsLoggedInAsAdmin()){

	$action 	= MicroGrid::GetParameter('action');
	$operation 	= MicroGrid::GetParameter('opearation');
	$operation_field = MicroGrid::GetParameter('operation_field');
	$rid    	= MicroGrid::GetParameter('rid');
	$language_id = (MicroGrid::GetParameter('language_id') != "") ? MicroGrid::GetParameter('language_id') : Languages::GetDefaultLang();
	$act    	= MicroGrid::GetParameter('act', false);
	$pid    	= MicroGrid::GetParameter('pid', false);
	$po    		= MicroGrid::GetParameter('po', false);
	$dir    	= MicroGrid::GetParameter('dir', false);
	
	$mode   = "view";
	$msg 	= "";
	
	$objPages = new PagesGrid($type);

	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objPages->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objPages->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objPages->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objPages->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		$objPage = new Pages($rid);
		if($objPage->MoveToTrash()){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objPage->error, false);
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
	draw_title_bar(prepare_breadcrumbs(array(_PAGE_MANAGEMENT=>"",(($type == "system") ? _SYSTEM : "")=>""),ucfirst($action)));
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	
	echo "<script type='text/javascript'>
		<!--
			function confirmRemoving(pid){
				if(!confirm('"._PAGE_REMOVE_WARNING."')){
					false;
				}else{
					document.location.href = 'index.php?admin=pages&mg_action=delete&mg_language_id=".$language_id."&mg_rid='+pid;
				}				
			}
		//-->
		</script>";

	if($mode == "view"){		
		$objPages->DrawViewMode();	
	}else if($mode == "add"){		
		$objPages->DrawAddMode();		
	}else if($mode == "edit"){		
		$objPages->DrawEditMode($rid);		
	}else if($mode == "details"){		
		$objPages->DrawDetailsMode($rid);		
	}
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}
?>