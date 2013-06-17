<?php

/***
 *  Class RSSFeed
 *  -------------- 
 *  Description : encapsulates rss feed properties
 *	Usage       : MicroCMS, MicroBlog, HotelSite, ShoppingCart 
 *  Updated	    : 11.11.2010
 *  Written by  : ApPHP
 *
 *  Version: 1.0.1
 *  
 *	Feed Validator:  http://validator.w3.org/feed/
 *	
 *	PUBLIC:				  	STATIC:				 	PRIVATE:
 * 	------------------	  	---------------     	---------------
 *	__construct			  	CleanTextRss 
 *	__destruct
 *	SetType
 *	SetChannel
 *	SetImage
 *	SetItem
 *	OutputFeed
 *	SaveFeed
 *
 *  1.0.1
 *  	- preg_replace('/&/', ' ', $text);
 *  	- 
 *  	-
 *  	-
 *  	-
 *	
 *	
 **/

class RSSFeed {

    private $channelUrl;
    private $channelTitle;
    private $channelDescription;
    private $channelLang;
    private $channelCopyright;
    private $channelDate;
	private $channelAuthor;
    private $channelCreator;
    private $channelSubject;
	
	private $rssType;
    
    private $imageUrl;

    private $arrItems = array();
    private $countItems;
	
	private $fileName;
    
	//==========================================================================
    // Class Constructor
	//==========================================================================
	function __construct()
	{
        $this->countItems=0;
        $this->channelUrl='';
        $this->channelTitle='';
        $this->channelDescription='';
        $this->channelLang='';
        $this->channelCopyright='';
        $this->channelDate='';
		$this->channelAuthor='';
        $this->channelCreator='';
        $this->channelSubject='';
        $this->imageUrl='';
		$this->rssType = 'rss1';
		
		$this->fileName = "feeds/rss.xml";
    }
    
	//==========================================================================
    // Class Destructor
	//==========================================================================
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }
	
	/**
	 * Sets RssFeed type
	 * 		@param $type - type
	 */ 
	public function SetType($type = "")
	{
		if($type) $this->rssType = $type;
	}

	/**
	 * Sets Channel
	 *		@param $url
	 *		@param $title
	 *		@param $description
	 *		@param $lang
	 *		@param $copyright
	 *		@param $creator
	 *		@param $subject
	 */
    public function SetChannel($url, $title, $description, $lang, $copyright, $creator, $subject)
	{
        $this->channelUrl=$url;
        $this->channelTitle=$title;
        $this->channelDescription=$description;
        $this->channelLang=$lang;
        $this->channelCopyright=$copyright;
        if($this->rssType == "rss1"){
			$this->channelDate=date("Y-m-d").'T'.date("H:i:s").'+02:00';
		}else if($this->rssType == "rss2"){
			$this->channelDate=date("D, d M Y H:i:s T");
		}else{
			$this->channelDate=date("Y-m-d").'T'.date("H:i:sT");
		}
		$this->channelCreator=$creator;
		$this->channelAuthor=$creator;
        $this->channelSubject=$subject;
    }
    
	/**
	 * Sets Image
	 *		@param $url
	 */
    public function SetImage($url)
	{
        $this->imageUrl=$url;
    }
    
	/**
	 * Sets Item
	 *		@param $url
	 *		@param $title
	 *		@param $description
	 */
    public function SetItem($url, $title, $description, $pub_date)
	{
        $this->arrItems[$this->countItems]['url']=$url;
        $this->arrItems[$this->countItems]['title']=$title;
        $this->arrItems[$this->countItems]['description']=$description;
		$this->arrItems[$this->countItems]['pub_date']=$pub_date;
        $this->countItems++;    
    }
    
	/**
	 * Returns Feed
	 */
    public function OutputFeed()
	{
		if($this->rssType == "atom"){
		// RSS Atom	

			$output =  '<?xml version="1.0" encoding="utf-8"?>'."\n";
			$output .= '<feed version="0.3" xmlns="http://purl.org/atom/ns#">'."\n";
			$output .= '<channel>'."\n";
			$output .= '<title>'.$this->channelTitle.'</title>'."\n";
			$output .= '<link href="'.$this->channelUrl.'" rel="alternate" type="text/html" />'."\n";
			$output .= '<modified>'.$this->channelDate.'</modified>'."\n";
			$output .= '<author>'."\n";
			$output .= '<name>'.$this->channelAuthor.'</name>'."\n";
			$output .= '</author>'."\n";
			#<id>tag:google.com,2005-10-15:/support/jobs</id>
			for($k=0; $k<$this->countItems; $k++) {
				$output .= '<entry>'."\n";
				$output .= '<title>'.$this->arrItems[$k]['title'].'</title>'."\n";
				$output .= '<link href="'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'" />'."\n";
				$output .= '<summary>'.$this->arrItems[$k]['description'].'</summary>'."\n";
				#<id>tag:google.com,2005-10-15:/support/jobs/hr-analyst</id>
				#<issued>2005-10-13T18:30:02Z</issued>
				$output .= '<modified>'.date("Y-m-d", strtotime($this->arrItems[$k]['pub_date'])).'T'.date("H:i:sT", strtotime($this->arrItems[$k]['pub_date'])).'</modified>'."\n";
				$output .= '</entry>'."\n";			
			}
			$output .= '</channel>'."\n";
			$output .= '</feed>'."\n";			
		
		}else if($this->rssType == "rss2"){
		// RSS 2.0
		
			$output =  '<?xml version="1.0" encoding="utf-8"?>'."\n";
			$output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
			$output .= '<channel>'."\n";
			$output .= '<atom:link href="'.$this->channelUrl.'" rel="self" type="application/rss+xml" />'."\n";
			$output .= '<title>'.$this->channelTitle.'</title>'."\n";
			$output .= '<link>'.$this->channelUrl.'</link>'."\n";
			$output .= '<description>'.$this->channelDescription.'</description>'."\n";
			$output .= '<language>'.$this->channelLang.'</language>'."\n";
			$output .= '<copyright>'.$this->channelCopyright.'</copyright>'."\n";
			$output .= '<pubDate>'.$this->channelDate.'</pubDate>'."\n";
			///$output .= '<lastBuildDate>'.$this->channelDate.'</lastBuildDate>'."\n";
			$output .= '<image>'."\n";
			$output .= '<url>'.$this->imageUrl.'</url>'."\n";
			$output .= '<title>'.$this->channelTitle.'</title>'."\n";
			$output .= '<link>'.$this->channelUrl.'</link>'."\n";
			$output .= '</image>'."\n";
			for($k=0; $k<$this->countItems; $k++) {
				$output .= '<item>'."\n";
				$output .= '<title>'.$this->arrItems[$k]['title'].'</title>'."\n";
				$output .= '<link>'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'</link>'."\n";
				$output .= '<description>'.$this->arrItems[$k]['description'].'</description>'."\n";
				$output .= '<author>'.$this->channelCreator.'</author>'."\n";
				$output .= '<guid>'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'</guid>'."\n";
				$output .= '<pubDate>'.date("D, d M Y H:i:s T", strtotime($this->arrItems[$k]['pub_date'])).'</pubDate>'."\n";
				$output .= '</item>'."\n";
			};
			$output .= '</channel>'."\n";
			$output .= '</rss>'."\n";			
		}else{
		// RSS 1.0
		
			// encoding='iso-8859-1'
			$output =  '<?xml version="1.0" encoding="utf-8"?>'."\n";
			$output .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">'."\n";
			$output .= '<channel rdf:about="'.$this->channelUrl.'">'."\n";
			$output .= '<title>'.$this->channelTitle.'</title>'."\n";
			$output .= '<link>'.$this->channelUrl.'</link>'."\n";
			$output .= '<description>'.$this->channelDescription.'</description>'."\n";
			$output .= '<dc:language>'.$this->channelLang.'</dc:language>'."\n";
			$output .= '<dc:rights>'.$this->channelCopyright.'</dc:rights>'."\n";
			$output .= '<dc:date>'.$this->channelDate.'</dc:date>'."\n";
			$output .= '<dc:creator>'.$this->channelCreator.'</dc:creator>'."\n";
			$output .= '<dc:subject>'.$this->channelSubject.'</dc:subject>'."\n";
			$output .= '<items>'."\n";
			$output .= '<rdf:Seq>';
			for($k=0; $k<$this->countItems; $k++) {
				$output .= '<rdf:li rdf:resource="'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'"/>'."\n";
			};
			$output .= '</rdf:Seq>'."\n";
			$output .= '</items>'."\n";
			$output .= '<image rdf:resource="'.$this->imageUrl.'"/>'."\n";
			$output .= '</channel>'."\n";
			for($k=0; $k<$this->countItems; $k++) {
				$output .= '<item rdf:about="'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'">'."\n";
				$output .= '<title>'.$this->arrItems[$k]['title'].'</title>'."\n";
				$output .= '<link>'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'</link>'."\n";
				$output .= '<description>'.$this->arrItems[$k]['description'].'</description>'."\n";
				$output .= '<feedburner:origLink>'.str_replace("&", "&amp;", $this->arrItems[$k]['url']).'</feedburner:origLink>'."\n";
				$output .= '</item>'."\n";
			};
			$output .= '</rdf:RDF>'."\n";			
		}
        
        return $output;
    }

  	/***
	 * Saves Feed
	 */
    public function SaveFeed()
	{
		if(SITE_MODE == "development"){
			$handle = @fopen($this->fileName,'w+');
			if($handle){
				@fwrite($handle, $this->OutputFeed());
				@fclose($handle);
			}						
		}else{
			$handle = @fopen($this->fileName,'w+');
			if($handle){
				@fwrite($handle, $this->OutputFeed());
				@fclose($handle);
			}			
		}
    }

	/**
	 *  Cleans text from all formating
	 *  	@param $text
	 */
	static public function CleanTextRss($text)
	{
		// $text = preg_replace( "']*>.*?'si", '', $text );
		/* Remove this line to leave URL's intact */
		/* $text = preg_replace( '/]*>([^<]+)<\/a>/is', '\2 (\1)', $text ); */
		$text = preg_replace('//', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace('/ /', ' ', $text);
		//$text = preg_replace('/&/', ' ', $text);
		$text = preg_replace('/"/', ' ', $text);
		/* add the second parameter to strip_tags to ignore the tag for URLs */
		$text = strip_tags($text, '');
		$text = stripcslashes($text);
		$text = htmlspecialchars($text);
		//$text = htmlentities( $text );
		
		return $text;
	}

}
?>