<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use JQuero\MusicDownloadManagerBundle\Business\WgetClient;
use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadClientInterface;

class YoutubeToMp3Client extends WgetClient implements MusicDownloadClientInterface {
	
	protected $serverUrl = 'http://www.youtube-mp3.org';
	
	protected $serverResourceUrl = 'http://www.youtube-mp3.org';
	
	protected $resourceDownloadTrack = '/get';
	
	protected $resourceTrackInfo = '/a/itemInfo/';
	
	protected $tmpDir = '/tmp/musicDownloadManager';
	
	protected $tracksInfo = array();
	
	public function getId() {
		return 'YOUTUBE_TO_MP3_CLIENT';
	}

	protected function getResourceParamsStructure() {
		return array(
			$this->resourceDownloadTrack => array(
				'video_id' => array( 'required' => true ),
				'h' => array( 'required' => true )
 			),
			$this->resourceTrackInfo => array(
				'video_id' => array( 'required' => true )
 			)
		);
	}

	public function getTrackId( $trackUrl ) {
		$parts = explode( '/', $trackUrl );
		if( count( $parts ) == 4 ) return end( $parts );
		return '';
	}

	public function getTrackName( $trackUrl, $trackNameAlt ) {
		$trackId = $this->getTrackId( $trackUrl );
		try {
			$trackInfo = $this->getTrackInfo( array( 'video_id' => $trackId ) );
			return \trim( $trackInfo->title ) . '.mp3';
			
		} catch( \Exception $e ){
			$this->trackLog->addMessage( $e->getMessage() );
			return ( empty( $trackNameAlt ) ? $this->getTrackId( $trackUrl ) : $trackNameAlt ). '.mp3';
		}
	}
	
	public function downloadTrackByUrl( $trackUrl, $trackNameAlt ){
		$this->trackLog = new MusicDownloadManagerTrackLog();
		
		try {
			$trackId = $this->getTrackId( $trackUrl );
			$trackName = $this->getTrackName( $trackUrl, $trackNameAlt );
			$hashParameter = $this->getHashParameter( $trackId );
			
			$time = \time();

			$this->addOption( 'filename', $trackName );
			$file = $this->downloadTrackByParams( array( 'video_id' => $trackId, 'h' => $hashParameter ) );
			
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
		return $this->get( $resource, $params );
	}

	protected function getServerResourceDownloadTrack(){
		return $this->serverResourceUrl . $this->resourceDownloadTrack;
	}

	protected function getServerResourceTrackInfo(){
		return $this->serverResourceUrl . $this->resourceTrackInfo;
	}

	public function getHashParameter( $trackId ) {
		try {
			$trackInfo = $this->getTrackInfo( array( 'video_id' => $trackId ) );
			
		} catch( \Exception $e ){
			$this->trackLog->addMessage( $e->getMessage() );
			return $trackId;
		}
		return $trackInfo->h;
	}
	
	protected function getTrackInfo( $params = array() ){
		$resource = $this->getServerResourceTrackInfo();

		$this->validateResourceParams( $resource, $params );

		$trackId = $params[ 'video_id' ];
		if ( isset( $this->tracksInfo[ $trackId ] ) ) return $this->tracksInfo[ $trackId ];

		$trackInfo = $this->getRest( $resource, $params );
		$str = substr($trackInfo, 7, strlen($trackInfo) - 8);

		$this->tracksInfo[ $trackId ] = json_decode($str);
		return $this->tracksInfo[ $trackId ];
	}
	
	public function match( $url ) {
		$match = strpos( $url, 'youtu.be' );
		return $match !== false;
	}

	
}

?>
