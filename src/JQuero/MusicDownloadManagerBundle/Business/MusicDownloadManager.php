<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use Symfony\Component\HttpFoundation\File\File;

class MusicDownloadManager {
	
	protected $maxTracksPerRequest = 0;
	
	protected $downloadDirectory = '/home/jquero/Descargas/musicDownloadManager';
	
	protected $files = array();
	
	protected $plainTextTracks = '';
	
	protected $log;
	
	public function __construct() {
		$this->log = new MusicDownloadManagerLog();
		
		$factory = MusicDownloadClientFactory::getInstance();
		$factory->registerMusicDownloadClient( new GoearClient() );
		$factory->registerMusicDownloadClient( new MyFreeMp3Client() );
		$factory->registerMusicDownloadClient( new YoutubeToMp3Client() );
	}


	public function addFile( File $file ){
		$this->files[] = $file;
	}
	
	public function addPlainTextTracks( $plainTextTracks ){
		$this->plainTextTracks .= $plainTextTracks;
	}
	
	public function download() {
		$tracks = $this->getAllTracks();
		
		if( count( $tracks ) > $this->maxTracksPerRequest && $this->maxTracksPerRequest > 0 ){
			throw new \Exception( 'Only ' . $this->maxTracksPerRequest . ' tracks per request' );
		}
		
		foreach( $tracks as $directory => $tracks ){
			$this->setDownloadDirectoryPath( $directory );
				
			foreach( $tracks as $trackStruct ){

				$mdmClient = MusicDownloadClientFactory::getInstance()->getMusicDownloadClientFromUrl( $trackStruct[ 'trackUrl' ] );
				if( !$mdmClient ) continue;

				$mdmClient->addOption( 'directory', $this->getDownloadDirectoryPath() );

				$trackLog = $mdmClient->downloadTrackByUrl( $trackStruct[ 'trackUrl' ], $trackStruct[ 'trackName' ] );

				$this->log->addTrackLog( $trackLog );
			}
		}
		
		return $this->log;
	}
	
	protected function getAllTracks(){
		return array_merge( $this->getTracksFromFiles(), $this->getTracksFromPlainText() );
	}
	
	protected function getTracksFromFiles(){
		if( empty( $this->files ) ) return array();
		
		$tracks = array();
		foreach( $this->files as $file ){
			if( !$this->checkFile( $file ) ) continue;

			$fileTracks = $this->readTracksFromFile( $file );
			if(empty( $fileTracks ) ) continue;
			
			$tracks = array_merge( $tracks, $fileTracks );
		}
		
		return $tracks;
	}
	
	protected function checkFile( File $file ){
		$error = false;
		
		if( $file->guessExtension() != 'txt' ){
			$this->get( 'session' )->getFlashBag()->add( 'error', 'El fichero tiene que tener extension txt' );
			$error = true;
		}
		
		if( $file->getMimeType() != 'text/plain' ){
			$this->get( 'session' )->getFlashBag()->add( 'error', 'El fichero tiene que contener texto plano' );
			$error = true;
		}
		
		if( $error ) return false;
		
		return true;
	}
	
	protected function readTracksFromFile( File $file ){
		$fd = $file->openFile();
		
		$data = '';
		while( $line = $fd->fgets() ){
			$data .= trim( $line ) . "\n";
		}
		
		return $this->parsePlainText( $data );
	}
	
	protected function getTracksFromPlainText(){
		if( $this->plainTextTracks == '' ) return array();
		return $this->parsePlainText( $this->plainTextTracks );
	}
	
	protected function parsePlainText( $data ){
		$hash = array();
		
		$currentDirectory = $this->downloadDirectory;
		$trackName = '';
		
		$lines = explode( "\n", $data);
		foreach( $lines as $line ){
			if( strpos( $line, ';' ) === 0 ) continue;
			
			if( strpos( $line, 'download_directory:' ) !== false ){
				$currentDirectory = trim( substr( $line, strlen( 'download_directory:' ) ) );
				if( !isset( $hash[ $currentDirectory ] ) ) $hash[ $currentDirectory ] = array();
				continue;
			}
			
			if( strpos( $line, 'track_name:' ) !== false ){
				$trackName = trim( substr( $line, strlen( 'track_name:' ) ) );
				continue;
			}
			
			$tracks = explode( 'http', $line );
			unset( $tracks[0] );

			foreach( $tracks as $track ){
				$track = 'http' . trim( $track );
				$hash[ $currentDirectory ][ md5( $track ) ] = array( 'trackUrl' => $track, 'trackName' => $trackName );
			}
		}
		
		return $hash;
	}
	
	public function generateZipFile(){
		$zipPath = $this->getDownloadDirectoryPath();
		$zipFile = $zipPath . '.zip';
		
		exec( 'zip -r ' . $zipFile . ' ' . $zipPath );
		exec( 'rm -rf ' . $zipPath );
		
		return $zipFile;
	}

	public function getDownloadDirectoryPath(){
		return $this->downloadDirectory;
	}
	
	public function setDownloadDirectoryPath( $downloadDirectory ){
		$this->downloadDirectory = $downloadDirectory;
	}
	
	public function getLog(){
		return $this->log;
	}
	
}

?>
