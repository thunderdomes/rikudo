<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

include_once("include/functions.admin.inc.php");

if(@$objLogin->IsLoggedInAsAdmin() && Modules::IsModuleInstalled("booking")){
	
    define ("TABS_DIR", "modules/tabs/");
    require_once(TABS_DIR."tabs.class.php");
	
	echo "<script type='text/javascript' src='http://www.google.com/jsapi'></script>";

	$first_tab_content 	= "";
	$second_tab_content = "";
	$third_tab_content 	= "";	
	$tabid 				= isset($_POST['tabid']) ? prepare_input($_POST['tabid']) : "1_1";
	
	$chart_type = isset($_POST['chart_type']) ? prepare_input($_POST['chart_type']) : "columnchart";
	$year 		= isset($_POST['year']) ? prepare_input($_POST['year']) : date("Y");

	if($tabid == "1_1") {		
		$first_tab_content = "
			<script type='text/javascript'>
				function drawVisualization(){
				// Create and populate the data table.
				var data = new google.visualization.DataTable();
				data.addColumn('string', '"._MONTH."');
				data.addColumn('number', '"._BOOKINGS."');";
				
				$selStatType = "COUNT(*)";
				$join_clause = "";
				$where_clause = " AND status = 2 ";
	
				$sql = "SELECT
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '01' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month1,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '02' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month2,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '03' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month3,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '04' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month4,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '05' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month5,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '06' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month6,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '07' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month7,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '08' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month8,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '09' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month9,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '10' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month10,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '11' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month11,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '12' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month12
				  FROM ".TABLE_BOOKINGS."
				  GROUP BY month1";         
	
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
				
		$first_tab_content .= "\n data.addRows(12);";		
		if($result[1] >= 0){
			$first_tab_content .= draw_set_values($result[0], $chart_type, _AMOUNT);
		}				 
		$first_tab_content .= " } </script>";
				 
		$first_tab_content .= "<script type='text/javascript'>";
		$first_tab_content .= "\n google.load('visualization', '1', {packages: ['".$chart_type."']});";
		$first_tab_content .= "\n google.setOnLoadCallback(drawVisualization);";
		$first_tab_content .= "\n function frmStatistics_Submit() { document.frmStatistics.submit(); }";
		$first_tab_content .= "</script>";
				   
		$first_tab_content .= get_chart_changer("1_1", $chart_type, $year, "mod_booking_statistics");		

		$first_tab_content .= "<div id='div_visualization' style='width:600px;height:310px;'>
		<img src='images/loading.gif' style='margin:100px auto;' alt='"._LOADING."...'></div>";

	}else if($tabid == "1_2"){
		
		$second_tab_content = "
			<script type='text/javascript'>
				function drawVisualization(){
				// Create and populate the data table.
				var data = new google.visualization.DataTable();
				data.addColumn('string', '"._MONTH."');
				data.addColumn('number', '"._BOOKINGS."');";
				
				// calculate summ according to default currency
				$selStatType = "FORMAT(SUM(b.payment_sum * c.rate + b.additional_payment * c.rate), 2) ";
				$join_clause = " INNER JOIN ".TABLE_CURRENCIES." c ON b.currency = c.code ";
				$where_clause = " AND status = 2 ";
	
				$sql = "SELECT
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '01' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month1,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '02' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month2,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '03' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month3,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '04' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month4,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '05' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month5,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '06' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month6,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '07' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month7,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '08' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month8,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '09' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month9,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '10' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month10,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '11' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month11,
				  (SELECT ".$selStatType." FROM ".TABLE_BOOKINGS." b ".$join_clause." WHERE SUBSTRING(b.payment_date, 6, 2) = '12' AND SUBSTRING(b.payment_date, 1, 4) = ".(int)$year." ".$where_clause.") as month12
				  FROM ".TABLE_BOOKINGS."
				  GROUP BY month1";         
	
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY, FETCH_ASSOC);
				
		$second_tab_content .= "\n data.addRows(12);";		
		if($result[1] >= 0){
			$default_currency = Currencies::GetDefaultCurrency();
			$second_tab_content .= draw_set_values($result[0], $chart_type, _INCOME, $default_currency);
		}				 
		$second_tab_content .= " } </script>";
				 
		$second_tab_content .= "<script type='text/javascript'>";
		$second_tab_content .= "\n google.load('visualization', '1', {packages: ['".$chart_type."']});";
		$second_tab_content .= "\n google.setOnLoadCallback(drawVisualization);";
		$second_tab_content .= "\n function frmStatistics_Submit() { document.frmStatistics.submit(); }";
		$second_tab_content .= "</script>";
				   
		$second_tab_content .= get_chart_changer("1_1", $chart_type, $year, "mod_booking_statistics");		

		$second_tab_content .= "<div id='div_visualization' style='width:600px;height:310px;'>
		<img src='images/loading.gif' style='margin:100px auto;' alt='"._LOADING."...'></div>";
		
	}else{
		
		$sql = "SELECT
					COUNT(*) as cnt,
					c.abbrv as country_abbrv,
					c.name as country_name
				FROM ".TABLE_BOOKINGS." b
					INNER JOIN ".TABLE_CLIENTS." u ON b.client_id = u.id
					INNER JOIN ".TABLE_COUNTRIES." c ON u.b_country = c.abbrv
				GROUP BY c.abbrv";				
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		$third_tab_content = "			
			<script type='text/javascript'>
			 google.load('visualization', '1', {'packages': ['geomap']});
			 google.setOnLoadCallback(drawMap);
			
			  function drawMap() {
				var data = new google.visualization.DataTable();";
				
				if($result[1] > 0){
					$third_tab_content .= "\n data.addRows(".$result[1].");";
					$third_tab_content .= "\n data.addColumn('string', '"._COUNTRY."');";
					$third_tab_content .= "\n data.addColumn('number', '"._POPULARITY."');";
					$third_tab_content .= "\n data.addColumn('string', 'HOVER', '');";
					for($i=0; $i < $result[1]; $i++){
						$third_tab_content .= "\n data.setValue(".$i.", 0, '".$result[0][$i]['country_abbrv']."');";
						$third_tab_content .= "\n data.setValue(".$i.", 1, ".$result[0][$i]['cnt'].");";
						$third_tab_content .= "\n data.setValue(".$i.", 2, '".$result[0][$i]['country_name']."');";
					}
				}else{
					$third_tab_content .= "\n data.addRows(1);";
					$third_tab_content .= "\n data.addColumn('string', '"._COUNTRY."');";
					$third_tab_content .= "\n data.addColumn('number', '"._POPULARITY."');";
					$third_tab_content .= "\n data.addColumn('string', 'HOVER', '');";
					$third_tab_content .= "\n data.setValue(0, 0, 'USA');";
					$third_tab_content .= "\n data.setValue(0, 1, 0)";
					$third_tab_content .= "\n data.setValue(0, 2, 'USA');";										
				}
				
		$third_tab_content .= "	
				var options = {};
				options['dataMode'] = 'regions';
				options['width'] = '675px';
				options['showLegend'] = true;
			
				var container = document.getElementById('map_canvas');
				var geomap = new google.visualization.GeoMap(container);
				geomap.draw(data, options);
			};
			</script>
			<div id='map_canvas' style='padding:1px 10px; 1px 10px;'></div>		
		";	
	}
	

	$tabs = new Tabs(1, "xp", TABS_DIR, "?admin=mod_booking_statistics");
	$tabs->SetToken(Application::Get("token"));
	//$tabs->SetHttpVars(array("admin"));
 
	$tab1=$tabs->AddTab(_BOOKINGS." ("._AMOUNT.")", $first_tab_content);
	$tab2=$tabs->AddTab(_BOOKINGS." ("._INCOME.")", $second_tab_content);
	$tab3=$tabs->AddTab(_BOOKINGS." ("._MAP_OVERLAY.")", $third_tab_content);
	 
	## +---------------------------------------------------------------------------+
	## | 2. Customizing:                                                           |
	## +---------------------------------------------------------------------------+
	## *** set container's width in pixels (px), inches (in) or points (pt)
	$tabs->SetWidth("696px");
 
	## *** set container's height in pixels (px), inches (in) or points (pt)
	$tabs->SetHeight("auto"); // "auto"
 
	## *** set alignment inside the container (left, center or right)
	$tabs->SetAlign("left");
 
	## *** set container's color in RGB format or using standard names
	/// $tabs->SetContainerColor("#64C864");
	## *** set border's width in pixels (px), inches (in) or points (pt)
	/// $tabs->SetBorderWidth("5px");
	## *** set border's color in RGB format or using standard names
	/// $tabs->SetBorderColor("#64C864");
	/// $tabs->SetBorderColor("blue");
	/// $tabs->SetBorderColor("#445566");
	## *** show debug info - false|true
	$tabs->Debug(false);
	## *** allow refresh selected tabs - false|true
	/// $tabs->AllowRefreshSelectedTabs(true);
	## *** set form submission type: "get" or "post"
	$tabs->SetSubmissionType("post");


	draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>"",_STATISTICS=>"")));	

	draw_content_start();	
	$tabs->Display();
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>