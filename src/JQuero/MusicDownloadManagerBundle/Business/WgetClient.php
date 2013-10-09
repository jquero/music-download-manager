<?php

/**
 * Description of AbstractRestClient
 *
 * @author jquero
 */

namespace JQuero\MusicDownloadManagerBundle\Business;

use Symfony\Component\HttpFoundation\File\File;

abstract class WgetClient extends AbstractRestClient {
	
	protected $tmpDir = '/tmp/WgetClient';
	
	protected $timestamp;
	
	public function __construct( $options = array( ) ) {
		parent::__construct( $options );
		
		$this->timestamp = date('YmdHis');
	}

	public function get( $resource = '', $params = array() ) {
		$httpGetParams = $this->prepareGetParams( $resource, $params );
		
		$directoryName = $this->getDownloadDirectoryPath();
		if( !\file_exists( $directoryName ) ) mkdir( $directoryName, '755', true );
		
		$fileName = $this->getFileName();
		
		if( !$this->existsFile( $directoryName . DIRECTORY_SEPARATOR . $fileName ) )
			copy( $resource . $httpGetParams, $directoryName . DIRECTORY_SEPARATOR . $fileName );
		
		return $this->getDownloadFile();
	}
	
	public function getRest( $resource = '', $params = array() ){
		return parent::get($resource, $params);
	}
	
	public function post( $resource = '', $params = array() ){
		throw new \Exception( "WgetClient don't allow post method. You must use get method" );
	}
	
	protected function existsFile( $filename ){
		return \file_exists( $filename );
	}
	
	protected function getTmpDirectory() {
		return $this->tmpDir . DIRECTORY_SEPARATOR . $this->timestamp;
	}

	public function getDirectoryDownloadName(){
		$parts = explode( DIRECTORY_SEPARATOR, $this->getDownloadDirectoryPath() );
		return end( $parts );
	}

	public function getDownloadDirectoryPath(){
		$options = $this->getOptions();
		
		if( isset( $options['directory'] ) ){
			$directoryName = $options['directory'];
			
		} else {
			$directoryName = $this->getTmpDirectory();
			
		}
		
		return $directoryName;
	}
	
	public function getFileName(){
		$options = $this->getOptions();
		
		if( isset( $options['filename'] ) ){
			$fileName = $options['filename'];
			
		} else {
			$fileName = md5( $this->getId() . time() );
			
		}
		
		return $fileName;
	}
	
	public function getDownloadFile(){
		$path = $this->getDownloadDirectoryPath() . DIRECTORY_SEPARATOR . $this->getFileName();
		return new File( $path, true );
		
	}
	
}

?>
