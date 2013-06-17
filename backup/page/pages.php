<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$mg_language_id = isset($_REQUEST['mg_language_id']) ? prepare_input($_REQUEST['mg_language_id']) : "";
$button_text = "";

if(@$objLogin->IsLoggedInAsAdmin()){
	$objPage = new Pages(Application::Get("page_id"), false);
}else{
	$objPage = new Pages(((Application::Get("system_page") != "") ? Application::Get("system_page") : Application::Get("page_id")), true);
}

if($objPage->CheckAccessRights(@$objLogin->IsLoggedIn())){	
	// Check if there is a page 
	if($objPage->GetId() != "") {
		if($objLogin->IsLoggedInAsAdmin()){		
			$button_text = "<a href='index.php?admin=pages".((Application::Get("type") == "system") ? "&type=system" : "")."&mg_language_id=".$mg_language_id."'>"._BUTTON_BACK."</a>";
		}
		if(Application::Get("page_id") != "home"){
			$objPage->DrawTitle($button_text);
		}
		$objPage->DrawText();	
	}else{
		draw_title_bar(_PAGES);
		draw_important_message(_PAGE_UNKNOWN);		
	}		
	
}else{
	draw_important_message(_MUST_BE_LOGGED);	
}

?>	
	