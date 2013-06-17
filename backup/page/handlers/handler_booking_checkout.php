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

?>