<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin()){
	
	$account 		    = new Admins($objLogin->GetLoggedID());
	$submit_type 	    = isset($_POST['submit_type']) ? prepare_input($_POST['submit_type']) : "";
	$preferred_language = isset($_POST['preferred_language']) ? prepare_input($_POST['preferred_language']) : "";
	$admin_email 	 	= isset($_POST['admin_email']) ? prepare_input($_POST['admin_email']) : "";
	$password_one 	 	= isset($_POST['password_one']) ? prepare_input($_POST['password_one']) : "";
	$password_two 	 	= isset($_POST['password_two']) ? prepare_input($_POST['password_two']) : "";
	$msg             = "";

	// change password
	if($submit_type == "1"){
		$msg = $account->ChangeLang($preferred_language);
	}else if($submit_type == "2"){
		$msg = $account->ChangeEmail($admin_email);
	}else if($submit_type == "3"){
		$msg = $account->ChangePassword($password_one, $password_two);
	}

	draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>"",_MY_ACCOUNT=>"")));	

	if($msg == "") draw_message(_ALERT_REQUIRED_FILEDS);
	else echo $msg;

	draw_content_start();
	
	
?>

	<?php draw_sub_title_bar(_GENERAL_INFO); ?>
	<form action="index.php?admin=my_account" method="post">
	<?php draw_hidden_field("submit_type", "1"); ?>
	<?php draw_token_field(); ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" class="main_text">
	<tr>
		<td>&nbsp;<?php echo _ACCOUNT_TYPE;?>:</td>
		<td><?php echo camel_case($account->GetParameter("account_type"));?></td>
	</tr>
	<tr>
		<td width="150px">&nbsp;<?php echo _PREFERRED_LANGUAGE;?> <font color="#c13a3a">*</font>:</td>
		<td>
		<?php
			// display language
			$total_languages = Languages::GetAllActive(); 
			echo get_languages_box("preferred_language", $total_languages[0], "abbreviation", "lang_name", $account->GetParameter("preferred_language")); 
		?>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>	
	<tr>
		<td style="padding-left:0px;" colspan="2"><input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_CHANGE; ?>"></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>	
	</table>
	</form>
	
	<?php draw_sub_title_bar(_PERSONAL_INFORMATION); ?>
	<form action="index.php?admin=my_account" method="post">
	<?php draw_hidden_field("submit_type", "2"); ?>
	<?php draw_token_field(); ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" class="main_text">
	<tr>
		<td width="150px">&nbsp;<?php echo _NAME;?>:</td>
		<td><?php echo $account->account_name;?></td>
	</tr>
	<tr>
		<td width="150px">&nbsp;<?php echo _EMAIL_ADDRESS;?> <font color="#c13a3a">*</font>:</td>
		<td><input class="form_text" name="admin_email" type="text" size="25" maxlength="125" value="<?php echo $account->GetParameter("email"); ?>"></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td style="padding-left:0px;" colspan="2"><input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_CHANGE; ?>"></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>	
	</table>	
	</form>

	<?php draw_sub_title_bar(_CHANGE_YOUR_PASSWORD); ?>
	<form action="index.php?admin=my_account" method="post">
	<?php draw_hidden_field("submit_type", "3"); ?>
	<?php draw_token_field(); ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" class="main_text">
	<tr>
		<td width="150px">&nbsp;<?php echo _PASSWORD;?> <font color="#c13a3a">*</font>:</td>
		<td width="405px"><input class="form_text" name="password_one" type="password" size="25" maxlength="15"></td>
	</tr>
	<tr>
		<td>&nbsp;<?php echo _RETYPE_PASSWORD;?> <font color="#c13a3a">*</font>:</td>
		<td colspan="2"><input class="form_text" name="password_two" type="password" size="25" maxlength="15"></td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="padding-left:0px;" colspan="2"><input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_CHANGE_PASSWORD ?>"></td>
		<td><?php if($objLogin->GetLoggedType() != "owner") echo "<input class='form_button' type='button' name='btnRemoveAccount' value='"._REMOVE_ACCOUNT."' />"; ?></td>
	</tr>
	</table>
	</form>

<?php
	draw_content_end();	
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}
?>