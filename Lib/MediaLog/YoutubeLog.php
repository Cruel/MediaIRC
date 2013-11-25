<?php

class YoutubeLog extends MediaLogBase {
	
	protected static $content_types = array(
		'text/html; charset=utf-8',
		'text/html'
	);
	protected static $url_regex = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
	
	protected function __construct($url = null){
		parent::__construct($url);
		if ($url){
			preg_match(self::$url_regex, $url, $matches);
			$video_info = file_get_contents("http://youtube.com/get_video_info?video_id=".$matches[1]);
			parse_str($video_info, $ytdata);
	
			$this->video_id = $matches[1];
			$this->title = $ytdata['title'];
			$this->allow_embed = $ytdata['allow_embed'];
			$this->view_count = $ytdata['view_count'];
			$this->length = $ytdata['length_seconds'];
			$this->image = $ytdata['iurl'];
		}
	}
	
	public function getHtml(){
		return "<h1>".$this->title."</h1>";
	}
	
}