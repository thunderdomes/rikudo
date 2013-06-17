<?php

// ADMIN FUNCTIONS
// Updated: 23.02.2011

/**
 * 	Creates a rss file
 */
function prepare_rss()
{
	global $objSettings, $objSiteDescription;
	$myfeed = new RSSFeed();
	
	$myfeed->SetType($objSettings->GetParameter("rss_feed_type"));
	$myfeed->SetChannel(APPHP_BASE.'feeds/rss.xml', $objSiteDescription->GetParameter("header_text"), $objSiteDescription->GetParameter("tag_description"), 'en-us', '(c) copyright', $objSiteDescription->GetParameter("header_text"), $objSiteDescription->GetParameter("tag_description"));
	$myfeed->SetImage(APPHP_BASE.'images/icons/logo.png');
	
	$objNews = new News();
	$all_news = $objNews->GetAllNews("previous");
	
	for($i=0; $i < $all_news[1] && $i < 10; $i++){					
		$rss_text = RSSFeed::CleanTextRss(strip_tags($all_news[0][$i]['body_text']));
		if(strlen($rss_text) > 512) $rss_text = substr_by_word($rss_text, 512)."...";
		#$rss_text = htmlentities($post_text, ENT_COMPAT, 'UTF-8');
		$myfeed->SetItem(APPHP_BASE.'index.php?page=news&nid='.$all_news[0][$i]['id'], $all_news[0][$i]['header_text'], $rss_text, $all_news[0][$i]['date_created']);
	}
	$myfeed->SaveFeed();
}

/**
 *  Get random string
 *  	@param $tabid
 *  	@param $chart_type
 *  	@param $year
 **/
function get_chart_changer($tabid, $chart_type, $year, $page = "accounts_statistics"){
	$output = "<form action='index.php?admin=".$page."' name='frmStatistics' method='post'>	
		<input type='hidden' name='tabid' value='".$tabid."' />
		".draw_token_field(true)."		
		<table width='98%' align='center'>
		<tr>
			<td valign='middle' width='140px'>
			"._TYPE.": <select name='chart_type' onchange='frmStatistics_Submit();'>
				<option value='barchart' ".(($chart_type == "barchart") ? " selected='selected'" : "").">Barchart</option>
				<option value='columnchart' ".(($chart_type == "columnchart") ? " selected='selected'" : "").">ColumnChart</option>
				<option value='piechart' ".(($chart_type == "piechart") ? " selected='selected'" : "").">PieChart</option>
				<option value='areachart' ".(($chart_type == "areachart") ? " selected='selected'" : "").">AreaChart</option>
			</select>
			</td>
			<td valign='middle' width='140px'>
			"._YEAR.": <select name='year' onchange='frmStatistics_Submit();'>";
				for($y = date("Y")-5; $y < date("Y")+5; $y++){
					$output .= "<option value='".$y."' ".(($year == $y) ? " selected='selected'" : "").">".$y."</option>";
				}
				$output .= "</select>
			</td>
			<td></td>
		</tr>
		</table>
		</form>";	
	return $output;
}

/**
 *  Draws set values for statistics
 *  	@param $result
 *  	@param $chart_type
 *  	@param $chart_name
 **/
function draw_set_values($result, $chart_type, $chart_name, $pre_addition = ""){
	$res_month1 = (isset($result['month1']) && $result['month1'] != "") ? $result['month1'] : "0";
	$res_month2 = (isset($result['month2']) && $result['month2'] != "") ? $result['month2'] : "0";
	$res_month3 = (isset($result['month3']) && $result['month3'] != "") ? $result['month3'] : "0";
	$res_month4 = (isset($result['month4']) && $result['month4'] != "") ? $result['month4'] : "0";
	$res_month5 = (isset($result['month5']) && $result['month5'] != "") ? $result['month5'] : "0";
	$res_month6 = (isset($result['month6']) && $result['month6'] != "") ? $result['month6'] : "0";
	$res_month7 = (isset($result['month7']) && $result['month7'] != "") ? $result['month7'] : "0";
	$res_month8 = (isset($result['month8']) && $result['month8'] != "") ? $result['month8'] : "0";
	$res_month9 = (isset($result['month9']) && $result['month9'] != "") ? $result['month9'] : "0";
	$res_month10 = (isset($result['month10']) && $result['month10'] != "") ? $result['month10'] : "0";
	$res_month11 = (isset($result['month11']) && $result['month11'] != "") ? $result['month11'] : "0";
	$res_month12 = (isset($result['month12']) && $result['month12'] != "") ? $result['month12'] : "0";	

	$output  = "\n data.setValue(0, 0, '"._JANUARY." (".$pre_addition.$res_month1.")');";
	$output .= "\n data.setValue(0, 1, ".$res_month1.");";
	$output .= "\n data.setValue(1, 0, '"._FEBRUARY." (".$pre_addition.$res_month2.")');";
	$output .= "\n data.setValue(1, 1, ".$res_month2.");";
	$output .= "\n data.setValue(2, 0, '"._MARCH." (".$pre_addition.$res_month3.")');";
	$output .= "\n data.setValue(2, 1, ".$res_month3.");";
	$output .= "\n data.setValue(3, 0, '"._APRIL." (".$pre_addition.$res_month4.")');";
	$output .= "\n data.setValue(3, 1, ".$res_month4.");";
	$output .= "\n data.setValue(4, 0, '"._MAY." (".$pre_addition.$res_month5.")');";
	$output .= "\n data.setValue(4, 1, ".$res_month5.");";
	$output .= "\n data.setValue(5, 0, '"._JUNE." (".$pre_addition.$res_month6.")');";
	$output .= "\n data.setValue(5, 1, ".$res_month6.");";
	$output .= "\n data.setValue(6, 0, '"._JULY." (".$pre_addition.$res_month7.")');";
	$output .= "\n data.setValue(6, 1, ".$res_month7.");";
	$output .= "\n data.setValue(7, 0, '"._AUGUST." (".$pre_addition.$res_month8.")');";
	$output .= "\n data.setValue(7, 1, ".$res_month8.");";
	$output .= "\n data.setValue(8, 0, '"._SEPTEMBER." (".$pre_addition.$res_month9.")');";
	$output .= "\n data.setValue(8, 1, ".$res_month9.");";
	$output .= "\n data.setValue(9, 0, '"._OCTOBER." (".$pre_addition.$res_month10.")');";
	$output .= "\n data.setValue(9, 1, ".$res_month10.");";
	$output .= "\n data.setValue(10, 0, '"._NOVEMBER." (".$pre_addition.$res_month11.")');";
	$output .= "\n data.setValue(10, 1, ".$res_month11.");";
	$output .= "\n data.setValue(11, 0, '"._DECEMBER." (".$pre_addition.$res_month12.")');";
	$output .= "\n data.setValue(11, 1, ".$res_month12.");";

	// Create and draw the visualization
	if($chart_type == "barchart"){
		$output .= "new google.visualization.BarChart(document.getElementById('div_visualization')).draw(data, {is3D: true, min:0, title:'".$chart_name."'});"; 
	}else if($chart_type == "piechart"){
		$output .= "new google.visualization.PieChart(document.getElementById('div_visualization')).draw(data, {is3D: true, min:0, title:'".$chart_name."'});";
	}else if($chart_type == "areachart"){
		$output .= "new google.visualization.AreaChart(document.getElementById('div_visualization')).draw(data, {is3D: true, min:0, title:'".$chart_name."'});";
	}else{ // columnchart
		$output .= "new google.visualization.ColumnChart(document.getElementById('div_visualization')).draw(data, {is3D: true, min:0, title:'".$chart_name."'});";
	}

	return $output;
}

?>