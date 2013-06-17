<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

    draw_title_bar(prepare_breadcrumbs(array(_LANGUAGES_SETTINGS=>"",_LANGUAGE_ADD_NEW=>"")));
	echo $msg;
	
	draw_content_start();
?>
	<form name='frmAddLang' method='post'>
		<?php draw_hidden_field("act", "add"); ?>
		<?php draw_token_field(); ?>
		
		<table width="100%" border="0" cellspacing="0" cellpadding="2" class="main_text">
		<tr>
			<td width="30%"><?php echo _LANGUAGE_NAME;?> <font color="#c13a3a">*</font>:</td>
			<td><input class="form_text" name="lang_name" id="frmAddLang_name" value="<?php echo decode_text($params['lang_name']); ?>" size="40" maxlength="20"></td>
		</tr>
		<tr>
			<td><?php echo _ABBREVIATION;?> <font color="#c13a3a">*</font>:</td>
			<td><input class="form_text" name="abbreviation" id="abbreviation" value="<?php echo decode_text($params['abbreviation']); ?>" size="5" maxlength="2"></td>
		</tr>
		<tr>
			<td><?php echo _HDR_TEXT_DIRECTION;?> :</td>
			<td>
				<select class="form_text" name="lang_dir" id="lang_dir">
					<option <?php echo (($params['lang_dir'] == "ltr") ? "selected" : "");?> value='ltr'><?php echo _LEFT_TO_RIGHT;?></option>
					<option <?php echo (($params['lang_dir'] == "rtl") ? "selected" : "");?> value='rtl'><?php echo _RIGHT_TO_LEFT;?></option>
				</select>		
			</td>
		</tr>
		<tr>
			<td><?php echo _ICON_IMAGE;?> : (images/flags/)</td>
 			<td><input type="text" class="form_text" name="icon_image" id="icon_image" value="<?php echo decode_text($params['icon_image']); ?>" size="40" maxlength="50" /></td>
		</tr>
		<tr>
			<td><?php echo _USED_ON;?> :</td>
			<td>
				<?php echo Languages::GetUsedToBox($params['used_on'], true); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo _ORDER;?> :</td>
			<td>
				<?php
					// output select tag as a total number of langs available
					$total_langs = Languages::GetAll();
					$total_langs[1]++;
					echo $total_langs[1];
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo _IS_DEFAULT; ?>? </td>
			<td><input type="checkbox" class="form_checkbox" name="is_default" id="is_default" value="1" <?php echo (($params['is_default'] == "1") ? "checked='checked'" : "");?>></td>
		</tr>
		<tr>
			<td><?php echo _ACTIVE; ?>? </td>
			<td><input type="checkbox" class="form_checkbox" name="is_active" id="is_active" value="1" <?php echo (($params['is_active'] == "1") ? "checked='checked'" : "");?>></td>
		</tr>
		<tr><td colspan="2" nowrap height="10px"></td></tr>
		<tr>
			<td colspan="2">
				<input class="form_button" type="submit" name="subAddLang" value="<?php echo _BUTTON_CREATE;?>">
                <input class="form_button" type="button" name="butCancelLang" onclick="document.location.href='index.php?admin=languages'" value="<?php echo _BUTTON_CANCEL;?>">
			</td>
		<tr>		
		</table>
		<br />
	</form>
	<script type="text/javascript">
		appSetFocus("frmAddLang_name");
	</script>
<?php
	draw_content_end();
}else{
    draw_title_bar(_ADMIN);
    draw_important_message(_NOT_AUTHORIZED);
}
?>