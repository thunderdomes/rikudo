<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin") && Modules::IsModuleInstalled("booking")){	

	$task = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
	$sel_type = isset($_POST['sel_type']) ? prepare_input($_POST['sel_type']) : "";
	$date_from = isset($_POST['date_from']) ? prepare_input($_POST['date_from']) : "";
	$date_to = isset($_POST['date_to']) ? prepare_input($_POST['date_to']) : "";
	$msg = "";
	$where_clause = "";
	$date_from_sql = "";
	$date_to_sql = "";

	if($objSettings->GetParameter('date_format') == "mm/dd/yyyy"){
		$calendar_date_format = "%m-%d-%Y";
		$date_from_sql = date("Y-m-d", mktime(0, 0, 0, substr($date_from, 0, 2), substr($date_from, 3, 2), substr($date_from, 6, 4)));
		$date_to_sql   = date("Y-m-d", mktime(0, 0, 0, substr($date_to, 0, 2), substr($date_to, 3, 2), substr($date_to, 6, 4)));
	}else{
		$calendar_date_format = "%d-%m-%Y";
		$date_from_sql = date("Y-m-d", mktime(0, 0, 0, substr($date_from, 3, 2), substr($date_from, 0, 2), substr($date_from, 6, 4)));
		$date_to_sql   = date("Y-m-d", mktime(0, 0, 0, substr($date_to, 3, 2), substr($date_to, 0, 2), substr($date_to, 6, 4)));
	}

	if($task == "prepare_report"){		
		if($sel_type == ""){
			$msg = draw_important_message(_SELECT_REPORT_ALERT, false);
		}else if($sel_type == "arriving" && $date_from == ""){
			$msg = draw_important_message(str_replace("_FIELD_", "'"._FROM."'", _FIELD_CANNOT_BE_EMPTY), false);
		}else if($sel_type == "departing" && $date_to == ""){
			$msg = draw_important_message(str_replace("_FIELD_", "'"._TO."'", _FIELD_CANNOT_BE_EMPTY), false);
		}else if($sel_type == "staying" && ($date_from == "" || $date_to == "")){
			$msg = draw_important_message(_DATE_EMPTY_ALERT, false);
		}
	}
	
	// Start main content
	draw_title_bar(
		prepare_breadcrumbs(array(_BOOKINGS=>"",_REPORTS=>"")),
		(($task == "prepare_report" && $msg == "") ? "<a href='javascript:window.print();'><img src='images/printer.png' alt='' /> "._PRINT."</a>" : "")
	);

    echo $msg;
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	//echo $msg;
	draw_content_start();

	echo '
	<style type="text/css">@import url(modules/jscalendar/skins/aqua/theme.css);</style>
	<script type="text/javascript" src="modules/jscalendar/calendar.js"></script>
	<script type="text/javascript" src="modules/jscalendar/lang/calendar-en.js"></script>
	<script type="text/javascript" src="modules/jscalendar/calendar-setup.js"></script>
	
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
		google.load("visualization", "1", {packages: ["table"]});
    </script>
    <script type="text/javascript">
		var visualization;
		var data;
		var options = {\'showRowNumber\': true};';

	if($task == "prepare_report" && $msg == ""){
		
		if($sel_type == 'arriving'){
			$where_clause = "br.checkin = '".$date_from_sql."'".(($date_to != "") ? " AND br.checkout <= '".$date_to_sql."'" : "");
		}else if($sel_type == 'departing'){
			$where_clause = "br.checkout = '".$date_to_sql."'".(($date_from != "") ? " AND br.checkin >= '".$date_from_sql."'" : "");
		}else if($sel_type == 'staying'){
			$where_clause = "br.checkin <= '".$date_from_sql."' AND br.checkout >= '".$date_to_sql."'";
		}
		
		$sql = "SELECT
					b.booking_number,
					IF(
						b.is_admin_reservation = 1,
						'Admin',
						CONCAT(c.first_name, ' ', c.last_name)
					) as full_name,
					br.checkin,
					br.checkout,
					r.room_type
				FROM ".TABLE_BOOKINGS." b
					INNER JOIN ".TABLE_BOOKINGS_ROOMS." br ON b.booking_number = br.booking_number
					INNER JOIN ".TABLE_ROOMS." r ON br.room_id = r.id
					LEFT OUTER JOIN ".TABLE_CLIENTS." c ON b.client_id = c.id					
				WHERE
					".$where_clause;
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);		
		
		echo "		
		function drawVisualization() {
		  // Create and populate the data table.
		  var dataAsJson =
		  {
		  cols:[
			{id:'A',label:'"._VISITOR."',type:'string'},
			{id:'B',label:'"._BOOKING_NUMBER."',type:'string'},
			{id:'C',label:'"._ROOM_TYPE."',type:'string'},
			{id:'D',label:'"._CHECK_IN."',type:'string'},
			{id:'E',label:'"._CHECK_OUT."',type:'string'}],
		  rows:[ ";

			for($i = 0; $i < $result[1]; $i++){
				echo "{c:[
				          {v:'".addslashes($result[0][$i]['full_name'])."'},
						  {v:'".$result[0][$i]['booking_number']."'},
						  {v:'".$result[0][$i]['room_type']."'},
						  {v:'".$result[0][$i]['checkin']."'},
						  {v:'".$result[0][$i]['checkout']."'}						  
						 ]}";
				echo (($i < $result[1]-1) ? "," : "")."\n";
			}
			
		echo "	
			]};
			data = new google.visualization.DataTable(dataAsJson);
		  
			// Set paging configuration options
			// Note: these options are changed by the UI controls in the example.
			options['page'] = 'enable';
			options['pageSize'] = 20;
			options['pagingSymbols'] = {prev: 'prev', next: 'next'};
			options['pagingButtonsConfiguration'] = 'auto';
		  
			// Create and draw the visualization.
			visualization = new google.visualization.Table(document.getElementById('table'));
		  
			draw();
		}";				
	}else{
		echo "function drawVisualization() {} ";
	}
	
	echo "    
    function draw() {
		".(($task == "prepare_report" && $msg == "") ? "visualization.draw(data, options);" : "")."  
    }    

    google.setOnLoadCallback(drawVisualization);

    // sets the number of pages according to the user selection.
    function setNumberOfPages(value){
		if(value){
			options['pageSize'] = parseInt(value, 10);
			options['page'] = 'enable';
		}else{
			options['pageSize'] = null;
			options['page'] = null;  
		}
		draw();
    }
    </script>";
	
    echo "<div style='margin-bottom: 10px; padding: 5px; border: 1px solid #cccccc;'>
		<form name='frmReport' action='index.php?admin=mod_booking_reports' method='post'>
		    <input type='hidden' name='task' value='prepare_report' />
			".draw_token_field(true)."
			
			<span style='font-size:12px;margin:0 7px;'>Rows:</span>
			<select style='font-size:12px' onchange='setNumberOfPages(this.value)'>
				<option value=''>"._ALL."</option>
				<option value='3'>3</option>
				<option value='5'>5</option>
				<option value='10'>10</option>
				<option value='15'>15</option>
				<option selected='selected' value='20'>20</option>
				<option value='50'>50</option>
				<option value='100'>100</option>
			</select>

			<span style='font-size:12px;margin:0 5px;'>Type:</span>
			<select style='font-size:12px' name='sel_type'>
				<option value=''>-- "._SELECT." --</option>
				<option value='arriving' ".(($sel_type == "arriving") ? "selected='selected'" : "").">People Arriving</option>
				<option value='departing' ".(($sel_type == "departing") ? "selected='selected'" : "").">People Departing</option>
				<option value='staying' ".(($sel_type == "staying") ? "selected='selected'" : "").">People Staying</option>
			</select>
			
			<span style='font-size:12px;margin:0 5px;'>"._FROM.":</span>
			<input type='textbox' size='10' readonly name='date_from' id='date_from' value='".$date_from."' />
			<img id='date_from_cal' src='images/cal.gif' alt='' title='"._SET_DATE."' style='margin-left:5px;margin-right:5px;cursor:pointer;' />
	
			<span style='font-size:12px;margin:0 5px;'>"._TO.":</span>
			<input type='textbox' size='10' readonly name='date_to' id='date_to' value='".$date_to."' />
			<img id='date_to_cal' src='images/cal.gif' alt='' title='"._SET_DATE."' style='margin-left:5px;margin-right:5px;cursor:pointer;' />
			
			<span style='margin:0 5px;'></span>
			".(($task == "prepare_report") ? "<input class='mgrid_button' type='button' name='btnReset' value='"._RESET."' onclick=\"javascript:appGoTo('admin=mod_booking_reports')\" />" : "")."
			<input class='mgrid_button' type='submit' name='btnSubmit' value='"._SEARCH."' />
	    </form>
      </div>
    <div id='table' style='width:100%'></div>

	
	<script type='text/javascript'> 
	Calendar.setup({firstDay : 0, inputField : 'date_from', ifFormat : '".$calendar_date_format."', showsTime : false, button : 'date_from_cal'});
	Calendar.setup({firstDay : 0, inputField : 'date_to', ifFormat : '".$calendar_date_format."', showsTime : false, button : 'date_to_cal'});
	</script>";
	
	draw_content_end();
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>