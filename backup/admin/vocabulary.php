<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin()){

	draw_title_bar(prepare_breadcrumbs(array(_LANGUAGES_SETTINGS=>"",_VOCABULARY=>"")));
	
	if($act == "edit" && $key != ""){
		// draw edit vocabulary key form
		$msg = draw_message(_ALERT_REQUIRED_FILEDS, false);
		draw_content_start();
		$objVocabulary->DrawEditForm($key);
		draw_content_end();
	}else if($act == "upload_form"){
		draw_content_start();
		$objVocabulary->DrawUploadForm($key);
		draw_content_end();		
	}else if($act == "upload_and_update"){
		draw_content_start();
		if($objVocabulary->UploadAndUpdate($language_id)){
			$msg = draw_success_message(str_replace("_KEYS_", $objVocabulary->updatedKeys, _VOC_KEYS_UPDATED), false);
			$objVocabulary->DrawRewriteButton();	
		}else{
			$msg = draw_important_message($objVocabulary->error, false);
			$objVocabulary->DrawRewriteButton();				
		}		
		draw_content_end();		
	}else if($submition_type == "1"){
		// update vocabulary key
		if(!$objVocabulary->IsKeyUpdated()){			
			$msg = draw_important_message($objVocabulary->error, false);
			draw_content_start();
			$objVocabulary->DrawEditForm($key);
			draw_content_end();
		}
	}else if($submition_type == "2"){
		// rewrite vocabulary 
		if($objVocabulary->RewriteVocabularyFile()){
			$msg = draw_success_message(_VOC_UPDATED);	
		}else{
			$msg = draw_important_message($objVocabulary->error, false);
		}
		draw_content_start();
		$objVocabulary->DrawRewriteButton();
		draw_content_end();
	}else{
		draw_content_start();
		$objVocabulary->DrawRewriteButton();
		draw_content_end();
	}
	//draw_content_end();
	
	if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');	
	echo $msg;
	
	draw_content_start();
	$objVocabulary->DrawVocabulary($key);
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>
