<?php

namespace JQuero\MusicDownloadManagerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadManager;
use JQuero\MusicDownloadManagerBundle\Business\MusicDownloadManagerLog;

class MusicDownloadManagerCommand extends Command {

	protected function configure() {
		$this->setName('MusicDownloadManager:MusicDownloadManager')
			->setDescription('Music Download Manager')
			->addArgument('files', InputArgument::IS_ARRAY, 'File list to read');
	}
	
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$files = $this->getFilesFromArgs( $input, $output );
		
		$musicDownloadManager = new MusicDownloadManager();
		foreach( $files as $file ) $musicDownloadManager->addFile( $file );
		
		$log = $musicDownloadManager->download();
		
		$this->processMusicDownloadManagerLog( $log );
	}
	
	protected function getFilesFromArgs( InputInterface $input, OutputInterface $output ){
		$fileNames = $input->getArgument('files');
		
		$files = array();
		foreach( $fileNames as $fileName ){
			$file = new File( $fileName );
			$files[] = $file;
		}
		
		return $files;
	}

	protected function processMusicDownloadManagerLog( MusicDownloadManagerLog $log ){
		
		foreach( $log->getTracksLog() as $trackLog ){
			echo ( $trackLog->getTrackName() != '' ? $trackLog->getTrackName() : $trackLog->getTrackUrl() ) .
					" | " . ( !is_null( $trackLog->getFile() ) ? $trackLog->getFile()->getPath() : '' ) .
					" | " . round( $trackLog->getFileSize(), 0 ) . ' ' . $trackLog->getMagnitude() . 
					" | " . $trackLog->getElapsedTime() . ' segs'. 
					" | " . $trackLog->getRate() . ' ' . $trackLog->getMagnitude() . '/seg';

			$arrayMsg = $trackLog->getMessages();
			if( !empty( $arrayMsg ) ) $messages = \implode( ', ', $arrayMsg );
			if( isset( $messages ) && $messages != '' ) echo ' | ' . $messages;

			echo "\n";
		}
	}
}

?>
