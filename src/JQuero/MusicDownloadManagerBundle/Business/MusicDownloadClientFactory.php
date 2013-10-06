<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use JQuero\MusicDownloadManagerBundle\Business\AbstractRestClient;
use JQuero\MusicDownloadManagerBundle\Business\AbstractFactory;

class MusicDownloadClientFactory extends AbstractFactory {
	
	protected $clients = array();
	
	public function registerMusicDownloadClient( AbstractRestClient $client ){
		$this->clients[ $client->getId() ] = $client;
	}

	public function getMusicDownloadClientFromUrl( $url ){
		foreach( $this->clients as $client ){
			if( $client->match( $url ) ) return $client;
		}
		
		return null;
	}
}

?>
