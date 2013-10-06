<?php

namespace JQuero\MusicDownloadManagerBundle\Business;

class MusicDownloadManagerLog {
	
	protected $tracksLog = array();
	
	public function addTrackLog( MusicDownloadManagerTrackLog $trackLog ){
		$this->tracksLog[] = $trackLog;
	}
	
	public function getTracksLog(){
		return $this->tracksLog;
	}
}

?>
