music-download-manager
======================

Symfon2 bundle to download music from youtube.com, goear.es and myfreemp3.eu

I do not take responsibility of your use of this code and music you download. Just offer a program for downloading music servers, which allow the download manually.

DESCRIPTION
===========

This is a Symfony2 Bundle to download music from two servers: goear.com, myfreemp3.eu and youtube.

There are two ways to download music. Via web and via console:


INSTALL
=======

1. We supose that you have installed and configured Symfony2.

2. Download this repository
	
	
	git clone https://github.com/jquero/music-download-manager.git
  
	
3. Copy src/JQuero folder existing in music-download-manager within your src Symfony folder.

4. Add bundle in AppKernel.php:

	new JQuero\MusicDownloadManagerBundle\MusicDownloadManagerBundle(),

5. Add route resource in your route file config (/app/config/routing.yml):

	
	music_download_manager:
	
		resource: "@MusicDownloadManagerBundle/Resources/config/routing.yml"
		
		prefix:   /mdm
		

6. Modify path to Directory download. This path is in file:

	
	src/JQuero/MusicDownloadManagerBundle/Business/MusicDownloadManager.php
	
	Change protected variable downloadDirectory.
	

USAGE
=====

VIA WEB:

1. Open yor web browser.

2. Go to the project site

		
	http://www.yoursite.com/mdm
	

3. Put a file as attachement or write the traks list in the textarea. Submit form. The tracks will be downloaded into your server.


VIA CONSOLE
===========

1. Open a console.

2. Go to your project root folder. For example:

		
	cd < synfony_project_directory >
	

3. Type:

	
	app/console MusicDownloadManager:MusicDownloadManager /< path_to_file_with_tracks_urls >/playlist.txt
	

This method accepts several files as parameter.

	
	app/console MusicDownloadManager:MusicDownloadManager /< path_to_file_with_tracks_urls >/playlist1.txt /< path_to_file_with_tracks_urls >/playlist2.txt
	

TRACKS FILE FORMAT
==================

	download_directory:/home/jquero/Music/Flamenco
	
	http://www.goear.com/listen/1e642a2/que-trata-de-andalucia-los-asdlanticos
	
	http://www.goear.com/listen/5b7518f/no-puedo-quitar-mis-ojos-de-ti-tucara-
	
	
	download_directory:/home/jquero/Music/House
	
	http://myfreemp3.eu/l/5l3sa9ubmet/
	
	http://myfreemp3.eu/l/fsswqklulwu/
	
	
	download_directory:/home/jquero/Music/Flamenco
	
	http://youtu.be/HSwu0WH7Vfc
	

OPTIONS
=======

You can override the download directory writting in your tracks list the keyword 'download_directory'. 
The default path of protected variable downloadDirectory will be overrided by de new path. 
You can override the download directory as many times as you want in the track list. 
All tracks listed after tag download_directory will be downloaded within this directory. 

