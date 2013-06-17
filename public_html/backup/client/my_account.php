<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsClient()){

	$task 		 = isset($_POST['task']) ? prepare_input($_POST['task']) : "";
	$send_updates = isset($_POST['send_updates']) ? (int)$_POST['send_updates'] : "0";
	$first_name  = isset($_POST['first_name']) ? prepare_input($_POST['first_name']) : "";
	$last_name   = isset($_POST['last_name']) ? prepare_input($_POST['last_name']) : "";

	$birth_date_year = isset($_POST['birth_date__nc_year']) ? prepare_input($_POST['birth_date__nc_year']) : "";
	$birth_date_month = isset($_POST['birth_date__nc_month']) ? prepare_input($_POST['birth_date__nc_month']) : "";
	$birth_date_day = isset($_POST['birth_date__nc_day']) ? prepare_input($_POST['birth_date__nc_day']) : "";
	$birth_date = $birth_date_year."-".$birth_date_month."-".$birth_date_day;
	if($birth_date == "--") $birth_date = "";

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
	///$user_name   = isset($_POST['user_name']) ? $_POST['user_name'] : "";
	$user_password1 = isset($_POST['user_password1']) ? prepare_input($_POST['user_password1']) : "";
	$user_password2 = isset($_POST['user_password2']) ? prepare_input($_POST['user_password2']) : "";
	$user_password  = "";
	$agree       = isset($_POST['agree']) ? (int)$_POST['agree'] : "";	
	$focus_field = "first_name";

	$msg_default = "";
	$msg = "";

	$account_created = false;
	
	if($task == "update"){
		
		if($first_name == ""){
			$msg = draw_important_message(_FIRST_NAME_EMPTY_ALERT, false);
		}else if($last_name == ""){
			$msg = draw_important_message(_LAST_NAME_EMPTY_ALERT, false);
		}else if($birth_date != "" && !check_date($birth_date)){
			$msg = draw_important_message(_BIRTH_DATE_VALID_ALERT, false);
		}else if($b_address == ""){
			$msg = draw_important_message(_ADDRESS_EMPTY_ALERT, false);
		}else if($b_city == ""){
			$msg = draw_important_message(_CITY_EMPTY_ALERT, false);
		}else if($b_country == ""){
			$msg = draw_important_message(_COUNTRY_EMPTY_ALERT, false);
		}else if($b_zipcode == ""){
			$msg = draw_important_message(_ZIPCODE_EMPTY_ALERT, false);
		}else if($phone == ""){
			$msg = draw_important_message(_PHONE_EMPTY_ALERT, false);
		}else if($email == ""){
			$msg = draw_important_message(_EMAIL_EMPTY_ALERT, false);
			$focus_field = "email";
		}else if(($email != "") && (!check_email_address($email))){
			$msg = draw_important_message(_EMAIL_VALID_ALERT, false);
			$focus_field = "email";
		}else if(($user_password1 != "") && (strlen($user_password1) < 6)){
			$msg = draw_important_message(_PASSWORD_IS_EMPTY, false);
			$user_password1 = $user_password2 = "";
			$focus_field = "user_password1";
		}else if(($user_password1 == "") && ($user_password2 != "")){
			$msg = draw_important_message(_PASSWORD_IS_EMPTY, false);
			$user_password1 = $user_password2 = "";
			$focus_field = "user_password";
		}else if(($user_password1 != "") && ($user_password2 == "")){
			$msg = draw_important_message(_CONF_PASSWORD_IS_EMPTY, false);
			$user_password1 = $user_password2 = "";
			$focus_field = "user_password1";
		}else if(($user_password1 != "") && ($user_password2 != "") && ($user_password1 != $user_password2)){
			$msg = draw_important_message(_CONF_PASSWORD_MATCH, false);
			$user_password1 = $user_password2 = "";
			$focus_field = "user_password1";
		}else{
			if(!PASSWORDS_ENCRYPTION){
				$user_password = "user_password='".$user_password1."'";
			}else{
				if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "aes"){					
					$user_password = "user_password=AES_ENCRYPT('".$user_password1."', '".PASSWORDS_ENCRYPT_KEY."')";
				}else if(strtolower(PASSWORDS_ENCRYPTION_TYPE) == "md5"){
					$user_password = "user_password=MD5('".$user_password1."')";
				}
			}			
		}
		
		// check if email already exists                    
		$sql = "SELECT * FROM ".TABLE_CLIENTS." WHERE email = '".mysql_real_escape_string($email)."' AND id != '".(int)@$objLogin->GetLoggedID()."'";
		$result = database_query($sql, DATA_AND_ROWS);
		if($result[1] > 0){
			$msg = draw_important_message(_USER_EMAIL_EXISTS_ALERT, false);
		}			
		
		// deny all operations in demo version
		if(strtolower(SITE_MODE) == "demo"){
			$msg = draw_important_message(_OPERATION_BLOCKED, false);
		}		
		
		if($msg == ""){			
			// insert new user
			$sql = "UPDATE ".TABLE_CLIENTS." SET
						first_name  = '".encode_text($first_name)."',
						last_name   = '".encode_text($last_name)."',
						birth_date  = '".(($birth_date != "") ? encode_text($birth_date) : "0000-00-00")."',
						company     = '".encode_text($company)."',
						b_address   = '".encode_text($b_address)."',
						b_address_2 = '".encode_text($b_address_2)."',
						b_city      = '".encode_text($b_city)."',
						b_state     = '".encode_text($b_state)."',
						b_country   = '".encode_text($b_country)."',
						b_zipcode   = '".encode_text($b_zipcode)."',
						phone       = '".encode_text($phone)."',
						email       = '".encode_text($email)."',
						url         = '".encode_text($url)."',						
						".((($user_password1 != "") && ($user_password2 != "")) ? $user_password."," : "")."
						notification_status_changed = IF(email_notifications <> '".(int)$send_updates."', '".date("Y-m-d H:i:s")."', notification_status_changed),
						email_notifications = '".(int)$send_updates."'
					WHERE id = ".(int)@$objLogin->GetLoggedID();
					
			if(database_void_query($sql) > 0){
				$msg = draw_success_message(_ACCOUNT_WAS_UPDATED, false);
			}else{
				$msg = draw_important_message(_UPDATING_ACCOUNT_ERROR, false);
			}                    		
		}		
	}

	$objClients = new Clients();
	$user_info = $objClients->GetInfoByID(@$objLogin->GetLoggedID());

	draw_title_bar(prepare_breadcrumbs(array(_MY_ACCOUNT=>"",_EDIT_MY_ACCOUNT=>"")));
	
?>
	<script type="text/javascript"> 
	function btnSubmitPD_OnClick(){
		frmEdit = document.getElementById("frmEditAccount");
		
		if(frmEdit.first_name.value == ""){
			alert("<?php echo _FIRST_NAME_EMPTY_ALERT; ?>");
			frmEdit.first_name.focus();
			return false;
		}else if(frmEdit.last_name.value == ""){
			alert("<?php echo _LAST_NAME_EMPTY_ALERT; ?>");
			frmEdit.last_name.focus();
			return false;        
		}else if(frmEdit.b_address.value == ""){
			alert("<?php echo _ADDRESS_EMPTY_ALERT; ?>");
			frmEdit.b_address.focus();
			return false;        
		}else if(frmEdit.b_city.value == ""){
			alert("<?php echo _CITY_EMPTY_ALERT; ?>");
			frmEdit.b_city.focus();
			return false;        
		}else if(frmEdit.b_country.value == ""){
			alert("<?php echo _COUNTRY_EMPTY_ALERT; ?>");
			frmEdit.b_country.focus();
			return false;        
		}else if(frmEdit.b_zipcode.value == ""){
			alert("<?php echo _ZIPCODE_EMPTY_ALERT; ?>");
			frmEdit.b_zipcode.focus();
			return false;        
		}else if(frmEdit.phone.value == ""){
			alert("<?php echo _PHONE_EMPTY_ALERT; ?>");
			frmEdit.phone.focus();
			return false;        
		}else if(frmEdit.email.value == ""){
			alert("<?php echo _EMAIL_EMPTY_ALERT; ?>");
			frmEdit.email.focus();
			return false;        
		}else if(!appIsEmail(frmEdit.email.value)){
			alert("<?php echo _EMAIL_VALID_ALERT; ?>");
			frmEdit.email.focus();
			return false;        
		}else if(frmEdit.user_name.value == ""){
			alert("<?php echo _USERNAME_EMPTY_ALERT; ?>");
			frmEdit.user_name.focus();
			return false;        
		}else if((frmEdit.user_password1.value != "") && (frmEdit.user_password1.value != frmEdit.user_password2.value)){
			alert("<?php echo _CONF_PASSWORD_MATCH; ?>");
			frmEdit.user_password2.focus();
			return false;        		
		}
		return true;
	}
	</script>

	<?php echo (($msg == "") ? $msg_default : $msg); ?>
	<?php draw_content_start(); ?>

	<p style='padding-left:3px;'>
		<?php echo _ALERT_REQUIRED_FILEDS; ?>						
	</p>						
	
	<form action="index.php?client=my_account" method="post" name="frmEditAccount" id="frmEditAccount">
		<input type="hidden" name="task" value="update" />
		<?php draw_token_field(); ?>
		
		<table cellspacing="2" cellpadding="2" width="100%">
		<tbody>
		
		<tr><td colspan="3"><b><?php echo _PERSONAL_DETAILS;?></b><hr size="1" noshade="noshade" /></td></tr>	
		<tr>
			<td width="38%" align="right"><?php echo _FIRST_NAME;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="first_name" name="first_name" size="32" maxlength="32" value="<?php echo decode_text($user_info['first_name']);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _LAST_NAME;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="last_name" name="last_name" size="32" maxlength="32" value="<?php echo decode_text($user_info['last_name']);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _BIRTH_DATE;?></td>
			<td>&nbsp;</td>
			<td>
				<?php echo draw_date_select_field("birth_date", $user_info['birth_date'], "90", "0", false); ?>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo _COMPANY;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap"><input type="text" id="company" name="company" size="32" maxlength="255" value="<?php echo decode_text($user_info['company']);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _WEB_SITE;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap"><input type="text" id="url" name="url" size="32" maxlength="128" value="<?php echo decode_text($user_info['url']);?>" /></td>
		</tr>

		<tr><td colspan="3"><b><?php echo _BILLING_ADDRESS;?></b><hr size="1" noshade="noshade" /></td></tr>		    
		<tr>
			<td align="right"><?php echo _ADDRESS;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="b_address" name="b_address" size="32" maxlength="64" value="<?php echo decode_text($user_info['b_address']);?>" /></td>
		</tr>	
		<tr>
			<td align="right"><?php echo _ADDRESS_2;?></td>
			<td>&nbsp;</td>
			<td nowrap="nowrap"><input type="text" id="b_address_2" name="b_address_2" size="32" maxlength="64" value="<?php echo decode_text($user_info['b_address_2']);?>" /></td>
		</tr>	
		<tr>
			<td align="right"><?php echo _CITY;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="b_city" name="b_city" size="32" maxlength="64" value="<?php echo decode_text($user_info['b_city']);?>" /></td>
		</tr>	
		<tr>
			<td align="right"><?php echo _STATE_PROVINCE;?></td>
			<td></td>
			<td nowrap="nowrap"><input type="text" id="b_state" name="b_state" size="32" maxlength="64" value="<?php echo decode_text($user_info['b_state']);?>" /></td>
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
						echo "<option ".(($user_info['b_country'] == $countries[0][$i]['abbrv']) ? "selected" : "")." value='".$countries[0][$i]['abbrv']."'>".decode_text($countries[0][$i]['name'])."</option>";
					}                    
				?>
				</select>
			</td>
		</tr>	
		<tr>
			<td align="right"><?php echo _ZIP_CODE;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="b_zipcode" name="b_zipcode" size="32" maxlength="32" value="<?php echo decode_text($user_info['b_zipcode']);?>" /></td>
		</tr>

		<tr><td height="20" colspan="3"><b><?php echo _CONTACT_INFORMATION;?></b><hr size="1" noshade="noshade" /></td></tr>
		<tr>
			<td align="right"><?php echo _PHONE;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="text" id="phone" name="phone" size="32" maxlength="32" value="<?php echo decode_text($user_info['phone']);?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo _EMAIL_ADDRESS;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap">				 
				<?php echo _ENTER_EMAIL_ADDRESS;?>
				<br />
				<input type="text" id="email" name="email" size="32" maxlength="128" value="<?php echo decode_text($user_info['email']);?>" />
			</td>
		</tr>

		<tr><td colspan="3"><b><?php echo _ACCOUNT_DETAILS;?></b><hr size="1" noshade="noshade" /></td></tr>
		<tr>
			<td align="right"><?php echo _USERNAME;?></td>
			<td class="mandatory_star">*</td>
			<td nowrap="nowrap"><label><?php echo decode_text($user_info['user_name']);?></label></td>
		</tr>		    
		<tr>
			<td align="right"><?php echo _PASSWORD;?></td>
			<td><font class="mandatory_star">*</font></td>
			<td nowrap="nowrap"><input type="password" id="user_password1" name="user_password1" size="32" maxlength="64" value="" /></td>
		</tr>		    
		<tr>
			<td align="right"><?php echo _CONFIRM_PASSWORD;?></td>
			<td class="mandatory_star">*</td>
			<td nowrap="nowrap"><input type="password" id="user_password2" name="user_password2" size="32" maxlength="32" value="" /></td>
		</tr>

		<tr><td colspan="3" nowrap height="7px"></td></tr>
		<tr>
			<td colspan="3" align="left">
			<table>					
			<tr valign="top">
				<td align="right"><input type='checkbox' name="send_updates" id="send_updates" <?php echo (($user_info['email_notifications'] == "1") ? "checked" : "");?> value="1"></td>
				<td>&nbsp;</td>
				<td><?php echo _NOTIFICATION_MSG; ?></td>
			</tr>					
			</table>
			</td>
		</tr>

		<tr>
			<td colspan="3" align="center">
				<br /><br />
				<input type='submit' name="btnSubmitPD" id="btnSubmitPD" value="<?php echo _SUBMIT; ?>" onclick="return btnSubmitPD_OnClick()">
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td colspan="3" align="right">
				<hr size="1" noshade="noshade" />
				<input type='button' name="btnRemoveAccount" id="btnRemoveAccount" value="<?php echo _REMOVE_ACCOUNT; ?>" onclick="javascript:appGoTo('client=account_remove');" />
			</td>
		</tr>

		</table>
	</form>

	<script type="text/javascript">
	appSetFocus('<?php echo $focus_field; ?>');
	</script>
	<?php draw_content_end(); ?>

<?php
} else draw_important_message(_NOT_AUTHORIZED);
?>