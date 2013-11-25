<?php

class ImageLog extends MediaLogBase {
	
	protected static $content_types = array(
		'image/gif',
		'image/jpeg',
		'image/pjpeg',
		'image/png'
	);
	// Matches any URL as long as the content type is an image
	protected static $url_regex = '//';
	
	protected function __construct($url = null){
		parent::__construct($url);
		$this->image = '';
	}
	
	public static function loadUrl($url, $headers){
		return parent::loadUrl($url, $headers);
	}
	
	public function getHtml(){
		return '<img src="" />';
	}
	
}