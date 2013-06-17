<?php
	// *** Make sure the file isn't accessed directly
	defined('APPHP_EXEC') or die('Restricted Access');
	//--------------------------------------------------------------------------

	$objReservation = new Reservation();
	//--------------------------------------------------------------------------
    // *** redirect if reservation cart is empty
	if($objReservation->IsCartEmpty()){
        header("location: index.php?page=booking");
        echo "<p>if your browser doesn't support redirection please click <a href='index.php?page=booking'>here</a>.</p>";        
        exit;		
	}

	$task			 	 = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
	$selected_user       = isset($_POST['selected_user']) ? prepare_input($_POST['selected_user']) : "";
	$payment_method  	 = isset($_POST['payment_method']) ? prepare_input($_POST['payment_method']) : "";
	$additional_info 	 = isset($_POST['additional_info']) ? prepare_input($_POST['additional_info']) : "";
	$extras 	         = isset($_POST['extras']) ? prepare_input($_POST['extras']) : array();
	$pre_payment         = isset($_POST['pre_payment']) ? prepare_input($_POST['pre_payment']) : "";
	
	$cc_params = array();
	$cc_params['cc_type'] 	       = isset($_POST['cc_type']) ? prepare_input($_POST['cc_type']) : "";
	$cc_params['cc_number'] 	   = isset($_POST['cc_number']) ? prepare_input($_POST['cc_number']) : "";
	$cc_params['cc_expires_month'] = isset($_POST['cc_expires_month']) ? prepare_input($_POST['cc_expires_month']) : "";
	$cc_params['cc_expires_year']  = isset($_POST['cc_expires_year']) ? prepare_input($_POST['cc_expires_year']) : "";
	$cc_params['cc_cvv_code']      = isset($_POST['cc_cvv_code']) ? prepare_input($_POST['cc_cvv_code']) : "";
	
?>