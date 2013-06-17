<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(!@$objLogin->IsLoggedIn()){

	$account_email = isset($_SESSION['reset_account_email']) ? $_SESSION['reset_account_email'] : "";
	$account_reset = isset($_SESSION['account_reset']) ? $_SESSION['account_reset'] : false;
	$msg = "";
	
	if($account_email != ""){
		if(Clients::ResetAccount($account_email)){
			$msg = draw_success_message(_ACCOUNT_SUCCESSFULLY_RESET, false);			
			$_SESSION['account_reset'] = true;
		}else{
			$msg = draw_important_message(Clients::GetError(), false);					
		}
		unset($_SESSION['reset_account_email']);
	}else{
		if(!$account_reset){
			$msg = draw_important_message(_WRONG_PARAMETER_PASSED, false);			
		}else{
			$msg = draw_message(_ACCOUNT_ALREADY_RESET, false);			
		}	
	}
   
	draw_title_bar(prepare_breadcrumbs(array(_CLIENT=>"",_RESET_ACCOUNT=>"")));
	echo $msg;	

}else if(@$objLogin->IsLoggedIn()){
    draw_title_bar(prepare_breadcrumbs(array(_CLIENT=>"")));
    draw_important_message(_NOT_AUTHORIZED);
}
?>