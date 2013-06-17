<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

	$act   					= isset($_POST['act']) ? $_POST['act'] : "";
	$params 				= array();		
	$params['lang_order']   = (isset($_POST['lang_order'])) ? prepare_input($_POST['lang_order']) : ""; 
	$params['lang_name']    = (isset($_POST['lang_name'])) ? prepare_input($_POST['lang_name']) : ""; 
	$params['abbreviation'] = (isset($_POST['abbreviation'])) ? prepare_input($_POST['abbreviation']) : ""; 
	$params['is_default']   = (isset($_POST['is_default'])) ? (int)$_POST['is_default'] : "0";
	$params['is_active']    = (isset($_POST['is_active'])) ? prepare_input($_POST['is_active']) : "0";
	$params['icon_image']   = (isset($_POST['icon_image'])) ? prepare_input($_POST['icon_image']) : "";
	$params['lang_dir']     = (isset($_POST['lang_dir'])) ? prepare_input($_POST['lang_dir']) : "ltr";
	$params['used_on']      = (isset($_POST['used_on'])) ? prepare_input($_POST['used_on']) : "global";
	$msg   					= "";
	
	$objLanguages  = new Languages();
	
	// add new language
	if($act == "add"){	
		if($objLanguages->LanguageAdd($params)) {
			$msg = draw_success_message(_LANGUAGE_ADDED, false);
			$user_session->SetMessage('notice', $msg);
			header("location: index.php?admin=languages");
			exit;
		}else{
			$msg = draw_important_message($objLanguages->error, false);
		}
	}

	if($msg == ""){
		$msg = draw_message(_ALERT_REQUIRED_FILEDS, false);
	}
}

?>