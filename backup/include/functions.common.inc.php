<?php

// GLOBAL FUNCTIONS 24.11.2010

/**
 * 	Returns time difference
 * 		@param first_time
 * 		@param last_time
 **/
function time_diff($last_time, $first_time)
{
	// convert to unix timestamps
	$time_diff=strtotime($last_time)-strtotime($first_time);	
	return $time_diff;
}

/**
 * 	Creates a random string with characters 1-10 and a-z
 * 		@param length - the length of the random string 	
 **/
function random_string($length = 10)
{
	$rand_string = "";
	for($i = 0; $i < $length; $i++){
		$x = mt_rand(0, 35);
		if($x > 9) $rand_string .= chr($x + 87);
		else $rand_string .= $x;
	}
	return $rand_string;
}

/**
 *	Draws messages 
 *  	@param $message - message text
 *      @param $is_draw
 **/
function draw_message($message, $is_draw=true, $bullet = false, $br = false, $style = "")
{
	if(!empty($style)) $style = " style='".$style."'";
	if(!empty($message)) $message = "<table width='100%' align='center' class='message_box'".$style." border='0' cellspacing='1' cellpadding='1'><tr>".(($bullet) ? "<td class='message_sign'><img src='images/attention_sign.gif' alt=\"\" border='0' /></td>" : "")."<td class='message_text".((!$bullet) ? "_single" : "")."'>".$message."</td></tr></table>".(($br == true) ? "<br>" : "");
	if(!$is_draw) return $message;
	else echo $message;	
}

/**
 *	Draws important messages 
 *  	@param $message - message text
 *      @param $is_draw
 **/
function draw_important_message($message, $is_draw=true, $bullet = true, $br = false, $style = "")
{
	if(!empty($style)) $style = " style='".$style."'";
	if(!empty($message)) $message = "<table width='100%' align='center' class='important_message_box'".$style." border='0' cellspacing='1' cellpadding='1'><tr>".(($bullet) ? "<td class='message_sign'><img src='images/error_sign.gif' alt=\"\" border='0' /></td>" : "")."<td class='message_text".((!$bullet) ? "_single" : "")."'>".$message."</td></tr></table>".(($br == true) ? "<br />" : "");
	if(!$is_draw) return $message;
	else echo $message;	
}

/**
 *	Draws success messages 
 *  	@param $message - message text
 *      @param $is_draw
 **/
function draw_success_message($message, $is_draw=true, $bullet = false, $br = false)
{
	if(!empty($message)) $message = "<table width='100%' align='center' class='success_message_box' border='0' cellspacing='1' cellpadding='1'><tr><td class='message_sign'><img src='images/success_sign.gif' alt=\"\" /></td><td class='message_text'>" . $message . "</td></tr></table>".(($br == true) ? "<br />" : "");
	if(!$is_draw) return $message;
	else echo $message;	
}

/**
 *	Draws reservation bar
 *  	@param $current_tab
 **/
function draw_reservation_bar($current_tab = "", $is_draw = true, $links_allowed = true)
{	
	global $objLogin;
	
	$selected_rooms_link = false;
	$booking_details_link = false;
	$reservation_link = false;
	$payment_link = false;
	if($links_allowed){
		if($current_tab == "booking_details"){
			$selected_rooms_link = true;
		}else if($current_tab == "reservation"){
			$booking_details_link = true;			
			$selected_rooms_link = true;
		}else if($current_tab == "payment"){
			$booking_details_link = false;			
			$selected_rooms_link = false;
			$reservation_link = true;
		}		
	}
	
	$output = "<table class='reservation_tabs' align='center' border='0'>";
	$output .= "<tr>";
	$output .= " <td class='".(($current_tab == "selected_rooms") ? "reservation_tab_active" : "reservation_tab")."'>".(($selected_rooms_link) ? "<a href='index.php?page=booking'>"._SELECTED_ROOMS."</a>" : _SELECTED_ROOMS)."</td>";
	$output .= " <td class='".(($current_tab == "booking_details") ? "reservation_tab_active" : "reservation_tab")."'>".(($booking_details_link) ? "<a href='index.php?page=booking_details".($objLogin->IsLoggedInAsAdmin() ? "" : "&m=edit")."'>"._BOOKING_DETAILS."</a>" : _BOOKING_DETAILS)."</td>";
	$output .= " <td class='".(($current_tab == "reservation") ? "reservation_tab_active" : "reservation_tab")."'>"._RESERVATION."</td>";
	$output .= " <td class='".(($current_tab == "payment") ? "reservation_tab_active" : "reservation_tab")."'>"._PAYMENT."</td>";
	$output .= "</tr>";
	$output .= "</table>";
	if(!$is_draw) return $output;
	else echo $output;		
}

/**
 *	Draws title bar
 *  	@param $title_1
 *  	@param $title_2
 *      @param $draw
 **/
function draw_title_bar($title_1, $title_2 = "", $draw = true)
{
	global $objLogin;
	
	$tag = ($objLogin->IsLoggedInAsAdmin() || Application::Get("preview") == "yes") ? "h2" : "h1";
	
	$output = "";
	if(!empty($title_1) && empty($title_2)){
		$output = "<".$tag." class='center_box_heading".((Application::Get("lang_dir") == "ltr") ? " align_left" :  " align_right")."'>".$title_1."</".$tag.">";
	}else if(!empty($title_1) && !empty($title_2)){
		$output = "<".$tag." class='center_box_heading".((Application::Get("lang_dir") == "ltr") ? " align_left" :  " align_right")."'>";
		$output .= "<table align='center' border='0' width='100%' cellspacing='0' cellpadding='0'>
			<tr>
			<td align='".Application::Get("defined_left")."'>".$title_1."</td>
			<td align='".Application::Get("defined_right")."'>".$title_2."</td>
			</tr>
			</table>";
		$output .= "</".$tag.">";
	}
	if($draw) echo $output;
	else return $output;
}

/**
 *	Draws sub title bar
 *  	@param $title
 *  	@param $class
 *      @param $template
 **/
function draw_sub_title_bar($title, $draw = true, $tag = "h3")
{
	$output = "";	
	if (!empty($title)){
		$output = "<".$tag." class='center_box_sub_heading'><span>".$title."</span></".$tag.">";
	}
	if($draw) echo $output;
	else return $output;
}

/**
 *	Draws content wrapper - start
 **/
function draw_content_start()
{
	echo "<div class='center_box_content'>";
}

/**
 *	Draws content wrapper - end
 **/
function draw_content_end()
{
	echo "</div>";
}

/**
 *	Draws line
 *  	@param $class
 *  	@param $image_directory
 **/
function draw_line($class = "no_margin_line", $image_directory = IMAGE_DIRECTORY, $draw = true)
{
	$result = "<div class='".$class."'><img src='".$image_directory."line_spacer.gif' width='100%' height='1px' alt=\"\" /></div>";
	if($draw) echo $result;
	else return $result;
}

/**
 *	Highlight rows
 *  	@param $offset
 **/
function highlight($offset = 1)
{
	if (!isset($GLOBALS['highlight_count'])) reset_highlight();
	if (($GLOBALS['highlight_count'] + $offset) % 2  == 0) $highlight = " class='highlight_light'";
	else $highlight = " class='highlight_dark'";
	$GLOBALS['highlight_count']++;
	return $highlight;
}

/**
 *	Reset highlighting rows
 **/
function reset_highlight()
{
	$GLOBALS['highlight_count'] = 0;
}

/**
 *  Draws dropdown box with numbers
 *  Output 'select' tag with its field name, values and default value(for numeric fields)
 *  	@param $field_name
 *  	@param $field_value
 *  	@param $start
 *      @param $end
 *  	@param $step
 *  	@param $class
 *  	@param $return
 **/
function draw_numbers_select_field($field_name, $field_value, $start, $end, $step = 1, $class = "", $return = false)
{
	$output = "<select name='$field_name' class='$class'>";
	$options = "";
	for ($i = $start; $i <= $end; $i = $i + $step) {
		$options .= "<option value='" . $i . "' ";
		$options .= ($i == $field_value) ? "selected='selected' " : "";
		$options .= ">" . $i . "</option>";
	}
	if($options == "") $options .= _NOT_AVAILABLE;
	$output .= $options."</select>";
	if($return) return $output;
	else echo $output;
}

/**
 *  Draws dropdown box with date selection
 *  	@param $field_name
 *  	@param $field_value
 *  	@param $return
 **/
function draw_date_select_field($field_name, $field_value = "", $min_year = "90", $max_year = "10", $draw = true)
{
	$lang = array();
	$lang['months'][1] = (defined('_JANUARY')) ? _JANUARY : "January";
	$lang['months'][2] = (defined('_FEBRUARY')) ? _FEBRUARY : "February";
	$lang['months'][3] = (defined('_MARCH')) ? _MARCH : "March";
	$lang['months'][4] = (defined('_APRIL')) ? _APRIL : "April";
	$lang['months'][5] = (defined('_MAY')) ? _MAY : "May";
	$lang['months'][6] = (defined('_JUNE')) ? _JUNE : "June";
	$lang['months'][7] = (defined('_JULY')) ? _JULY : "July";
	$lang['months'][8] = (defined('_AUGUST')) ? _AUGUST : "August";
	$lang['months'][9] = (defined('_SEPTEMBER')) ? _SEPTEMBER : "September";
	$lang['months'][10] = (defined('_OCTOBER')) ? _OCTOBER : "October";
	$lang['months'][11] = (defined('_NOVEMBER')) ? _NOVEMBER : "November";
	$lang['months'][12] = (defined('_DECEMBER')) ? _DECEMBER : "December";

	$datetime_format = "Y-m-d";
	
	if(strlen($field_value) < 10) $field_value = "";		
	$year = substr($field_value, 0, 4);
	$month = substr($field_value, 5, 2);
	$day = substr($field_value, 8, 2);

	$arr_ret_date = array();
	$arr_ret_date["y"] = "<select name='".$field_name."__nc_year' id='".$field_name."__nc_year'><option value=''>"._YEAR."</option>"; for($i=@date("Y")-$min_year; $i<=@date("Y")+$max_year; $i++) { $arr_ret_date["y"] .= "<option value='".$i."'".(($year == $i) ? " selected='selected'" : "").">".$i."</option>"; }; $arr_ret_date["y"] .= "</select>";                            
	$arr_ret_date["m"] = "<select name='".$field_name."__nc_month' id='".$field_name."__nc_month'><option value=''>"._MONTH."</option>"; for($i=1; $i<=12; $i++) { $arr_ret_date["m"] .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($month == $i) ? " selected='selected'" : "").">".$lang['months'][$i]."</option>"; }; $arr_ret_date["m"] .= "</select>";
	$arr_ret_date["d"] = "<select name='".$field_name."__nc_day' id='".$field_name."__nc_day'><option value=''>"._DAY."</option>"; for($i=1; $i<=31; $i++) { $arr_ret_date["d"] .= "<option value='".(($i < 10) ? "0".$i : $i)."'".(($day == $i) ? " selected='selected'" : "").">".(($i < 10) ? "0".$i : $i)."</option>"; }; $arr_ret_date["d"] .= "</select>";

	$output  = $arr_ret_date[strtolower(substr($datetime_format, 0, 1))];
	$output .= $arr_ret_date[strtolower(substr($datetime_format, 2, 1))];
	$output .= $arr_ret_date[strtolower(substr($datetime_format, 4, 1))];
	
	if($draw) echo $output;
	else return $output;
}

/**
 *  Draws select box 
 *  Output 'select' tag 
 *  	@param $field_name
 *  	@param $select_array
 *  	@param $option_value
 *  	@param $option_name
 *  	@param $selected_item
 *  	@param $class
 **/
function get_languages_box($field_name, $select_array, $option_value, $option_name, $selected_item = "", $class = "", $on_event = "")
{
	$options = "<select name='$field_name' class='$class' $on_event>";	
	foreach($select_array as $key => $val){
		$options .= "<option value='" . $val[$option_value] . "' ";
		$options .= ($selected_item == $val[$option_value]) ? "selected='selected' " : "";
		$options .= ">" . $val[$option_name] . "</option>";
	}
	if($options=="")$options .= "-- select --";
	return $options."</select>";
}

/**
 *  Get random string
 *  	@param $length
 **/
function get_random_string($length = 20)
{
	$template = "1234567890abcdefghijklmnopqrstuvwxyz";
	settype($template, "string");
	settype($length, "integer");
	settype($rndstring, "string");
	settype($a, "integer");
	settype($b, "integer");           
	for ($a = 0; $a <= $length; $a++) {
		$b = rand(0, strlen($template) - 1);
		$rndstring .= $template[$b];
	}       
	return $rndstring;       
}

/**
 *  Draw hidden fields
 *  	@param $field_name
 *  	@param $field_value
 **/
function draw_hidden_field($field_name, $field_value = "", $return = false)
{
	$output = "<input type='hidden' name='$field_name' id='$field_name' value='$field_value' />";
	if($return) return $output;
	else echo $output;
}

/**
 *  Draw token hidden field (protection agains csrf attaks)
 **/
function draw_token_field($return = false)
{
	$output = "<input type='hidden' name='token' value='".Application::Get("token")."' />";
	if($return) return $output;
	else echo $output;
}

/**
 *  Draw top block 
 **/
function draw_block_top($block_name = "", $ind = "", $status = "maximized")
{
	global $objLogin;
	$block_top_image = "";
	$block_middle_image = "";
	$display = "";

	if($objLogin->IsLoggedInAsAdmin()){
		echo "<div class='left_box_container' id='categories'>";
		if($ind != ""){
			echo "<h3 class='left_box_heading".((Application::Get("lang_dir") == "ltr") ? " left_box_heading_bg" : " left_box_heading_bg_rtl")."' id='categoriesHeading' onclick='toggle_menu_block(".$ind.")'>".$block_name."</h3>";
		}else{
			echo "<h3 class='left_box_heading".((Application::Get("lang_dir") == "ltr") ? " left_box_heading_bg" : " left_box_heading_bg_rtl")."' id='categoriesHeading'>".$block_name."</h3>";
		}
		if(Application::Get("preview") != "yes"){
			$display = (isset($_COOKIE['side_box_content_'.$ind]) && ($_COOKIE['side_box_content_'.$ind] == "maximized")) ? "" : "none";
			if($display == "" && $status == "maximized") $display = "";
		}
		echo "<div id='side_box_content_".$ind."' class='side_box_content".((Application::Get("lang_dir") == "ltr") ? " left" :  " right")."' style='display:".$display.";'>";
	}else{
		echo "<div class='left_box_container'>";
		echo "<h3 class='left_box_heading".((Application::Get("lang_dir") == "ltr") ? " left_box_heading_bg" : " left_box_heading_bg_rtl")."'>".$block_name."</h3>";
		echo "<div class='side_box_content".((Application::Get("lang_dir") == "ltr") ? " left" :  " right")."'>";
	}
}

/**
 *  Draw top enpty block 
 **/
function draw_block_top_empty()
{
	global $objLogin;
	if(!@$objLogin->IsLoggedInAsAdmin()){
		$width = "style='width:203px;";
	}else{
		$width = "style='width:195px;";
	}
	echo "<div class='left_box_container' id='categories' ".$width." padding-left:0px; padding-top:5px; padding-bottom:5px;'>
		  <div class='side_box_content'>";				
}


/**
 *  Draw bottom block 
 **/
function draw_block_bottom()
{
	echo "</div>";
	echo "</div>";
}

/**
 *  Draw block footer 
 **/
function draw_block_footer()
{
	echo "<div>&nbsp;</div>";
}

/**
 *  Retuerns divider image
 **/
function get_divider()
{
	return "<img src='images/divider.gif' width='1px' height='10px' alt=\"\" style='margin:auto;' />";
}

/**
 *  Camel Case
 *  	@param $string
 **/
function camel_case($string)
{
	if(function_exists('mb_convert_case')){
		return mb_convert_case($string, MB_CASE_TITLE, mb_detect_encoding($string));				
	}else{
		return $string;				
	}	
}

/**
 *  Create SEO url from string
 *  	@param $string
 **/
function create_seo_url($string = "")
{
	$forbidden_simbols = array("\\", '"', "'", "(", ")", "[", "]", "*", ".", ",", "&", ";", ":", "&amp;", "?", "!", "=");

	$string = str_replace($forbidden_simbols, "", $string);
	$splitted_string = explode(" ", $string);
	$seo_url = "";
	$words_counter = 0;
	foreach($splitted_string as $key){
		if(trim($key) != ""){
			if($words_counter++ < 6){
				$seo_url .= ($seo_url != "") ? "-".$key : $key;   
			}else{
				break;   
			}               
		}           
	}
	return substr($seo_url, 0, 125);
}

/**
 *  Get base URL 
 **/
function get_base_url()
{
	$protocol = "http://";
	$port = "";
	$http_host = $_SERVER['HTTP_HOST'];
	if((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != "off")) ||
		strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == "https"){
		$protocol = "https://";
	}	
	if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != "80"){
        if(strpos($_SERVER['HTTP_HOST'], ":")) $port = ":".$_SERVER['SERVER_PORT'];
	}	
	$folder = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);	
	return $protocol.$http_host.$port.$folder;
}

/**
 *  Get page URL 
 **/
function get_page_url() {
	$protocol = "http://";
	$port = "";
	$http_host = $_SERVER['HTTP_HOST'];
	if((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != "off")) ||
		strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == "https"){
		$protocol = "https://";
	}	
	if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != "80"){
        if(!strpos($_SERVER['HTTP_HOST'], ":")){
			$port = ":".$_SERVER['SERVER_PORT'];
		}
	}		
	// fixed for work with both Apache and IIS
	if(!isset($_SERVER['REQUEST_URI'])){	
		$uri = substr($_SERVER['PHP_SELF'],0);
		if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {
			$uri .= "?".$_SERVER['QUERY_STRING'];
		}
	}else{
		$uri = prepare_input($_SERVER["REQUEST_URI"]);	
	}	
	if(isset($_GET['p'])){
		$uri = str_replace("&p=".(int)$_GET['p'], "", $uri);
	}
	return $protocol.$port.$http_host.$uri;
}

/**
 *  Read subfolders of directory
 **/
function read_directory_subfolders($dir = "."){
	$folder=dir($dir); 
	$arrFolderEntries = array();
	while($folderEntry=$folder->read()){
		if($folderEntry != '.' && $folderEntry != '..' && is_dir($dir.$folderEntry) && strtolower($folderEntry) != "admin") 
			$arrFolderEntries[] = $folderEntry; 
	}     
	$folder->close(); 
	return $arrFolderEntries;
}

/**
 *  Cut string by last word
 **/
function substr_by_word($text, $length = "0", $three_dots = false, $lang = "en")
{
	$output = substr($text, 0, (int)$length);
	if(strlen($text) > $length){
		$blank_pos = strrpos($output, " ");		
        if($lang == "en"){
            if($blank_pos > 0) $output = substr($output, 0, $blank_pos);		
        }else{
			if($blank_pos > 0) $output = mb_substr($text, 0, $length, 'UTF-8');
        }
		if($three_dots) $output .= "...";
	}
	return $output;
}

/**
 *  Get current IP
 **/
function get_current_ip()
{
	if(isset($_SERVER['HTTP_X_FORWARD_FOR']) && $_SERVER['HTTP_X_FORWARD_FOR']) { 
		$user_ip = $_SERVER['HTTP_X_FORWARD_FOR'];
	} else {
		$user_ip = $_SERVER['REMOTE_ADDR'];
	}
	return $user_ip;
}

/**
 *  Format datetime
 */
function format_datetime($datetime, $format = "", $empty_text = "")
{
	$format = ($format == "") ? get_date_format() : $format;
	if(($datetime != "" && $datetime != "0000-00-00" && $datetime != "0000-00-00 00:00:00")){
		$datetime_new = mktime(substr($datetime, 11, 2), substr($datetime, 14, 2),
						   substr($datetime, 17, 2), substr($datetime, 5, 2),
						   substr($datetime, 8, 2), substr($datetime, 0, 4));
		return date($format, $datetime_new);						
	}else{
		return $empty_text;
	}
}

/**
 * Get date format
 */
function get_date_format($show_hours = true){
	global $objSettings;	
	
	if($objSettings->GetParameter("date_format") == "dd/mm/yyyy"){
		$date_format = ($show_hours) ? "d M, Y g:i A" : "d M, Y";
	}else{
		$date_format = ($show_hours) ? "M d, Y g:i A" : "M d, Y";
	}
	return $date_format;
}


/**
 * Prepare pagination part #1
 **/
function pagination_prepare($page_size, $from_sql, &$start_row, &$total_pages){
	$total_products = 0;
	$current_page 	= isset($_GET['p']) ? (int)$_GET['p'] : "1";

	$sql = "SELECT COUNT(*) as cnt FROM ".$from_sql;
	$pages_result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
	$total_products = $pages_result['cnt'];
	
	$total_pages = (int)($total_products / $page_size);
	
	if(!is_numeric($current_page) || (int)$current_page <= 0) $current_page = 1;
	if($current_page > ($total_pages+1)) $current_page = 1;
	if(($total_products % $page_size) != 0) $total_pages++;
	
	$start_row = ($current_page - 1) * $page_size;
}

/**
 * Prepare pagination part #2
 **/

function pagination_get_links($total_pages, $url){
	$current_page 		= isset($_GET['p']) ? (int)$_GET['p'] : "1";
	$output = "";
	
	if($total_pages > 1){
		$output .= "<div class='pagging'>&nbsp;"._PAGES.": ";
		for($page_ind = 1; $page_ind <= $total_pages; $page_ind++){
			$output .= "<a class='pagging_link' href='".$url."&p=".$page_ind."'>".(($page_ind == $current_page) ? "<b>[".$page_ind."]</b>" : $page_ind)."</a> ";
		}
		$output .= "</div>"; 
	}
	return $output;	
}

function draw_months_select_box($field_name, $field_value, $class="", $month_names=false, $draw=true){
	$output = "<select name='".$field_name."' ".(($class != '') ? " class='".$class."'" : "").">";
	$options = "";
	for($i = 1; $i <= 12; $i++){
		$options .= "<option value='".convert_to_decimal($i)."'";		
		$options .= ($i == $field_value) ? " selected='selected'" : "";
		$options .= ">".(($month_names) ? get_month_local($i) : convert_to_decimal($i))."</option>";
	}
	$output .= $options."</select>";
	if($draw) echo $output;	
	else return $output;
}

function draw_years_select_box($field_name, $field_value, $class="", $draw=true){
	$output = "<select name='".$field_name."' ".(($class != '') ? " class='".$class."'" : "").">";
	$options = "";
	for($i = date("Y"); $i <= date("Y") + 5; $i++){
		$options .= "<option value='".$i."'";		
		$options .= ($i == $field_value) ? " selected='selected'" : "";
		$options .= ">".$i."</option>";
	}
	$output .= $options."</select>";
	if($draw) echo $output;	
	else return $output;
}

function get_month_local($mon){
	$months = array(
		"1" => _JANUARY,
		"2" => _FEBRUARY,
		"3" => _MARCH,
		"4" => _APRIL,
		"5" => _MAY,
		"6" => _JUNE,
		"7" => _JULY,
		"8" => _AUGUST,
		"9" => _SEPTEMBER,
		"10" => _OCTOBER,
		"11" => _NOVEMBER,
		"12" => _DECEMBER
	);
	return isset($months[(int)$mon]) ? $months[(int)$mon] : "";
}

function nights_diff($datefrom, $dateto){
	$datefrom = strtotime($datefrom, 0);
	$dateto = strtotime($dateto, 0);
	$difference = $dateto - $datefrom; // Difference in seconds
     
    $datediff = floor($difference / 86400);
	return $datediff;
}

/***
 * Draw breadcrumbs
 **/
function prepare_breadcrumbs($breadcrumbs)
{
	$output = "";
	if(is_array($breadcrumbs)){
		foreach($breadcrumbs as $key => $val){
			if(!empty($key)){
				if(!empty($output)) $output .= " &raquo; ";
				if(!empty($val)) $output .= "<a class='cbh' href='".$val."'>".$key."</a>";
				else $output .= $key;
			}
		}	
	}
	return $output;
}

/**
 *	Remove bad chars from input
 *	  	@param $str_words - input
 **/
function prepare_input($str_words, $escape = false, $level = "high")
{
	$found = false;
	if($level == "low"){
		$bad_string = array("%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<applet", "<meta", "<style", "<form", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "include_path", "prefix", "ftp://", "smb://", "onmouseover=", "onmouseout=");
	}else if($level == "medium"){
		$bad_string = array("xp_", "%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "script>", "<iframe", "<applet", "<meta", "<style", "<form", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "ftp://", "smb://", "onmouseover=", "onmouseout=");		
	}else{
		$bad_string = array("select", "drop", "--", "insert", "xp_", "%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "script>", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "http://", "https://", "ftp://", "smb://", "onmouseover=", "onmouseout=");
	}
	for($i = 0; $i < count($bad_string); $i++){
		$str_words = str_ireplace($bad_string[$i], "", $str_words);	
	}
	
	if($escape){
		$str_words = mysql_real_escape_string($str_words); 
	}
	
	return $str_words;            
}

function check_input($input, $level = "medium"){
	
	if($input == "") return true;
	
    $error = 0;
	$bad_string = array("%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "http://", "https://", "ftp://", "smb://" );
	foreach($bad_string as $string_value){
		if(strstr($input, $string_value)) $error = 1;
	}
	
	if((preg_match("/<[^>]*script*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*object*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*iframe*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*applet*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*meta*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*style*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*form*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*img*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*onmouseover*\"?[^>]*>/i", $input)) ||
		(preg_match("/<[^>]*body*\"?[^>]*>/i", $input)) ||
		(preg_match("/\([^>]*\"?[^)]*\)/i", $input)) || 
		(preg_match("/ftp:\/\//i", $input)) || 
		(preg_match("/https:\/\//i", $input)) || 
		(preg_match("/http:\/\//i", $input)) )
	{		
		$error = 1;
	}
	
	$ss = $_SERVER['HTTP_USER_AGENT'];
	
	if((preg_match("/libwww/i",$ss)) ||
	    (preg_match("/^lwp/i",$ss))  ||
	    (preg_match("/^Jigsaw/i",$ss)) ||
	    (preg_match("/^Wget/i",$ss)) ||
	    (preg_match("/^Indy\ Library/i",$ss)) )
	{ 
	    $error = 1;
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if(!empty($_SERVER['HTTP_REFERER'])){
			if(!preg_match("/".$_SERVER['HTTP_HOST']."/i", $_SERVER['HTTP_REFERER'])) $error = 1;
		}
	}
    if($error){
        return "";
    }
	return true;
}

/**
 * Start Caching of page
 *      @param $cachefile - name of file to be cached
 */
function start_caching($cachefile)
{
	global $objSettings;	

	$cache_lifetime = (int)@$objSettings->GetParameter("cache_lifetime");
	
	if($cachefile != "" && file_exists(CACHE_DIRECTORY.$cachefile)) {        
		$cachetime = $cache_lifetime * 60; /* cache lifetime in minutes */
		// Serve from the cache if it is younger than $cachetime
		if(file_exists(CACHE_DIRECTORY.$cachefile) && (filesize(CACHE_DIRECTORY.$cachefile) > 0) && ((time() - $cachetime) < filemtime(CACHE_DIRECTORY.$cachefile))){
			// the page has been cached from an earlier request output the contents of the cache file
			include_once(CACHE_DIRECTORY.$cachefile); 
			echo "<!-- Generated from cache at ".@date('H:i', filemtime(CACHE_DIRECTORY.$cachefile))." -->\n";
			return true;
		}        
	}
	// start the output buffer
	ob_start();
}

/**
 * Finish Caching of page
 * 	    @param $cachefile - name of file to be cached
 */
function finish_caching($cachefile)
{
	if($cachefile != ""){
		$fp = @fopen(CACHE_DIRECTORY.$cachefile, 'w'); 
		@fwrite($fp, ob_get_contents());
		@fclose($fp); 
		// Send the output to the browser
		ob_end_flush();
		// check if we exeeded max number of cache files
		check_cache_files();
	}
}

/**
 * Delete all cache files
 */
function delete_cache()
{
	global $objSettings;	
	
	///if(!@$objSettings->GetParameter("caching_allowed")) return false;
	
	if($hdl = @opendir(CACHE_DIRECTORY)){
		while(false !== ($obj = @readdir($hdl))){
			if($obj == '.' || $obj == '..' || $obj == '.htaccess') continue; 
			@unlink(CACHE_DIRECTORY.$obj);
		}
	}
}    

/**
 * Check chache files
 */
function check_cache_files()
{		
	$files_count = 0;
	$oldest_file_name = "";
	$oldest_file_time = @date("Y-m-d H:i:s");

	if($hdl = opendir(CACHE_DIRECTORY)){
		while (false !== ($obj = @readdir($hdl))){
			if($obj == '.' || $obj == '..' || $obj == '.htaccess') continue; 
			$file_time = @date("Y-m-d H:i:s", filectime(CACHE_DIRECTORY.$obj));
			if($file_time < $oldest_file_time){
				$oldest_file_time = $file_time;
				$oldest_file_name = CACHE_DIRECTORY.$obj;
			}				
			$files_count++;
		}
	}		
	if($files_count > 100){
		@unlink($oldest_file_name);		
	}
}

/**
 *	Convert to decimal number with leading zero
 *  	@param $number
 */	
function convert_to_decimal($number)
{
	return (($number < 0) ? "-" : "").((abs($number) < 10) ? "0" : "").abs($number);
}

/**
 *	Get encoded text
 *		@param $string
 **/
function encode_text($string = "")
{
	$search	 = array("\\","\0","\n","\r","\x1a","'",'"',"\'",'\"');
	$replace = array("\\\\","\\0","\\n","\\r","\Z","\'",'\"',"\\'",'\\"');
	return str_replace($search, $replace, $string);
}

/**
 *	Get decoded text
 *		@param $string
 **/
function decode_text($string = "", $code_quotes = true, $quotes_type = "")
{
	$single_quote = "'";
	$double_quote = '"';		
	if($code_quotes){
		if(!$quotes_type){
			$single_quote = "&#039;";
			$double_quote = "&#034;";
		}else if($quotes_type == "single"){
			$single_quote = "&#039;";
		}else if($quotes_type == "double"){
			$double_quote = "&#034;";
		}
	}
	
	$search  = array("\\\\","\\0","\\n","\\r","\Z","\\'",'\\"','"',"'");
	$replace = array("\\","\0","\n","\r","\x1a","\'",'\"',$double_quote,$single_quote);
	return str_replace($search, $replace, $string);
}

/**
 * Prepare permanent link
 */
function prepare_permanent_link($href, $link, $target, $css_class="")
{
	$css_class = ($css_class != "") ? " class='".$css_class."'" : "";
	$target = ($target != "") ? " target='".$target."'" : "";
	
	return "<a".$css_class.$target." href='".$href."'>".$link."</a>";
}

/**
 * Prepare link
 */
function prepare_link($page_type, $page_id_param, $page_id, $page_url_name, $page_name, $css_class = "", $title = "")
{
	global $objSettings;
	
	$css_class = ($css_class != "") ? " class='".$css_class."'" : "";
	$title = ($title != "") ? " title='".decode_text($title)."'" : "";
	$page_url_name = str_replace(" ", "-", (($page_url_name != "") ? $page_url_name : $page_name));
	
	// Use SEO optimized link	
	if($objSettings->GetParameter("seo_urls") == "1"){
		return "<a".$css_class.$title." href='".$page_type.(($page_id != "") ? "/".$page_id : "")."/".$page_url_name.".html'>".$page_name."</a>";
	}else{
		return "<a".$css_class.$title." href='index.php?page=".$page_type."&amp;".$page_id_param."=".$page_id."'>".decode_text($page_name)."</a>";
	}
	
}

/**
 * Draw top banners code
 */
function draw_banners_top(&$banner_image, $show_always = true)
{
	$default_banner_image = "";
	
	if(Modules::IsModuleInstalled("banners")){
		$objBannersSet  = new ModulesSettings("banners");
		$is_banners_active = $objBannersSet->GetSettings("is_active");
		$rotate_delay	= $objBannersSet->GetSettings("rotate_delay");
		$rotation_type	= $objBannersSet->GetSettings("rotation_type");				
		
		$objBanners = new Banners();
		if($is_banners_active == "yes"){
			if($rotation_type == "slide show"){
				$arrBanners = $objBanners->GetBannersArray();
				if($show_always || (!$show_always && Application::Get("page") == "home" && Application::Get("client") == "" && Application::Get("admin") == "")){
					$output = "<script src='js/jquery.cross-slide.min.js' type='text/javascript'></script>\n";
					$output .= "<script type='text/javascript'>\n";
					$output .= "jQuery(function() {
						jQuery('#slideshow').crossSlide({
						  sleep: ".$rotate_delay.", fade: 2,variant: true
						}, [						
					";
					$ind = "0";
					foreach($arrBanners as $key => $val){
						if($ind == "0"){
							$default_banner_image = "images/banners/".$val['image_file'];
						}else{
							$output .= ",";
						}
						$output .= "{ src: 'images/banners/".$val['image_file']."', alt: '".encode_text($val['image_text']).(($val['link_url'] != "") ? "##".$val['link_url'] : "")."', to: 'up' }";
						$ind++;
					}
					$output .= "], function(idx, img, idxOut, imgOut) {
						if(idxOut == undefined){
						  // starting single image phase, put up caption
						  if(img.alt != ''){
							
						  }
						}else{
						  
						}}) });";
					$output .= "</script>\n";
					if($ind == 1){
						$banner_image = "<div class='banners-box-random' id='slideshow'>".$objBanners->GetRandomBanner()."</div>";
					}else{
						echo $output;
						$banner_image = "<div class='banners-box-slideshow' id='slideshow'></div><div class='slideshow-caption'></div>";						
					}
				}
			}else{
				if($show_always || (!$show_always && Application::Get("page") == "home" && Application::Get("client") == "" && Application::Get("admin") == "")){
					$banner_image = "<div class='banners-box-random' id='slideshow'>".$objBanners->GetRandomBanner()."</div>";
				}
			}					
		}
	}
}

/**
 * Returns timezone by offset
 */
function get_timezone_by_offset($offset)
{
	$zonelist = array
	(
		'Kwajalein' => -12.00,
		'Pacific/Midway' => -11.00,
		'Pacific/Honolulu' => -10.00,
		'Pacific/Marquesas' => -9.50,
		'America/Anchorage' => -9.00,
		'America/Los_Angeles' => -8.00,
		'America/Denver' => -7.00,
		'America/Tegucigalpa' => -6.00,
		'America/New_York' => -5.00,
		'America/Caracas' => -4.50,
		'America/Halifax' => -4.00,
		'America/St_Johns' => -3.50,
		'America/Argentina/Buenos_Aires' => -3.00,
		//'America/Sao_Paulo' => -3.00,
		'Atlantic/South_Georgia' => -2.00,
		'Atlantic/Azores' => -1.00,
		'Europe/Dublin' => 0,
		'Europe/Belgrade' => 1.00,
		'Europe/Minsk' => 2.00,
		'Asia/Kuwait' => 3.00,
		'Asia/Tehran' => 3.50,
		'Asia/Muscat' => 4.00,
		'Asia/Kabul' => 4.50,
		'Asia/Yekaterinburg' => 5.00,
		'Asia/Kolkata' => 5.50,
		'Asia/Katmandu' => 5.75,
		'Asia/Dhaka' => 6.00,
		'Asia/Rangoon' => 6.50,
		'Asia/Krasnoyarsk' => 7.00,
		'Asia/Brunei' => 8.00,
		'Asia/Seoul' => 8.75,		
		'Asia/Seoul' => 9.00,
		'Australia/Darwin' => 9.50,
		'Australia/Canberra' => 10.00,
		'Australia/Lord_Howe' => 10.50,
		'Asia/Magadan' => 11.00,
		'Pacific/Norfolk' => 11.50,
		'Pacific/Fiji' => 12.00,
		'Pacific/Chatham' => 12.75,
		'Pacific/Tongatapu' => 13.00,
		'Pacific/Kiritimati' => 14.00
	);
	$index = array_keys($zonelist, $offset);
	if(sizeof($index)!=1) return false;
	return $index[0];
} 

/**
 * Get OS name
 */
function get_os_name()
{
	// some possible outputs
	// Linux: Linux localhost 2.4.21-0.13mdk #1 Fri Mar 14 15:08:06 EST 2003 i686		
	// FreeBSD: FreeBSD localhost 3.2-RELEASE #15: Mon Dec 17 08:46:02 GMT 2001		
	// WINNT: Windows NT XN1 5.1 build 2600		
	// MAC: Darwin Ron-Cyriers-MacBook-Pro.local 10.6.0 Darwin Kernel Version 10.6.0: Wed Nov 10 18:13:17 PST 2010; root:xnu-1504.9.26~3/RELEASE_I386 i386
	$os_name = strtoupper(substr(PHP_OS, 0, 3));
	switch($os_name){
		case 'WIN':
			return "windows"; break;
		case 'LIN':
			return "linux"; break;
		case 'FRE':
			return "freebsd"; break;
		case 'DAR':
			return "mac"; break;
		default:
			return "windows"; break;
	}
}

?>