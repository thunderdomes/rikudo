<?php

////////////////////////////////////////////////////////////////////////////////
// PayPal Order Notify
// Last modified: 01.06.2011
////////////////////////////////////////////////////////////////////////////////

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(Modules::IsModuleInstalled("booking")){
	$objBookingSettings = new ModulesSettings("booking");
	$mode = $objBookingSettings->GetSettings("mode");
	
	if($objBookingSettings->GetSettings("is_active") == "yes"){		

		//----------------------------------------------------------------------
		define("LOG_MODE", false);
		define("LOG_TO_FILE", false);
		define("LOG_ON_SCREEN", false);
		
		define("TEST_MODE", ($mode == "TEST MODE") ? true : false);
		$log_data    = "";
		$msg         = "";

		// --- Get PayPal response
		$objPaymentIPN 		= new PaymentIPN($_REQUEST, "paypal");
		$status 			= $objPaymentIPN->GetPaymentStatus();
		$booking_number		= $objPaymentIPN->GetParameter("custom");
	    $transaction_number = $objPaymentIPN->GetParameter("txn_id");
		$payment_type		= $objPaymentIPN->GetParameter("payer_status");
		$total 				= $objPaymentIPN->GetParameter("mc_gross");
		
		// 0 - Online Order, 1 - PayPal, 2 - Credit Card, 3- 2CO
		///$payment_type = ($objPaymentIPN->GetParameter('payment_type') == "echeck") ? "3" : "0";
		if($payment_type == "verified"){
			$payment_type = "1";
		}else{
			$payment_type = "2";
		}
				
		//----------------------------------------------------------------------
		if(TEST_MODE){
			$status = "Completed";
		}

		////////////////////////////////////////////////////////////////////////
		if(LOG_MODE){
			if(LOG_TO_FILE){
				$myFile = "tmp/logs/payment_paypal.log";
				$fh = fopen($myFile, 'a') or die("can't open file");				
			}
	  
			$log_data .= "\n\n=== [".date("Y-m-d H:i:s")."] ===================\n";
			$log_data .= "<br />---------------<br />\n";
			$log_data .= "<br />POST<br />\n";
			foreach($_POST as $key=>$value) {
				$log_data .= $key."=".$value."<br />\n";        
			}
			$log_data .= "<br />---------------<br />\n";
			$log_data .= "<br />GET<br />\n";
			foreach($_GET as $key=>$value) {
				$log_data .= $key."=".$value."<br />\n";        
			}        
		}      
		////////////////////////////////////////////////////////////////////////  

		switch($status)    
		{
			// 1 order pending
			case 'Pending':
				$pending_reason = $objPaymentIPN->GetParameter("pending_reason");
				$msg = "Pending Payment - $pending_reason";
				break;
			case 'Completed':
				// 2 order completed					
				$sql = "SELECT id, booking_number, booking_description, order_price, vat_fee, payment_sum, currency, rooms_amount, client_id 
						FROM ".TABLE_BOOKINGS."
						WHERE booking_number = '".$booking_number."' AND status = 0";
				$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
				if($result[1] > 0){
					write_log($sql);					

					// check for possible problem or hack attack
					if($total <= 1 || abs($total - $result[0]['payment_sum']) > 1){
						$ip_address = (isset($_SERVER['HTTP_X_FORWARD_FOR']) && $_SERVER['HTTP_X_FORWARD_FOR']) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
						$message  = "From IP: ".$ip_address."<br />";
						$message .= "Status: ".$status."<br />";
						$message .= "Possible Attempt of Hack Attack? <br />";
						$message .= "Order Price: ".$result[0]['payment_sum']." <br />";
						$message .= "Payment Processing Gross Price: ".$total;
						write_log($message);
						break;            
					}

					// update user orders/products amount
					$sql = "UPDATE ".TABLE_CLIENTS." SET
								orders_count = orders_count + 1,
								rooms_count = rooms_count + ".(int)$result[0]['rooms_amount']."
							WHERE id = ".(int)$result[0]['client_id'];
					database_void_query($sql);
					write_log($sql);
					
					$sql = "UPDATE ".TABLE_BOOKINGS." SET
								status = 2,
								transaction_number = '".$transaction_number."',
								payment_date = '".date("Y-m-d H:i:s")."',
								payment_type = ".$payment_type."
							WHERE booking_number = '".$booking_number."'";
					if(database_void_query($sql)){
						$objReservation = new Reservation();

						// send email to user
						$objReservation->SendOrderEmail($booking_number, "completed", (int)$result[0]['client_id']);
						write_log($sql, _ORDER_PLACED_MSG);

						$objReservation->EmptyCart();
					}else{
						write_log($sql, mysql_error());
					}
				}else{
					write_log($sql, "Error: no records found. ".mysql_error());
				}				
				break;
			case 'Updated':
				// 3 updated already
				$msg = "Thank you for your order!<br /><br />";
				break;
			case 'Failed':
				// 4 this will only happen in case of echeck.
				$msg = "Payment Failed";
				break;
			case 'Denied':
				// 5 denied payment by us
				$msg = "Payment Denied";
				break;
			case 'Refunded':
				// 6 payment refunded by us
				$msg = "Payment Refunded";			
				break;
			case 'Canceled':
				/* 7 reversal cancelled
				 mark the payment as dispute cancelled */
				$msg = "Cancelled reversal";
				break;	
			default:
				// 0 order is not good
				$msg = "Unknown Payment Status - please try again.";
				// . $objPaymentIPN->GetPaymentStatus();
				break;
		}


		////////////////////////////////////////////////////////////////////////
		if(LOG_MODE){
			$log_data .= "<br />\n".$msg."<br />\n";    
			if(LOG_TO_FILE){
				fwrite($fh, strip_tags($log_data));
				fclose($fh);        				
			}
			if(LOG_ON_SCREEN){
				echo $log_data;
			}
		}
		////////////////////////////////////////////////////////////////////////

		if(TEST_MODE){
			header("location: index.php?page=booking_return");
		}
	}else{
		draw_important_message(_NOT_AUTHORIZED);
	}	
}else{
    draw_important_message(_NOT_AUTHORIZED);
}


function write_log($sql, $msg = ""){
    global $log_data;
    if(LOG_MODE){
        $log_data .= "<br />\n".$sql;
        if($msg != "") $log_data .= "<br />\n".$msg;
    }    
}

?>