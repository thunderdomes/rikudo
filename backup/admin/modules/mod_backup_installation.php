<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin()){
	
	$submition_type = isset($_POST['submition_type']) ? prepare_input($_POST['submition_type']) : "";
	$backup_file 	= isset($_POST['backup_file']) ? str_replace(array("'", '"', "\\", "/", " "), "", prepare_input($_POST['backup_file'])) : "";
	$st 			= isset($_GET['st']) ? prepare_input($_GET['st']) : "";
	$fname 		    = isset($_GET['fname']) ? prepare_input($_GET['fname']) : "";
	$msg            = "";		
	
	$objBackup = new Backup();
	
	if($submition_type == "1"){		
		// save backup
		if($objBackup->ExecuteBackup($backup_file)){
			$msg = draw_success_message(str_replace("_FILE_NAME_", $fname, _BACKUP_WAS_CREATED), false);	
		}else{
			$msg = draw_important_message($objBackup->error, false);
		}		
	}else if($st == "delete"){
		// delete previouse backup		
		if($objBackup->DeleteBackup($fname)){
			$msg = draw_success_message(str_replace("_FILE_NAME_", $fname, _BACKUP_WAS_DELETED), false);	
		}else{
			$msg = draw_important_message($objBackup->error, false);
		}
	}

	// draw title bar and message
	draw_title_bar(prepare_breadcrumbs(array(_MODULES=>"", _BACKUP_INSTALLATION=>"")));	
	echo $msg;	

	draw_content_start();		
?>    

	<table align="center" width="100%" border="0" cellspacing="0" cellpadding="3" class="main_text">
	<tr valign="top">
		<td width="45%">
			<form action="index.php?admin=mod_backup_installation" method="post">
			<input type="hidden" name="submition_type" value="1" />
			<?php draw_token_field(); ?>
			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="3" class="main_text">
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td align="<?php echo Application::Get("defined_left");?>" colspan="2"><b><?php echo _BACKUP_YOUR_INSTALLATION; ?>: </b></td>		
			</tr>
			<tr>
				<td align="<?php echo Application::Get("defined_left");?>" width="1%"><input type="text" name="backup_file" value="<?php echo date("M-d-Y");?>" size="24" maxlength="20" /></td>
				<td align="<?php echo Application::Get("defined_left");?>"><input class="form_button" type="submit" name="submit" value="<?php echo _BACKUP;?>" /></td>
			</tr>
			</table>
			</form>
		</td>
		<td width="55%">
			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="3" class="main_text">
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td align="left" colspan="2"><b><?php echo _BACKUPS_EXISTING; ?>: </b></td>		
			</tr>
			<?php
				$objBackup->ShowPreviousBackups("delete");
			?>
		</table>			
		</td>
	</tr>
	</table>

<?php
	draw_content_end();	
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}
?>