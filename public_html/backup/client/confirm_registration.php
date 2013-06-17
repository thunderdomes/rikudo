<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if(!@$objLogin->IsLoggedIn()){        
    
	draw_title_bar(_REGISTRATION_CONFIRMATION);
	
	echo $msg;
	
	echo "<div class='pages_contents'>";
	if(!$confirmed){
		echo "<br />
		<form action='index.php?client=confirm_registration' method='post' name='frmConfirmCode' id='frmConfirmCode'>
			"._ENTER_CONFIRMATION_CODE.": 
			<input type='hidden' name='task' value='post_submission' />
			<input type='text' name='c' id='c' value='' size='27' maxlength='25' /><br /><br />
			<input class='form_button' type='submit' name='btnSubmit' id='btnSubmit' value='Submit'>
			".draw_token_field(true)."
		</form>
		<script type='text/javascript'>appSetFocus('c')</script>";
	}
	echo "</div>";

}else{
	draw_title_bar(prepare_breadcrumbs(array(_CLIENT=>"", _REGISTRATION_CONFIRMATION=>"")));
    draw_important_message(_NOT_AUTHORIZED);
}

?>