<?php

App::uses('MediaLogBase', 'MediaLog');

class MediaLog {
	
	public static function loadId($id){
		$model = ClassRegistry::init('Link');
		if ($model->exists($id)){
			return self::loadModel($model->find('first', array(
				'conditions' => array('Link.id' => $id)
			)));
		}
		return false;
	}
	
	public static function loadModel($Link){
		$class = $Link['Link']['type'];
		App::uses($class, 'MediaLog');
		$obj = $class::loadModel($Link);
		if ($obj)
			return $obj;
		return false;
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