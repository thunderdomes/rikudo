<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

	$act   = isset($_POST['act']) ? prepare_input($_POST['act']) : "";
	$lid   = isset($_REQUEST['lid']) ? (int)$_REQUEST['lid'] : "0";
	$msg   = "";
	
	$objLanguages  = new Languages($lid);
	
	$params = array();		
	// edit new language
	if($act == "update"){		
		$params['id']         = $lid;
		if(isset($_POST['lang_name']))    $params['lang_name'] = prepare_input($_POST['lang_name']);
		if(isset($_POST['lang_order']))   $params['lang_order'] = prepare_input($_POST['lang_order']);
		if(isset($_POST['abbreviation'])) $params['abbreviation'] = prepare_input($_POST['abbreviation']);
		$params['is_default'] = (isset($_POST['is_default'])) ? (int)$_POST['is_default'] : "0";
		$params['is_active']  = (isset($_POST['is_active'])) ? (int)$_POST['is_active'] : "0";
		$params['icon_image'] = (isset($_POST['icon_image'])) ? prepare_input($_POST['icon_image']) : "";
		$params['lang_dir']   = (isset($_POST['lang_dir'])) ? prepare_input($_POST['lang_dir']) : "ltr";
		$params['used_on']      = (isset($_POST['used_on'])) ? prepare_input($_POST['used_on']) : "global";
		
		if($objLanguages->LanguageUpdate($params)) {
			$msg = draw_success_message(_LANGUAGE_EDITED, false);
			$user_session->SetMessage('notice', $msg);
			header("location: index.php?admin=languages");
			exit;
		}else{
			$msg = draw_important_message($objLanguages->error, false);
		}
	}else{
		$params['lang_name']  	= $objLanguages->GetParameter('lang_name');
		$params['lang_order']	= $objLanguages->GetParameter('lang_order');
		$params['abbreviation'] = $objLanguages->GetParameter('abbreviation');
		$params['is_default'] 	= $objLanguages->GetParameter('is_default');
		$params['is_active']	= $objLanguages->GetParameter('is_active');
		$params['icon_image'] 	= $objLanguages->GetParameter('icon_image');
		$params['lang_dir']   	= $objLanguages->GetParameter('lang_dir');
		$params['used_on']   	= $objLanguages->GetParameter('used_on');
	}

	if($msg == ""){
		$msg = draw_message(_ALERT_REQUIRED_FILEDS, false);
	}
}

?>