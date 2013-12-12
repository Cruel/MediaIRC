<?php

abstract class MediaLogBase {
	
	// Variable to hold the Link model instance
	protected $Link;
	
	protected $url,
	          $model = null,
	          $image = null,
	          $data = array();

	protected function __construct($url = null){
		$this->Link = ClassRegistry::init('Link');
		$this->url = $url;
	}
	
	// Called from MediaLog::loadId() after fetching model from id
	public static function loadModel($model){
		$classname = get_called_class();
		$class = new $classname();
		$class->model = $model;
		$class->data = json_decode($model['data'], true);
		return $class;
	}
	
	public static function loadUrl($url, $headers){
		if (!static::isValid($url, $headers))
			return false;
		$classname = get_called_class();
		return new $classname($url);
	}
	
	public static function isValid($url, $headers){
		$mime = $headers['Content-Type'];
		$mime = (is_array($mime)) ? end($mime): $mime; // Check for array (if 301/302 redirect headers)
		if (!(in_array($mime, static::$content_types) &&
			preg_match(static::$url_regex, $url)))
				return false;
		return true;
	}
	
	public function getImageFilename($size='thumb') {
		if ($size != '')
			$size .= '_';
		$filename = "files/link/image/{$this->model['id']}/$size{$this->model['image']}";
		if (file_exists(WWW_ROOT.$filename))
			return '/'.$filename;
		else
			return "/img/{$size}404.jpg";
	}
	
	public function save($bot_id, $author, $context){
		$record = array(
				'Link' => array(
						'bot_id'  => $bot_id,
						'url'     => $this->url,
						'image'   => $this->image,
						'author'   => $author,
						'type'    => get_called_class(),
						'data'    => json_encode($this->data),
						'context' => $context,
						'date'    => null
				)
		);
		$this->Link->create();
		$this->Link->save($record);
	}
	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'],
			E_USER_NOTICE);
		return null;
	}

	public function __isset($name){ return isset($this->data[$name]); }
		
	public function __unset($name){ unset($this->data[$name]); }
	
	public function getHtml(){}
	
}