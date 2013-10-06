<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

class MyFreeMp3HtmlParser {
	
	protected $data = '';
	
	protected $songName = '';
	
	protected $artistName = '';
	
	protected $albumName = '';
	
	public function __construct( $data ) {
		$this->setData( $data );
		$this->parse();
	}
	
	public function setData( $data ){
		$this->data = $data;
		$this->cleanData();
	}
	
	public function parse(){
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
	}
	
	protected function cleanData(){
		$regexp = '/\/\/<!\[CDATA\[\n/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/\n\/\/\]\]>\n/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/\t+/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/\n+/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/(\s){2,}/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/<script(.*?)>(.*?)<\/script>/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/<br>/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/<br\/>/';
		$this->data = preg_replace( $regexp, '', $this->data);
		
		$regexp = '/<!--(.*?)-->/';
		$this->data = preg_replace( $regexp, '', $this->data);
	}
	
	public function getSongName(){
		return $this->songName;
	}
	
	public function getArtistName(){
		return $this->artistName;
	}
	
	public function getAlbumName(){
		return $this->albumName;
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
