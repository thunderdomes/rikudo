<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

	$submition_type = isset($_POST['submition_type']) ? prepare_input($_POST['submition_type']) : "";
	$backup_file 	= isset($_POST['backup_file']) ? prepare_input($_POST['backup_file']) : "";
	$st 			= isset($_GET['st']) ? prepare_input($_GET['st']) : "";
	$fname 		    = isset($_GET['fname']) ? prepare_input($_GET['fname']) : "";
	$msg            = "";		
	
	$objBackup = new Backup();
	
	if($st == "restore"){
		// restore previouse backup
		if($objBackup->RestoreBackup($fname)){
			$msg = draw_success_message(str_replace("_FILE_NAME_", $fname, _BACKUP_WAS_RESTORED), false);	
		}else{
			$msg = draw_important_message($objBackup->error, false);
		}
	}else{
		$msg = draw_message(_BACKUP_RESTORE_NOTE, false);
	}
	
	// draw title bar and message
	draw_title_bar(prepare_breadcrumbs(array(_MODULES=>"", _BACKUP_RESTORE=>"")));	
	echo $msg;

	draw_content_start();		
?>    

	<table align="center" width="96%" border="0" cellspacing="1" cellpadding="3" class="main_text">
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr>
		<td align="left" colspan="5" nowrap>&nbsp;<b><?php echo _BACKUP_CHOOSE_MSG; ?>:</b>&nbsp;</td>
		<td width="200px">&nbsp;</td>
	</tr>
	<tr><td colspan="6" height="5px" nowrap></td></tr>
	<?php $objBackup->ShowPreviousBackups("restore"); ?>
	</table>

<?php
	draw_content_end();	
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

?>