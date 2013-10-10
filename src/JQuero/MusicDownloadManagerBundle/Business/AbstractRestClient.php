<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

use \Symfony\Component\HttpKernel\Exception\HttpException;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;

abstract class AbstractRestClient {
	
	protected $options = array();
	
	protected $headers = array();
	
	/**
	 * Rest client
	 * @var \Guzzle\Http\Client;
	 */
	protected $client;
	
	public function __construct( $options = array() ) {
		$this->setOptions( $options );
		$this->client = new Client( $this->getUrl(), $options );
	}
	
	abstract function getId();

	public function get( $resource = '', $params = array() ) {
		$httpGetParams = $this->prepareGetParams( $resource, $params );
		
		$request = $this->client->get( $resource . $httpGetParams, $this->getHeaders(), $this->getOptions() );
		$response = $request->send();
		
		return $this->processResponse( $response );
	}
	
	public function post( $resource = '', $params = array() ){
		$this->preparePostParams( $resource, $params );
		
		$request = $this->client->post( $resource, $this->getHeaders(), $params, $this->getOptions() );
		$response = $request->send();
		
		return $this->processResponse( $response );
	}
	
	public function getUrl() {
		return $this->serverUrl;
	}

	public function getOptions() {
		return $this->options;
	}

	public function setOptions( $options ) {
		$this->options = $options;
	}
	
	public function addOption( $key, $value ){
		$this->options[ $key ] = $value;
	}
	
	public function delOption( $key ){
		if( isset( $this->options[ $key ] ) ) unset( $this->options[ $key ] );
	}

	public function getHeaders() {
		return $this->headers;
	}

	public function setHeaders( $headers ) {
		$this->headers = $headers;
	}
	
	public function prepareGetParams( $resource = '', $params = array() ){
		$this->validateResourceParams( $resource, $params );
		
		$httpGetParams = '';
		foreach( $params as $param => $value ){
			if( $httpGetParams != '' ) $httpGetParams .= '&';
			else $httpGetParams .= '?';
			$httpGetParams .= $param . '=' . htmlspecialchars( $value ) ;
		}
		
		return $httpGetParams;
	}
	
	public function preparePostParams( $resource = '', $params = array() ){
		$this->validateResourceParams( $resource, $params );
	}
	
	protected function validateResourceParams( $resource = '', $params = array() ){
		$resourceParamsStructure = $this->getResourceParamsStructure();
		
		if( isset( $resourceParamsStructure[ $resource ] ) ){
			$resourceStructure = $resourceParamsStructure[ $resource ];
			
			foreach( $resourceStructure as $paramName => $paramStructure ){
				$this->validateRequiredParam( $resource, $paramName, $paramStructure, $params );
				$this->validateFormatParam( $resource, $paramName, $paramStructure, $params );
			}
		}
	}
	
	protected function validateRequiredParam( $resource, $paramName, $paramStructure, $params ){
		if( isset( $paramStructure[ 'required' ] ) && $paramStructure[ 'required' ] == true && !isset( $params[ $paramName ] ) ){
			throw new ResourceParamRequiredException( 'Param "' . $paramName . '" is required in resource "' . $resource . '" from web service "' . $this->getUrl() . '"' );
		}
	}
	
	protected function validateFormatParam( $resource, $paramName, $paramStructure, $params ){
		if( isset( $paramStructure[ 'format' ] ) ){
			$formater = $paramStructure[ 'format' ];
			
			try {
				$formater->validate( $params[ $paramName ] );
				
			} catch( ResourceParamFormatException $e ){
				$msg = 'Param "' . $paramName . '" must have format ' . $e->getMessage() . ' to resource "' . $resource . '" from web service "' . $this->getUrl() . '"';
				throw new ResourceParamFormatException( $msg, null, $e );
			}
		}
	}
	
	/**
	 * Must return an array structured:
	 * [resourceName] => array(
	 *		[paramName] => array(
	 *			'required' => true | false,
	 *			'format' => object instace of ParamFormater
	 *		)
	 * )
	 */
	abstract protected function getResourceParamsStructure();
	
	protected function processResponse( Response $response ){
		if( $response->getStatusCode() != 200 ){
			throw new HttpException( $response->getStatusCode(), 'Error in server response. Message received: "' . $response->getMessage() . '"' );
		}
		return $response->getBody();
	}
	
	public function match( $url ){
		$match = strpos( $url, $this->getUrl() );
		return $match !== false;
	}
	
}

?>
