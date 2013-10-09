<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

class MyFreeMp3HtmlParser {
	
	protected $data = '';
	
	protected $songName = '';
	
	protected $artistName = '';
	
	protected $albumName = '';
	
	public function __construct( $data ) {
		$this->setData( $data );
	}
	
	public function setData( $data ){
		$this->data = $data;
		$this->cleanData();
	}
	
	public function parse(){
		
		if( empty( $this->data ) ) throw new \Exception( 'Can not get track name from MyFreeMp3 because web service to get track names returned a empty response' );
		
		try {
			$doc = new \DOMDocument();

			$doc->strictErrorChecking = false;
			$doc->loadHTML( $this->data );

			$xpath = new \DOMXPath( $doc );

			$i = 0;
			$lis = $xpath->query( '//ul[@class="breadcrumb"]/li' );

			foreach( $lis as $li ){
				switch ( $i ){
					case 1:
						$this->artistName = $li->nodeValue;
						break;

					case 2: 
						$this->songName = $li->nodeValue;
						break;
				}

				$i++;
			}
		} catch( \Exception $e ){
			throw new \Exception( 'Error to parse HTML response: ' . $e->getMessage(), null, $e );
		}
	}
	
	protected function cleanData(){
		if( ( $posStart = strpos( $this->data, '<ul class="breadcrumb">' ) ) !== false ){
			$ulStart = substr( $this->data, $posStart, strlen( $this->data ) );
			
			if( ( $posEnd = strpos( $ulStart, '</ul>' ) ) !== false ){
				$ulEnd = substr( $ulStart, 0, $posEnd + strlen( '</ul>' ) );
			}
		}
		
		if( !empty( $ulEnd ) ){
			$this->data = $ulEnd;
		} else {
			return "";
		}
	}
	
	public function getSongName(){
		return \utf8_decode( $this->songName );
	}
	
	public function getArtistName(){
		return \utf8_decode( $this->artistName );
	}
	
	public function getAlbumName(){
		return \utf8_decode( $this->albumName );
	}
	
	public function getTrackName(){
		$trackName = '';
		if( $this->getArtistName() != '' ) $trackName .= $this->getArtistName();
		
		if( $this->getAlbumName() != '' ){
			if( $trackName != '' ) $trackName .= ' - ';
			$trackName .= $this->getAlbumName();
		}
		
		if( $this->getSongName() != '' ){
			if( $trackName != '' ) $trackName .= ' - ';
			$trackName .= $this->getSongName ();
		}
		
		return $trackName . '.mp3';
	}
	
}

?>
