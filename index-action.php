<?php

# changelog
# 2015-04-06 21:04:23
# 2015-04-12 14:26:15 - login from mediaarchive
# 2015-04-13 18:47:02 - go to empty page after login
# 2015-05-06 22:14:19 - adding location logic
# 2015-05-07 18:27:06 - dropping items.location field
# 2015-05-09 18:36:29 - adding location contents field
# 2015-08-24 11:25:25
# 2015-08-24 11:25:29 - Fails creating thumbnail
# 2015-10-23 12:55:51 - adding batteries AA, AAA, C, D, E and 3R12
# 2016-03-09 00:41:48 - updating css
# 2016-03-27 17:47:53 - adding files table
# 2016-09-22 22:09:08 - mysql
# 2017-01-26 21:31:29 - adding materials column
# 2017-05-13 15:32:31 - adding weight
# 2017-05-13 17:49:51 - adding packlist
# 2017-05-13 23:06:26 - adding packlist items
# 2017-05-21 20:42:04 - adding packlist inuse

if (!isset($action)) die();

# check what action we have
switch ($action) {
	case 'images_to_files':
		$r = db_query($link, 'SELECT * FROM items ORDER BY id');

		foreach ($r as $k => $v) {
			# is this item already completed, the go next
			if ((int)$v['id_files'] !== -1) continue;
			$original_path = FILE_DIR.$v['id'].'.jpg';

			# no file found?
			if (!file_exists($original_path)) {
				# then update this item with no file
				$sql = 'UPDATE items SET id_files=0 WHERE id="'.dbres($link, $v['id']).'"';
				echo $sql."\n";
				$r2 = db_query($link, $sql);
				$iu['id_files'] = 0;
			} else {
				# add this item to database
				$iu = array(
					'id' => $v['id'],
					'created' => date('Y-m-d H:i:s', filemtime($original_path)),
					'mime' => mime_content_type($original_path)
				);
				$iu = dbpia($link, 	$iu);
				$sql = 'INSERT INTO files ('.implode(',', array_keys($iu)).') VALUES('.implode(',', $iu).')';
				echo $sql."\n";
				$r2 = db_query($link, $sql);
				# update the db
				$sql = 'UPDATE items SET id_files='.$v['id'].' WHERE id="'.dbres($link, $v['id']).'"';
				echo $sql."\n";
				$r2 = db_query($link, $sql);
			}
		}
		die();
	#case 'doit':
	#	die();
	#	$r = db_query($link, 'SELECT * FROM items');
	#	mysql_set_charset('utf8', $link);

	#	foreach ($r as $k => $v) {
	#		$r = db_query($link, 'UPDATE items SET title="'.dbres($link, $v['title']).'", description="'.dbres($link, $v['description']).'", source="'.dbres($link, $v['source']).'" WHERE id="'.$v['id'].'"');
	#	}
	#	break;

	case 'insert_update_category': # to insert or update a category
		if (!is_logged_in()) break;

		# make sure required fields are filled in
		if (strlen($title) < 3) die('Fields are not filled in.');

		# make an array to insert or update
		$iu = array(
			'title' => $title,
		);

		# is this an existing item?
		if ($id_categories) {
			$iu = dbpua($link, $iu);
			$sql = 'UPDATE categories SET '.implode($iu, ',').' WHERE id="'.dbres($link, $id_categories).'"';
			db_query($link, $sql);
		# or is it a new item?
		} else {
			$iu = dbpia($link, $iu);
			$sql = 'INSERT INTO categories ('.implode(array_keys($iu), ',').') VALUES('.implode($iu, ',').')';
			db_query($link, $sql);
			$id_categories = db_insert_id($link);
		}

		break;
	case 'insert_update_location': # to insert or update a location
		if (!is_logged_in()) break;
		# if (!$id_locations) die('id_locations is missing.');

		# make sure required fields are filled in
		if (strlen($title) < 3) die('Fields are not filled in.');

		# make an array to insert or update
		$iu = array(
			'title' => $title,
			'contents' => $contents
		);

		# is this an existing item?
		if ($id_locations) {
			$iu = dbpua($link, $iu);
			$sql = 'UPDATE locations SET '.implode($iu, ',').' WHERE id="'.dbres($link, $id_locations).'"';
			db_query($link, $sql);
		# or is it a new item?
		} else {
			$iu = dbpia($link, $iu);
			$sql = 'INSERT INTO locations ('.implode(array_keys($iu), ',').') VALUES('.implode($iu, ',').')';
			db_query($link, $sql);
			$id_locations = db_insert_id($link);
		}

		# upload file management

   		# Undefined | Multiple Files | $_FILES Corruption Attack
		# If this request falls under any of them, treat it invalid.
		if (
			!isset($_FILES['file']['error']) ||
			is_array($_FILES['file']['error'])
		) {
			die('Invalid parameters.');
		}

		# check error value
		switch ($_FILES['file']['error']) {
			case UPLOAD_ERR_OK:

				# is there an image supplied?
				$id_files = (int)$id_files;

				# filesize check
				if ($_FILES['file']['size'] > 100000000) {
					die('Exceeded filesize limit.');
				}

				# mime check - do not trust $_FILES mime value
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				if (false === $ext = array_search(
					$finfo->file($_FILES['file']['tmp_name']),
					array(
						'jpg' => 'image/jpeg'
						#,
						#'png' => 'image/png',
						#'gif' => 'image/gif',
					),
					true
				)) {
					die('Invalid file format.');
				}

				# missing file dir?
				if (
					!is_dir(FILE_DIR)
					|| trim(FILE_DIR) === '/'
					|| substr(FILE_DIR, -1,1) !== '/'
				) die('Fatal, file directory does not exist: '.FILE_DIR);

				# is there no files id supplied?
				if (!$id_files) {
					# then insert a new file id
					$iu = array(
						'created' => date('Y-m-d H:i:s'),
						'mime' => mime_content_type($_FILES['file']['tmp_name'])
					);
					$iu = dbpia($link, $iu);
					$sql = 'INSERT INTO files ('.implode(',', array_keys($iu)).') VALUES('.implode(',', $iu).')';
					$r_insert_files = db_query($link, $sql);
					$id_files = db_insert_id($link);
				}

				# update the file location
				$sql = 'UPDATE locations SET id_files="'.dbres($link, $id_files).'" WHERE id="'.dbres($link, $id_locations).'"';
				$r_update_locations = db_query($link, $sql);

				# set the target file path
				$targetfile = FILE_DIR.$id_files.'.jpg';

				# make sure it does not exist
				if (file_exists($targetfile)) {
					# die('I want to delete: '.$targetfile);
					if (!unlink($targetfile)) die('Failed deleting '.$targetfile);
				}

				# You should name it uniquely.
				# DO NOT USE $_FILES['file']['name'] WITHOUT ANY VALIDATION !!
				# On this example, obtain safe unique name from its binary data.
				if (!move_uploaded_file(
					$_FILES['file']['tmp_name'],
					$targetfile
				)) {
					die('Failed to move uploaded file.');
				}

				# missing thumbnail dir?
				if (
					!is_dir(THUMBNAIL_DIR)
					|| trim(THUMBNAIL_DIR) === '/'
					|| substr(THUMBNAIL_DIR, -1,1) !== '/'
				) die('Fatal, thumbnail directory does not exist: '.THUMBNAIL_DIR);

				# make a thumbnail of it, if it is not already there

				# set the target file path
				$thumbfile = THUMBNAIL_DIR.$id_files.'.jpg';

				# make sure it does not exist
				if (file_exists($thumbfile)) {
					# die('I want to delete: '.$thumbfile);
					if (!unlink($thumbfile)) die('Failed deleting '.$thumbfile);
				}

				$s = trim(exec('ps ax|grep convert|grep -v grep'));
				if (strlen($s)) return false;

				# 40 funkar ok, himlar blir visserligen pixlade men inte så mkt
				# 30 är gränsfall, det är randigt om man kollar noga
				# 25 är himlar synbart randiga
				# 10-15 pajar ansikten på 160x120
				# 5 är fruktansvärt

				$return2 = exec(MAGICK_PATH.'convert '.escapeshellarg($targetfile).' -quality 75 -auto-orient -strip -sample 320x240 '.escapeshellarg($thumbfile), $output, $return);

				if (!file_exists($thumbfile)) die('Failed creating thumbnail: '.$thumbfile.'. Command output was: '.implode("\n", $output).$return2.$return);
				# upload complete

				break;
			case UPLOAD_ERR_NO_FILE:
				# no file uploaded, that is ok
				break;
			case UPLOAD_ERR_INI_SIZE:
				die('Exceeded filesize limit in ini setting.');
			case UPLOAD_ERR_FORM_SIZE:
				die('Exceeded filesize limit in form.');
			default:
				die('Unknown errors.');
		}

		break;

	case 'delete_location':

		if (!is_logged_in()) break;

		if (!is_numeric($id_locations)) die('Missing id_locations parameter.');

		# check connected locations
		$sql = 'SELECT * FROM relations_items_locations WHERE id_locations="'.dbres($link, $id_locations).'"';
		$r = db_query($link, $sql);

		if ($r) die('Location with id #'.(int)$id_locations.' has relations, remove them first.');

		$sql = 'DELETE FROM locations WHERE id="'.dbres($link, $id_locations).'"';
		# die($sql);
		# unset($sql);
		db_query($link, $sql);
		break;
	case 'insert_update_item': # to insert or update an item
		if (!is_logged_in()) break;

		# is new category field filled in?
		if (strlen($category)) {
			# check if it already is posted
			$sql = 'SELECT * FROM categories WHERE LOWER(title)=LOWER("'.dbres($link, $category).'")';
			$r = db_query($link, $sql);
			# did we find any matching categories?
			if (count($r)) {
				# then take the id from that
				$id_categories = $r[0]['id'];
				# remove the category
				$category = false;
			} else {
				$sql = 'INSERT INTO categories (title) VALUES("'.dbres($link, $category).'")';
				db_query($link, $sql);
				$id_categories = db_insert_id($link);
				$category = false;
			}
		}

		# make sure required fields are filled in
		if (strlen($title) < 3) die('Fields are not filled in.');

		# make an array to insert or update
		$iu = array(
			'acquired' => $acquired,
			'batteries_aaa' => $batteries_aaa,
			'batteries_aa' => $batteries_aa,
			'batteries_c' => $batteries_c,
			'batteries_d' => $batteries_d,
			'batteries_e' => $batteries_e,
			'batteries_3r12' => $batteries_3r12,
			'materials' => $materials,
			'description' => $description,
			'disposed' => $disposed,
			'id_categories' => $id_categories,
			'id_files' => $id_files,
			'inuse' => $inuse,
			# 'location' => $location,
			'price' => $price,
			'source' => $source,
			'status' => $status,
			'title' => $title,
			'updated' => date('Y-m-d H:i:s'),
			'watt_max' => $watt_max,
			'watt' => $watt,
			'weight' => $weight
		);

		# is this an existing item?
		if ($id_items) {
			$iu = dbpua($link, $iu);
			$sql = 'UPDATE items SET '.implode($iu, ',').' WHERE id="'.dbres($link, $id_items).'"';
			db_query($link, $sql);

		# or is it a new item?
		} else {
			$iu['created'] = date('Y-m-d H:i:s');
			$iu = dbpia($link, $iu);
			$sql = 'INSERT INTO items ('.implode(array_keys($iu), ',').') VALUES('.implode($iu, ',').')';
			db_query($link, $sql);
			$id_items = db_insert_id($link);
		}

		# --- check locations

		$locations = explode('+', $location);
		$valid_id_relations_items_locations = array();

		# walk locations and trim spaces
		foreach ($locations as $k => $v) {
			$locations[$k] = trim($v);
			$v = $locations[$k];
			if (!strlen($v)) continue;
			# get all matching locations based on title
			$sql = 'SELECT id,title FROM locations WHERE title="'.dbres($link, $v).'"';
			$loc = db_query($link, $sql);
			# was a location found?
			if (count($loc)) {
				# take the first one
				$loc = $loc[0];
			# was no location found?
			} else {
				# then insert the location
				$sql = 'INSERT INTO locations (title,contents) VALUES("' . dbres($link, $v) .'", "")';
				$loc = db_query($link, $sql);
				$loc = array(
					'id' => db_insert_id($link),
					'title' => $v
				);
			}


			# get all matching relations based on id of location and item
			$sql = 'SELECT * FROM relations_items_locations WHERE id_locations="'.dbres($link, $loc['id']).'" AND id_items="'.dbres($link, $id_items).'"';
			$r = db_query($link, $sql);
			# no relation match?
			if (!count($r)) {
				# then insert the relation
				$sql = 'INSERT INTO relations_items_locations (id_items, id_locations) VALUES('.(int)$id_items.','.(int)$loc['id'].')';
				$r = db_query($link, $sql);
				$valid_id_relations_items_locations[] = db_insert_id($link);
			} else {
				$valid_id_relations_items_locations[] = $r[0]['id'];
			}
		}

		# delete all invalid ones
		if (count($valid_id_relations_items_locations)) {
			$sql = 'DELETE FROM relations_items_locations WHERE id_items="'.(int)$id_items.'" AND id NOT IN ('.implode(',', $valid_id_relations_items_locations).')';
			db_query($link, $sql);
		}

		# --- end of location

	   # Undefined | Multiple Files | $_FILES Corruption Attack
		# If this request falls under any of them, treat it invalid.
		if (
			!isset($_FILES['file']['error']) ||
			is_array($_FILES['file']['error'])
		) {
			die('Invalid parameters.');
		}

		# check error value
		switch ($_FILES['file']['error']) {
			case UPLOAD_ERR_OK:

				# is there an image supplied?
				$id_files = (int)$id_files;

				# filesize check
				if ($_FILES['file']['size'] > 100000000) {
					die('Exceeded filesize limit.');
				}

				# mime check - do not trust $_FILES mime value
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				if (false === $ext = array_search(
					$finfo->file($_FILES['file']['tmp_name']),
					array(
						'jpg' => 'image/jpeg'
						#,
						#'png' => 'image/png',
						#'gif' => 'image/gif',
					),
					true
				)) {
					die('Invalid file format.');
				}

				# missing file dir?
				if (
					!is_dir(FILE_DIR)
					|| trim(FILE_DIR) === '/'
					|| substr(FILE_DIR, -1,1) !== '/'
				) die('Fatal, file directory does not exist: '.FILE_DIR);


				# is there no files id supplied?
				if (!$id_files) {
					# then insert a new file id
					$iu = array(
						'created' => date('Y-m-d H:i:s'),
						'mime' => mime_content_type($_FILES['file']['tmp_name'])
					);
					$iu = dbpia($link, $iu);
					$sql = 'INSERT INTO files ('.implode(',', array_keys($iu)).') VALUES('.implode(',', $iu).')';
					$r_insert_files = db_query($link, $sql);
					$id_files = db_insert_id($link);
				}

				# update the file location
				$sql = 'UPDATE items SET id_files="'.dbres($link, $id_files).'" WHERE id="'.dbres($link, $id_items).'"';
				$r_update_items = db_query($link, $sql);

				# set the target file path
				# $targetfile = FILE_DIR.$id_items.'.jpg';
				$targetfile = FILE_DIR.$id_files.'.jpg';

				# make sure it does not exist
				if (file_exists($targetfile)) {
					# die('I want to delete: '.$targetfile);
					if (!unlink($targetfile)) die('Failed deleting '.$targetfile);
				}

				# You should name it uniquely.
				# DO NOT USE $_FILES['file']['name'] WITHOUT ANY VALIDATION !!
				# On this example, obtain safe unique name from its binary data.
				if (!move_uploaded_file(
					$_FILES['file']['tmp_name'],
					$targetfile
				)) {
					die('Failed to move uploaded file.');
				}

				# missing thumbnail dir?
				if (
					!is_dir(THUMBNAIL_DIR)
					|| trim(THUMBNAIL_DIR) === '/'
					|| substr(THUMBNAIL_DIR, -1,1) !== '/'
				) die('Fatal, thumbnail directory does not exist: '.THUMBNAIL_DIR);

				# make a thumbnail of it, if it is not already there

				# set the target file path
				$thumbfile = THUMBNAIL_DIR.$id_files.'.jpg';

				# make sure it does not exist
				if (file_exists($thumbfile)) {
					# die('I want to delete: '.$thumbfile);
					if (!unlink($thumbfile)) die('Failed deleting '.$thumbfile);
				}

				$s = trim(exec('ps ax|grep convert|grep -v grep'));
				if (strlen($s)) return false;

				# 40 funkar ok, himlar blir visserligen pixlade men inte så mkt
				# 30 är gränsfall, det är randigt om man kollar noga
				# 25 är himlar synbart randiga
				# 10-15 pajar ansikten på 160x120
				# 5 är fruktansvärt

				$return2 = exec(MAGICK_PATH.'convert '.escapeshellarg($targetfile).' -quality 75 -auto-orient -strip -sample 320x240 '.escapeshellarg($thumbfile), $output, $return);

				if (!file_exists($thumbfile)) die('Failed creating thumbnail: '.$thumbfile.'. Command output was: '.implode("\n", $output).$return2.$return);
				# upload complete

				break;
			case UPLOAD_ERR_NO_FILE:
				# no file uploaded, that is ok
				break;
			case UPLOAD_ERR_INI_SIZE:
				die('Exceeded filesize limit in ini setting.');
			case UPLOAD_ERR_FORM_SIZE:
				die('Exceeded filesize limit in form.');
			default:
				die('Unknown errors.');
		}

		switch ($view) {
			default: # edit new item

				# clear field parameters so we can insert a new item
				# $acquired = false;
				$description = false;
				# $disposed = false;
				# $id_categories = false; # leave this, useful if it is the same
				# $status = false;
				$title = false;
				# $source = false;
				$price = false;
				$id_items = false;
				# $view = 'edit_item';
				break;
			case 'index':
				$view = 'index';
				break;
		}

		break;

	case 'insert_update_packlist': # to insert or update a packlist
		if (!is_logged_in()) break;
		# if (!$id_packlists) die('id_packlists is missing.');

		# make sure required fields are filled in
		if (strlen($title) < 3) die('Fields are not filled in.');

		# make an array to insert or update
		$iu = array(
			'title' => $title
		);

		# is this an existing item?
		if ($id_packlists) {
			$iu['updated'] = date('Y-m-d H:i:s');
			$iu = dbpua($link, $iu);
			$sql = 'UPDATE packlists SET '.implode($iu, ',').' WHERE id="'.dbres($link, $id_packlists).'"';
			db_query($link, $sql);
		# or is it a new item?
		} else {
			$iu['created'] = date('Y-m-d H:i:s');
			$iu['updated'] = date('Y-m-d H:i:s');
			$iu = dbpia($link, $iu);
			$sql = 'INSERT INTO packlists ('.implode(array_keys($iu), ',').') VALUES('.implode($iu, ',').')';
			db_query($link, $sql);
			$id_packlists = db_insert_id($link);
		}

		break;
	case 'insert_update_relations_packlists_items':

		if (!is_logged_in()) break;

		if (!is_numeric($id_items)) die('Missing id_items parameter.');
		if (!is_numeric($id_packlists)) die('Missing id_packlists parameter.');

		# check that relation is not there before
		$sql = 'SELECT * FROM relations_packlists_items WHERE id_packlists="'.dbres($link, $id_packlists).'" AND id_items="'.dbres($link, $id_items).'"';
		$r = db_query($link, $sql);
		if (count($r)) {
			echo json_encode(array(
				'status' => true
			));
			die();
		}

		# delete packlist relations
		$sql = 'INSERT INTO  relations_packlists_items (id_packlists, id_items) VALUES("'.dbres($link, $id_packlists).'","'.dbres($link, $id_items).'")';
		$r = db_query($link, $sql);

		echo json_encode(array(
			'status' => true
		));
		die();

	case 'delete_packlist':

		if (!is_logged_in()) break;

		if (!is_numeric($id_packlists)) die('Missing id_packlists parameter.');

		# delete packlist relations
		$sql = 'DELETE FROM relations_packlists_items WHERE id_packlists="'.dbres($link, $id_packlists).'"';
		$r = db_query($link, $sql);

		# delete packlist
		$sql = 'DELETE FROM packlists WHERE id="'.dbres($link, $id_packlists).'"';
		db_query($link, $sql);
		break;

	case 'delete_relation_packlists_items':

		if (!is_logged_in()) break;

		if (!is_numeric($id_relations_packlists_items)) die('Missing id_relations_packlists_items parameter.');

		# delete packlist relations
		$sql = 'DELETE FROM relations_packlists_items WHERE id="'.dbres($link, $id_relations_packlists_items).'"';
		$r = db_query($link, $sql);

		break;

	case 'insert_update_packlist_item':

		if (!is_logged_in()) break;

		if (!is_numeric($id_packlists)) die('Missing id_packlists parameter.');
		if (!strlen($title)) die('Missing title parameter.');
		if (!strlen($weight)) die('Missing weight parameter.');

		if ($id_packlist_items) {
			# update packed status
			$sql = 'UPDATE packlist_items SET title="'.dbres($link, $title).'", weight="'.dbres($link, $weight).'" WHERE id="'.dbres($link, $id_packlist_items).'"';
			$r = db_query($link, $sql);
		} else {
			# delete packlist relations
			$sql = 'INSERT INTO  packlist_items (id_packlists, title, weight) VALUES("'.dbres($link, $id_packlists).'","'.dbres($link, $title).'","'.dbres($link, $weight).'")';
			$r = db_query($link, $sql);
		}
		break;

	case 'delete_packlist_item':

		if (!is_logged_in()) break;

		if (!is_numeric($id_packlist_items)) die('Missing id_packlist_items.');

		# delete packlist relations
		$sql = 'DELETE FROM packlist_items WHERE id="'.dbres($link, $id_packlist_items).'"';
		$r = db_query($link, $sql);

		break;

	case 'update_relations_packlists_items_inuse':

		if (!is_logged_in()) break;

		if (!is_numeric($id_relations_packlists_items)) die('Missing id_packlist_items parameter.');
		if (!is_numeric($inuse)) die('Missing inuse parameter.');

		# update inuse status
		$sql = 'UPDATE relations_packlists_items SET inuse="'.dbres($link, $inuse).'" WHERE id="'.dbres($link, $id_relations_packlists_items).'"';
		$r = db_query($link, $sql);

		echo json_encode(array(
			'status' => true
		));
		die();

	case 'update_packlist_items_inuse':

		if (!is_logged_in()) break;

		if (!is_numeric($id_packlist_items)) die('Missing id_packlist_items parameter.');
		if (!is_numeric($inuse)) die('Missing inuse parameter.');

		# update inuse status
		$sql = 'UPDATE packlist_items SET inuse="'.dbres($link, $inuse).'" WHERE id="'.dbres($link, $id_packlist_items).'"';
		$r = db_query($link, $sql);

		echo json_encode(array(
			'status' => true
		));
		die();

	case 'update_relations_packlists_items_packed':

		if (!is_logged_in()) break;

		if (!is_numeric($id_relations_packlists_items)) die('Missing id_packlist_items parameter.');
		if (!is_numeric($packed)) die('Missing packed parameter.');

		# update packed status
		$sql = 'UPDATE relations_packlists_items SET packed="'.dbres($link, $packed).'" WHERE id="'.dbres($link, $id_relations_packlists_items).'"';
		$r = db_query($link, $sql);

		echo json_encode(array(
			'status' => true
		));
		die();

	case 'update_packlist_items_packed':

		if (!is_logged_in()) break;

		if (!is_numeric($id_packlist_items)) die('Missing id_packlist_items parameter.');
		if (!is_numeric($packed)) die('Missing packed parameter.');

		# update packed status
		$sql = 'UPDATE packlist_items SET packed="'.dbres($link, $packed).'" WHERE id="'.dbres($link, $id_packlist_items).'"';
		$r = db_query($link, $sql);

		echo json_encode(array(
			'status' => true
		));
		die();

	case 'login': # login taken from mediaarchive

		if (is_logged_in()) break;
		if (!$ticket) die('Missing ticket.');
		$method='http';
		if ($method === 'http') {
			# this is what is needed to get Visum login over HTTP
			require_once('class-visum.php');
			 $visum = new Visum();
			# var_dump($visum->getUserByTicket($ticket));
		} else if ($method === 'direct') {
			# this is what is needed to get Visum login directly
			#define('DATABASE_NAME', 'visum'); # just because base wants this
			#require_once('base.php'); # needed because of connection functions and such
			# require_once('../include/functions.php'); # visum functionality used for direct communication
			require_once('class-visum.php'); # visum client class
			#file_get_contents('class-visum.php');
			#$link = get_database_connection();
			# mysql_set_charset('utf8', $link);
			$visum = new Visum(VISUM_METHOD_DIRECT, $link);
		}

		try {
			$visum_user = $visum->getUserByTicket($ticket);
		} catch(VisumException $e) {
			$t = $e->getResponseArray();
			die('Error: '.$t['error']);
		} catch(Exception $e) {
			die($e->getMessage());
		}

		if (!isset($visum_user['id_users'])) die('Missing user id in visum response.');
		$id_visum = $visum_user['id_users'];

		# update local credentials with what we got from visum
		$iu = array();
		# scan visum response for credentials to update
		foreach (array('gender','nickname','birth') as $k => $v) {
			if (!isset($visum_user[$v])) continue;
			# put it into the update array
			$iu[$v] = $visum_user[$v];
		}

		# was there anything to update supplied?
		if (count($iu) > 0) {
			$iu['updated'] = date('Y-m-d H:i:s');
			$iu = dbpua($link, $iu);
			$sql = 'UPDATE users SET '.implode(',',$iu).' WHERE id_visum="'.dbres($link, $id_visum).'"';
			$r = db_query($link, $sql);
		}

		# try to find the user, did it exist in local db?
		$sql = 'SELECT * FROM users WHERE id_visum="'.dbres($link, $id_visum).'"';
		$r = db_query($link, $sql);

		# mysql_result_as_array($result, $users);
		if (count($r) < 1) die(_('No user found in local db.'));
		$user = reset($r);

		# this means user is logged in
		$_SESSION[SITE_SHORTNAME]['user'] = $user;

		# now we have a visum user id to match against our own database and then create a login, that's all that is needed

		header('Location: ./');

		break;

	case 'logout':
		if (!is_logged_in()) { report_sysmessage(SYSMESSAGE_NOTICE, 'Redan utloggad.'); $view=''; break; }
		$_SESSION[SITE_SHORTNAME]['user'] = false;
		unset($_SESSION[SITE_SHORTNAME]['user']);
		break;

	case 'fix':
		die();
		if (!is_logged_in()) { report_sysmessage(SYSMESSAGE_NOTICE, 'Redan utloggad.'); $view=''; break; }

		$sql = 'SELECT * FROM items ORDER BY location';
		$items = db_query($link, $sql);

		# --- check locations

		foreach ($items as $itemk => $itemv	) {
			$locations = explode('+', $items[$itemk]['location']);
			$id_items = $items[$itemk]['id'];
			$valid_id_relations_items_locations = array();

			# walk locations and trim spaces
			foreach ($locations as $k => $v) {

				$locations[$k] = trim($v);
				$v = $locations[$k];

				if (!strlen($v)) continue;

				# get all matching locations based on title
				$sql = 'SELECT id,title FROM locations WHERE title="'.dbres($link, $v).'"';
				$loc = db_query($link, $sql);

				# was a location found?
				if (count($loc)) {
					# take the first one
					$loc = $loc[0];
				# was no location found?
				} else {
					# then insert the location
					$sql = 'INSERT INTO locations (title) VALUES("' . dbres($link, $v) .'")';
					$loc = db_query($link, $sql);
					$loc = array(
						'id' => db_insert_id($link),
						'title' => $v
					);
				}

				# get all matching relations based on id of location and item
				$sql = 'SELECT * FROM relations_items_locations WHERE id_locations="'.dbres($link, $loc['id']).'" AND id_items="'.dbres($link, $id_items).'"';
				$r = db_query($link, $sql);
				# no relation match?
				if (!count($r)) {
					# then insert the relation
					$sql = 'INSERT INTO relations_items_locations (id_items, id_locations) VALUES('.(int)$id_items.','.(int)$loc['id'].')';
					$r = db_query($link, $sql);
					$valid_id_relations_items_locations[] = db_insert_id($link);
				} else {
					$valid_id_relations_items_locations[] = $r[0]['id'];
				}
			}

			# delete all invalid ones
			if (count($valid_id_relations_items_locations)) {
				$sql = 'DELETE FROM relations_items_locations WHERE id_items="'.(int)$id_items.'" AND id NOT IN ('.implode(',', $valid_id_relations_items_locations).')';
				db_query($link, $sql);
			}
		}

		break;
}
?>
