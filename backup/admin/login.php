<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

// Draw title bar
draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>"",_ADMIN_LOGIN=>"")));

// Check if admin is logged in
if(!@$objLogin->IsLoggedInAsAdmin()){	
	if (@$objLogin->IsWrongLogin()) draw_important_message(_WRONG_LOGIN);
	draw_content_start();
?>
	<form class="login-form" action="index.php?admin=login" method="post">
		<?php draw_hidden_field("submit_login", "login"); ?>
		<?php draw_hidden_field("type", "admin"); ?>		
		<table width="96%" cellspacing="4" border="0">
		<tr>
			<td width="12%" nowrap='nowrap'><?php echo _USERNAME;?></td>
			<td width="88%"><input type="text" id="txt_user_name" name="user_name" style="width:145px" maxlength="50" autocomplete="off" /></td>
		</tr>
		<tr>
			<td><?php echo _PASSWORD;?></td>
			<td><input type="password" name="password" style="width:145px" maxlength="15" autocomplete="off" /></td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>				
		<tr>
			<td colspan="2">
				<input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_LOGIN;?>">&nbsp;
				<a href='index.php?admin=password_forgotten'><?php echo _FORGOT_PASSWORD;?>?</a>
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>		
		</table>
	</form>
	<script>appSetFocus("txt_user_name");</script>
<?php
	draw_content_end();
} else {
	draw_important_message(_ALREADY_LOGGED);
	draw_content_start();
?>
	<form action="index.php?page=logout" method="post">
		<?php draw_hidden_field("submit_logout", "logout"); ?>
		<input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_LOGOUT;?>">
	</form>
<?php
}
?>