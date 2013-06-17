<?php

/**
 *	Class PaymentIPN
 *  -------------- 
 *  Description : encapsulates Payment IPN properties
 *  Updated	    : 08.12.2010
 *	Written by  : ApPHP
 *	Version     : 1.0.1
 *
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct
 *	__destruct
 *  GetPaymentStatus
 *  GetParameter
 *  
 *  1.0.2
 *  	- added 2co type
 *  	- 
 *  	-
 *  	-
 *  	-
 **/

class PaymentIPN
{
	private $post_vars;
	private $response;
	private $timeout;

	private $error_email;
	private $pp_type;
	
	//==========================================================================
    // Class Constructor
	// 		@param $post_vars
	//==========================================================================
	function __construct($post_vars, $pp_type)
	{
		$this->post_vars = $post_vars;
		$this->pp_type = $pp_type;
		$this->timeout = 120;
	}

	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }
	
	/**
	 *	Returns payment status
	 */
	public function GetPaymentStatus()
	{
		$payment_status = "";
		
		if($this->pp_type == "paypal"){
			$payment_status = isset($this->post_vars['payment_status']) ? $this->post_vars['payment_status'] : "";
		}else if($this->pp_type == "2co"){
			$payment_status = isset($this->post_vars['invoice_status']) ? $this->post_vars['invoice_status'] : "";
		}
		return $payment_status;
	}

	/**
	 *	Returns parameter
	 *		@param $param
	 */
	public function GetParameter($param)
	{
		$param_value = "";
		
		if($this->pp_type == "paypal"){
			$param_value = isset($this->post_vars[$param]) ? $this->post_vars[$param] : "";
		}else if($this->pp_type == "2co"){
			$param_value = isset($this->post_vars[$param]) ? $this->post_vars[$param] : "";
		}		
		return $param_value;
	}

} 

?>