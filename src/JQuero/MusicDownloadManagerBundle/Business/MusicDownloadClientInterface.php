<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

interface MusicDownloadClientInterface {
	
	/**
	 * Get the track id from track url.
	 * @param $trackFriendlyUrl Url for the track in some server.
	 */
	public function getTrackId( $trackFriendlyUrl );
	
	/**
	 * Get the track name. This name will be the file's name when track will be saved.
	 * @param $trackFriendlyUrl Url for the track in some server.
	 */
	public function getTrackName( $trackFriendlyUrl );
	
	/**
	 * Download the track in your sever using the url for the track.
	 * @param $trackFriendlyUrl Url for the track in some server.
	 */
	public function downloadTrackByUrl( $trackFriendlyUrl );
	
}

?>
