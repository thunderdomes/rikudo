<?php

/***
 *	Class Application
 *  -------------- 
 *  Description : encapsulates application properties and methods
 *  Usage       : HotelSite ONLY
 *  Updated	    : 31.05.2011
 *  Version     : 1.0.0
 *	Written by  : ApPHP
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	                        Init
 *	                        Param
 *	                        Get
 *	                        Set
 *
 *  ChangeLog:
 *  ----------
 *	1.0.0
 *		- added Set()
 *	    - 
 *	    - 
 *	    - 
 *	    -
 **/

class Application {
	
	private static $params = array(
		"template"        	=> "default",
		"tag_title"       	=> "",
		"tag_description" 	=> "",
		"tag_keywords"    	=> "",
		
		"defined_left" 	  	=> "left",
		"defined_right"   	=> "right",
		"defined_alignment" => "left",
		"preview"           => "",
		
		"admin"             => "",
		"client"            => "",
		"system_page"       => "",
		"type"              => "",
		"page"              => "",
		"page_id"           => "",
		
		"lang"              => "en",
		"lang_dir"          => "ltr",
		"lang_name"         => "English",
		
		"news_id"        	=> "",
		
		"album_code"        => "",
		
		"currency"          => "",
		"currency_code"     => "",
		"currency_symbol"   => "",
		"currency_rate"     => "",
		"currency_symbol_place" => "",
		
		"token"             => ""
	);

	
	//==========================================================================
    // Class Initialization
	//==========================================================================
	public static function Init()
	{
		global $objLogin, $objSettings, $objSiteDescription;

		self::$params["page"]        = isset($_GET['page']) ? prepare_input($_GET['page']) : "home";
		self::$params["page_id"]     = isset($_REQUEST['pid']) ? prepare_input($_REQUEST['pid']) : "home";
		self::$params["system_page"] = isset($_GET['system_page']) ? prepare_input($_GET['system_page']) : "";
		self::$params["type"]        = isset($_GET['type']) ? prepare_input($_GET['type']) : "";
		self::$params["admin"]  	 = isset($_GET['admin']) ? prepare_input($_GET['admin']) : "";
		self::$params["client"]		 = isset($_GET['client']) ? prepare_input($_GET['client']) : "";
		self::$params["news_id"]     = isset($_GET['nid']) ? (int)$_GET['nid'] : "";
		self::$params["album_code"]  = isset($_GET['acode']) ? prepare_input($_GET['acode']) : "";
		self::$params["lang"]        = isset($_GET['lang']) ? prepare_input($_GET['lang']) : "";
		self::$params["currency"]    = isset($_GET['currency']) ? prepare_input($_GET['currency']) : "";
		self::$params["token"]       = isset($_GET['token']) ? prepare_input($_GET['token']) : "";
		$req_preview    			 = isset($_GET['preview']) ? prepare_input($_GET['preview']) : "";


		//------------------------------------------------------------------------------
		// check and set token
		$token = md5(uniqid(rand(), true));	
		self::$params["token"] = $_SESSION['token'] = $token;
		
		//------------------------------------------------------------------------------
		// set language
		if(@$objLogin->IsLoggedInAsAdmin()){
			$pref_lang = @$objLogin->GetPreferredLang();
			if(Languages::LanguageExists($pref_lang, false)){
				self::$params["lang"] = $pref_lang;
			}else{
				self::$params["lang"] = Languages::GetDefaultLang();
			}
			self::$params["lang_dir"] = Languages::GetLanguageDirection(self::$params["lang"]);
			self::$params["lang_name"] = Languages::GetLanguageName(self::$params["lang"]);	
		}else{
			if(!@$objLogin->IsLoggedIn() && (self::$params["admin"] == "login" || self::$params["admin"] == "password_forgotten")){
				self::$params["lang"]      = Languages::GetDefaultLang();
				self::$params["lang_dir"]  = Languages::GetLanguageDirection(self::$params["lang"]);
				self::$params["lang_name"] = Languages::GetLanguageName(self::$params["lang"]);
			}else if(!empty(self::$params["lang"]) && Languages::LanguageExists(self::$params["lang"])){
				self::$params["lang"]      = $_SESSION['lang'] = self::$params["lang"];
				self::$params["lang_dir"]  = $_SESSION['lang_dir'] = Languages::GetLanguageDirection(self::$params["lang"]);
				self::$params["lang_name"] = $_SESSION['lang_name'] = Languages::GetLanguageName(self::$params["lang"]);
			}else if(isset($_SESSION['lang']) && isset($_SESSION['lang_dir']) && isset($_SESSION['lang_name'])){
				self::$params["lang"]      = $_SESSION['lang'];
				self::$params["lang_dir"]  = $_SESSION['lang_dir'];
				self::$params["lang_name"] = $_SESSION['lang_name'];
			}else{
				self::$params["lang"]      = Languages::GetDefaultLang();
				self::$params["lang_dir"]  = Languages::GetLanguageDirection(self::$params["lang"]);
				self::$params["lang_name"] = Languages::GetLanguageName(self::$params["lang"]);
			}
		}		

		//------------------------------------------------------------------------------
		// set currency
		if(!empty(self::$params["currency"]) && Currencies::CurrencyExists(self::$params["currency"])){
			self::$params["currency_code"]   = $_SESSION['currency_code'] = self::$params["currency"];
			$currency_info = Currencies::GetCurrencyInfo(self::$params["currency_code"]);
			self::$params["currency_symbol"] = $_SESSION['currency_symbol'] = $currency_info['symbol'];
			self::$params["currency_rate"]   = $_SESSION['currency_rate'] = $currency_info['rate'];
			self::$params["currency_symbol_place"] = $_SESSION['symbol_placement'] = $currency_info['symbol_placement'];
		}else if(isset($_SESSION['currency_code']) && isset($_SESSION['currency_symbol']) && isset($_SESSION['currency_rate']) && isset($_SESSION['symbol_placement'])){
			self::$params["currency_code"]   = $_SESSION['currency_code'];
			self::$params["currency_symbol"] = $_SESSION['currency_symbol'];
			self::$params["currency_rate"]   = $_SESSION['currency_rate'];
			self::$params["currency_symbol_place"] = $_SESSION['symbol_placement'];
		}else{
			$currency_info = Currencies::GetDefaultCurrencyInfo();
			self::$params["currency_code"]   = $currency_info['code'];
			self::$params["currency_symbol"] = $currency_info['symbol'];
			self::$params["currency_rate"]   = $currency_info['rate'];
			self::$params["currency_symbol_place"] = $currency_info['symbol_placement'];
		}

		// preview allowed only for admins
		// -----------------------------------------------------------------------------
		if(@$objLogin->IsLoggedInAsAdmin()){
			if($req_preview == "yes" || $req_preview == "no"){
				self::$params["preview"] = $_SESSION['preview'] = $req_preview;
			}else if((self::$params["admin"] == "") && isset($_SESSION['preview']) && ($_SESSION['preview'] == "yes" || $_SESSION['preview'] == "no")){
				self::$params["preview"] = $_SESSION['preview'];
			}else{
				self::$params["preview"] = $_SESSION['preview'] = "no";
			}
		}

		// *** draw offline message
		// -----------------------------------------------------------------------------
		if($objSettings->GetParameter("is_offline")){
			if(!@$objLogin->IsLoggedIn() && self::$params["admin"] != "login"){
				echo $objSettings->GetParameter("offline_message");
				exit;
			}
		}
		
		// *** run cron jobs file
		// -----------------------------------------------------------------------------
		if($objSettings->GetParameter("cron_type") == "non-batch"){
			include_once("cron.php");		
		}
		
		// *** default client page
		// -----------------------------------------------------------------------------
		if(@$objLogin->IsLoggedInAsClient()){
			if(self::$params["client"] == "" || self::$params["page"] == "") self::$params["client"] = "home";
		}		

		// *** get site template
		// -----------------------------------------------------------------------------
		self::$params["template"] = ($objSettings->GetTemplate() != "") ? $objSettings->GetTemplate() : DEFAULT_TEMPLATE;
		if(@$objLogin->IsLoggedInAsAdmin() && (self::$params["preview"] != "yes" || self::$params["admin"] != "")) self::$params["template"] = "admin";
		else if(!@$objLogin->IsLoggedIn() && (self::$params["admin"] == "login" || self::$params["admin"] == "password_forgotten")) self::$params["template"] = "admin";

		// *** use direction of selected language
		// -----------------------------------------------------------------------------
		self::$params["defined_left"]  = (self::$params["lang_dir"] == "ltr") ? "left" : "right";
		self::$params["defined_right"] = (self::$params["lang_dir"] == "ltr") ? "right" : "left";
		self::$params["defined_alignment"] = (self::$params["lang_dir"] == "ltr") ? "left" : "right";
		
		// *** get page parameters
		// -----------------------------------------------------------------------------
		if(self::$params["system_page"] != ""){
			$objPage = new Pages(self::$params["system_page"], true);			
		}else{
			$objPage = new Pages(self::$params["page_id"], true);
		}
		self::$params["tag_title"] = ($objPage->GetParameter("tag_title") != "") ? $objPage->GetParameter("tag_title") : $objSiteDescription->GetParameter("tag_title");
		self::$params["tag_keywords"] = ($objPage->GetParameter("tag_keywords") != "") ? $objPage->GetParameter("tag_keywords") : $objSiteDescription->GetParameter("tag_keywords");
		self::$params["tag_description"] = ($objPage->GetParameter("tag_description") != "") ? $objPage->GetParameter("tag_description") : $objSiteDescription->GetParameter("tag_description");

	}
	
	/**
	 * Returns parameter
	 * 		@param $param
	 */
	public static function Get($param = "")
	{
		return isset(self::$params[$param]) ? self::$params[$param] : "";
	}

	/**
	 * Set parameter value
	 * 		@param $param
	 */
	public static function Set($param = "", $val = "")
	{
		self::$params[$param] = $val;
	}

}
?>