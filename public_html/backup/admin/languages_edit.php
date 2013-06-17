<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(@$objLogin->IsLoggedInAs("owner","mainadmin")){

    draw_title_bar(prepare_breadcrumbs(array(_LANGUAGES_SETTINGS=>"",_LANGUAGE_EDIT=>"")));
	echo $msg;

	draw_content_start();
?>
	<script type="text/javascript">
	function used_on_OnClick(el){
		if(el.checked == true){
			if(document.getElementById('used_on').options[2]){
				document.getElementById('used_on').selectedIndex = 0;
				document.getElementById('used_on').options[2].disabled = true;
			}
		}else{
			if(document.getElementById('used_on').options[2]){
				document.getElementById('used_on').options[2].disabled = false;
			}
		}
	}
	</script>

	<form name='frmEditLang' method='post'>
		<?php draw_hidden_field("act", "update"); ?>
		<?php draw_hidden_field("lid", $lid); ?>
		<?php draw_token_field(); ?>
		
		<table width="100%" border="0" cellspacing="0" cellpadding="2" class="main_text">
		<tr>
			<td width="30%"><?php echo _LANGUAGE_NAME;?> <font color="#c13a3a">*</font>:</td>
			<td><input class="form_text" name="lang_name" id="frmEditLang_name" value="<?php echo decode_text($params['lang_name']);?>" size="40" maxlength="20"></td>
		</tr>
		<tr>
			<td><?php echo _ABBREVIATION;?> :</td>
			<td>
				<input type="hidden" name="abbreviation" id="abbreviation" value="<?php echo decode_text($params['abbreviation']);?>" maxlength="2" />
				<?php echo strtoupper($params['abbreviation']); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo _HDR_TEXT_DIRECTION;?> :</td>
			<td>
				<select class="form_text" name="lang_dir" id="lang_dir">
					<option <?php echo (($params['lang_dir'] == "ltr") ? "selected='selected'" : "");?> value='ltr'><?php echo _LEFT_TO_RIGHT;?></option>
					<option <?php echo (($params['lang_dir'] == "rtl") ? "selected='selected'" : "");?> value='rtl'><?php echo _RIGHT_TO_LEFT;?></option>
				</select>		
			</td>
		</tr>
		<tr>
			<td><?php echo _ICON_IMAGE;?> : (images/flags/)</td>
			<td><input type="text" class="form_text" name="icon_image" id="icon_image" value="<?php echo decode_text($params['icon_image']);?>" size="40" maxlength="50" /></td>
		</tr>
		<tr>
			<td><?php echo _USED_ON;?> :</td>
			<td>
				<?php echo Languages::GetUsedToBox($params['used_on'], (($params['is_default'] == "1") ? true : false)); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo _ORDER;?> :</td>
			<td>
				<?php
					// output select tag as a total number of langs available
					$total_langs = Languages::GetAll();
					// display one more order option for the next language
					draw_numbers_select_field("lang_order",$params['lang_order'],1,$total_langs[1]); 
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo _IS_DEFAULT;?>? </td>
			<td><input type="checkbox" class="form_checkbox" name="is_default" id="is_default" value="1" onclick='used_on_OnClick(this)' <?php echo (($params['is_default'] == "1") ? "checked='checked' disabled='disabled'" : "");?>></td>
		</tr>
		<tr>
			<td><?php echo _ACTIVE;?>? </td>
            <td> 
                <?php
                    if($params['is_default'] == "1"){
                        echo "<input type='checkbox' class='form_checkbox' name='_is_active' id='_is_active' value='1' ".(($params['is_active'] == "1") ? "checked='checked'" : "")." disabled='disabled' />";
                        echo "<input type='hidden' name='is_active' id='is_active' value='1' />";
                    }else{
                        echo "<input type='checkbox' class='form_checkbox' name='is_active' id='is_active' value='1' ".(($params['is_active'] == "1") ? "checked='checked'" : "")." />";
                    }
                ?>
            </td>			
		</tr>
		<tr><td colspan="2" nowrap height="10px"></td></tr>
		<tr>
			<td colspan="2">
				<input class="form_button" type="submit" name="subAddLang" value="<?php echo _BUTTON_UPDATE;?>">
				<input class="form_button" type="button" name="butCancelLang" onclick="document.location.href='index.php?admin=languages'" value="<?php echo _BUTTON_CANCEL;?>">
			</td>
		<tr>		
		</table>
		<br />
	</form>
	<script type="text/javascript">
		appSetFocus("frmEditLang_name");
	</script>
<?php
	draw_content_end();
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}
?>