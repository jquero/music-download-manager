<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use Symfony\Component\HttpFoundation\File\File;

class MusicDownloadManagerFileLog {
	
	protected $file = '';
	
	protected $tracks = array();
	
	public function getFileName() {
		return $this->file;
	}

	public function setFileName( File $file ) {
		$this->file = $file;
	}

	public function getTracksName() {
		return $this->tracks;
	}

	public function addTracksName( $trackName, $size = '', $downloadTime = '' ) {
		$this->tracks[] = array( 'trackName' => $trackName, 'size' => $size, 'downloadTime' => $downloadTime );
	}

	public function toString( $lr = '<br>' ){
		$str = "Fichero cargado: " . $this->file->getPathname() . $lr;
		
		foreach( $this->tracks as $track ){
			
			$str .= $track[ 'trackName' ] . ' [' . $track[ 'size' ] . '] [' . $track[ 'downloadTime' ] . ']' . $lr;
		}
		
		return $str;
	}
}

?>
