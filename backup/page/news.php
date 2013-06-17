<?php

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

$objNews = new News();
$news = $objNews->GetNews(Application::Get("news_id"));

// Draw title bar
if($news[1] == 1){		
	$news_type = isset($news[0]["type"]) ? $news[0]["type"] : "news";
	$header_text = isset($news[0]["header_text"]) ? str_replace("\'", "'", $news[0]["header_text"]) : "";
	$body_text = isset($news[0]["body_text"]) ? str_replace("\'", "'", $news[0]["body_text"]) : "";

	if($news_type == "events"){
		draw_title_bar(_EVENTS); 	
	}else{
		draw_title_bar(_NEWS); 	
	}
	
	echo "<div class='center_box_heading_news'>".$header_text."</div>";
	echo "<div class='center_box_contents_news'>".$body_text."</div>";
	echo "<div class='center_box_bottom_news'><i><b>"._POSTED_ON.":</b>&nbsp;".format_datetime($news[0]["date_created"])."</i></div>";

	if($news_type == "events"){
		$objNews->DrawRegistrationForm(Application::Get("news_id"), $header_text);
	}
}else{
	draw_title_bar(_NEWS);
	echo "<br>";
	draw_important_message(_WRONG_PARAMETER_PASSED);
}        


?>