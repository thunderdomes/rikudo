<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin()){
	
	$submition_type = isset($_POST['submition_type']) ? prepare_input($_POST['submition_type']) : "";
	$site_template  = isset($_POST['site_template']) ? prepare_input($_POST['site_template']) : "";
	$cron_type      = isset($_POST['cron_type']) ? prepare_input($_POST['cron_type']) : $objSettings->GetParameter("cron_type");
	$http_host 	    = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : _UNKNOWN;
	$language_id    = isset($_POST['sel_language_id']) ? prepare_input($_POST['sel_language_id']) : Languages::GetDefaultLang();
	$msg            = "";
	$focus_on_field = "";
	
	$objSiteDescription->LoadData($language_id);

	$params_tab2 = array();
	$params_tab2["header_text"] = isset($_POST['header_text']) ? prepare_input($_POST['header_text'], false, "medium") : $objSiteDescription->GetParameter("header_text");
	$params_tab2["slogan_text"] = isset($_POST['slogan_text']) ? prepare_input($_POST['slogan_text']) : $objSiteDescription->GetParameter("slogan_text");
	$params_tab2["footer_text"] = isset($_POST['footer_text']) ? prepare_input($_POST['footer_text'], false, "medium") : $objSiteDescription->GetParameter("footer_text");

	$params_tab3 = array();
	$params_tab3["tag_title"] 	    = isset($_POST['tag_title']) ? prepare_input($_POST['tag_title']) : $objSiteDescription->GetParameter("tag_title");
	$params_tab3["tag_description"] = isset($_POST['tag_description']) ? prepare_input($_POST['tag_description']) : $objSiteDescription->GetParameter("tag_description");
	$params_tab3["tag_keywords"]    = isset($_POST['tag_description']) ? prepare_input($_POST['tag_keywords']) : $objSiteDescription->GetParameter("tag_keywords");
	$apply_to_all_pages             = isset($_POST['apply_to_all_pages']) ? prepare_input($_POST['apply_to_all_pages']) : "";
	
	$params = array();
	$params["seo_urls"] 	   = isset($_POST['seo_urls']) ? prepare_input($_POST['seo_urls']) : $objSettings->GetParameter("seo_urls");
	$params["admin_email"] 	   = isset($_POST['admin_email']) ? prepare_input($_POST['admin_email']) : $objSettings->GetParameter("admin_email");
	$params["mailer"] 	       = isset($_POST['mailer']) ? prepare_input($_POST['mailer']) : $objSettings->GetParameter("mailer");
	$params["rss_feed"]        = isset($_POST['rss_feed']) ? prepare_input($_POST['rss_feed']) : $objSettings->GetParameter("rss_feed");
	$params["rss_feed_type"]   = isset($_POST['rss_feed_type']) ? prepare_input($_POST['rss_feed_type']) : $objSettings->GetParameter("rss_feed_type");
	$params["is_offline"]      = isset($_POST['is_offline']) ? prepare_input($_POST['is_offline']) : $objSettings->GetParameter("is_offline");
	$params["offline_message"] = isset($_POST['offline_message']) ? prepare_input($_POST['offline_message']) : $objSettings->GetParameter("offline_message");
	$params["caching_allowed"] = isset($_POST['caching_allowed']) ? prepare_input($_POST['caching_allowed']) : $objSettings->GetParameter("caching_allowed");
	$params["cache_lifetime"]  = isset($_POST['cache_lifetime']) ? prepare_input($_POST['cache_lifetime']) : $objSettings->GetParameter("cache_lifetime");
	$params["date_format"] 	   = isset($_POST['date_format']) ? prepare_input($_POST['date_format']) : $objSettings->GetParameter("date_format");
	$params["wysiwyg_type"]    = isset($_POST['wysiwyg_type']) ? prepare_input($_POST['wysiwyg_type']) : $objSettings->GetParameter("wysiwyg_type");

	$params_cron = array();
	$params_cron["cron_type"]  = isset($_POST['cron_type']) ? prepare_input($_POST['cron_type']) : $objSettings->GetParameter("cron_type");
	$params_cron["cron_run_period"]       = isset($_POST['cron_run_period']) ? prepare_input($_POST['cron_run_period']) : $objSettings->GetParameter("cron_run_period");
	$params_cron["cron_run_period_value"] = isset($_POST['cron_run_period_value']) ? prepare_input($_POST['cron_run_period_value']) : $objSettings->GetParameter("cron_run_period_value");

	// SAVE CHANGES			
	if($submition_type == "general"){
		if($params["admin_email"] == ""){
			$msg = draw_important_message(_ADMIN_EMAIL_IS_EMPTY, false);
			$params["admin_email"] = $objSettings->GetParameter("admin_email");
		}else if(!check_email_address($params["admin_email"])){
			$msg = draw_important_message(_ADMIN_EMAIL_WRONG, false);
			$params["admin_email"] = $objSettings->GetParameter("admin_email");
		}else{			
			if($objSettings->UpdateFields($params) == true){
				$msg = draw_success_message(_SETTINGS_SAVED, false);
			}else $msg = draw_important_message($objSettings->error, false);
		}
	}else if($submition_type == "clean_cache"){
		delete_cache();
		$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
	}else if($submition_type == "visual_settings"){
		if($params_tab2["header_text"] == ""){
			$msg = draw_important_message(_HEADER_IS_EMPTY, false);
			$focus_on_field = "header_text";
		}else if(strlen($params_tab2["header_text"]) > 255){
			$msg_text = str_replace("_FIELD_", _HDR_HEADER_TEXT, _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "255", $msg_text);
			$msg = draw_important_message($msg_text, false);
			$focus_on_field = "header_text";
		}else if(strlen($params_tab2["slogan_text"]) > 512){
			$msg_text = str_replace("_FIELD_", _HDR_SLOGAN_TEXT, _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "512", $msg_text);
			$msg = draw_important_message($msg_text, false);
			$focus_on_field = "slogan_text";
		}else if($params_tab2["footer_text"] == ""){
			$msg = draw_important_message(_FOOTER_IS_EMPTY, false);
			$focus_on_field = "footer_text";
		}else if(strlen($params_tab2["footer_text"]) > 512){
			$msg_text = str_replace("_FIELD_", _HDR_FOOTER_TEXT, _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "512", $msg_text);
			$msg = draw_important_message($msg_text, false);
			$focus_on_field = "footer_text";
		}else{			
			if($objSiteDescription->UpdateFields($params_tab2, $language_id) == true){
				$msg = draw_success_message(_SETTINGS_SAVED, false);
			}else $msg = draw_important_message($objSiteDescription->error, false);
		}		
	}else if($submition_type == "meta_tags"){
		if($params_tab3["tag_title"] == ""){
			$msg = draw_important_message(_TAG_TITLE_IS_EMPTY, false);
			$params_tab3["tag_title"] = $objSiteDescription->GetParameter("tag_title");
			$focus_on_field = "tag_title";
		}else if(strlen($params_tab3["tag_title"]) > 255){
			$msg_text = str_replace("_FIELD_", "TITLE", _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "255", $msg_text);
			$msg = draw_important_message($msg_text, false);
			$focus_on_field = "tag_title";
		}else if(strlen($params_tab3["tag_keywords"]) > 512){
			$msg_text = str_replace("_FIELD_", "KEYWORDS", _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "512", $msg_text);
			$msg = draw_important_message($msg_text, false);
			$focus_on_field = "tag_keywords";
		}else if(strlen($params_tab3["tag_description"]) > 512){
			$msg_text = str_replace("_FIELD_", "DESCRIPTION", _FIELD_LENGTH_ALERT);
			$msg_text = str_replace("_LENGTH_", "512", $msg_text);
			$msg = draw_important_message($msg_text, false);
			$focus_on_field = "tag_description";
		}else{
			if($objSiteDescription->UpdateFields($params_tab3, $language_id) == true){
				if($apply_to_all_pages == "1") Pages::UpdateMetaTags($params_tab3, $language_id);
				$msg = draw_success_message(_SETTINGS_SAVED, false);	
			}else $msg = draw_important_message($objSiteDescription->error, false);			
		}
	}else if($submition_type == "templates"){
		if(!empty($site_template)){
			if($objSettings->SetTemplate($site_template) == true){
				delete_cache();
				$msg = draw_success_message(_SETTINGS_SAVED, false);
			}else $msg = draw_important_message($objSettings->error, false);									
		}else $msg = draw_important_message(_TEMPLATE_IS_EMPTY, false);
	}else if($submition_type == "site_info"){		
		$params_ranks = array();
		$params_ranks["alexa_rank"] = number_format((float)$objSettings->CheckAlexaRank($http_host));
		$params_ranks["google_rank"] = (int)$objSettings->CheckGoogleRank($http_host);		
		if($objSettings->UpdateFields($params_ranks) == true) $msg = draw_success_message(_CHANGES_WERE_SAVED, false);	
		else $msg = draw_important_message($objSettings->error, false);					
	}else if($submition_type == "cron_settings"){
		if($objSettings->UpdateFields($params_cron) == true) $msg = draw_success_message(_CHANGES_WERE_SAVED, false);	
		else $msg = draw_important_message($objSettings->error, false);
	}
	
	$template = $objSettings->GetTemplate();	
	if($submition_type == "general" || $submition_type == "visual_settings" || $submition_type == "meta_tags"){
		$objSiteDescription->LoadData();
		prepare_rss();	
	}	

}
?>