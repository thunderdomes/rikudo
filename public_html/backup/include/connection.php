<?php
@session_start();

//------------------------------------------------------------------------------
require_once("shared.inc.php");
require_once("settings.inc.php");
require_once("functions.database.inc.php");
require_once("functions.common.inc.php");
require_once("functions.validation.inc.php");

define('APPHP_BASE', get_base_url());

// autoloading classes
//------------------------------------------------------------------------------
function __autoload($class_name){
	require_once("classes/".$class_name.".class.php");
}

// Set time zone
//------------------------------------------------------------------------------
@date_default_timezone_set(TIME_ZONE);

// setup connection
//------------------------------------------------------------------------------
$database_connection = @mysql_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD) or die(((SITE_MODE == "development") ? mysql_error() : "Fatal Error: Please check database connection parameters!"));
@mysql_select_db(DATABASE_NAME, $database_connection) or die(((SITE_MODE == "development") ? mysql_error() : "Fatal Error: Please check your database exists!"));
// set collation
set_collation();
// set group_concat max length
set_group_concat_max_length();

$user_session 		= new Session();
$objLogin 			= new Login();
$objSettings 		= new Settings();
$objSiteDescription = new SiteDescription();
Application::Init();
Modules::Init();

// include files for administrator use only
//------------------------------------------------------------------------------
if(@$objLogin->IsLoggedInAsAdmin()){
	include_once("functions.admin.inc.php");
}

// set time zone
//------------------------------------------------------------------------------
$hotelInfo = Hotel::GetHotelInfo();
$time_zone = (isset($hotelInfo['time_zone'])) ? @get_timezone_by_offset($hotelInfo['time_zone']) : "";
if($time_zone) @date_default_timezone_set($time_zone);

// include language file
//------------------------------------------------------------------------------
if(get_os_name() == "windows"){
	$lang_file_path = str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME'])."include\messages.".Application::Get("lang").".inc.php";
}else{
	$lang_file_path = "include/messages.".Application::Get("lang").".inc.php";
}
if(file_exists($lang_file_path)){
	include_once($lang_file_path);
}else if(file_exists("include/messages.inc.php")){
	include_once("include/messages.inc.php");
}

?>