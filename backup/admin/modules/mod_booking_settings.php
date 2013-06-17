<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin") && Modules::IsModuleInstalled("booking")){	

	$action = MicroGrid::GetParameter('action');
	$rid    = MicroGrid::GetParameter('rid');
	$settings_key    = MicroGrid::GetParameter('settings_key', false);
	$settings_value  = MicroGrid::GetParameter('settings_value', false);
	$mode   = "view";
	$msg    = "";
	
	$objCartSettings = new ModulesSettings("booking");
	
	if($action=="add"){		
		$mode = "add";
	}else if($action=="create"){
		if($objCartSettings->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objCartSettings->error, false);
			$mode = "add";
		}
	}else if($action=="edit"){
		$mode = "edit";
	}else if($action=="update"){
		if($objCartSettings->UpdateRecord($rid)){
			if($settings_key == "vat_value"){
				$objCountries = new Countries();
				$objCountries->UpdateVAT($settings_value);
			}
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = "view";
		}else{
			$msg = draw_important_message($objCartSettings->error, false);
			$mode = "edit";
		}		
	}else if($action=="delete"){
		if($objCartSettings->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objCartSettings->error, false);
		}
		$mode = "view";
	}else if($action=="details"){		
		$mode = "details";		
	}else if($action=="cancel_add"){		
		$mode = "view";		
	}else if($action=="cancel_edit"){				
		$mode = "view";
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>"",_BOOKING_SETTINGS=>"",ucfirst($action)=>"")));
	
    echo "<br />";
	
	//if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();
	if($mode == "view"){		
		$objCartSettings->DrawViewMode();
		
		echo "<br /><br />
		<fieldset class='instructions'>
		<legend>
			<b>INSTRUCTIONS:
				&nbsp;<a id='lnkOnline' style='font-weight:bold' href='javascript:appToggleElements(\"instOnline\", \"lnkOnline\", \"inst2CO\", \"lnk2CO\", \"instPayPal\", \"lnkPayPal\")'>[ On-Line Order ]</a>
				&nbsp;<a id='lnkPayPal' href='javascript:appToggleElements(\"instPayPal\", \"lnkPayPal\", \"instOnline\", \"lnkOnline\", \"inst2CO\", \"lnk2CO\")'>[ PayPal ]</a>
				&nbsp;<a id='lnk2CO' href='javascript:appToggleElements(\"inst2CO\", \"lnk2CO\", \"instOnline\", \"lnkOnline\", \"instPayPal\", \"lnkPayPal\")'>[ 2CO ]</a>
			</b>
		</legend>

		<div id='instOnline' style='display:;padding:10px;'>
			Online Order is designed to allow the client to place order on the site without any advance payment.<br />
			The administrator receives a notification about placing the order and can complete the order by himself.
			<br /><br />
			IMPORTANT:
			<ol>
				<li>Administrator can view orders <a href='index.php?admin=mod_booking_bookings'>here</a>.</li>
				<li>Administrator may allow collecting of credit card info via <b>Modules -> Booking Settings</b></li>
			</ol>
		</div>
		
		<div id='instPayPal' style='display:none;padding:10px;'>
		To make PayPal processing system works on your site you have to perform the following steps:<br/><br/>
		<ol>
			<li>Create an account on PayPal: <a href='https://www.paypal.com' target='_new'>https://www.paypal.com</a></li>
			<li>After account is created, log into and select from the top menu: <b>My Account -> Profile</b></li>
			<li>On <b>Profile Summary</b> page select from the <b>Selling Preferences</b> column: <b>Instant Payment Notification (IPN) Preferences</b>.  </li>
			<li>Turn 'On' IPN by selecting <b>Receive IPN messages (Enabled)</b> and write into <b>Notification URL</b>: {site}/index.php?page=booking_notify_paypal, where {site} is a full path to your site.<br /><br />
				<span class='code'>
			    For example: <b>http://your_domain.com/index.php?page=booking_notify_paypal</b> or <br /><b>http://your_domain.com/new_site/index.php?page=booking_notify_paypal</b>
				</span>
			</li>
			<li>
				Then go to <b>My Account -> Profile -> Website Payment Preferences</b>, turn <b>Auto Return</b> 'On' and write into <b>Return URL</b>: {site}/index.php?page=order_return, where {site} is a full path to your site.<br /><br />
				<span class='code'>
			    For example: <b>http://your_domain.com/index.php?page=order_return</b>
				</span>
			</li>
		</ol>
		</div>

		<div id='inst2CO' style='display:none;padding:10px;'>
		To make 2CO processing system works on your site you have to perform the following steps:<br/><br/>
		<ol>
			<li>Create an account on 2Checkout: <a href='http://www.2checkout.com' target='_new'>http://www.2checkout.com</a></li>
			<li>After account is created, <a href='https://www.2checkout.com/2co/login'>log into</a> and select from the top menu: <b>Notifications -> Settings</b></li>
			<li>On <b>Instant Notification Settings</b> page enter into <b>Global URL</b> textbox: {site}/index.php?page=booking_notify_2co, where {site} is a full path to your site. <br /><br />
				<span class='code'>
			    For example: <b>http://your_domain.com/index.php?page=booking_notify_2co</b> or <br /><b>http://your_domain.com/new_site/index.php?page=booking_notify_2co</b>
				</span>	<br /><br />
				Then click on <b>Enable All Notifications</b> and <b>Save Settings</b> buttons.
			</li>			
			<li>
				Go to <b>Account -> Site Management</b>, set <b>Demo Setting</b> on 'Off' and enter into <b>Approved URL</b> textbox: {site}/index.php?page=order_return, where {site} is a full path to your site.<br /><br />
				<span class='code'>
			    For example: <b>http://your_domain.com/index.php?page=order_return</b> or <br /><b>http://your_domain.com/new_site/index.php?page=order_return</b>
				</span>	<br />
			</li>
		</ol>
		</div>
		</fieldset>";
		
	}else if($mode == "add"){		
		$objCartSettings->DrawAddMode();		
	}else if($mode == "edit"){		
		$objCartSettings->DrawEditMode($rid);		
	}else if($mode == "details"){ 
		$objCartSettings->DrawDetailsMode($rid);		
	}
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>