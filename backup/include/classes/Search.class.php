<?php

/**
 *	Class Search (for HotelSite ONLY)
 *  -------------- 
 *  Description : encapsulates search properties
 *  Usage       : HotelSite
 *  Updated	    : 02.06.2010
 *	Written by  : ApPHP
 *	
 *	PUBLIC:					STATIC:					PRIVATE:
 *  -----------				-----------				-----------
 *  __construct				DrawQuickSearch						 
 *  __destruct
 *  SearchBy
 *  DrawSearchResult
 *  DrawPopularSearches
 *  
 **/

class Search {

	private $pageSize;
	private $totalSearchRecords;

	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{		
		$this->pageSize = 15;
		$this->totalSearchRecords = 0;        
    }

	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	/**
	 * Searchs in pages by keyword
	 *		@param $keyword - keyword
	 *		@param $page
	 *		@param $search_in
	 */	
	public function SearchBy($keyword, $page = 1, $search_in = "site")
	{
	    $lang_id = Application::Get("lang");
		
		if($search_in == "rooms"){
			$sql = "SELECT 
						CONCAT('page=room_description&room_id=', r.id) as url,
						rd.room_type as title,
						rd.room_short_description as text,
						'article' as content_type,
						'' as link_url 
					FROM ".TABLE_ROOMS_DESCRIPTION." rd
						INNER JOIN ".TABLE_ROOMS." r ON rd.room_id = r.id
					WHERE
					    ( rd.room_type LIKE '%".encode_text($keyword)."%' OR
						  rd.room_long_description LIKE '%".encode_text($keyword)."%'
						) AND
						rd.language_id = '".$lang_id."'";
			$order_field = "r.id";
		}else if($search_in == "news"){
			$sql = "SELECT
						CONCAT('page=news&nid=', id) as url,
						header_text as title,
						body_text as text,
						'article' as content_type,
						'' as link_url 
					FROM ".TABLE_NEWS." n
					WHERE
						language_id = '".$lang_id."' AND
						(
						  header_text LIKE '%".encode_text($keyword)."%' OR
						  body_text LIKE '%".encode_text($keyword)."%'
						)";
			$order_field = "n.id";						
		}else{
			$sql = "SELECT
						CONCAT('page=pages&pid=', id) as url,
						page_title as title,
						page_text as text,
						content_type,
						link_url 
					FROM ".TABLE_PAGES." p
					WHERE
						language_id = '".$lang_id."' AND
						is_published = 1 AND
						show_in_search = 1 AND
						is_removed = 0 AND 
						(
						  page_title LIKE '%".encode_text($keyword)."%' OR
						  page_text LIKE '%".encode_text($keyword)."%'
						)";
			$order_field = "p.id";			
		}

		if(!is_numeric($page) || (int)$page <= 0) $page = 1;
		$this->totalSearchRecords = (int)database_query($sql, ROWS_ONLY);
		$total_pages = ($this->totalSearchRecords / $this->pageSize);			
		if(($this->totalSearchRecords % $this->pageSize) != 0) $total_pages = (int)$total_pages + 1;
		$start_row = ($page - 1) * $this->pageSize;
		
		$result = database_query($sql." ORDER BY ".$order_field." ASC LIMIT ".$start_row.", ".$this->pageSize, DATA_AND_ROWS);		

		// update search results table		
		if($result[1] > 0){
			$sql = "INSERT INTO ".TABLE_SEARCH_WORDLIST." (word_text, word_count) VALUES ('".$keyword."', 1) ON DUPLICATE KEY UPDATE word_count = word_count + 1";
			database_void_query($sql);
		}		
		return $result;
	}
	
	/**
	 * Draws search result
	 *		@param $search_result - search result
	 *		@param $page
	 */	
	public function DrawSearchResult($search_result, $page = 1)
	{
		$total_pages = (int)($this->totalSearchRecords / $this->pageSize);			
		if(!is_numeric($total_pages) || (int)$total_pages <= 0) $total_pages = 1;
		
		if($search_result != "" && $search_result[1] > 0){
			echo "<div class='pages_contents'>";		
			for($i = 0; $i < $search_result[1]; $i++){
				if($search_result[0][$i]['content_type'] == "article"){
					echo ($i+1).". <a href='index.php?".$search_result[0][$i]['url']."'>".decode_text($search_result[0][$i]['title'])."</a><br />";
	
					$page_text = $search_result[0][$i]['text'];
					$arr_replace = array("\\r", "\\n", "{module:gallery}", "{module:about_us}", "{module:contact_us}", "{module:album=CODE}", "{module:rooms}");
					$page_text = str_replace($arr_replace, "", $page_text);
					$page_text = strip_tags($page_text);
					$page_text = decode_text($page_text);				
	
					echo substr_by_word(strip_tags($page_text), 255)."...<br />";					
				}else{
					echo ($i+1).". <a href='".$search_result[0][$i]['link_url']."'>".decode_text($search_result[0][$i]['title'])."</a> <img src='images/external_link.gif' alt=''><br />";					
				}
				echo "<div class='line-hor no_margin'></div>";
			}
			echo "<b>"._PAGES.":</b> ";
			for($i = 1; $i <= $total_pages; $i++){
				echo "<a class='pagging_link' href='javascript:void(0);' onclick='javascript:appPerformSearch(".$i.");'>".(($i == $page) ? "<b>[".$i."]</b>" : $i)."</a> ";
			}
			echo "</div>";			
		}else{
			draw_important_message(_NO_RECORDS_FOUND);		
		}				
	}

	/**
	 * Draws popular search keywords
	 */
	public function DrawPopularSearches()
	{
		$sql = "SELECT word_text, word_count FROM ".TABLE_SEARCH_WORDLIST." ORDER BY word_count DESC LIMIT 0, 20";
		$result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
		if($result[1] > 0){
			echo "<div class='pages_contents'><a href='javascript:void(0);' onclick='appToggleJQuery(\"popular_search\")'>"._POPULAR_SEARCH." +</a></div>";
			echo "<fieldset class='popular_search'>";
			echo "<legend>"._KEYWORDS."</legend>";
			for($i = 0; $i < $result[1]; $i++){
				if($i > 0) echo ", ";
				echo "<a onclick='javascript:appPerformSearch(1, \"".$result[0][$i]['word_text']."\");' href='javascript:void(0)'>".$result[0][$i]['word_text']."</a>";
			}
			echo "</fieldset>";			
		}
	}
	
	/**
	 * Draws quick search form
	 */
	static public function DrawQuickSearch()
	{	
		$keyword = isset($_POST['keyword']) ? trim(prepare_input($_POST['keyword'])) : _SEARCH_KEYWORDS."...";
		$keyword   = str_replace('"', "&#034;", $keyword);
		$keyword   = str_replace("'", "&#039;", $keyword);			
		$search_in = isset($_POST['search_in']) ? prepare_input($_POST['search_in']) : "site";
		
		$output = "<div class='header-search f".Application::Get("defined_right")."'>
			<form id='search-form' name='frmQuickSearch' action='index.php?page=search' method='post'>
				<input type='hidden' name='task' value='quick_search' />
				<input type='hidden' name='p' value='1' />
				<input type='hidden' name='search_in' id='search_in' value='".$search_in."' />
				".draw_token_field(true)."
				<input onblur=\"if(this.value == '') this.value='"._SEARCH_KEYWORDS."...';\"
					   onfocus=\"if(this.value == '"._SEARCH_KEYWORDS."...') this.value = '';\"
					   maxLength='30' size='".(strlen(_SEARCH_KEYWORDS)+5)."'
					   value='".$keyword."' name='keyword' class='search_field' />
				<input class='button' type='button' value='"._SEARCH."' onclick='appQuickSearch()' />
			</form>
		</div>";
		
		return $output;		
	}

}

?>