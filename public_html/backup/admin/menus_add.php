<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs('owner','mainadmin')){

    draw_title_bar(prepare_breadcrumbs(array(_MENU_MANAGEMENT=>"",_MENU_ADD=>"")));
	echo $msg;

	draw_content_start();	
?>
	<form name='frmAddMenu' method='post'>
		<?php draw_hidden_field("act", "add"); ?>
		<?php draw_token_field(); ?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2" class="main_text">
		<tr>
			<td width="20%"><?php echo _MENU_NAME;?> <font color="#c13a3a">*</font>:</td>
			<td><input class="form_text" name="name" id="frmAddMenu_name" value="" size="40" maxlength="20"></td>
		</tr>
		<tr>
			<td><?php echo _DISPLAY_ON;?>:</td>
			<td><?php echo Menu::DrawMenuPlacementBox(); ?></td>
		</tr>
		<tr>
			<td nowrap>
				<?php echo _ACCESS; ?>:&nbsp;
			</td>
			<td>
				<?php echo Menu::DrawMenuAccessSelectBox(); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo _LANGUAGE;?> <font color="#c13a3a">*</font>:</td>
			<td>
				<?php
					// display language
					$total_languages = Languages::GetAllActive();
					echo get_languages_box("language_id", $total_languages[0], "abbreviation", "lang_name", $language_id); 
				?>
			</td>
		</tr>
		<tr><td height="10px" nowrap="nowrap"></td></tr>		
		<tr>
			<td colspan="2">
				<input class="form_button" type="submit" name="subAddMenu" value="<?php echo _BUTTON_CREATE;?>">
				<input class='form_button' type='button' onclick="document.location.href='index.php?admin=menus'" value="<?php echo _BUTTON_CANCEL; ?>">
			</td>
		</tr>		
		</table>
		<br />
	</form>
	<script type="text/javascript">
		appSetFocus("frmAddMenu_name");
	</script>
<?php
	draw_content_end();	
}else{
	draw_title_bar(_ADMIN);
    draw_important_message(_NOT_AUTHORIZED);
}

?>