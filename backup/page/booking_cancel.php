<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(Modules::IsModuleInstalled("booking")){
	$objBookingSettings = new ModulesSettings("booking");
	if($objBookingSettings->GetSettings("is_active") == "yes"){

		draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>"",_BOOKING_CANCELED=>"")));		
		
		draw_content_start();
		draw_message(_BOOKING_WAS_CANCELED_MSG, true, true);
		draw_content_end();		
	}else{
		draw_important_message(_NOT_AUTHORIZED);
	}	
}else{
    draw_important_message(_NOT_AUTHORIZED);
}
?>