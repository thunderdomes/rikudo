<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin() && Modules::IsModuleInstalled("news")){	

	$action = MicroGrid::GetParameter('action');
	$rid    = MicroGrid::GetParameter('rid');
	$mode   = "view";
	$msg    = "";
	

	$objNews = new News();
	
	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objNews->AddRecord()){
			
			// --- START clone to other languages ---
			$total_languages = Languages::GetAllActive();
			$language_id 	= MicroGrid::GetParameter('language_id', false);
			$news_code 	    = MicroGrid::GetParameter('news_code', false);
			$header_text 	= MicroGrid::GetParameter('header_text', false);
			$body_text 		= MicroGrid::GetParameter('body_text', false);
			$date_created 	= MicroGrid::GetParameter('date_created', false);
			
			for($i = 0; $i < $total_languages[1]; $i++){
				if($language_id != "" && $total_languages[0][$i]['abbreviation'] != $language_id){
					$sql = "INSERT INTO ".TABLE_NEWS." (id, news_code, header_text, body_text, date_created, language_id)
					        VALUES(NULL, '".encode_text($news_code)."', '".encode_text($header_text)."', '".encode_text($body_text)."', '".encode_text($date_created)."', '".encode_text($total_languages[0][$i]['abbreviation'])."')";
					database_void_query($sql);
					$objNews->SetSQLs('insert_lan_'.$total_languages[0][$i]['abbreviation'], $sql);
				}								
			}
			// --- END clone to other languages ---

			$objNewsSettings = new ModulesSettings("news");
			$news_rss = $objNewsSettings->GetSettings("news_rss");
			if($news_rss == "yes"){
				prepare_rss();
			}			
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objNews->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objNews->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objNews->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		if($objNews->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objNews->error, false);
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
	draw_title_bar(prepare_breadcrumbs(array(_MODULES=>"",_NEWS_MANAGEMENT=>"",ucfirst($action)=>"")));

	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();
	if($mode == "view"){		
		$objNews->DrawViewMode();	
	}else if($mode == "add"){		
		$objNews->DrawAddMode();		
	}else if($mode == "edit"){		
		$objNews->DrawEditMode($rid);		
	}else if($mode == "details"){		
		$objNews->DrawDetailsMode($rid);		
	}
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>