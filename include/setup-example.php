<?php
	# inventory setup fill in this file and rename it to setup.php

	# the site-id in visum authentication, leave it as it is
	define('ID_VISUM', false);

	# database setup, fill this in
	define('DATABASE_HOST', 'localhost');
	define('DATABASE_USERNAME', 'www');
	define('DATABASE_PASSWORD', 'www');
	define('DATABASE_NAME', 'inventory');

	# not implemented
	# define('DATABASE_TABLES_PREFIX', '' /* 'mediaarchive_'*/);
	
	# where to put generated thumbnails, a directory writable for the httpd user
	define('THUMBNAIL_DIR', '/opt/inventory/thumbnails/');

	# where to store original image, must be writable for the httpd user
	define('FILE_DIR', '/opt/inventory/originals/');

	# where imagemagick convert resides
	# install with: sudo apt-get install imagemagick
	define('MAGICK_PATH','/usr/bin/');
	
	# the jpeg quality for thumbnails, not implemented
	# define('MAGICK_THUMBNAIL_QUALITY', 30);

	# allow login to be available or not, not implemented
	# define('LOGIN_MODE', false);

	# compose a random string here of more than 16 characters to use when encrypting local passwords
	$password_salt = '';

	# temporarily uncomment this and fill it in to create a local user to use for login
	# $editusers = array(
	#	array(
	#		'username' => '',
	#		'password' => ''
	#	)
	#);

?>
