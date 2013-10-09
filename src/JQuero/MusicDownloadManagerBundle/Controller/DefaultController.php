<?php

namespace JQuero\MusicDownloadManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadManagerLog;

use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadManager;

class DefaultController extends Controller {

	public function indexAction() {
		return $this->render( 'MusicDownloadManagerBundle:Default:index.html.twig' );
	}
	
	public function getAction() {
		$musicDownloadManager = new MusicDownloadManager();
		
		$uploadedFile = $this->getRequest()->files->get( 'tracksFile' );
		if( $uploadedFile ) $musicDownloadManager->addFile( $uploadedFile );
		
		$plainTextTracks = $this->getRequest()->request->get( 'tracks' );
		if( $plainTextTracks ) $musicDownloadManager->addPlainTextTracks( $plainTextTracks );
		
		$log = $musicDownloadManager->download();
		$tracks  = $this->processMusicDownloadManagerLog( $log );
		
		if( !$this->getRequest()->request->get( 'downloadZip' ) ){
			return $this->render( 'MusicDownloadManagerBundle:Default:get.html.twig', 
				array( 'tracks' => $tracks ) );
		
		}
	}
	
	protected function processMusicDownloadManagerLog( MusicDownloadManagerLog $log ){
		$tracks = array();
		
		foreach( $log->getTracksLog() as $trackLog ){
			$tracks[] = array(
				'trackId' => $trackLog->getTrackId(),
				'trackName' => $trackLog->getTrackName(),
				'trackUrl' => $trackLog->getTrackUrl(),
				'file' => $trackLog->getFile() ? $trackLog->getFile()->getPath() : false,
				'size' => round( $trackLog->getFileSize(), 0 ) . ' ' . $trackLog->getMagnitude(),
				'time' => $trackLog->getElapsedTime() . ' segs',
				'rate' => $trackLog->getRate() . ' ' . $trackLog->getMagnitude() . '/seg',
				'messages' => $trackLog->getMessages()
			);
		}
		
		return $tracks;
	}
}
