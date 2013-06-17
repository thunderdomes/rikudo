<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsClient()){

	$submit = isset($_POST['submit']) ? prepare_input($_POST['submit']) : "";
	$msg = "";
	$account_deleted = false;
	
	if($submit == "remove"){                
		if(strtolower(SITE_MODE) == "demo"){
			$msg = draw_important_message(_OPERATION_BLOCKED, false);
		}else{
			if($objLogin->RemoveAccount()){
				$msg = draw_success_message(_ACCOUNT_WAS_DELETED, false);
				$account_deleted = true;
		
				////////////////////////////////////////////////////////////
				$sender 	= $objSettings->GetParameter("admin_email");
				$recipiant 	= $objLogin->GetLoggedEmail();
				$user_name 	= $objLogin->GetLoggedName();
				
				$objEmailTemplates = new EmailTemplates();
				$email_template = $objEmailTemplates->GetTemplate("account_deleted_by_user", Application::Get("lang"));
				
				$subject = $email_template["template_subject"];
				$body_middle = $email_template["template_content"];
				
				$body_middle = str_ireplace("{USER NAME}", $user_name, $body_middle);
				
				$body   = "<div style=direction:".Application::Get("lang_dir").">";
				$body  .= $body_middle;
				$body  .= "</div>";			
				
				$text_version = strip_tags($body);
				$html_version = nl2br($body);
				
				$objEmail = new Email($recipiant, $sender, $subject);                     
				$objEmail->textOnly = false;
				$objEmail->content = $html_version;                
				$objEmail->Send();
				////////////////////////////////////////////////////////////
				
				$_SESSION = array();
			}else{
				$msg = draw_important_message(_DELETING_ACCOUNT_ERROR, false);
			}			
		}
	}
            
	draw_title_bar(prepare_breadcrumbs(array(_MY_ACCOUNT=>"",_REMOVE_ACCOUNT=>"")));
	
?>
	<form action='index.php' method='post' id='frmLogout' style='display:inline; margin-top:0px; padding-top:0px;' >
		<input type="hidden" name="submit_logout" value="logout">
	</form>
		
	<form action="index.php?client=account_remove" method="post" name="frmProfile" onsubmit="return confirm('<?php echo _REMOVE_ACCOUNT_ALERT; ?>');">
		<input type="hidden" name="submit" value="remove" />
		<br />
		<?php        
			echo $msg;
			if($account_deleted){
				echo "<script type='text/javascript'>
					  setTimeout(function(){appFormSubmit('frmLogout')}, 5000);					  
					  </script>";
			}else{
				draw_message(_REMOVE_ACCOUNT_WARNING);
			}        
		?>
		<?php if(!$account_deleted){ ?>
		<table align='center' border="0" cellspacing="1" cellpadding="2" width="96%">
		<tbody>
		<tr><td colspan="3">&nbsp;</td></tr>            
		<tr>
			<td align="left" colspan="2">
				<input type='button' class="form_button" value="<?php echo _BUTTON_CANCEL; ?>" onclick="javascript:appGoTo('client=my_account');" />
			</td>
			<td align="right">
				<input type='submit' class="form_button" name="btnSubmitPD" id="btnSubmitPD" value="<?php echo _REMOVE; ?>" />
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>            
		</table>
		<?php } ?>
	</form>

<?php
}else if(@$objLogin->IsLoggedIn()){
    draw_title_bar(prepare_breadcrumbs(array(_CLIENT=>"", _REMOVE_ACCOUNT=>"")));
    draw_important_message(_NOT_AUTHORIZED);
}else{
    draw_title_bar(prepare_breadcrumbs(array(_CLIENT=>"", _REMOVE_ACCOUNT=>"")));
    draw_important_message(_MUST_BE_LOGGED);
}
?>