<?php

App::uses('MediaLogBase', 'MediaLog');
App::uses('Bot', 'Model');
App::uses('Link', 'Model');

class MediaLog {
	
	private function __construct(){
		//
	}
	
	public static function loadId($id){
		$obj = new MediaLog();
		
		return $obj;
	}
	
	public static function loadUrl($url){
		$headers = get_headers($url, 1);
		if (!$headers)
			return false;
		$loggers = Configure::read('MediaLog.loggers');
		foreach ($loggers as $logger){
			$class = $logger."Log";
			App::uses($class, 'MediaLog');
			$obj = $class::loadUrl($url, $headers);
			if ($obj)
				return $obj;
		}
		return false;
	}

}