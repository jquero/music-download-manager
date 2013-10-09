<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

interface MusicDownloadClientInterface {
	
	/**
	 * Get the track id from track url.
	 * @param $trackUrl Url for the track in some server.
	 */
	public function getTrackId( $trackUrl );
	
	/**
	 * Get the track name. This name will be the file's name when track will be saved.
	 * @param $trackUrl Url for the track in some server.
	 * @param $trackNameAlt Alternative track name if it can't be got any way.
	 */
	public function getTrackName( $trackUrl, $trackNameAlt );
	
	/**
	 * Download the track in your sever using the url for the track.
	 * @param $trackUrl Url for the track in some server.
	 * @param $trackNameAlt Alternative track name if it can't be got any way.
	 */
	public function downloadTrackByUrl( $trackUrl, $trackNameAlt );
	
}

?>
