<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsClient()){

    draw_title_bar(prepare_breadcrumbs(array(_GENERAL=>"",_CLIENT_PANEL=>"")));

	draw_content_start();

		Campaigns::DrawCampaignBanner();

		$msg = "<div style='padding:9px;min-height:250px'>";
        $welcome_text = _WELCOME_CLIENT_TEXT;
        $welcome_text = str_replace("_FIRST_NAME_", @$objLogin->GetLoggedFirstName(), $welcome_text);
		$welcome_text = str_replace("_LAST_NAME_", @$objLogin->GetLoggedLastName(), $welcome_text);
        $welcome_text = str_replace("_TODAY_", _TODAY.": <b>".date("M d, Y g:i A")."</b>", $welcome_text);
		$welcome_text = str_replace("_LAST_LOGIN_", _LAST_LOGIN.": <b>".format_datetime(@$objLogin->GetLastLoginTime())."</b>", $welcome_text);
		$welcome_text = str_replace("_HOME_", _HOME, $welcome_text);
        $welcome_text = str_replace("_EDIT_MY_ACCOUNT_", _EDIT_MY_ACCOUNT, $welcome_text);
        $welcome_text = str_replace("_MY_BOOKINGS_", _MY_BOOKINGS, $welcome_text);        
        $msg .= $welcome_text;
        $msg .= "</div>";
		
		draw_message($msg, true, false);
	
	draw_content_end();		

} else draw_important_message(_NOT_AUTHORIZED);
?>