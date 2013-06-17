<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(!@$objLogin->IsLoggedIn()){
	
	include_once("modules/captcha/securimage.php");
	$objImg = new Securimage();
	
	$act 		  = isset($_POST['act']) ? prepare_input($_POST['act']) : "";
	$send_updates = isset($_POST['send_updates']) ? prepare_input($_POST['send_updates']) : "1";
	$first_name   = isset($_POST['first_name']) ? prepare_input($_POST['first_name']) : "";
	$last_name    = isset($_POST['last_name']) ? prepare_input($_POST['last_name']) : "";

	$birth_date_year = isset($_POST['birth_date__nc_year']) ? prepare_input($_POST['birth_date__nc_year']) : "";
	$birth_date_month = isset($_POST['birth_date__nc_month']) ? prepare_input($_POST['birth_date__nc_month']) : "";
	$birth_date_day = isset($_POST['birth_date__nc_day']) ? prepare_input($_POST['birth_date__nc_day']) : "";
	$birth_date = $birth_date_year."-".$birth_date_month."-".$birth_date_day;
	if($birth_date == "--") $birth_date = "0000-00-00";

	$company     = isset($_POST['company']) ? prepare_input($_POST['company']) : "";
	$b_address   = isset($_POST['b_address']) ? prepare_input($_POST['b_address']) : "";
	$b_address_2 = isset($_POST['b_address_2']) ? prepare_input($_POST['b_address_2']) : "";
	$b_city      = isset($_POST['b_city']) ? prepare_input($_POST['b_city']) : "";
	$b_state     = isset($_POST['b_state']) ? prepare_input($_POST['b_state']) : "";
	$b_country   = isset($_POST['b_country']) ? prepare_input($_POST['b_country']) : "";
	$b_zipcode   = isset($_POST['b_zipcode']) ? prepare_input($_POST['b_zipcode']) : "";
	$phone       = isset($_POST['phone']) ? prepare_input($_POST['phone']) : "";
	$email       = isset($_POST['email']) ? prepare_input($_POST['email']) : "";
	$url         = isset($_POST['url']) ? prepare_input($_POST['url'], false, "medium") : "";
	$user_name   = isset($_POST['user_name']) ? prepare_input($_POST['user_name']) : "";
	$user_password1 = isset($_POST['user_password1']) ? prepare_input($_POST['user_password1']) : "";
	$user_password2 = isset($_POST['user_password2']) ? prepare_input($_POST['user_password2']) : "";
	$agree       = isset($_POST['agree']) ? prepare_input($_POST['agree']) : "";
	$focus_field = "";

	$objClientsSettings = new ModulesSettings("clients");
	$reg_confirmation = $objClientsSettings->GetSettings("reg_confirmation");
	$image_verification_allow = $objClientsSettings->GetSettings("image_verification_allow");
	
	$msg_default = draw_message(_ACCOUNT_CREATE_MSG, false);
	$msg = "";

	$account_created = false;
	if($act == ""){
		$focus_field = "first_name";
	}else if($act == "create"){
		$captcha_code = isset($_POST['captcha_code']) ? prepare_input($_POST['captcha_code']) : "";
		
		if($first_name == ""){
			$msg = draw_important_message(_FIRST_NAME_EMPTY_ALERT, false);
			$focus_field = "first_name";
		}else if($last_name == ""){
			$msg = draw_important_message(_LAST_NAME_EMPTY_ALERT, false);
			$focus_field = "last_name";
		}else if($birth_date != "" && !check_date($birth_date)){
			$msg = draw_important_message(_BIRTH_DATE_VALID_ALERT, false);
		}else if($b_address == ""){
			$msg = draw_important_message(_ADDRESS_EMPTY_ALERT, false);
			$focus_field = "b_address";
		}else if($b_city == ""){
			$msg = draw_important_message(_CITY_EMPTY_ALERT, false);
			$focus_field = "b_city";
		}else if($b_country == ""){
			$msg = draw_important_message(_COUNTRY_EMPTY_ALERT, false);
			$focus_field = "b_country";
		}else if($b_zipcode == ""){
			$msg = draw_important_message(_ZIPCODE_EMPTY_ALERT, false);
			$focus_field = "b_zipcode";
		}else if($phone == ""){
			$msg = draw_important_message(_PHONE_EMPTY_ALERT, false);
			$focus_field = "phone";
		}else if($email == ""){
			$msg = draw_important_message(_EMAIL_EMPTY_ALERT, false);
			$focus_field = "email";
		}else if($email != "" && !check_email_address($email)){
			$msg = draw_important_message(_EMAIL_VALID_ALERT, false);
			$focus_field = "email";
		}else if($user_name == ""){
			$msg = draw_important_message(_USERNAME_EMPTY_ALERT, false);
			$focus_field = "frmReg_user_name";
		}else if(($user_name != "") && (strlen($user_name) < 4)){
			$msg = draw_important_message(_USERNAME_LENGTH_ALERT, false);
			$focus_field = "frmReg_user_name";
		}else if($user_password1 == ""){
			$msg = draw_important_message(_PASSWORD_IS_EMPTY, false);
			$user_password1 = $user_password2 = "";			
			$focus_field = "frmReg_user_password1";
		}else if(($user_password1 != "") && (strlen($user_password1) < 6)){
			$msg = draw_important_message(_PASSWORD_IS_EMPTY, false);
			$user_password1 = $user_password2 = "";			
			$focus_field = "frmReg_user_password1";
		}else if(($user_password1 != "") && ($user_password2 == "")){			
			$msg = draw_important_message(_CONF_PASSWORD_IS_EMPTY, false);
			$user_password1 = $user_password2 = "";			
			$focus_field = "frmReg_user_password1";
		}else if(($user_password1 != "") && ($user_password2 != "") && ($user_password1 != $user_password2)){			
			$msg = draw_important_message(_CONF_PASSWORD_MATCH, false);
			$user_password1 = $user_password2 = "";			
			$focus_field = "frmReg_user_password1";
		}else if($agree == ""){
			$msg = draw_important_message(_CONFIRM_TERMS_CONDITIONS, false);
		}else if($image_verification_allow == "yes" && !$objImg->check($captcha_code)){			
			$msg = draw_important_message(_WRONG_CODE_ALERT, false);			
			$focus_field = "frmReg_captcha_code";
		}

		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			$msg = draw_important_message(_OPERATION_BLOCKED, false);
		}				

		// check if user IP or email don't blocked
		if($msg == ""){
			if($objLogin->IpAddressBlocked(get_current_ip())) $msg = draw_important_message(_IP_ADDRESS_BLOCKED, false);
			else if($objLogin->EmailBlocked($email)) $msg = draw_important_message(_EMAIL_BLOCKED, false);			
		}

		if($msg == ""){
			// check if email already exists                    
			$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE email = '".encode_text($email)."'";
			$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
			if($result[1] > 0){
				if($result[0]['user_name'] != ''){
					$msg = draw_important_message(_USER_EMAIL_EXISTS_ALERT, false);	
				}else{
					$_SESSION['reset_account_email'] = $result[0]['email'];
					$msg = draw_important_message(_NO_USER_EMAIL_EXISTS_ALERT, false);
				}					
			}else{
				// check if user already exists
				$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE user_name != '' AND user_name = '".encode_text($user_name)."'";
				$result = database_query($sql, DATA_AND_ROWS);
				if($result[1] > 0){
					$msg = draw_important_message(_USER_EXISTS_ALERT, false);
				}					
			}
		}
		
		if($msg == ""){
			$user_ip = get_current_ip();			
			if($reg_confirmation == "by email"){
				$registration_code = strtoupper(get_random_string(19));
				$is_active = "0";
			}else if($reg_confirmation == "by admin"){
				$registration_code = "";
				$is_active = "0";
			}else{
				$registration_code = "";
				$is_active = "1";
			}
			
			if(!PASSWORDS_ENCRYPTION){
				$user_password = "'".encode_text($user_password1)."'";
			}else{
				if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){					
					$user_password = "AES_ENCRYPT('".encode_text($user_password1)."', '".PASSWORDS_ENCRYPT_KEY."')";
				}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
					$user_password = "MD5('".encode_text($user_password1)."')";
				}
			}
			
			// insert new user
			$sql = "INSERT INTO ".TABLE_CLIENTS."(
						first_name, last_name, birth_date, company,
						b_address, b_address_2, b_city, b_state, b_country, b_zipcode,
						phone, email, url,
						user_name, user_password,
						date_created, registered_from_ip, last_logged_ip,
						email_notifications, is_active, is_removed, comments, registration_code)
					VALUES(
						'".encode_text($first_name)."', '".encode_text($last_name)."', '".encode_text($birth_date)."', '".encode_text($company)."',
						'".encode_text($b_address)."', '".encode_text($b_address_2)."', '".encode_text($b_city)."', '".encode_text($b_state)."', '".encode_text($b_country)."', '".encode_text($b_zipcode)."',
						'".encode_text($phone)."', '".encode_text($email)."', '".encode_text($url)."',
						'".encode_text($user_name)."', ".$user_password.",
						'".date("Y-m-d H:i:s")."', '".encode_text($user_ip)."', '',
						'".(int)$send_updates."', '".(int)$is_active."', 0, '', '".encode_text($registration_code)."')";
			if(database_void_query($sql) > 0){
		
				////////////////////////////////////////////////////////////
				$sender = $objSettings->GetParameter("admin_email");
				$recipiant = $email;
				
				$objEmailTemplates = new EmailTemplates();				
				if($reg_confirmation == "by email"){
					$email_template = $objEmailTemplates->GetTemplate("new_account_created_confirm_by_email", Application::Get("lang"));					
				}else if($reg_confirmation == "by admin"){
					$email_template = $objEmailTemplates->GetTemplate("new_account_created_confirm_by_admin", Application::Get("lang"));					
				}else{
					$email_template = $objEmailTemplates->GetTemplate("new_account_created", Application::Get("lang"));					
				}
				$subject = $email_template["template_subject"];
				
				$arr_constants = array("{FIRST NAME}", "{LAST NAME}", "{USER NAME}", "{USER PASSWORD}", "{BASE URL}", "{YEAR}", "{REGISTRATION CODE}");
				$arr_values    = array($first_name, $last_name, $user_name, $user_password1, APPHP_BASE, date("Y"), $registration_code);

				$body   = "<div style=direction:".Application::Get("lang_dir").">";
				$body  .= str_ireplace($arr_constants, $arr_values, $email_template["template_content"]);
				$body  .= "</div>";
				
				$text_version = strip_tags($body);
				$html_version = nl2br($body);

				$objEmail = new Email($recipiant, $sender, $subject);                     
				$objEmail->textOnly = false;
				$objEmail->content = $html_version;                
				$objEmail->Send();
				////////////////////////////////////////////////////////////
		
				if($reg_confirmation == "by email" || $reg_confirmation == "by admin"){
					$msg = draw_success_message(_ACCOUNT_CREATED_CONF_MSG, false);
					$msg .= "<br />".draw_message(_ACCOUT_CREATED_CONF_LINK, false);
				}else{
					$msg = draw_success_message(_ACCOUNT_CREATED_NON_CONFIRM_MSG, false);
					$msg .= "<br />".draw_message(_ACCOUNT_CREATED_NON_CONFIRM_LINK, false);
				}

				$account_created = true;
			
			}else{
				$msg = draw_important_message(_CREATING_ACCOUNT_ERROR, false);
			}                    		
		}		
	}


	draw_title_bar(_CREATING_NEW_ACCOUNT);
	
?>

<?php if($account_created){ ?>
	
	<div>
		<div><?php echo (($msg == "") ? $msg_default : $msg); ?></div>
	</div>

<?php }else{ ?>

	<script type="text/javascript"> 
	function btnSubmitPD_OnClick(){
		frmReg = document.getElementById("frmRegistration");
		
		if(frmReg.first_name.value == "")	  { alert("<?php echo _FIRST_NAME_EMPTY_ALERT; ?>"); frmReg.first_name.focus(); return false;
		}else if(frmReg.last_name.value == ""){ alert("<?php echo _LAST_NAME_EMPTY_ALERT; ?>"); frmReg.last_name.focus(); return false;        
		}else if(frmReg.b_address.value == ""){ alert("<?php echo _ADDRESS_EMPTY_ALERT; ?>"); frmReg.b_address.focus(); return false;        
		}else if(frmReg.b_city.value == "")   { alert("<?php echo _CITY_EMPTY_ALERT; ?>"); frmReg.b_city.focus(); return false;        
		}else if(frmReg.b_country.value == ""){ alert("<?php echo _COUNTRY_EMPTY_ALERT; ?>"); frmReg.b_country.focus(); return false;        
		}else if(frmReg.b_zipcode.value == ""){ alert("<?php echo _ZIPCODE_EMPTY_ALERT; ?>"); frmReg.b_zipcode.focus(); return false;        
		}else if(frmReg.phone.value == "")    { alert("<?php echo _PHONE_EMPTY_ALERT; ?>"); frmReg.phone.focus(); return false;        
		}else if(frmReg.email.value == "")    { alert("<?php echo _EMAIL_EMPTY_ALERT; ?>"); frmReg.email.focus(); return false;        
		}else if(!appIsEmail(frmReg.email.value))  { alert("<?php echo _EMAIL_VALID_ALERT; ?>"); frmReg.email.focus(); return false;        
		}else if(frmReg.user_name.value == "")     { alert("<?php echo _USERNAME_EMPTY_ALERT; ?>"); frmReg.user_name.focus(); return false;        
		}else if(frmReg.user_password1.value == ""){ alert("<?php echo _PASSWORD_IS_EMPTY; ?>"); frmReg.user_password1.focus(); return false;        
		}else if(frmReg.user_password2.value == ""){ alert("<?php echo _CONF_PASSWORD_IS_EMPTY; ?>"); frmReg.user_password2.focus(); return false;        
		}else if(frmReg.user_password1.value != frmReg.user_password2.value){ alert("<?php echo _CONF_PASSWORD_MATCH; ?>"); frmReg.user_password2.focus(); return false;        		
		<?php if($image_verification_allow == "yes"){ ?> }else if(frmReg.captcha_code.value == "")  { alert("<?php echo _IMAGE_VERIFY_EMPTY; ?>"); frmReg.captcha_code.focus(); return false; <?php } ?>
		}else if(!frmReg.agree.checked)            { alert("<?php echo _CONFIRM_TERMS_CONDITIONS; ?>"); return false; }
		return true;
	}
	</script>

	<?php draw_content_start(); ?>

	<a name="top"></a>		
	<p style='padding-left:3px;'>
		<?php echo _ALERT_REQUIRED_FILEDS; ?>
	</p>		
			
	<?php echo $msg; ?>        
	
	<form action="index.php?client=create_account" method="post" name="frmRegistration" id="frmRegistration">
		<input type="hidden" name="act" value="create" />
		<?php draw_token_field(); ?>
		
		<table cellspacing="2" cellpadding="2" width="100%">
		<tbody>		
		<tr><td colspan="3"><b><?php echo _PERSONAL_DETAILS;?></b><hr size="1" noshade="noshade" /></td></tr>	
		<tr>
			<td width="38%" align="right"><?php echo _FIRST_NAME;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="first_name" name="first_name" size="32" maxlength="32" value="<?php echo decode_text($first_name);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _LAST_NAME;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="last_name" name="last_name" size="32" maxlength="32" value="<?php echo decode_text($last_name);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _BIRTH_DATE;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap">
				<?php echo draw_date_select_field("birth_date", $birth_date, "90", "0", false); ?>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo _COMPANY;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap"><input type="text" id="company" name="company" size="32" maxlength="255" value="<?php echo decode_text($company);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _WEB_SITE;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap"><input type="text" id="url" name="url" size="32" maxlength="128" value="<?php echo decode_text($url);?>" /></td>
		</tr>

		<tr><td colspan="3"><b><?php echo _BILLING_ADDRESS;?></b><hr size="1" noshade="noshade" /></td></tr>		    
		<tr>
			<td align="right"><?php echo _ADDRESS;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="b_address" name="b_address" size="32" maxlength="64" value="<?php echo decode_text($b_address);?>" /></td>
		</tr>	
		<tr>
			<td align="right"><?php echo _ADDRESS_2;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap"><input type="text" id="b_address_2" name="b_address_2" size="32" maxlength="64" value="<?php echo decode_text($b_address_2);?>" /></td>
		</tr>	
		<tr>
			<td align="right"><?php echo _CITY;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="b_city" name="b_city" size="32" maxlength="64" value="<?php echo decode_text($b_city);?>" /></td>
		</tr>	
		<tr>
			<td align="right"><?php echo _STATE_PROVINCE;?></td>
			<td></td>
			<td nowrap="nowrap"><input type="text" id="b_state" name="b_state" size="32" maxlength="64" value="<?php echo decode_text($b_state);?>" /></td>
		</tr>					
		<tr>
			<td align="right"><?php echo _COUNTRY;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap">
			
				<select name="b_country" id="b_country">
				<option value="">-- <?php echo _SELECT;?> --</option>
				<?php
					$sql = "SELECT * FROM ".TABLE_COUNTRIES." ORDER BY priority_order DESC, name ASC";            
					$countries = database_query($sql, DATA_AND_ROWS);
					for($i=0; $i < $countries[1]; $i++){
						echo "<option ".(($b_country == $countries[0][$i]['abbrv']) ? "selected" : "")." value='".$countries[0][$i]['abbrv']."'>".decode_text($countries[0][$i]['name'])."</option>";
					}                    
				?>
				</select>
			</td>
		</tr>	
		<tr>
			<td align="right"><?php echo _ZIP_CODE;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="b_zipcode" name="b_zipcode" size="32" maxlength="32" value="<?php echo decode_text($b_zipcode);?>" /></td>
		</tr>

		<tr><td height="20" colspan="3"><b><?php echo _CONTACT_INFORMATION;?></b><hr size="1" noshade="noshade" /></td></tr>
		<tr>
			<td align="right"><?php echo _PHONE;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="phone" name="phone" size="32" maxlength="32" value="<?php echo decode_text($phone);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _EMAIL_ADDRESS;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap">				 
				<?php echo _ENTER_EMAIL_ADDRESS;?>
				<br />
				<input type="text" id="email" name="email" size="32" maxlength="128" value="<?php echo decode_text($email);?>" />
			</td>
		</tr>


		<tr><td colspan="3"><b><?php echo _ACCOUNT_DETAILS;?></b><hr size="1" noshade="noshade" /></td></tr>
		<tr>
			<td align="right"><?php echo _USERNAME;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="frmReg_user_name" name="user_name" size="32" maxlength="32" value="<?php echo decode_text($user_name);?>" /></td>
		</tr>		    
		<tr>
			<td align="right"><?php echo _PASSWORD;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="password" id="frmReg_user_password1" name="user_password1" size="32" maxlength="64" value="<?php echo decode_text($user_password1);?>" /></td>
		</tr>		    
		<tr>
			<td align="right"><?php echo _CONFIRM_PASSWORD;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="password" id="frmReg_user_password2" name="user_password2" size="32" maxlength="32" value="<?php echo decode_text($user_password1);?>" /></td>
		</tr>

		<?php if($image_verification_allow == "yes"){?>
		<tr><td height="20" colspan="3"><b><?php echo _IMAGE_VERIFICATION; ?></b><hr size="1" noshade="noshade" /></td></tr>
		<tr valign="top">
		<td align="left">
			<?php echo _TYPE_CHARS; ?> 			    
		</td>
		<td></td>
		<td>
			<table border='0'>
			<tr>
				<td><img style="padding:0px; margin:0px;" id="captcha_image" src="modules/captcha/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>" /></td>				
				<td>
					<a href="modules/captcha/securimage_play.php"><img style="padding:0px; margin:0px; border:0px;" id="captcha_image_play" src="modules/captcha/images/audio_icon.gif" title="<?php echo _PLAY; ?>" alt="<?php echo _PLAY; ?>" /></a><br />
					<img style="cursor:pointer; padding:0px; margin:0px; border:0px;" id="captcha_image_reload" src="modules/captcha/images/refresh.gif" style="cursor:pointer;" onclick="document.getElementById('captcha_image').src = 'modules/captcha/securimage_show.php?sid=' + Math.random(); return false" title="<?php echo _REFRESH; ?>" alt="<?php echo _REFRESH; ?>" />				
				</td>				
				</tr>
				<tr>
				<td colspan="2">
					<input type="text" id="frmReg_captcha_code" name="captcha_code" style="width:148px;" value="" />
				</td>
			</tr>
			</table>			    			    
		</td>
		</tr>
		<?php } ?>

		<tr><td colspan="3" nowrap height="7px"></td></tr>
		<tr>
			<td colspan="3" align="left">
			<table>					
			<tr valign="top">
				<td align="right"><input type='checkbox' name="send_updates" id="send_updates" <?php echo (($send_updates == "1") ? "checked" : "");?> value="1"></td>
				<td>&nbsp;</td>
				<td><?php echo _NOTIFICATION_MSG; ?></td>
			</tr>					
			<tr><td colspan="3" nowrap height="5px"></td></tr>
			<tr valign="middle">
				<td align="right"><input type='checkbox' name="agree" id="agree" value="1" <?php echo ($agree == "1") ? "checked='checked'" : ""; ?>></td>
				<td>&nbsp;</td>
				<td><a href="index.php?client=create_account#top" onclick="javascript:appShowTermsAndConditions()"><?php echo _AGREE_CONF_TEXT; ?></a></td>
			</tr>					
			</table>
			</td>
		</tr>

		<tr>
			<td colspan="3" align="center">
			<br /><br />
			<input type='submit' class="form_button" name="btnSubmitPD" id="btnSubmitPD" value="<?php echo _SUBMIT; ?>" onclick="return btnSubmitPD_OnClick()">
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
		<td colspan="3" align="left">
			<p><?php echo _CREATE_ACCOUNT_NOTE; ?></p>
		</td>
		</tr>
		</tbody>
		</table>
	</form>

	<div id="fade" class="black_overlay" onclick="javascript:appCloseTermsAndConditions();"></div>
	<div id="light">
		<div class="white_header">
			<div class='title_left'>
			<?php
				$objPageTemp = new Pages("terms", true);				
				$objPageTemp->DrawTitle(); 
			?>
			</div>
			<div class='title_right'>
				<a href = "javascript:void(0)" onclick="javascript:appCloseTermsAndConditions();"><?php echo _CLOSE; ?></a>
			</div>			
		</div>
		<div class="white_content">	
			<?php
				$objPageTemp->DrawText();	
			?>
		</div>
	</div>		

	<script type="text/javascript">
	appSetFocus('<?php echo $focus_field; ?>');
	</script>
	<?php draw_content_end(); ?>
<?php
	}	
}else{
	draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>"",_CREATING_NEW_ACCOUNT=>"")));
	
	draw_content_start();
	draw_message(_ALREADY_LOGGED);
	draw_content_end();
}
?>