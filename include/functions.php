<?php
	session_start();
	# changelog
	# 2015-04-05 17:33:48
	# 2015-04-06 21:00:50
	# 2015-04-12 14:36:07 - visum login
	# 2015-04-13 18:44:55
	# 2015-07-15 13:10:46 - adding shutdown function
	# 2015-08-24 11:32:04 - imagemagick check
	# 2015-08-24 11:32:09
	# 2015-08-25 11:16:45
	# 2015-10-23 12:53:54 - adding batteries AA, AAA, C, D, 9V and 3R12
	# 2015-10-24 18:51:07 - adding wished and wished - skipped to statuses
	# 2016-03-27 18:41:17 - adding images to locations
	# 2016-09-22 22:09:48 - base 2 to base 3
	# 2017-02-01 18:32:42 - dotpointer domain edit

	define('SITE_SHORTNAME', 'inventory');
	define('DATABASE_NAME', 'inventory');
	require_once('config.php');
	require_once('base3.php');

	# CREATE DATABASE inventory;	
	# CREATE TABLE items (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, id_categories INT NOT NULL, id_files INT NOT NULL DEFAULT 0, title TINYTEXT NOT NULL, description TEXT NOT NULL, batteries_aa INT NOT NULL DEFAULT 0, batteries_aaa INT NOT NULL DEFAULT 0, batteries_c INT NOT NULL DEFAULT 0, batteries_d INT NOT NULL DEFAULT 0, batteries_e INT NOT NULL DEFAULT 0, batteries_3r12 INT NOT NULL DEFAULT 0, materials tinytext not null, watt_max float, price FLOAT NOT NULL, source TINYTEXT NOT NULL, location TINYTEXT NOT NULL, status INT NOT NULL DEFAULT 1, inuse INT NOT NULL, acquired DATETIME NOT NULL, disposed DATETIME NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL);
	# CREATE TABLE categories (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, title TINYTEXT NOT NULL);
	
	# CREATE TABLE inventory.users(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, id_visum INT NOT NULL UNIQUE, nickname VARCHAR(16) NOT NULL, gender enum('0','1','2') NOT NULL, birth DATETIME NOT NULL, updated DATETIME NOT NULL, created DATETIME NOT NULL);
***REMOVED***
***REMOVED***
***REMOVED***

	# CREATE TABLE relations_items_locations(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, id_items INT NOT NULL, id_locations INT NOT NULL );
	# CREATE TABLE locations(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, id_files INT NOT NULL DEFAULT 0, title TINYTEXT NOT NULL);

	# CREATE TABLE files (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, mime TINYTEXT NOT NULL, created DATETIME NOT NULL);

	/*
		1 = own
		2 = sold
		3 = given away
		4 = dumped
	*/

	$link = db_connect();
	# mysql_set_charset('utf8', $link);
	
	if (!function_exists('shutdown_function')) {
		# a function to run when the script shutdown
		function shutdown_function($link) {
			if ($link) {
				db_close($link);
			}
		}
	}

	# register a shutdown function
	register_shutdown_function('shutdown_function', $link);	

	define('STATUS_OWN', 1);
	define('STATUS_OWNSELL', 2);
	define('STATUS_SOLD', 3);
	define('STATUS_GIVENAWAY', 4);
	define('STATUS_DUMPED', 5);
	define('STATUS_WISHED', 6);
	define('STATUS_WISHED_SKIPPED', 7);
	
	$statuses = array(
		STATUS_OWN => array('text' => 'Äger', 'name' => 'own'),
		STATUS_OWNSELL => array('text' => 'Säljes', 'name' => 'ownsell'),		
		STATUS_SOLD => array('text' => 'Såld', 'name' => 'sold'),
		STATUS_GIVENAWAY =>  array('text' => 'Bortskänkt', 'name' => 'givenaway'),
		STATUS_DUMPED =>  array('text' => 'Kastad', 'name' => 'dumped'),
		STATUS_WISHED => array('text' => 'Önskad', 'name' => 'wished'),	
		STATUS_WISHED_SKIPPED => array('text' => 'Önskad - Dumpad', 'name' => 'wishedskipped'),	
	);

	define('USAGE_FREQUENT', 4);
	define('USAGE_SOMETIMES', 3);
	define('USAGE_SELDOM', 2);
	define('USAGE_NEVER', 1);
	define('USAGE_UNKNOWN', 0);
	$usage = array(

		USAGE_FREQUENT => 'Ofta',
		USAGE_SOMETIMES => 'Ibland',
		USAGE_SELDOM => 'Sällan',
		USAGE_NEVER => 'Inte',
		USAGE_UNKNOWN => 'Okänt'		
	);
	


	define('THUMBNAIL_DIR', DATA_DIR.'inventory/thumbnails/');
	define('FILE_DIR', DATA_DIR.'inventory/originals/');
	
	define('MAGICK_PATH','/usr/bin/');


define('ID_VISUM', false); # our site-id in visum
	
	function is_logged_in() {
		if (!isset($_SESSION[SITE_SHORTNAME])) {
			return false;
		}

		if (!isset($_SESSION[SITE_SHORTNAME]['user'])) {
			return false;
		}
		return true;
	}

	function get_logged_in_user($field=false) {
		if (!is_logged_in()) {
			return false;
		}
		if (!$field) {
			return $_SESSION[SITE_SHORTNAME]['user'];
		}
		if (!isset($_SESSION[SITE_SHORTNAME]['user'][$field])) {
			return false;
		}
		return $_SESSION[SITE_SHORTNAME]['user'][$field];
	}
	
	function print_login() {
?>
<p class="login">
	<a href="http://www.dotpointer.tk/?section=visum&id_sites=<?php echo ID_VISUM?>">Inloggning krävs, klicka här för att logga in.<a/>
</p>
<?php
		return true;	
	}
	
	if (!file_exists(MAGICK_PATH.'convert')) {
		die('ImageMagick is not installed.');
	}

?>
