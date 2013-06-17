<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$checkin_year_month 	= isset($_POST['checkin_year_month']) ? prepare_input($_POST['checkin_year_month']) : date("Y")."-".(int)date("m");
$checkin_year_month_parts = explode("-", $checkin_year_month);
$checkin_year 			= isset($checkin_year_month_parts[0]) ? $checkin_year_month_parts[0] : "";
$checkin_month 			= isset($checkin_year_month_parts[1]) ? convert_to_decimal($checkin_year_month_parts[1]) : "";
$checkin_day 			= isset($_POST['checkin_monthday']) ? convert_to_decimal($_POST['checkin_monthday']) : date("d");

$curr_date 				= mktime(0, 0, 0, date("m"), date("d")+1, date("y"));
$checkout_year_month 	= isset($_POST['checkout_year_month']) ? prepare_input($_POST['checkout_year_month']) : date("Y")."-".(int)date("m");
$checkout_year_month_parts = explode("-", $checkout_year_month);
$checkout_year 			= isset($checkout_year_month_parts[0]) ? $checkout_year_month_parts[0] : "";
$checkout_month 		= isset($checkout_year_month_parts[1]) ? convert_to_decimal($checkout_year_month_parts[1]) : "";
$checkout_day 			= isset($_POST['checkout_monthday']) ? convert_to_decimal($_POST['checkout_monthday']) : date("d", $curr_date);

$max_adults 			= isset($_POST['max_adults']) ? (int)$_POST['max_adults'] : "";
$max_children 			= isset($_POST['max_children']) ? (int)$_POST['max_children'] : "";

$nights = nights_diff($checkin_year."-".$checkin_month."-".$checkin_day, $checkout_year."-".$checkout_month."-".$checkout_day);


draw_title_bar(_AVAILABLE_ROOMS);

// Check if there is a page 
if($checkin_year_month == "0" || $checkin_day == "0" || $checkout_year_month == "0" || $checkout_day == "0"){
	draw_important_message(_WRONG_PARAMETER_PASSED);
}else if(!checkdate($checkout_month, $checkout_day, $checkout_year)){
	draw_important_message(_WRONG_CHECKOUT_DATE_ALERT);
}else if($checkin_year.$checkin_month.$checkin_day < date("Ymd")){
	draw_important_message(_PAST_TIME_ALERT);		
}else if($nights < 1){
	draw_important_message(_BOOK_ONE_NIGHT_ALERT);
}else if(Modules::IsModuleInstalled("booking")){
	$objBookingSettings = new ModulesSettings("booking");
	$minimum_nights = $objBookingSettings->GetSettings("minimum_nights");
	if($minimum_nights > $nights){
		draw_important_message(str_replace("_NIGHTS_", $minimum_nights, _MINIMUM_NIGHTS_ALERT));
	}else{
		
		$nights_text = ($nights > 1) ? $nights." "._NIGHTS : $nights." "._NIGHT;
		
		draw_content_start();
		if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
			draw_sub_title_bar(_FROM.": ".get_month_local($checkin_month)." ".$checkin_day.", ".$checkin_year." "._TO.": ".get_month_local($checkout_month)." ".$checkout_day.", ".$checkout_year." (".$nights_text.")", true, "h4");
		}else{
			draw_sub_title_bar(_FROM.": ".$checkin_day." ".get_month_local($checkin_month)." ".$checkin_year." "._TO.": ".$checkout_day." ".get_month_local($checkout_month)." ".$checkout_year." (".$nights_text.")", true, "h4");
		}
		
		$objRooms = new Rooms();
		$params = array(
			"from_date"	=>$checkin_year."-".$checkin_month."-".$checkin_day,
			"to_date"	=>$checkout_year."-".$checkout_month."-".$checkout_day,
			"nights"	=>$nights,
			"from_year"	=>$checkin_year,
			"from_month"=>$checkin_month,
			"from_day"	=>$checkin_day,
			"to_year"	=>$checkout_year,
			"to_month"	=>$checkout_month,
			"to_day"	=>$checkout_day,
			"max_adults"=>$max_adults,
			"max_children"=>$max_children
		);
		
		$p = isset($_POST['p']) ? (int)$_POST['p'] : "";	
			
		if($objRooms->SearchFor($params) > 0){
			$objRooms->DrawSearchResult($p, $params);			
		}else{
			draw_important_message(_NO_ROOMS_FOUND);		
		}
		draw_content_end();	
	}
}

?>