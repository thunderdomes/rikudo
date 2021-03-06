<?php
################################################################################
##              -= YOU MAY NOT REMOVE OR CHANGE THIS NOTICE =-                 #
## --------------------------------------------------------------------------- #
##  ApPHP HotelSite Pro version 3.3.0                                          #
##  Developed by:  ApPHP <info@apphp.com>                                      #
##  License:       GNU LGPL v.3                                                #
##  Site:          http://www.apphp.com/php-hotel-site/                        #
##  Copyright:     ApPHP HotelSite (c) 2010-2011. All rights reserved.         #
##                                                                             #
##  Additional modules (embedded):                                             #
##  -- ApPHP EasyInstaller v2.0.5 (installation module)       http://apphp.com #
##  -- ApPHP Tabs v2.0.3 (tabs menu control)        		  http://apphp.com #
##  -- openWYSIWYG v1.4.7 (WYSIWYG editor)              http://openWebWare.com #
##  -- TinyMCE (WYSIWYG editor)                   http://tinymce.moxiecode.com #
##  -- Crystal Project Icons (icons set)               http://www.everaldo.com #
##  -- Securimage v2.0 BETA (captcha script)         http://www.phpcaptcha.org #
##  -- jQuery 1.4.2 (New Wave Javascript)             		 http://jquery.com #
##  -- Google AJAX Libraries API                  http://code.google.com/apis/ #
##  -- Lytebox v3.22                             http://www.dolem.com/lytebox/ #
##  -- JsCalendar v1.0 (DHTML/JavaScript Calendar)      http://www.dynarch.com #
##  -- RokBox System 			  				   http://www.rockettheme.com/ #
##  -- VideoBox	  						   http://videobox-lb.sourceforge.net/ #
##  -- CrossSlide jQuery plugin v0.6.2 	by Tobia Conforto                      #
##                                                                             #
################################################################################

// *** check if database connection parameters file exists
if(!file_exists("include/base.inc.php")){
	header("location: install.php");
	exit;
}

## uncomment, if your want to prevent "Web Page exired" message when use $submission_method = "post";
// session_cache_limiter('private, must-revalidate');    

// *** set flag that this is a parent file
define("APPHP_EXEC", "access allowed");

require_once("include/base.inc.php");
require_once("include/connection.php");

// *** call handler if exists
// -----------------------------------------------------------------------------
if((Application::Get("page") != "") && file_exists("page/handlers/handler_".Application::Get("page").".php")){
	include_once("page/handlers/handler_".Application::Get("page").".php");
}else if((Application::Get("client") != "") && file_exists("client/handlers/handler_".Application::Get("client").".php")){
	if(Modules::IsModuleInstalled("clients")){	
		include_once("client/handlers/handler_".Application::Get("client").".php");
	}
}else if((Application::Get("admin") != "") && file_exists("admin/handlers/handler_".Application::Get("admin").".php")){
	include_once("admin/handlers/handler_".Application::Get("admin").".php");
}

// *** get site content
// -----------------------------------------------------------------------------
if(Application::Get("page") != "booking_notify_paypal" && Application::Get("page") != "booking_notify_2co"){	
	$cachefile = "";
	if(@$objSettings->GetParameter("caching_allowed") && !@$objLogin->IsLoggedIn()){
		$c_page        = Application::Get("page");
		$c_page_id     = Application::Get("page_id");
		$c_system_page = Application::Get("system_page");
		$c_album_code  = Application::Get("album_code");
		$c_news_id     = Application::Get("news_id");
		$c_client      = Application::Get("client");
		$c_admin       = Application::Get("admin");

		if(($c_page == "" && $c_client == "" && $c_admin == "") || 
		   ($c_page == "pages" && $c_page_id != "") || 
		   ($c_page == "news" && $c_news_id != "") ||
		   ($c_page == "gallery" && $c_album_code != "")
		   )
		{	
			$cachefile = md5($c_page."-".
							 $c_page_id."-".
							 $c_system_page."-".
							 $c_album_code."-".
							 $c_news_id."-".
							 Application::Get("lang")."-".
							 Application::Get("currency_code")).".cch";	
			if($c_page == "news" && $c_news_id != ""){
				$objTempNews = new News();
				if(!$objTempNews->CacheAllowed($c_news_id)) $cachefile = "";			
			}else{
				$objTempPage = new Pages((($c_system_page != "") ? $c_system_page : $c_page_id));
				if(!$objTempPage->CacheAllowed()) $cachefile = "";			
			}			
			if(start_caching($cachefile)) exit;
		}
	}
	require_once("templates/".Application::Get("template")."/default.php");
	if(@$objSettings->GetParameter("caching_allowed") && !@$objLogin->IsLoggedIn()) finish_caching($cachefile);
}

echo "\n<!-- This script was generated by ApPHP HotelSite v".CURRENT_VERSION." (http://www.apphp.com/php-hotel-site/) -->";
?>