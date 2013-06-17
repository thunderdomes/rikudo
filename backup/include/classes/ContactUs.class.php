<?php

/**
 *	Class ContactUs
 *  -------------- 
 *  Description : encapsulates ContactUs properties
 *  Updated	    : 24.11.2010
 *	Written by  : ApPHP
 *	
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct										
 *  __destruct
 *  DrawContactUsForm
 *  
 **/

class ContactUs {

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
	 *	Draws Contact Us form
	 */
	public function DrawContactUsForm()
	{		
		global $objSettings, $objSiteDescription, $objLogin;
	
		if(!Modules::IsModuleInstalled("contact_us")) return "";

		$output = "";
		$from_email   = $objSettings->GetParameter("admin_email");
		
		$objContactUs = new ModulesSettings("contact_us");
		$admin_email  = $objContactUs->GetSettings("email");
		$delay_length = $objContactUs->GetSettings("delay_length");
		$is_send_delay = $objContactUs->GetSettings("is_send_delay");
		$image_verification = $objContactUs->GetSettings("image_verification_allow");		
		
		$focus_element = "first_name";
		
		// post fields
		$task             = isset($_POST['task']) ? prepare_input($_POST['task']) : "";		
		$first_name       = isset($_POST['first_name']) ? prepare_input($_POST['first_name']) : "";
		$last_name        = isset($_POST['last_name']) ? prepare_input($_POST['last_name']) : "";
		$email            = isset($_POST['email']) ? prepare_input($_POST['email']) : "";
		$phone            = isset($_POST['phone']) ? prepare_input($_POST['phone']) : "";
		$subject          = isset($_POST['subject']) ? prepare_input($_POST['subject']) : "";
		$message          = isset($_POST['message']) ? prepare_input($_POST['message']) : "";
		$captcha_code 	  = isset($_POST['captcha_code']) ? prepare_input($_POST['captcha_code']) : "";
		$msg              = "";
		$contact_mail_sent = isset($_SESSION['contact_mail_sent']) ? $_SESSION['contact_mail_sent'] : false;
		$contact_mail_sent_time = isset($_SESSION['contact_mail_sent_time']) ? $_SESSION['contact_mail_sent_time'] : "";

        if($image_verification == "yes"){
			include_once("modules/captcha/securimage.php");
			$objImg = new Securimage();			
		}

		if($task == "contact"){			
			$time_elapsed = (time_diff(date("Y-m-d H:i:s"), $contact_mail_sent_time));
			if($contact_mail_sent && $is_send_delay == "yes" && $time_elapsed < $delay_length){
				$msg = draw_message(str_replace("_WAIT_", $delay_length - $time_elapsed, _CONTACT_US_ALREADY_SENT), false, true);
			}else{			
				if($first_name == ""){
					$msg = draw_important_message(_FIRST_NAME_EMPTY_ALERT, false);
					$focus_element = "first_name";
				}else if($last_name == ""){
					$msg = draw_important_message(_LAST_NAME_EMPTY_ALERT, false);
					$focus_element = "last_name";
				}else if($email == ""){
					$msg = draw_important_message(_EMAIL_EMPTY_ALERT, false);
					$focus_element = "email";
				}else if(($email != "") && (!check_email_address($email))){
					$msg = draw_important_message(_EMAIL_VALID_ALERT, false);
					$focus_element = "email";
				}else if($subject == ""){        
					$msg = draw_important_message(_SUBJECT_EMPTY_ALERT, false);
					$focus_element = "subject";
				#}else if($phone == ""){        
				#	$msg = draw_important_message(str_replace("_FIELD_", _PHONE, _FIELD_CANNOT_BE_EMPTY), false);
				#	$focus_element = "phone";
				}else if($message == ""){        
					$msg = draw_important_message(_MESSAGE_EMPTY_ALERT, false);
					$focus_element = "message";
				}else if(($image_verification == "yes") && !$objImg->check($captcha_code)){
					$msg = draw_important_message(_WRONG_CODE_ALERT, false);
					$focus_element = "captcha_code";
				}
				
				// deny all operations in demo version
				if(strtolower(SITE_MODE) == "demo"){
					$msg = draw_important_message(_OPERATION_BLOCKED, false);
				}						

				if($msg == ""){            
					////////////////////////////////////////////////////////////
					$sender = $from_email;
					$recipiant = $admin_email;
				
					$msg_title = "Question from visitor (via Contact Us - ".@$objSiteDescription->GetParameter("header_text").")";
					
					$body   = "<div style=direction:".Application::Get("lang_dir").">";
					$body  .= _FIRST_NAME.": ".str_replace("\\", "", $first_name)."<br />";
					$body  .= _LAST_NAME.": ".str_replace("\\", "", $last_name)."<br />";
					$body  .= _EMAIL_ADDRESS.": ".str_replace("\\", "", $email)."<br />";
					$body  .= _PHONE.": ".str_replace("\\", "", $phone)."<br />";
					$body  .= _SUBJECT.": ".str_replace("\\", "", $subject)."<br />";
					$body  .= _MESSAGE.": ".str_replace("\\", "", $message);
					$body  .= "</div>";
					
					$textversion = strip_tags($body);
					$htmlversion = $body;
				
					$objEmail = new Email($recipiant, $sender, $msg_title); 
					
					$objEmail->textOnly = false;
					$objEmail->content = $htmlversion;
					$objEmail->replyTo = $email;
				
					$objEmail->Send();
					////////////////////////////////////////////////////////////
			
					$msg = draw_success_message(_CONTACT_US_EMAIL_SENT, false);
					$_SESSION['contact_mail_sent'] = true;
					$_SESSION['contact_mail_sent_time'] = date("Y-m-d H:i:s");
					
					$first_name = $last_name = $email = $phone = $subject = $message = "";
				}
			}
		}

		$output .= "
        <form method='post' name='frmContactUs' id='frmContactUs'>
		    <input type='hidden' name='task' value='contact' />
			".draw_token_field(true)."
		    
			".(($msg != "") ? $msg."<br />" : "")."
			
		    <table cellspacing='1' cellpadding='2' border='0' width='100%'>
		    <tbody>
		    <tr>
			    <td width='25%' align='".Application::Get("defined_right")."'>"._FIRST_NAME.":</td>
			    <td><font class='mandatory_star'>*</font></td>
			    <td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type=\"text\" id=\"first_name\" name=\"first_name\" size=\"34\" maxlength=\"32\" value=\"".decode_text($first_name)."\" autocomplete=\"off\" /></td>
		    </tr>
		    <tr>
			    <td align='".Application::Get("defined_right")."'>"._LAST_NAME.":</td>
			    <td><font class='mandatory_star'>*</font></td>
			    <td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type=\"text\" id=\"last_name\" name=\"last_name\" size=\"34\" maxlength=\"32\" value=\"".decode_text($last_name)."\" autocomplete=\"off\" /></td>
		    </tr>
		    <tr>
                <td align='".Application::Get("defined_right")."'>"._EMAIL_ADDRESS.":</td>
                <td><font class='mandatory_star'>*</font></td>
                <td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type=\"text\" id=\"email\" name=\"email\" size=\"34\" maxlength=\"128\" value=\"".decode_text($email)."\" autocomplete=\"off\"  /></td>
		    </tr>
		    <tr>
                <td align='".Application::Get("defined_right")."'>"._PHONE.":</td>
                <td></td>
                <td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type=\"text\" id=\"phone\" name=\"phone\" size=\"22\" maxlength=\"32\" value=\"".decode_text($phone)."\" autocomplete=\"off\"  /></td>
		    </tr>
		    <tr>
                <td align='".Application::Get("defined_right")."'>"._SUBJECT.":</td>
                <td><font class='mandatory_star'>*</font></td>
                <td nowrap='nowrap' align='".Application::Get("defined_left")."'><input type=\"text\" id=\"subject\" name=\"subject\" style=\"width:385px;\" maxlength=\"128\" value=\"".decode_text($subject)."\" autocomplete=\"off\"  /></td>
		    </tr>
		    <tr valign='top'>
                <td align='".Application::Get("defined_right")."'>"._MESSAGE.":</td>
                <td><font class='mandatory_star'>*</font></td>
                <td nowrap='nowrap' align='".Application::Get("defined_left")."'>
                    <textarea id=\"message\" name=\"message\" style=\"width:385px;\" rows=\"8\">".$message."</textarea>                
                </td>
		    </tr>
			<tr>
				<td colspan='2'></td>
				<td colspan='2'>";				
					if($image_verification == "yes"){
						$output .= "<table border='0' cellspacing='2' cellpadding='2'>
						<tr>
							<td>
								<img style='padding:0px; margin:0px;' id='captcha_image' src='modules/captcha/securimage_show.php?sid=".md5(uniqid(time()))."' />
							</td>	
							<td>
								<img style='cursor:pointer; padding:0px; margin:0px;' id='captcha_image_reload' src='modules/captcha/images/refresh.gif' style='cursor:pointer;' onclick=\"document.getElementById('captcha_image').src = 'modules/captcha/securimage_show.php?sid=' + Math.random(); return false\" title='"._REFRESH."' alt='"._REFRESH."' /><br />
								<a href='modules/captcha/securimage_play.php'><img border='0' style='padding:0px; margin:0px;' id='captcha_image_play' src='modules/captcha/images/audio_icon.gif' title='"._PLAY."' alt='"._PLAY."' /></a>						
							</td>					
							<td align='left'>
								"._TYPE_CHARS."<br />								
								<input type=\"text\" name=\"captcha_code\" id=\"captcha_code\" style=\"width:175px;margin-top:5px;\" value=\"\" autocomplete=\"off\" />
							</td>
						</tr>
						</table>";
					}				
				$output .= "</td>
			</tr>
		    <tr><td height='20' colspan='3'>&nbsp;</td></tr>            
		    <tr>
				<td colspan='3' align='center'>
				<br /><br />
				<input type='submit' ".($objLogin->IsLoggedInAsAdmin() ? "disabled" : "")." class='form_button' name='btnSubmitPD' id='btnSubmitPD' value='"._SEND."'>
				</td>
		    </tr>
		    <tr><td colspan='3'>&nbsp;</td></tr>		    		    
		    </table>
		</form>\n";
		if($focus_element != ""){
			$output .= "<script type='text/javascript'>\n
		    appSetFocus('".$focus_element."');\n
		    </script>";
		}
        return $output;      
    }

}

?>