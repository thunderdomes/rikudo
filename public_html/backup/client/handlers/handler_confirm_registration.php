<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(!@$objLogin->IsLoggedIn()){        
    
    $code  = isset($_REQUEST['c']) ? prepare_input($_REQUEST['c']) : "";
	$task  = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
    $msg   = "";
	$confirmed = false;	

    if($code != ""){
        $sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE registration_code = '".encode_text($code)."' AND is_active = 0";
		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);		
        if($result[1] > 0){            
			$sql = "UPDATE ".TABLE_CLIENTS."
					SET is_active = 1, registration_code = ''
					WHERE registration_code = '".encode_text($code)."' AND is_active = 0";
			database_void_query($sql);
			$msg = draw_success_message(_CONFIRMED_SUCCESS_MSG, false);
			$confirmed = true;
            $msg .= "<script type='text/javascript'>setTimeout('appGoTo(\"client=login\")', 15000);</script>";
        }else{
            if(strlen($code) == 20){
				$confirmed = true;
                $msg = draw_message(_CONFIRMED_ALREADY_MSG, false);                        
            }else{
				$msg = draw_important_message(_WRONG_CONFIRMATION_CODE, false);
            }		
        }
    }else{
		if($task == "post_submission") $msg = draw_important_message(str_replace("_FIELD_", _CONFIRMATION_CODE, _FIELD_CANNOT_BE_EMPTY), false);
    }    
}

?>