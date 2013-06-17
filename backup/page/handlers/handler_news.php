<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$new_news_id = News::GetNewsId(Application::Get("news_id"), Application::Get("lang"));
if($new_news_id != "" && Application::Get("news_id") != $new_news_id){
    $url = get_page_url();
    $url = str_replace("nid=".Application::Get("news_id"), "nid=".$new_news_id, $url);
    header("location: ".$url);
    exit;
} 	

?>