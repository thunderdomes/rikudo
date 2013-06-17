<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------


if(!@$objLogin->IsLoggedInAsClient()){
	$user_session->SetMessage("notice", _MUST_BE_LOGGED);
	header("location: index.php?client=login");
	exit;
}
?>