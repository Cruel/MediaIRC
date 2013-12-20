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
			
			$google = new Google_Client();
			$google->setDeveloperKey(Configure::read('Google.APIKEY'));
			$youtube = new Google_Service_YouTube($google);
			$resp = $youtube->videos->listVideos('snippet,statistics,status,contentDetails', array(
				'id' => $matches[1],
			));
			$video = $resp->items[0];

			$this->url = 'http://www.youtube.com/embed/'.$video->id;
			$this->video_id = $video->id;
			$this->title = $video->getSnippet()->title;
			$this->embeddable = $video->getStatus()->embeddable;
			$this->view_count = $video->getStatistics()->viewCount;
			$this->duration = $video->getContentDetails()->duration;
			$this->image = $video->getSnippet()->getThumbnails()->medium['url'];
		}
	}
	
	public function getHtml(){
		return "<h3>".$this->title."</h3>".'<div><img src="'.$this->getImageFilename('thumb').'"/><span class="glyphicon glyphicon-play-circle"></span></div>';
	}
	
}