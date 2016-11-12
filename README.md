music-download-manager
======================

Symfony2 bundle to download music from youtube.com, goear.es and myfreemp3.eu

I do not take responsibility of your usage of this code and the music you download. Just offer a program for downloading from music servers, which allow manual download.

Description
===========

This is a Symfony2 Bundle to download music from two servers: goear.com, myfreemp3.eu and youtube.

There are two ways to download music. Via web and via console:


Installing
==========

1. We suppose that you have installed and configured Symfony2.

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
	

Usage
=====

*VIA WEB:*

1. Open your web browser.

2. Go to the project site

		
	http://www.yoursite.com/mdm
	

3. Put a file as attachement or write the traks list in the textarea. Submit form. The tracks will be downloaded into your server.


*VIA CONSOLE:*

1. Open a console.

2. Go to your project root folder. For example:

		
    `cd <symfony_project_directory>`
	

3. Type:

	
    app/console MusicDownloadManager:MusicDownloadManager /<path_to_file_with_tracks_urls>/playlist.txt
	

This method accepts several files as parameter.

	
    app/console MusicDownloadManager:MusicDownloadManager /<path_to_file_with_tracks_urls>/playlist1.txt /<path_to_file_with_tracks_urls>/playlist2.txt
	

Track file format
=================

	download_directory:/home/jquero/Music/Flamenco
	
	track_name:Los asdlánticos - Que trata de andalucía
	
	http://www.goear.com/listen/1e642a2/que-trata-de-andalucia-los-asdlanticos
	
	track_name:Tucara - No puedo quitar mis ojos de ti
	
	http://www.goear.com/listen/5b7518f/no-puedo-quitar-mis-ojos-de-ti-tucara-
	
	
	download_directory:/home/jquero/Music/House
	
	track_name:Philip Bader, Nicone &amp; Sascha Braemer - Dantze Girl (Original Mix)
	
	http://myfreemp3.eu/l/5l3sa9ubmet/
	
	track_name:Eric Prydz - Pjanoo (Original Club Mix)
	
	http://myfreemp3.eu/l/fsswqklulwu/
	
	
	download_directory:/home/jquero/Music/Flamenco
	
	track_name:Mártires del Compás - Colores
	
	http://youtu.be/HSwu0WH7Vfc
	

Options in track list
=====================

download_directory

You can override the download directory writting in your tracks list the keyword 'download_directory'. 
The default path of protected variable downloadDirectory will be overrided by de new path. 
You can override the download directory as many times as you want in the track list. 
All tracks listed after tag download_directory will be downloaded within this directory. 

Note: If you use this bundle via web you must be enought permission into directories where you download the files.


track_name

You can set the track name as alternative when the download manager can't get the track name anyway. You can see an example of this in TRACKS FILE FORMAT section.

License
=======

    Copyright (c) 2004-2013 Fabien Potencier

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is furnished
    to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
