<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------


$act 		    = isset($_POST['act']) ? prepare_input($_POST['act']) : "";
$password_sent 	= isset($_SESSION['password_sent']) ? $_SESSION['password_sent'] : false;
$email 			= isset($_POST['email']) ? prepare_input($_POST['email']) : "";
$msg 			= "";

if($act == "send"){
    if(!$password_sent){
		if(Clients::SendPassword($email)){
			$msg = draw_success_message(_PASSWORD_SUCCESSFULLY_SENT, false);
			$_SESSION['password_sent'] = true;
		}else{
			$msg = draw_important_message(Clients::GetError(), false);					
		}
	}else{
		$msg = draw_message(_PASSWORD_ALREADY_SENT, false);
	}
}


// Draw title bar
draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>"",_PASSWORD_FORGOTTEN=>"")));

// Check if user is logged in
if(!@$objLogin->IsLoggedIn()){	
	echo $msg;
	draw_content_start();	
?>
	<form class="forgot-password-form" action="index.php?client=password_forgotten" method="post">
		<?php draw_hidden_field("act", "send"); ?>
		<?php draw_hidden_field("type", "client"); ?>
		<?php draw_token_field(); ?>
		
		<table width="96%" border="0">
		<tr>
			<td colspan='2'>
				<?php echo "<p>"._PASSWORD_RECOVERY_MSG."</p>"; ?>
			</td>
		</tr>
		<tr>
			<td width="15%" nowrap='nowrap'><?php echo _EMAIL_ADDRESS;?>:</td>
			<td width="85%"><input type="text" name="email" id="txt_forgotten_email" size="25" maxlength="100" autocomplete="off" /></td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>			
		<tr>
			<td colspan="2">
				<input class="form_button" type="submit" name="btnSend" value="<?php echo _SEND;?>">
			</td>
		</tr>
		<tr><td colspan='2' nowrap height='5px'></td></tr>		
		<tr>
			<td valign='top' colspan="2">
				<a href='index.php?client=login'><?php echo _CLIENT_LOGIN;?></a>
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>				
		</table>
		<script>appSetFocus("txt_forgotten_email");</script>
	</form>
<?php
	draw_content_end();	
}else{
	draw_content_start();
	draw_important_message(_NOT_AUTHORIZED);
	draw_content_end();	
}
?>