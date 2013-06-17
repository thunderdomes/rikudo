<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

	draw_title_bar(prepare_breadcrumbs(array(_LANGUAGES_SETTINGS=>"",_LANGUAGES=>"")));
	
	$act 			= (!empty($_GET['act'])) ? prepare_input($_GET['act']) : "";
	$lid 			= (!empty($_GET['lid'])) ? (int)$_GET['lid'] : "";
	$lo 			= (!empty($_GET['lo'])) ? (int)$_GET['lo'] : "";
	$dir 			= (!empty($_GET['dir'])) ? prepare_input($_GET['dir']) : "";
	$submition_type = (!empty($_POST['submition_type'])) ? prepare_input($_POST['submition_type']) : "";
	$msg            = "";
	
	$objLanguages = new Languages();
	
	if($act=="delete"){		
		// delete menu action
		if($objLanguages->LanguageDelete($lid, $lo)){
			$msg = draw_success_message(_LANG_DELETED, false);
		}else{
			$msg = draw_important_message($objLanguages->error, false);
		}
	}else if($act=="move"){
		// move language action
		if($objLanguages->LanguageMove($lid, $dir, $lo)){
			$msg = draw_success_message(_LANG_ORDER_CHANGED, false);
		}else{
			$msg = draw_important_message($objLanguages->error, false);
		}		
	}

	if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();
	$objLanguages->DrawLanguages();
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>