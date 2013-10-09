<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadClientInterface;

class GoearClient extends WgetClient implements MusicDownloadClientInterface {
	
	protected $serverUrl = 'http://www.goear.com';
	
	protected $serverResourceUrl = 'http://www.goear.com';
	
	protected $resourceDownloadTrack = '/action/sound/get/';
	
	protected $tmpDir = '/tmp/musicDownloadManager';
	
	protected $trackLog = null;
	
	public function getId() {
		return 'GOEAR_CLIENT';
	}

	protected function getResourceParamsStructure() {
		return array(
			$this->resourceDownloadTrack => array(
				'trackId' => array(
					'required' => true
				)
			)
		);
	}
	
	public function getTrackId( $trackUrl ){
		$parts = explode( '/', $trackUrl );
		if( count( $parts ) == 6 ) return $parts[4];
		return '';
	}
	
	public function getTrackName( $trackUrl, $trackNameAlt ){
		$parts = explode( '/', $trackUrl );
		if( count( $parts ) == 6 ) return \trim( str_replace( '-', ' ', $parts[5] ) ) . '.mp3';
		else return $trackNameAlt;
	}
	
	public function downloadTrackByUrl( $trackUrl, $trackNameAlt ){
		$this->trackLog = new MusicDownloadManagerTrackLog();
		
		try {
			$trackId = $this->getTrackId( $trackUrl );
			$trackName = $this->getTrackName( $trackUrl, $trackNameAlt );

			$this->addOption( 'filename', $trackName );
		
			$time = \time();
		
			$file = $this->downloadTrackByParams( array( 'trackId' => $trackId ) );
			
			$this->trackLog->setTrackId( $trackId );
			$this->trackLog->setTrackName( $file->getFileName() );
			$this->trackLog->setTrackUrl( $trackUrl );
			$this->trackLog->setElapsedTime( \time() - $time );
			$this->trackLog->setFile( $file );
			
		} catch ( \Exception $e ){
			$this->trackLog->addMessage( $e->getMessage() );
		}
		
		$trackLog = clone( $this->trackLog );
		unset( $this->trackLog );
		return $trackLog;
	}
	
	protected function downloadTrackByParams( $params = array() ){
		$resource = $this->getServerResourceDownloadTrack();
		
		// The params validation we do here because we call get method only with resource URL without query params
		$this->validateResourceParams( $this->resourceDownloadTrack, $params );
		
		$trackId = $params['trackId'];
		return $this->get( $resource . $trackId );
	}

	protected function getServerResourceDownloadTrack(){
		return $this->serverResourceUrl . $this->resourceDownloadTrack;
	}

}

?>
