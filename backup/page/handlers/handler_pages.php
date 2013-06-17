<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$new_page_id = Pages::GetPageId(Application::Get("page_id"), Application::Get("lang"));
if($new_page_id != "" && Application::Get("page_id") != $new_page_id){
    $url = get_page_url();
    $url = str_replace("pid=".Application::Get("page_id"), "pid=".$new_page_id, $url);
    header("location: ".$url);
    exit;
} 	

?>