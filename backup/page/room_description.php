<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------


$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : "";
$back_button = isset($_GET['b']) ? (boolean)$_GET['b'] : true;

$objRoom = new Rooms();

if($room_id != "") {	
	$objRoom->DrawRoomDescription($room_id, $back_button); 
}else{
	draw_important_message(_WRONG_PARAMETER_PASSED);		
}
	
?>