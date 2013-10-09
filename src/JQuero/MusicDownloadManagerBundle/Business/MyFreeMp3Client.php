<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use Symfony\Component\HttpKernel\Exception\HttpException;

use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadClientInterface;
use JQuero\MusicDownloadManagerBundle\Business\MyFreeMp3HtmlParser;

use Guzzle\Http\Client;

class MyFreeMp3Client extends WgetClient implements MusicDownloadClientInterface {
	
	protected $serverUrl = 'http://myfreemp3.eu';
	
	protected $serverResourceUrl = 'http://5.39.109.235';
	
	protected $resourceDownloadTrack = '/dvv.php';
	
	protected $tmpDir = '/tmp/musicDownloadManager';
	
	public function getId() {
		return 'MY_FREE_MP3_CLIENT';
	}
	
	protected function getResourceParamsStructure() {
		return array(
			$this->resourceDownloadTrack => array(
				'q' => array(
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
		try {
			return $this->getTrackNameFromService( $trackUrl );
			
		} catch( \Exception $e ) {
			$this->trackLog->addMessage( $e->getMessage() );
			return ( empty( $trackNameAlt ) ? $this->getTrackId( $trackUrl ) : $trackNameAlt ). '.mp3';
		}
	}
	
	public function downloadTrackByUrl( $trackUrl, $trackNameAlt ){
		$this->trackLog = new MusicDownloadManagerTrackLog();
		
		try {
			$trackId = $this->getTrackId( $trackUrl );
			$trackName = $this->getTrackName( $trackUrl, $trackNameAlt );

			$this->addOption( 'filename', $trackName );
		
			$time = \time();
			
			$file = $this->downloadTrackByParams( array( 'q' => $trackId ) );
			
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
	
	protected function getTrackNameFromService( $trackUrl ){
		$clientTrackName = new Client( $trackUrl );
		$request = $clientTrackName->get();
		
		$response = $request->send();
		$data = $this->processResponse( $response );
		
		$parser = new MyFreeMp3HtmlParser( $data );
		$parser->parse();
		return \trim( $parser->getTrackName() );
	}

}

?>
