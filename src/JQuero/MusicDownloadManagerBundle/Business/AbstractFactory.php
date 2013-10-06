<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

abstract class AbstractFactory {
	
	protected static $instance = null;
	
	static public function getInstance(){
		if( !static::$instance ){
			static::$instance = new static;
		}
		
		return static::$instance;
	}
	
	protected function __construct() {}
	
}

?>
