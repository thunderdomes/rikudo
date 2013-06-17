<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$task    = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
$keyword = isset($_POST['keyword']) ? trim(prepare_input($_POST['keyword'], true)) : "";
$keyword = stripslashes($keyword);
$keyword = str_replace('"', "&#034;", $keyword);
$keyword = str_replace("'", "&#039;", $keyword);

if($keyword == _SEARCH_KEYWORDS."...") $keyword = "";
$p = isset($_POST['p']) ? (int)$_POST['p'] : "";
$search_in = (isset($_POST['search_in'])) ? prepare_input($_POST['search_in']) : "";

$objSearch = new Search();
$search_result = "";

$title_bar = "<table width='100%' align='center' cellspacing='0' cellpadding='0'>
	<tr>
		<td align='left'><b>"._SEARCH_RESULT_FOR.": ".$keyword."</b></td>
		<td align='right'>
			"._LOOK_IN.":
			<select class='look_in' name='sel_search_in' onchange=\"javascript:document.getElementById('search_in').value=this.value;\">
				<option value='site' ".(($search_in == "site") ? "selected='selected'" : "").">"._WHOLE_SITE."</option>
				<option value='news' ".(($search_in == "news") ? "selected='selected'" : "").">"._NEWS."</option>
				<option value='rooms' ".(($search_in == "rooms") ? "selected='selected'" : "").">"._ROOMS."</option>
			</select>
			&nbsp;&nbsp;
		</td>
	</tr>
	</table><br />";

// Check if there is a page 
if($keyword != ""){	
	echo $title_bar; 
	
	if($task == "quick_search"){
		$search_result = $objSearch->SearchBy($keyword, $p, $search_in);	
	}	
	$objSearch->DrawPopularSearches();
	$objSearch->DrawSearchResult($search_result, $p);
}else{
	draw_title_bar(_SEARCH_RESULT_FOR.": ".$keyword."");	
	draw_important_message(_NO_RECORDS_FOUND);		
}
	
?>