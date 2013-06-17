<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

// Draw title bar
draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>"",_CLIENT_LOGIN=>"")));

// Check if client is logged in
if(!@$objLogin->IsLoggedIn()){	

	if($objLogin->IsWrongLogin()) draw_important_message(_WRONG_LOGIN)."<br />";
	else if($objLogin->IsIpAddressBlocked()) draw_important_message(_IP_ADDRESS_BLOCKED)."<br />";
	else if($objLogin->IsEmailBlocked()) draw_important_message(_EMAIL_BLOCKED)."<br />";
	else if($user_session->IsMessage('notice')) draw_message($user_session->GetMessage('notice'), true, true);

	draw_content_start();	
?>
	<form class="login-form" action="index.php?client=login" method="post">
		<?php draw_hidden_field("submit_login", "login"); ?>
		<?php draw_hidden_field("type", "client"); ?>
		<?php draw_token_field(); ?>

		<table width="96%" cellspacing="4" border="0">
		<tr>
			<td width="13%" nowrap='nowrap'><?php echo _USERNAME;?></td>
			<td width="87%"><input type="text" name="user_name" id="txt_user_name" style="width:145px" maxlength="50" autocomplete="off" /></td>
		</tr>
		<tr>
			<td><?php echo _PASSWORD;?></td>
			<td><input type="password" name="password" size="22" style="width:145px" maxlength="20" autocomplete="off" /></td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>		
		<tr>
			<td colspan="2">
				<input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_LOGIN;?>">&nbsp;
			</td>
		</tr>
		<tr><td colspan='2' nowrap height='5px'></td></tr>		
		<tr>
			<td valign='top' colspan="2">
				<a href='index.php?client=create_account'><?php echo _CREATE_ACCOUNT;?></a><br />
				<a href='index.php?client=password_forgotten'><?php echo _FORGOT_PASSWORD;?>?</a>
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>		
		</table>
	</form>
	<script>appSetFocus("txt_user_name");</script>
<?php
	draw_content_end();
} else {
	draw_content_start();	
	draw_important_message(_ALREADY_LOGGED);
?>
	<form action="index.php?page=logout" method="post">
		<?php draw_hidden_field("submit_logout", "Logout"); ?>
		<input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_LOGOUT;?>">
	</form>
<?php
	draw_content_end();
}	
?>