<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use Symfony\Component\HttpFoundation\File\File;

class MusicDownloadManagerTrackLog {
	
	protected $file = null;
	
	protected $trackId = null;
	
	protected $trackName = null;
	
	protected $trackUrl = null;
	
	protected $elapsedTime = 0;
	
	protected $messages = array();
	
	protected $magnitude = 0;
	
	public function getFile() {
		return $this->file;
	}

	public function setFile( File $file ) {
		$this->file = $file;
	}
	
	public function getFileSize(){
		if( is_null( $this->file ) ) return 0;
		
		$this->magnitude = 0;
		$size = $this->getFile()->getSize(); 
		while( $size >= 1024 ){
			$size /= 1024;
			$this->magnitude++;
		}
		
		return $size;
	}
	
	public function getTrackId() {
		return $this->trackId;
	}

	public function setTrackId( $trackId ) {
		$this->trackId = $trackId;
	}
		
	public function getTrackName() {
		return $this->trackName;
	}

	public function setTrackName( $trackName ) {
		$this->trackName = $trackName;
	}
		
	public function getTrackUrl() {
		return $this->trackUrl;
	}

	public function setTrackUrl( $trackUrl ) {
		$this->trackUrl = $trackUrl;
	}

	public function getElapsedTime() {
		return $this->elapsedTime;
	}

	public function setElapsedTime( $timeElapsed ) {
		$this->elapsedTime = $timeElapsed;
	}
	
	public function getRate(){
		if( !$this->elapsedTime ) return 0;
		return \round( $this->getFileSize() / $this->elapsedTime, 2 );
	}
	
	public function getMessages() {
		return $this->messages;
	}

	public function addMessage( $message ) {
		$this->messages[] = $message;
	}
	
	public function getMagnitude(){
		switch( $this->magnitude ){
			case 0:
				return 'B';
				
			case 1:
				return 'KB';
				
			case 2:
				return 'MB';
				
			case 3:
				return 'KB';
				
			default:	
				return 'TB';
		}
	}
	
}

?>
