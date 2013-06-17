<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAsAdmin()){

	$act = isset($_GET['act']) ? prepare_input($_GET['act']) : "";
	$language_id = (isset($_REQUEST['language_id']) && $_REQUEST['language_id'] != "") ? prepare_input($_REQUEST['language_id']) : Languages::GetDefaultLang();
	$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : "";
	$msg = "";

	$objPage = new Pages($pid);	
	// do delete action
	if($act=="delete"){
		if($objPage->PageDelete($pid)){
			$msg = draw_success_message(_PAGE_DELETED, false);
		}else{
			$msg = draw_important_message($objPage->error, false);
		}
	// do restore action	
	}else if($act=="restore"){
		if($objPage->PageRestore($pid)){
			$msg = draw_success_message(_PAGE_RESTORED, false);
		}else{
			$msg = draw_important_message($objPage->error, false);
		}		
	}

	
	// start main content
	$all_pages = array();
	$all_pages = Pages::GetAll($language_id, "removed");
	$total_languages = Languages::GetAllActive();
	
	draw_title_bar(prepare_breadcrumbs(array(_PAGE_MANAGEMENT=>"",_TRASH_PAGES=>"")));

	if($user_session->IsMessage('notice')) echo $user_session->GetMessage('notice');
	echo $msg;

	draw_content_start();			
?>
	<script type="text/javascript">
	<!--
		function confirmDelete(pid){
			if(!confirm('<?php echo _PAGE_DELETE_WARNING;?>')){
				false;
			}else{
				document.location.href = 'index.php?admin=pages_trash&act=delete&language_id=<?php echo $language_id; ?>&pid='+pid;
			}
			
		}

		function confirmRestore(pid){
			if(!confirm('<?php echo _PAGE_RESTORE_WARNING;?>')){
				false;
			}else{
				document.location.href = 'index.php?admin=pages_trash&act=restore&language_id=<?php echo $language_id; ?>&pid='+pid;
			}
			
		}
	//-->
	</script>

	<table width="99%" border="0" cellspacing="0" cellpadding="2" class="main_text">
	<tr>
		<td align='<?php echo Application::Get("defined_right");?>'>
		<?php echo get_languages_box("language_id", $total_languages[0], "abbreviation", "lang_name", $language_id, "", "onchange=\"document.location.href='index.php?admin=pages_trash&language_id='+this.value\""); ?>
		</td>
	</tr>
	</table>

	<table width="99%" border="0" cellspacing="0" cellpadding="2" class="main_text">
	<tr><td colspan="6" height="3px" nowrap></td></tr>
	<?php
	if($all_pages[1] > 0){ ?>
		<tr>
			<th align='<?php echo Application::Get("defined_left"); ?>'><?php echo _MENU_LINK;?></th>
			<th width="20%" nowrap align="center"><?php echo _REMOVED;?></th>
			<th width="12%" nowrap><?php echo _CONTENT_TYPE;?></th>
			<th width="12%"><?php echo _MENU_WORD;?></th>
			<th width="13%"><?php echo _ACTIONS_WORD;?></th>
		</tr>
		<tr><td colspan="6" height="3px" nowrap><?php draw_line(); ?></td></tr>
		<?php
			for($i=0;$i<$all_pages[1];$i++){

				// prepare page header for display
				$page_header = $all_pages[0][$i]['page_title'];
				if(strlen($page_header) > 60) $page_header = substr($page_header,0,60)."..";

				// prepare menu link for display
				$menu_name = $all_pages[0][$i]['menu_name'];
				if(strlen($menu_name) > 18) $menu_name = substr($menu_name,0,18)."..";
				
				// display page row
				echo '<tr '.highlight(0).' onmouseover="oldColor=this.style.backgroundColor;this.style.backgroundColor=\'#e7e7e7\';" onmouseout="this.style.backgroundColor=oldColor">
						<td align="'.Application::Get("defined_left").'">'.$page_header.'</td>
						<td align="center">'.format_datetime($all_pages[0][$i]['status_changed']).'</td>
						<td align="center">'.ucfirst($all_pages[0][$i]['content_type']).'</td>
						<td align="center">'.(($menu_name!="")?$menu_name:_NOT_AVAILABLE).'</td>
						<td align="center" nowrap>
							<a href="index.php?page=pages&pid='.$all_pages[0][$i]['id'].'">'._VIEW_WORD.'</a>&nbsp;'.get_divider().'
							<a href="javascript:confirmRestore(\''.$all_pages[0][$i]['id'].'\');">'._RESTORE.'</a>&nbsp;'.get_divider().'
							<a href="javascript:confirmDelete(\''.$all_pages[0][$i]['id'].'\');">'._DELETE_WORD.'</a>
						</td>
					</tr>';
			}
		?>
	</table>
<?php
	}else{
		draw_important_message(_PAGE_NOT_FOUND, true, true, false, "width:100%");
	} 
	draw_content_end();			
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}
?>