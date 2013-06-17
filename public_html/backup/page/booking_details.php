<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

draw_reservation_bar("booking_details");

if(Modules::IsModuleInstalled("booking"))
{
	if($is_active == "yes")
	{
		if($current_client_id != "" && $m == "edit"){
			draw_sub_title_bar(_UPDATING_ACCOUNT, true, "h4");
		}else{
			echo "<table width='100%'>";
			echo "<tr>";
			echo "<td align='".Application::Get("defined_left")."'>".draw_sub_title_bar(_CREATING_NEW_ACCOUNT, false, "h4")."</td>";
			echo "<td align='".Application::Get("defined_right")."'>"._ALREADY_HAVE_ACCOUNT."</td>";
			echo "</tr>";
			echo "</table>";
		} 		

?>

		<!-- NOT LOGGED -->
		<script type="text/javascript"> 
		function btnSubmitPD_OnClick(){

			frmReg = document.getElementById("frmRegistration");
			
			if(frmReg.first_name.value == ""){
				alert("<?php echo _FIRST_NAME_EMPTY_ALERT; ?>");
				frmReg.first_name.focus();
				return false;
			}else if(frmReg.last_name.value == ""){
				alert("<?php echo _LAST_NAME_EMPTY_ALERT; ?>");
				frmReg.last_name.focus();
				return false;        
			}else if(frmReg.b_address.value == ""){
				alert("<?php echo _ADDRESS_EMPTY_ALERT; ?>");
				frmReg.b_address.focus();
				return false;        
			}else if(frmReg.b_city.value == ""){
				alert("<?php echo _CITY_EMPTY_ALERT; ?>");
				frmReg.b_city.focus();
				return false;        
			}else if(frmReg.b_country.value == ""){
				alert("<?php echo _COUNTRY_EMPTY_ALERT; ?>");
				frmReg.b_country.focus();
				return false;        
			}else if(frmReg.b_zipcode.value == ""){
				alert("<?php echo _ZIPCODE_EMPTY_ALERT; ?>");
				frmReg.b_zipcode.focus();
				return false;        
			}else if(frmReg.phone.value == ""){
				alert("<?php echo _PHONE_EMPTY_ALERT; ?>");
				frmReg.phone.focus();
				return false;        
			}else if(frmReg.email.value == ""){
				alert("<?php echo _EMAIL_EMPTY_ALERT; ?>");
				frmReg.email.focus();
				return false;        
			}else if(!appIsEmail(frmReg.email.value)){
				alert("<?php echo _EMAIL_VALID_ALERT; ?>");
				frmReg.email.focus();
				return false;        
			}
			<?php if($current_client_id != "" && $m == "edit"){ ?>
			
			<?php }else{ ?>
				<?php if($registration_required == "yes"){ ?>
				if(frmReg.user_name.value == ""){
					alert("<?php echo _USERNAME_EMPTY_ALERT; ?>");
					frmReg.user_name.focus();
					return false;        
				}else if(frmReg.user_password1.value == ""){
					alert("<?php echo _PASSWORD_IS_EMPTY; ?>");
					frmReg.user_password1.focus();
					return false;        
				}else if(frmReg.user_password2.value == ""){
					alert("<?php echo _CONF_PASSWORD_IS_EMPTY; ?>");
					frmReg.user_password2.focus();
					return false;        
				}else if(frmReg.user_password1.value != frmReg.user_password2.value){
					alert("<?php echo _CONF_PASSWORD_MATCH; ?>");
					frmReg.user_password2.focus();
					return false;        		
				}
				<?php } ?>
				if(frmReg.captcha_code && frmReg.captcha_code.value == ""){
					alert("<?php echo _IMAGE_VERIFY_EMPTY; ?>");
					frmReg.captcha_code.focus();
					return false;
				}else if(frmReg.agree && !frmReg.agree.checked){
					alert("<?php echo _CONFIRM_TERMS_CONDITIONS; ?>");
					return false;
				}
			<?php } ?>
			return true;
		}
		</script>
	
		<p style='padding-left:3px;'>
			<?php echo _ALERT_REQUIRED_FILEDS; ?>
		</p>		
				
		<?php echo $msg; ?>        
		
		<form action="index.php?page=booking_details" method="post" name="frmRegistration" id="frmRegistration">
		<?php draw_token_field(); ?>
			<?php if($current_client_id != "" && $m == "edit"){ ?>
				<input type="hidden" name="act" value="update" />
				<input type="hidden" name="m" value="edit" />
			<?php }else{ ?>
				<input type="hidden" name="act" value="create" />
			<?php } ?>
			
			<table cellspacing="1" cellpadding="2" width="100%">
			<tbody>
			
			<tr><td colspan="3"><b><?php echo _PERSONAL_DETAILS;?></b><hr size="1" noshade="noshade" /></td></tr>	
			<tr>
				<td width="38%" align="right"><?php echo _FIRST_NAME;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap"><input type="text" id="first_name" name="first_name" size="32" maxlength="32" value="<?php echo $first_name;?>" /></td>
			</tr>
			<tr>
				<td align="right"><?php echo _LAST_NAME;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap"><input type="text" id="last_name" name="last_name" size="32" maxlength="32" value="<?php echo $last_name;?>" /></td>
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
				<td nowrap="nowrap"><input type="text" id="company" name="company" size="32" maxlength="255" value="<?php echo $company;?>" /></td>
			</tr>
			<tr>
				<td align="right"><?php echo _WEB_SITE;?></td>
				<td>&nbsp;</td>
				<td nowrap="nowrap"><input type="text" id="url" name="url" size="32" maxlength="128" value="<?php echo $url;?>" /></td>
			</tr>
	
			<tr><td colspan="3"><b><b><?php echo _BILLING_ADDRESS;?></b><hr size="1" noshade="noshade" /></td></tr>		    
			<tr>
				<td align="right"><?php echo _ADDRESS;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap"><input type="text" id="b_address" name="b_address" size="32" maxlength="64" value="<?php echo $b_address;?>" /></td>
			</tr>	
			<tr>
				<td align="right"><?php echo _ADDRESS_2;?></td>
				<td>&nbsp;</td>
				<td nowrap="nowrap"><input type="text" id="b_address_2" name="b_address_2" size="32" maxlength="64" value="<?php echo $b_address_2;?>" /></td>
			</tr>	
			<tr>
				<td align="right"><?php echo _CITY;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap"><input type="text" id="b_city" name="b_city" size="32" maxlength="64" value="<?php echo $b_city;?>" /></td>
			</tr>	
			<tr>
				<td align="right"><?php echo _STATE_PROVINCE;?></td>
				<td></td>
				<td nowrap="nowrap"><input type="text" id="b_state" name="b_state" size="32" maxlength="64" value="<?php echo $b_state;?>" /></td>
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
							echo "<option ".(($b_country == $countries[0][$i]['abbrv']) ? "selected" : "")." value='".$countries[0][$i]['abbrv']."'>".$countries[0][$i]['name']."</option>";
						}                    
					?>
					</select>
				</td>
			</tr>	
			<tr>
				<td align="right"><?php echo _ZIP_CODE;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap"><input type="text" id="b_zipcode" name="b_zipcode" size="32" maxlength="32" value="<?php echo $b_zipcode;?>" /></td>
			</tr>
	
			<tr><td height="20" colspan="3"><b><?php echo _CONTACT_INFORMATION;?></b><hr size="1" noshade="noshade" /></td></tr>
			<tr>
				<td align="right"><?php echo _PHONE;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap"><input type="text" id="phone" name="phone" size="32" maxlength="32" value="<?php echo $phone;?>" /></td>
			</tr>
			<tr>
				<td align="right"><?php echo _EMAIL_ADDRESS;?></td>
				<td><font class="mandatory_star">*</font></td>
				<td nowrap="nowrap">				 
					<?php echo _ENTER_EMAIL_ADDRESS;?>
					<br />
					<input type="text" id="email" name="email" size="32" maxlength="128" value="<?php echo $email;?>" />
				</td>
			</tr>
	
			<?php if($current_client_id != "" && $m == "edit"){ ?>
				<tr>
					<td colspan="3" align="center">
						<br /><br />
						<input type='submit' class="form_button" name="btnSubmitPD" id="btnSubmitPD" value="<?php echo _BUTTON_UPDATE; ?>" onclick="return btnSubmitPD_OnClick()">
						&nbsp;&nbsp;&nbsp;
						<input type='button' class='form_button' onclick='javascript:appGoTo("page=booking_details")' value='<?php echo _CHECKOUT; ?>' />
					</td>
				</tr>
			<?php }else{ ?>
				<?php if($registration_required == "yes"){ ?>
				<tr><td colspan="3"><b><?php echo _ACCOUNT_DETAILS;?></b><hr size="1" noshade="noshade" /></td></tr>
				<tr>
					<td align="right"><?php echo _USERNAME;?></td>
					<td class="mandatory_star">*</td>
					<td nowrap="nowrap"><input type="text" id="user_name" name="user_name" size="32" maxlength="32" value="<?php echo $user_name;?>" /></td>
				</tr>		    
				<tr>
					<td align="right"><?php echo _PASSWORD;?></td>
					<td><font class="mandatory_star">*</font></td>
					<td nowrap="nowrap"><input type="password" id="user_password1" name="user_password1" size="32" maxlength="64" value="<?php echo $user_password1;?>" /></td>
				</tr>		    
				<tr>
					<td align="right"><?php echo _CONFIRM_PASSWORD;?></td>
					<td class="mandatory_star">*</td>
					<td nowrap="nowrap"><input type="password" id="user_password2" name="user_password2" size="32" maxlength="32" value="<?php echo $user_password1;?>" /></td>
				</tr>
				<?php } ?>
				<tr><td height="20" colspan="3"><b><?php echo _IMAGE_VERIFICATION; ?></b><hr size="1" noshade="noshade" /></td></tr>
				<tr valign="top">
					<td align="left"><?php echo _TYPE_CHARS; ?></td>
					<td></td>
					<td>
						<table>
						<tr>
							<td><img style="padding:0px; margin:0px;" id="captcha_image" src="modules/captcha/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>" /></td>
							<td>
								<a href="modules/captcha/securimage_play.php"><img style="padding:0px; margin:0px; border:0px;" id="captcha_image_play" src="modules/captcha/images/audio_icon.gif" title="<?php echo _PLAY; ?>" alt="<?php echo _PLAY; ?>" /></a><br />
								<img style="cursor:pointer; padding:0px; margin:0px; border:0px;" id="captcha_image_reload" src="modules/captcha/images/refresh.gif" style="cursor:pointer;" onclick="document.getElementById('captcha_image').src = 'modules/captcha/securimage_show.php?sid=' + Math.random(); return false" title="<?php echo _REFRESH; ?>" alt="<?php echo _REFRESH; ?>" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="text" name="captcha_code" size="21" value="" />
							</td>
						</tr>
						</table>			    			    
					</td>
				</tr>		
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
							<td><a href="index.php?page=booking_details#top" onclick="javascript:appShowTermsAndConditions()"><?php echo _AGREE_CONF_TEXT; ?></a></td>
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
			<?php } ?>
			
			<tr><td colspan="2">&nbsp;</td></tr>
			<?php if($registration_required == "yes"){ ?>
			<tr>
			<td colspan="3" align="left">
				<p><?php echo _CREATE_ACCOUNT_NOTE; ?></p>
			</td>
			</tr>
			<?php } ?>
			
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
		
	<?php		 
	} 
}
?>