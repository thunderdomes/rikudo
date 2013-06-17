<?php

/***
 *	Class Cron 
 *  -------------- 
 *  Description : encapsulates cron job properties
 *	Usage       : MicroCMS, MicroBlog, HotelSite, ShoppingCart (has differences)
 *  Updated	    : 20.04.2011
 *	Written by  : ApPHP
 *	Version     : 1.0.1
 *
 *	PUBLIC:					STATIC:					PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct             Run
 *	__destruct
 *	
 *  1.0.1
 *      - 
 *      -
 *      -
 *      -
 *      -       
 **/

class Cron {

	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{

	}

	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/**
	 * Run - called by outside cron
	 */
	static public function Run()
	{
		// add here your code...
        // Class::Method();
		$perform_actions = false;

        // update last time running
		$sql = "SELECT
					cron_type,
					cron_run_last_time,
					cron_run_period,
					cron_run_period_value,
					CASE
						WHEN cron_run_last_time = '0000-00-00 00:00:00' THEN '999'
						WHEN cron_run_period = 'minute' THEN TIMESTAMPDIFF(MINUTE, cron_run_last_time, '".date("Y-m-d H:i:s")."')
						ELSE TIMESTAMPDIFF(HOUR, cron_run_last_time, '".date("Y-m-d H:i:s")."')						
					END as time_diff					
				FROM ".TABLE_SETTINGS;
		$result = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);

        if($result['cron_type'] == 'batch'){
			$perform_actions = true;    
        }else if($result['cron_type'] == 'non-batch' && $result['time_diff'] > $result['cron_run_period_value']){
			$perform_actions = true;
		}else{
			$perform_actions = false;
		}
        
		if($perform_actions)
		{
			///////////////////////////////////////
			// For Shopping Cart and Hotel Site
			// close expired discount campaigns
			Campaigns::UpdateStatus();
			
			///////////////////////////////////////
			// For Hotel Site
			// remove expired 'Preparing' bookings
			Bookings::RemoveExpired();            
	
			// update last time running
			$sql = "UPDATE ".TABLE_SETTINGS." SET cron_run_last_time = '".date("Y-m-d H:i:s")."'";
			database_void_query($sql);
		}
	}
}
?>