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
# 2018-02-19 20:08:00 - adding packlist from and to and copy packlist
# 2018-02-22 22:21:00 - adding packlist item relation comment
# 2018-03-14 23:02:00 - adding criteria handling
# 2018-03-15 00:47:00 - adding criteria handling continued
# 2018-04-08 12:08:55 - adding location history
# 2018-04-09 12:10:00 - cleanup
# 2018-04-11 13:39:00 - bugfix, correction for location query parameter that was used in actions
# 2018-04-13 23:49:00 - adding packlist notes
# 2018-06-24 17:58:00 - adding local login
# 2018-06-25 18:58:00 - adding local user management and multi user support
# 2018-06-26 16:09:00 - adding error handling
# 2018-06-27 18:11:00 - adding password salt check
# 2018-07-02 19:31:00 - bugfix, image upload was not checked
# 2018-07-16 16:52:36
# 2018-07-19 18:00:02 - indentation change, tab to 2 spaces
# 2019-02-27 18:35:00 - bugfixes, packlist criteria relation additions missed user id, packlist item relations were undeleteable

if (!isset($action)) die();

# is the editusers setup array set
if (isset($editusers)) {

  if (strlen($password_salt) > 15) {
    # walk this array
    foreach ($editusers as $user) {
      if (!isset($user['username']) || !isset($user['password'])) {
        continue;
      }

      if (!validate_user($user['username'])) {
        $errors[] = t('Username in editusers array is too short, too long or contain invalid characters.');
        break;
      }

      if (!validate_pass($user['password'])) {
        $errors[] = t('Password in editusers array is too short or does not contain letters or digits.');
        break;
      }

      $sql = '
        SELECT
          *
        FROM
          users
        WHERE
          username="'.dbres($link, $user['username']).'" OR
          nickname="'.dbres($link, $user['username']).'"
        ';
      $result = db_query($link, $sql);

      $iu = array(
        'username' => $user['username'],
        'updated' => date('Y-m-d H:i:s')
      );

      if (!count($result)) {
        $iu['created'] = date('Y-m-d H:i:s');
        $iu = dbpia($link, 	$iu);
        # set password separately
        $iu['password'] = 'ENCRYPT("'.dbres($link, $user['password']).'", "'.dbres($link, $password_salt).'")';
        $sql = '
          INSERT INTO users (
            '.implode(',', array_keys($iu)).'
          ) VALUES(
            '.implode(',', $iu).'
          )';
        db_query($link, $sql);
      } else {
        # make sure visum users are not tampered with
        if ($result[0]['id_visum'] !== '0') {
          $errors[] = t('A username in editusers array matches a Visum user. Cannot edit Visum users with the editusers array.');
          break;
        }
        $iu['updated'] = date('Y-m-d H:i:s');
        $iu = dbpua($link, $iu);
        $iu['password'] = 'password=ENCRYPT("'.dbres($link, $user['password']).'", "'.dbres($link, $password_salt).'")';
        $sql = '
          UPDATE
            users
          SET
            '.implode($iu, ',').'
          WHERE
            id="'.dbres($link, $result[0]['id']).'"
          ';
        db_query($link, $sql);
      }
    }
    $errors[] = t('Users noted in the user editing array has been created and updated. Please comment out the array in the setup file when done with it, otherwise this will continue to override user settings made on the site.');
  } else {
    $errors[] = t('The password salt text is too short, please set a longer one in the setup file.');
  }
}

# check what action we have
switch ($action) {
  case 'images_to_files':
    die();
    $r = db_query($link, '
      SELECT
        *
      FROM
        items
      ORDER BY
        id
      ');

    foreach ($r as $k => $v) {
      # is this item already completed, the go next
      if ((int)$v['id_files'] !== -1) continue;
      $original_path = FILE_DIR.$v['id'].'.jpg';

      # no file found?
      if (!file_exists($original_path)) {
        # then update this item with no file
        $sql = '
          UPDATE
            items
          SET
            id_files=0
          WHERE
            id="'.dbres($link, $v['id']).'"';
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
        $sql = '
          INSERT INTO files (
            '.implode(',', array_keys($iu)).'
          ) VALUES(
            '.implode(',', $iu).'
          )';
        $r2 = db_query($link, $sql);
        # update the db
        $sql = '
          UPDATE
            items
          SET
            id_files='.$v['id'].'
          WHERE
            id="'.dbres($link, $v['id']).'"
          ';
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
    if (strlen($title) < 3) {
      $errors[] = t('Fields are not filled in.');
      $view = 'edit_category';
      break;
    }

    # make an array to insert or update
    $iu = array(
      'title' => $title,
    );

    # is this an existing item?
    if ($id_categories) {
      # make sure it belongs to this user
      $sql = '
        SELECT
          id
        FROM
          categories
        WHERE
          id="'.dbres($link, $id_categories).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ';
      if (!count(db_query($link, $sql))) {
        $errors[] = t('Could not find the category, maybe this is not yours.');
        $view = 'edit_category';
        break;
      }

      $iu = dbpua($link, $iu);
      $sql = '
        UPDATE
          categories
        SET
          '.implode($iu, ',').'
        WHERE
          id="'.dbres($link, $id_categories).'"
        ';
      db_query($link, $sql);
    # or is it a new item?
    } else {
      $iu = dbpia($link, $iu);
      $iu['id_users'] = get_logged_in_user('id');
      $sql = '
        INSERT INTO categories (
          '.implode(array_keys($iu), ',').'
        ) VALUES(
          '.implode($iu, ',').'
        )';
      db_query($link, $sql);
      $id_categories = db_insert_id($link);
    }

    break;

  case 'insert_update_criteria': # to insert or update a criteria
    if (!is_logged_in()) break;

    # make sure required fields are filled in
    if (strlen($title) < 3) {
      $errors[] = t('Fields are not filled in.');
      $view = 'edit_criteria';
      break;
    }

    # make an array to insert or update
    $iu = array(
      'title' => $title,
      'add_to_new_packlists' => $add_to_new_packlists,
      'interval_days' => $interval_days,
    );

    # is this an existing item?
    if ($id_criterias) {
      # make sure it belongs to this user
      $sql = '
        SELECT
          id
        FROM
          criterias
        WHERE
          id="'.dbres($link, $id_criterias).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ';
      if (!count(db_query($link, $sql))) {
        $errors[] = t('Could not find the criteria, maybe this is not yours.');
        $view = 'edit_criteria';
        break;
      }

      $iu['updated'] = date('Y-m-d H:i:s');
      $iu = dbpua($link, $iu);
      $sql = '
        UPDATE
          criterias
        SET
          '.implode($iu, ',').'
        WHERE
          id="'.dbres($link, $id_criterias).'"
        ';
      db_query($link, $sql);
    # or is it a new item?
    } else {
      $iu['created'] = date('Y-m-d H:i:s');
      $iu['updated'] = date('Y-m-d H:i:s');
      $iu['id_users'] = get_logged_in_user('id');
      $iu = dbpia($link, $iu);
      $sql = '
        INSERT INTO criterias (
          '.implode(array_keys($iu), ',').'
        ) VALUES(
          '.implode($iu, ',').'
        )';
      db_query($link, $sql);
      $id_criterias = db_insert_id($link);
    }

    break;

  case 'insert_update_location': # to insert or update a location
    if (!is_logged_in()) break;

    # make sure required fields are filled in
    if (strlen($title) < 3) {
      $errors[] = t('Fields are not filled in.');
      $view = 'edit_location';
      break;
    }

    # make an array to insert or update
    $iu = array(
      'contents' => $contents,
      'title' => $title
    );

    # is this an existing item?
    if ($id_locations) {
      # make sure it belongs to this user
      $sql = '
        SELECT
          id
        FROM
          locations
        WHERE
          id="'.dbres($link, $id_locations).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ';
      if (!count(db_query($link, $sql))) {
        $errors[] = t('Could not find the location, maybe this is not yours.');
        $view = 'edit_location';
        break;
      }

      $iu = dbpua($link, $iu);
      $sql = '
        UPDATE
          locations
        SET
          '.implode($iu, ',').'
        WHERE
          id="'.dbres($link, $id_locations).'"
        ';
      db_query($link, $sql);
    # or is it a new item?
    } else {
      $iu = get_logged_in_user('id');
      $iu = dbpia($link, $iu);
      $sql = '
        INSERT INTO locations (
          '.implode(array_keys($iu), ',').'
        ) VALUES(
          '.implode($iu, ',').'
        )';
      db_query($link, $sql);
      $id_locations = db_insert_id($link);
    }

    # upload file management

      # Undefined | Multiple Files | $_FILES Corruption Attack
    # If this request falls under any of them, treat it invalid.
    if (
      isset($_FILES['file']['error'])
      && !is_array($_FILES['file']['error'])
    ) {

      # check error value
      switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:

          # is there an image supplied?
          $id_files = (int)$id_files;

          # filesize check
          if ($_FILES['file']['size'] > 100000000) {
            $errors[] = t('Exceeded filesize limit.');
            $view = 'edit_location';
            break;
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
            $errors[] = t('Invalid file format.');
            $view = 'edit_location';
            break;
          }

          # missing file dir?
          if (
            !is_dir(FILE_DIR)
            || trim(FILE_DIR) === '/'
            || substr(FILE_DIR, -1,1) !== '/'
          ) {
            $errors[] = t('Fatal, file directory does not exist').': '.FILE_DIR;
            $view = 'edit_location';
            break;
          }

          # is there no files id supplied?
          if (!$id_files) {
            # then insert a new file id
            $iu = array(
              'created' => date('Y-m-d H:i:s'),
              'mime' => mime_content_type($_FILES['file']['tmp_name'])
            );
            $iu = dbpia($link, $iu);
            $sql = '
              INSERT INTO files (
                '.implode(',', array_keys($iu)).'
              ) VALUES(
                '.implode(',', $iu).'
              )';
            $r_insert_files = db_query($link, $sql);
            $id_files = db_insert_id($link);
          }

          # update the file location
          $sql = '
            UPDATE
              locations
            SET
              id_files="'.dbres($link, $id_files).'"
            WHERE
              id="'.dbres($link, $id_locations).'"
            ';
          $r_update_locations = db_query($link, $sql);

          # set the target file path
          $targetfile = FILE_DIR.$id_files.'.jpg';

          # make sure it does not exist
          if (file_exists($targetfile)) {
            if (!unlink($targetfile)) {
              $errors[] = t('Failed deleting').' '.$targetfile;
              $view = 'edit_location';
              break;

            }
          }

          if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            $targetfile
          )) {
            $errors[] = t('Failed to move uploaded file.');
            $view = 'edit_location';
            break;
          }

          # missing thumbnail dir?
          if (
            !is_dir(THUMBNAIL_DIR)
            || trim(THUMBNAIL_DIR) === '/'
            || substr(THUMBNAIL_DIR, -1,1) !== '/'
          ) {
            $errors[] = t('Fatal, thumbnail directory does not exist').': '.THUMBNAIL_DIR;
            $view = 'edit_location';
            break;
          }

          # make a thumbnail of it, if it is not already there

          # set the target file path
          $thumbfile = THUMBNAIL_DIR.$id_files.'.jpg';

          # make sure it does not exist
          if (file_exists($thumbfile)) {
            if (!unlink($thumbfile)) {
              $errors[] = t('Failed deleting').' '.$thumbfile;
              $view = 'edit_location';
              break;
            }
          }

          $s = trim(exec('ps ax|grep convert|grep -v grep'));
          if (strlen($s)) return false;

          # 40 funkar ok, himlar blir visserligen pixlade men inte så mkt
          # 30 är gränsfall, det är randigt om man kollar noga
          # 25 är himlar synbart randiga
          # 10-15 pajar ansikten på 160x120
          # 5 är fruktansvärt

          $return2 = exec(MAGICK_PATH.'convert '.escapeshellarg($targetfile).' -quality 75 -auto-orient -strip -sample 320x240 '.escapeshellarg($thumbfile), $output, $return);

          if (!file_exists($thumbfile)) {
            $errors[] = t('Failed creating thumbnail').': '.$thumbfile.'. Command output was: '.implode("\n", $output).$return2.$return;
            $view = 'edit_location';
            break;
          }
          # upload complete

          break;
        case UPLOAD_ERR_NO_FILE:
          # no file uploaded, that is ok
          break;
        case UPLOAD_ERR_INI_SIZE:
          $errors[] = t('Exceeded filesize limit in ini setting.');
          $view = 'edit_location';
          break;
        case UPLOAD_ERR_FORM_SIZE:
          $errors[] = t('Exceeded filesize limit in form.');
          $view = 'edit_location';
          break;
        default:
          $errors[] = t('Unknown error.');
          $view = 'edit_location';
          break;
      }
    }

    break;

  case 'location_fix_fill_location_history':

    die();

    # get all items
    $items = db_query($link, '
      SELECT
        id,
        updated
      FROM
        items
    ');

    # walk items
    foreach ($items as $item) {
      $id_items = $item['id'];
      # try to get the locations of the item
      $locations = db_query($link, '
        SELECT
          r.id AS id_relations_items_locations,
          l.id AS id_locations,
          l.title
        FROM
          relations_items_locations AS r,
          locations AS l
        WHERE
          r.id_locations = l.id
          AND
          r.id_items="'.dbres($link, $id_items).'"
        ');

      if (count($locations) < 1) {
        continue;
      }

      foreach ($locations as $key => $value) {
        $locations[$key] = $value['title'];
      }
      $locations = json_encode($locations);

      # get last location history title
      $sql = '
        SELECT
          title
        FROM
          location_history
        WHERE
          id_items="'.dbres($link, $id_items).'"
        ORDER BY
          id DESC
        LIMIT 1';
      $location_history = db_query($link, $sql);
      if (!count($location_history) || $location_history[0]['title'] !== $locations) {
        # then insert the relation
        $sql = '
          INSERT INTO location_history (
            id_items,
            title,
            created
          ) VALUES(
            '.(int)$id_items.',
            "'.dbres($link, $locations).'",
            "'.dbres($link, $item['updated']).'"
          )';
        $r = db_query($link, $sql);
      }
    }

    die();

  case 'delete_location':

    if (!is_logged_in()) break;

    if (!is_numeric($id_locations)) {
      $errors[] = t('Missing').' id_locations.';
      $view = 'locations';
      break;
    }

    # check connected locations
    $sql = '
      SELECT
        *
      FROM
        relations_items_locations
      WHERE
        id_locations="'.dbres($link, $id_locations).'"
      ';
    $r = db_query($link, $sql);

    if ($r) {
      $errors[] = t('Location with id #').(int)$id_locations.' '.t('has relations, remove them first.');
      $view = 'locations';
      break;
    }

    $sql = '
      DELETE FROM
        locations
      WHERE
        id="'.dbres($link, $id_locations).'"
      ';
    # die($sql);
    # unset($sql);
    db_query($link, $sql);
    break;
  case 'insert_update_item': # to insert or update an item
    if (!is_logged_in()) break;

    # is new category field filled in?
    if (strlen($category)) {
      # check if it already is posted
      $sql = '
        SELECT
          *
        FROM
          categories
        WHERE
          LOWER(title)=LOWER("'.dbres($link, $category).'")
        ';
      $r = db_query($link, $sql);
      # did we find any matching categories?
      if (count($r)) {
        # then take the id from that
        $id_categories = $r[0]['id'];
        # remove the category
        $category = false;
      } else {
        $sql = '
          INSERT INTO categories (
            title
          ) VALUES(
            "'.dbres($link, $category).'"
          )';
        db_query($link, $sql);		# try to get the locations of the item
    $locations = db_query($link, '
      SELECT
        r.id AS id_relations_items_locations,
        l.id AS id_locations,
        l.title
      FROM
        relations_items_locations AS r,
        locations AS l
      WHERE
        r.id_locations = l.id
        AND
        r.id_items="'.dbres($link, $id_items).'"
      ');

    foreach ($locations as $key => $value) {
      $locations[$key] = $value['title'];
    }
    $locations = implode(', ', $locations);

    # get last location history title
    $sql = '
      SELECT
        title
      FROM
        location_history
      WHERE
        id_items="'.dbres($link, $id_items).'"
      ORDER BY
        id DESC
      LIMIT 1';
    $location_history = db_query($link, $sql);
    if (!count($location_history) || $location_history[0]['title'] !== $locations) {
        # then insert the relation
        $sql = '
          INSERT INTO location_history (
            id_items,
            title,
            created
          ) VALUES(
            '.(int)$id_items.',
            "'.dbres($link, $locations).'",
            "'.date('Y-m-d H:i:s').'"
          )';
        $r = db_query($link, $sql);
    }
        $id_categories = db_insert_id($link);
        $category = false;
      }
    }

    # make sure required fields are filled in
    if (strlen($title) < 3) {
      $errors[] = t('Fields are not filled in.');
      $view = 'edit_item';
      break;
    }

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
      # make sure it belongs to this user
      $sql = '
        SELECT
          id
        FROM
          items
        WHERE
          id="'.dbres($link, $id_items).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ';
      if (!count(db_query($link, $sql))) {
        $errors[] = t('Could not find the item, maybe this is not yours.');
        $view = 'edit_item';
        break;
      }

      $iu = dbpua($link, $iu);
      $sql = '
        UPDATE
          items
        SET
          '.implode($iu, ',').'
        WHERE
          id="'.dbres($link, $id_items).'"
        ';
      db_query($link, $sql);

    # or is it a new item?
    } else {
      $iu['created'] = date('Y-m-d H:i:s');
      $iu['id_users'] = get_logged_in_user('id');
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
      $sql = '
        SELECT
          id,
          title
        FROM
          locations
        WHERE
          title="'.dbres($link, $v).'"
        ';
      $loc = db_query($link, $sql);
      # was a location found?
      if (count($loc)) {
        # take the first one
        $loc = $loc[0];
      # was no location found?
      } else {
        # then insert the location
        $sql = '
          INSERT INTO locations (
            title,
            contents
          ) VALUES(
            "' . dbres($link, $v) .'",
            ""
          )';
        $loc = db_query($link, $sql);
        $loc = array(
          'id' => db_insert_id($link),
          'title' => $v
        );
      }


      # get all matching relations based on id of location and item
      $sql = '
        SELECT
          *
        FROM
          relations_items_locations
        WHERE
          id_locations="'.dbres($link, $loc['id']).'" AND
          id_items="'.dbres($link, $id_items).'"
        ';
      $r = db_query($link, $sql);
      # no relation match?
      if (!count($r)) {
        # then insert the relation
        $sql = '
          INSERT INTO relations_items_locations (
            id_items,
            id_locations
          ) VALUES(
            '.(int)$id_items.',
            '.(int)$loc['id'].'
          )';
        $r = db_query($link, $sql);
        $valid_id_relations_items_locations[] = db_insert_id($link);
      } else {
        $valid_id_relations_items_locations[] = $r[0]['id'];
      }
    }

    # delete all invalid ones
    if (count($valid_id_relations_items_locations)) {
      $sql = '
        DELETE FROM
          relations_items_locations
        WHERE
          id_items="'.(int)$id_items.'" AND
          id NOT IN ('.implode(',', $valid_id_relations_items_locations).')
        ';
      db_query($link, $sql);
    }

    # try to get the locations of the item
    $locations = db_query($link, '
      SELECT
        r.id AS id_relations_items_locations,
        l.id AS id_locations,
        l.title
      FROM
        relations_items_locations AS r,
        locations AS l
      WHERE
        r.id_locations = l.id
        AND
        r.id_items="'.dbres($link, $id_items).'"
      ');

    foreach ($locations as $key => $value) {
      $locations[$key] = $value['title'];
    }
    $locations = json_encode($locations);

    # get last location history title
    $sql = '
      SELECT
        title
      FROM
        location_history
      WHERE
        id_items="'.dbres($link, $id_items).'"
      ORDER BY
        id DESC
      LIMIT 1
      ';
    $location_history = db_query($link, $sql);
    if (!count($location_history) || $location_history[0]['title'] !== $locations) {
      # then insert the relation
      $sql = '
        INSERT INTO location_history (
          id_items,
          title,
          created
        ) VALUES(
          '.(int)$id_items.',
          "'.dbres($link, $locations).'",
          "'.date('Y-m-d H:i:s').'"
        )';
      $r = db_query($link, $sql);
    }

    # --- end of location

    # Undefined | Multiple Files | $_FILES Corruption Attack
    # If this request falls under any of them, treat it invalid.
    if (
      isset($_FILES['file']['error']) &&
      !is_array($_FILES['file']['error'])
    ) {

      # check error value
      switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:

          # is there an image supplied?
          $id_files = (int)$id_files;

          # filesize check
          if ($_FILES['file']['size'] > 100000000) {
            $errors[] = t('Exceeded filesize limit.');
            $view = 'edit_item';
            break;
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
            $errors[] = t('Invalid file format.');
            $view = 'edit_item';
            break;
          }

          # missing file dir?
          if (
            !is_dir(FILE_DIR)
            || trim(FILE_DIR) === '/'
            || substr(FILE_DIR, -1,1) !== '/'
          ) {
            $errors[] = t('Fatal, file directory does not exist').': '.FILE_DIR;
            $view = 'edit_item';
            break;
          }

          # is there no files id supplied?
          if (!$id_files) {
            # then insert a new file id
            $iu = array(
              'created' => date('Y-m-d H:i:s'),
              'mime' => mime_content_type($_FILES['file']['tmp_name']),
              'id_users' => get_logged_in_user('id')
            );
            $iu = dbpia($link, $iu);
            $sql = '
              INSERT INTO files (
                '.implode(',', array_keys($iu)).'
              ) VALUES(
                '.implode(',', $iu).'
              )';
            $r_insert_files = db_query($link, $sql);
            $id_files = db_insert_id($link);
          }

          # update the file location
          $sql = '
            UPDATE
              items
            SET
              id_files="'.dbres($link, $id_files).'"
            WHERE
              id="'.dbres($link, $id_items).'"
            ';
          $r_update_items = db_query($link, $sql);

          # set the target file path
          # $targetfile = FILE_DIR.$id_items.'.jpg';
          $targetfile = FILE_DIR.$id_files.'.jpg';

          # make sure it does not exist
          if (file_exists($targetfile)) {
            if (!unlink($targetfile)) {
              $errors[] = t('Failed deleting').' '.$targetfile;
              $view = 'edit_item';
              break;
            }
          }

          if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            $targetfile
          )) {
            $errors[] = t('Failed to move uploaded file.');
            $view = 'edit_item';
            break;
          }

          # missing thumbnail dir?
          if (
            !is_dir(THUMBNAIL_DIR)
            || trim(THUMBNAIL_DIR) === '/'
            || substr(THUMBNAIL_DIR, -1,1) !== '/'
          ) {
            $errors[] = t('Fatal, thumbnail directory does not exist').': '.THUMBNAIL_DIR;
            $view = 'edit_item';
            break;
          }

          # make a thumbnail of it, if it is not already there

          # set the target file path
          $thumbfile = THUMBNAIL_DIR.$id_files.'.jpg';

          # make sure it does not exist
          if (file_exists($thumbfile)) {
            if (!unlink($thumbfile)) {
              $errors[] = t('Failed deleting').' '.$thumbfile;
              $view = 'edit_item';
              break;
            }
          }

          $s = trim(exec('ps ax|grep convert|grep -v grep'));
          if (strlen($s)) {
            $errors[] = t('A image conversion is already in progress.');
            $view = 'edit_item';
            break;
          }

          # 40 funkar ok, himlar blir visserligen pixlade men inte så mkt
          # 30 är gränsfall, det är randigt om man kollar noga
          # 25 är himlar synbart randiga
          # 10-15 pajar ansikten på 160x120
          # 5 är fruktansvärt

          unset ($c, $o, $r);
          $c = MAGICK_PATH.'convert '.escapeshellarg($targetfile).' -quality 75 -auto-orient -strip -sample 320x240 '.escapeshellarg($thumbfile);
          exec($c, $o, $r);
          if ($r !== 0) {
            $errors[] = t('Failed creating thumbnail').': '.$thumbfile.'. Command output was: '.implode("\n", $o).' ('.$r.')';
            $view = 'edit_item';
            break;
          }

          if (!file_exists($thumbfile)) {
            $errors[] = t('Failed creating thumbnail').': '.$thumbfile.'. Command output was: '.implode("\n", $o).' ('.$r.')';
            $view = 'edit_item';
            break;
          }
          # upload complete

          break;
        case UPLOAD_ERR_NO_FILE:
          # no file uploaded, that is ok
          break;
        case UPLOAD_ERR_INI_SIZE:
          $errors[] = t('Exceeded filesize limit in ini setting.');
          $view = 'edit_item';
          break;
        case UPLOAD_ERR_FORM_SIZE:
          $errors[] = t('Exceeded filesize limit in form.');
          $view = 'edit_item';
          break;

        default:
          $errors[] = t('Unknown error.');
          $view = 'edit_item';
          break;
      }
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

    # make sure required fields are filled in
    if (strlen($title) < 3) {
      $errors[] = t('Fields are not filled in.');
      $view = 'edit_packlist';
      break;
    }

    # make an array to insert or update
    $iu = array(
      'title' => $title,
      '`from`' => str_replace('T', ' ', $from),
      '`to`' => str_replace('T', ' ', $to)
    );

    # is this an existing item?
    if ($id_packlists) {
      # make sure it belongs to this user
      $sql = '
        SELECT
          id
        FROM
          packlists
        WHERE
          id="'.dbres($link, $id_packlists).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ';
      if (!count(db_query($link, $sql))) {
        $errors[] = t('Could not find the packlist, maybe this is not yours.');
        $view = 'edit_packlist';
        break;
      }

      $iu['updated'] = date('Y-m-d H:i:s');
      $iu = dbpua($link, $iu);
      $sql = '
        UPDATE
          packlists
        SET
          '.implode($iu, ',').'
        WHERE
          id="'.dbres($link, $id_packlists).'"
        ';
      db_query($link, $sql);
    # or is it a new item?
    } else {
      $iu['created'] = date('Y-m-d H:i:s');
      $iu['id_users'] = get_logged_in_user('id');
      $iu['updated'] = date('Y-m-d H:i:s');
      $iu = dbpia($link, $iu);
      $sql = '
        INSERT INTO packlists (
          '.implode(array_keys($iu), ',').'
        ) VALUES(
          '.implode($iu, ',').'
        )';
      db_query($link, $sql);
      $id_packlists = db_insert_id($link);
    }

    # is packlist copying requested
    if ($id_packlists_from) {
      copy_packlist($link, $id_packlists_from, $id_packlists);
    }

    $id_criterias = is_array($id_criterias) ? $id_criterias : array();

    foreach ($id_criterias as $k => $v) {
      $id_criterias[$k] = dbres($link, $v);
    }

    # remove all relations between criterias and packlists matching this packlist but not the desired criteria id:s
    $sql = 'DELETE FROM
          relations_criterias_packlists
        WHERE
          id_packlists="'.dbres($link, $id_packlists).'"
          '.(count($id_criterias) ? 'AND id_criterias NOT IN ('.implode($id_criterias, ',').')' : '');
    db_query($link, $sql);

    # are there any criterias to add?
    if (count($id_criterias)) {
      # get all criterias that are in the id list, but not in the relations table
      $sql = 'SELECT
            id
          FROM
            criterias WHERE id IN ('.implode($id_criterias, ',').')
            AND id NOT IN (
              SELECT
                id_criterias
              FROM
                relations_criterias_packlists
              WHERE
                id_packlists="'.dbres($link, $id_packlists).'"
            )
          ';
      $r = db_query($link, $sql);
      # walk the ids that needs to be added
      foreach ($r as $k => $v) {
        $iu = array(
          'id_criterias' => $v['id'],
          'id_packlists' => $id_packlists,
          'id_users' => get_logged_in_user('id'),
          'created' => date('Y-m-d H:i:s')
        );
        $iu = dbpia($link, $iu);
        $sql = '
          INSERT INTO relations_criterias_packlists (
            '.implode(array_keys($iu), ',').'
          ) VALUES(
            '.implode($iu, ',').'
          )';
        db_query($link, $sql);
      }
    }

    break;

  case 'insert_update_relations_criterias_items':

    if (!is_logged_in()) break;

    if (!is_numeric($id_items)) {
      $errors[] = t('Missing').' id_items.';
      $view = 'index';
      break;
    }

    if (!is_numeric($id_criterias)) {
      $errors[] = t('Missing').' id_criterias.';
      $view = 'index';
      break;
    }

    # check that relation is not there before
    $sql = '
      SELECT
        *
      FROM
        relations_criterias_items
      WHERE
        id_criterias="'.dbres($link, $id_criterias).'" AND
        id_items="'.dbres($link, $id_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    $r = db_query($link, $sql);
    if (count($r)) {
      echo json_encode(array(
        'status' => true
      ));
      die();
    }

    # delete criteria relations
    $sql = '
      INSERT INTO relations_criterias_items (
        id_criterias,
        id_items,
        id_users
      ) VALUES(
        "'.dbres($link, $id_criterias).'",
        "'.dbres($link, $id_items).',
        "'.dbres($link, get_logged_in_user('id')).'"
      )';
    $r = db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'insert_update_relations_packlists_items':

    if (!is_logged_in()) break;

    if (!is_numeric($id_items)) {
      $errors[] = t('Missing').' id_items.';
      $view = 'packlists';
      break;
    }

    if (!is_numeric($id_packlists)) {
      $errors[] = t('Missing').' id_packlists.';
      $view = 'packlists';
      break;
    }

    # check that relation is not there before
    $sql = '
      SELECT
        *
      FROM
        relations_packlists_items
      WHERE
        id_packlists="'.dbres($link, $id_packlists).'" AND
        id_items="'.dbres($link, $id_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    $r = db_query($link, $sql);
    if (count($r)) {
      echo json_encode(array(
        'status' => true
      ));
      die();
    }

    # delete packlist relations
    $sql = '
      INSERT INTO
        relations_packlists_items
      (
        id_packlists,
        id_items
      ) VALUES(
        "'.dbres($link, $id_packlists).'",
        "'.dbres($link, $id_items).'"
      )';
    $r = db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'update_packlist_notes':

    if (!is_logged_in()) break;

    if (!is_numeric($id_packlists)) {
      $errors[] = t('Missing').' id_packlists.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        packlists
      WHERE
        id="'.dbres($link, $id_packlists).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the packlist, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    $iu = dbpua($link, array(
      'notes' => $notes
    ));
    $sql = '
      UPDATE
        packlists
      SET
        '.implode($iu, ',').'
      WHERE
        id="'.dbres($link, $id_packlists).'"
      ';
    db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'update_relation_packlists_items':

    if (!is_logged_in()) break;

    if (!is_numeric($id_relations_packlists_items)) {
      $errors[] = t('Missing').' id_relations_packlists_items.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        relations_packlists_items
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the relation, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    $iu = dbpua($link, array(
      'comment' => $comment
    ));
    $sql = '
      UPDATE
        relations_packlists_items
      SET
        '.implode($iu, ',').'
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'"
      ';
    db_query($link, $sql);

    break;

  case 'insert_update_user': # to insert or update a user

    if (!is_logged_in()) break;

    # make sure required fields are filled in
    if (strlen($username) < 3) {
      $errors[] = t('Fields are not filled in.');
      $view = 'edit_user';
      break;
    }

    # make an array to insert or update
    $iu = array(
      'username' => $username,
    );

    # make sure username does not already exist
    $sql = '
      SELECT
        *
      FROM
        users
      WHERE
        username="'.dbres($link, $username).'"
      ';
    if ($id_users) {
      $sql .= ' AND NOT id="'.dbres($link, $id_users).'"';
    }
    $result = db_query($link, $sql);

    if (count($result)) {
      $errors[] = t('A user with the selected username already exists.');
      $view = 'edit_user';
      break;
    }

    if (!validate_user($username)) {
      $errors[] = t('Username is too short, too long or contain invalid characters.');
      $view = 'edit_user';
      break;
    }

    # is this an existing item?
    if ($id_users) {

      # has password been sent in
      if ($password) {
        if ($result[0]['id_visum'] !== '0') {
          $errors[] = t('A username in editusers array matches a Visum user. Cannot edit Visum users with the editusers array.');
          $view = 'edit_user';
          break;
        }

        if (!validate_pass($password)) {
          $errors[] = t('Password is too short or does not contain letters or digits.');
          $view = 'edit_user';
          break;
        }

        if ($password != $password_retype) {
          $errors[] = t('Password does not match password retype.');
          $view = 'edit_user';
          break;
        }

        $iu['password'] = $password;
      }

      $iu['updated'] = date('Y-m-d H:i:s');
      $iu = dbpua($link, $iu);
      $sql = '
        UPDATE
          users
        SET
          '.implode($iu, ',').'
        WHERE
          id="'.dbres($link, $id_users).'"
        ';
      db_query($link, $sql);
    # or is it a new item?
    } else {
      if (!validate_pass($password)) {
        $errors[] = t('Password is too short or does not contain letters or digits.');
        $view = 'edit_user';
        break;
      }

      if ($password != $password_retype) {
        $errors[] = t('Password does not match password retype.');
        $view = 'edit_user';
        break;
      }

      $iu['id_visum'] = 0; # visum disabled for this user
      $iu['password'] = $password;
      $iu['created'] = date('Y-m-d H:i:s');
      $iu['updated'] = date('Y-m-d H:i:s');
      $iu = dbpia($link, $iu);
      $sql = '
        INSERT INTO users (
          '.implode(array_keys($iu), ',').'
        ) VALUES(
          '.implode($iu, ',').'
        )';
      db_query($link, $sql);
      $id_users = db_insert_id($link);
    }
    break;

  case 'delete_criteria':

    if (!is_logged_in()) break;

    if (!is_numeric($id_criterias)) {
      $errors[] = t('Missing').' id_criterias.';
      $view = 'criterias';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        criterias
      WHERE
        id="'.dbres($link, $id_criterias).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the criteria, maybe this is not yours.');
      $view = 'criterias';
      break;
    }

    # delete criteria relations
    $sql = '
      DELETE FROM
        relations_criterias_items
      WHERE
        id_criterias="'.dbres($link, $id_criterias).'"
      ';
    $r = db_query($link, $sql);

    $sql = '
      DELETE FROM
        relations_criterias_packlists
      WHERE
        id_criterias="'.dbres($link, $id_criterias).'"
      ';
    $r = db_query($link, $sql);

    # delete criteria
    $sql = '
      DELETE FROM
        criterias
      WHERE
        id="'.dbres($link, $id_criterias).'"
      ';
    db_query($link, $sql);
    break;

  case 'delete_packlist':

    if (!is_logged_in()) break;

    if (!is_numeric($id_packlists)) {
      $errors[] = t('Missing').' id_packlists.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        packlists
      WHERE
        id="'.dbres($link, $id_packlists).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the packlist, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # delete packlist relations
    $sql = '
      DELETE FROM
        relations_packlists_items
      WHERE
        id_packlists="'.dbres($link, $id_packlists).'"
      ';
    $r = db_query($link, $sql);

    # delete packlist
    $sql = '
      DELETE FROM
        packlists
      WHERE
        id="'.dbres($link, $id_packlists).'"
      ';
    db_query($link, $sql);
    break;

  case 'delete_relation_criterias_items':

    if (!is_logged_in()) break;

    if (!is_numeric($id_relations_criterias_items)) {
      $errors[] = t('Missing').' id_relations_criterias_items.';
      $view = 'criterias';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        relations_criterias_items
      WHERE
        id="'.dbres($link, $id_relations_criterias_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the relation, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # delete criteria relations
    $sql = '
      DELETE FROM
        relations_criterias_items
      WHERE
        id="'.dbres($link, $id_relations_criterias_items).'"
      ';
    $r = db_query($link, $sql);

    break;

  case 'delete_relation_packlists_items':

    if (!is_logged_in()) break;

    if (!is_numeric($id_relations_packlists_items)) {
      $errors[] = t('Missing').' id_relations_packlists_items.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        relations_packlists_items
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'"
        AND id_users="'.dbres($link, get_logged_in_user('id')).'"
    ';

    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the relation, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # delete packlist relations
    $sql = '
      DELETE FROM
        relations_packlists_items
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'"
      ';
    $r = db_query($link, $sql);

    break;

  case 'insert_update_packlist_item':

    if (!is_logged_in()) break;

    if (!is_numeric($id_packlists)) {
      $errors[] = t('Missing').' id_packlists.';
      $view = 'packlists';
      break;
    }

    if (!strlen($title)) {
      $errors[] = t('Missing').' title.';
      $view = 'packlists';
      break;
    }
    if (!strlen($weight)) {
      $errors[] = t('Missing').' weight.';
      $view = 'packlists';
      break;
    }

    if ($id_packlist_items) {
      # make sure it belongs to this user
      $sql = '
        SELECT
          id
        FROM
          packlist_items
        WHERE
          id="'.dbres($link, $id_packlist_items).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ';
      if (!count(db_query($link, $sql))) {
        $errors[] = t('Could not find the packlist item, maybe this is not yours.');
        $view = 'packlists';
        break;
      }

      # update packlist item
      $sql = '
        UPDATE
          packlist_items
        SET
          title="'.dbres($link, $title).'",
          weight="'.dbres($link, $weight).'"
        WHERE
          id="'.dbres($link, $id_packlist_items).'"
        ';
      $r = db_query($link, $sql);
    } else {
      # insert packlist item
      $sql = '
        INSERT INTO packlist_items (
          id_packlists,
          id_users,
          title,
          weight
        ) VALUES(
          "'.dbres($link, $id_packlists).'",
          "'.dbres($link, get_logged_in_user('id')).'",
          "'.dbres($link, $title).'",
          "'.dbres($link, $weight).'"
        )';
      $r = db_query($link, $sql);
    }
    break;

  case 'delete_packlist_item':

    if (!is_logged_in()) break;

    if (!is_numeric($id_packlist_items)) {
      $errors[] = t('Missing').' id_packlist_items.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        packlist_items
      WHERE
        id="'.dbres($link, $id_packlist_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the packlist item, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # delete packlist relations
    $sql = '
      DELETE FROM
        packlist_items
      WHERE
        id="'.dbres($link, $id_packlist_items).'"
      ';
    $r = db_query($link, $sql);

    break;

  case 'delete_user':

    if (!is_logged_in()) break;

    if (!is_numeric($id_users)) {
      $errors[] = t('Missing').' id_users.';
      $view = 'users';
      break;
    }

    # delete packlist relations
    $sql = '
      DELETE FROM
        users
      WHERE
        id="'.dbres($link, $id_users).'"
      ';
    $r = db_query($link, $sql);

    break;

  case 'update_relations_packlists_items_inuse':

    if (!is_logged_in()) break;

    if (!is_numeric($id_relations_packlists_items)) {
      $errors[] = t('Missing').' id_packlist_items.';
      $view = 'packlists';
      break;
    }
    if (!is_numeric($inuse)) {
      $errors[] = t('Missing').' inuse.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        relations_packlists_items
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the relation, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # update inuse status
    $sql = '
      UPDATE
        relations_packlists_items
      SET
        inuse="'.dbres($link, $inuse).'"
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'"
      ';
    $r = db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'update_packlist_items_inuse':

    if (!is_logged_in()) break;

    if (!is_numeric($id_packlist_items)) {
      $errors[] = t('Missing').' id_packlist_items.';
      $view = 'packlists';
      break;
    }

    if (!is_numeric($inuse)) {
      $errors[] = t('Missing').' inuse.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        packlist_items
      WHERE
        id="'.dbres($link, $id_packlists_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the packlist item, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # update inuse status
    $sql = '
      UPDATE
        packlist_items
      SET
        inuse="'.dbres($link, $inuse).'"
      WHERE
        id="'.dbres($link, $id_packlist_items).'"
      ';
    $r = db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'update_relations_packlists_items_packed':

    if (!is_logged_in()) break;

    if (!is_numeric($id_relations_packlists_items)) {
      $errors[] = t('Missing').' id_packlist_items.';
      $view = 'packlists';
      break;
    }
    if (!is_numeric($packed)) {
      $errors[] = t('Missing').' packed.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        packlist_items
      WHERE
        id="'.dbres($link, $id_packlists_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the packlist item, maybe this is not yours.');
      $view = 'packlists';
      break;
    }

    # update packed status
    $sql = '
      UPDATE
        relations_packlists_items
      SET
        packed="'.dbres($link, $packed).'"
      WHERE
        id="'.dbres($link, $id_relations_packlists_items).'"
      ';
    $r = db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'update_packlist_items_packed':

    if (!is_logged_in()) break;

    if (!is_numeric($id_packlist_items)) {
      $errors[] = t('Missing').' id_packlist_items.';
      $view = 'packlists';
      break;
    }
    if (!is_numeric($packed)) {
      $errors[] = t('Missing').' packed.';
      $view = 'packlists';
      break;
    }

    # make sure it belongs to this user
    $sql = '
      SELECT
        id
      FROM
        packlist_items
      WHERE
        id="'.dbres($link, $id_packlists_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the packlist item, maybe this is not yours.');
      $view = 'packlists';
      break;
    }
    # update packed status
    $sql = '
      UPDATE
        packlist_items
      SET
        packed="'.dbres($link, $packed).'"
      WHERE
        id="'.dbres($link, $id_packlist_items).'"
      ';
    $r = db_query($link, $sql);

    echo json_encode(array(
      'status' => true
    ));
    die();

  case 'login': # login taken from mediaarchive
    if (is_logged_in()) break;
    if ($logintype === 'visum') {
      if (!file_exists(dirname(__FILE__).'/class-visum.php')) {
        $errors[] = t('Local Visum support is not available.');
        $view = 'login';
        break;
      }
      # visum login begin
      if (!$ticket) {
        $errors[] = t('Missing').' ticket.';
        $view = 'login';
        break;
      }
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
        $errors[] = t('Error').': '.$t['error'];
        $view = 'login';
        break;
      } catch(Exception $e) {
        $errors[] = $e->getMessage();
        $view = 'login';
        break;
      }

      if (!isset($visum_user['id_users'])) {
        $errors[] = t('Missing user id in Visum response.');
        $view = 'login';
        break;
      }
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
        $sql = '
          UPDATE
            users
          SET
            '.implode(',',$iu).'
          WHERE
            id_visum="'.dbres($link, $id_visum).'"
          ';
        $r = db_query($link, $sql);
      }

      # try to find the user, did it exist in local db?
      $sql = '
        SELECT
          *
        FROM
          users
        WHERE
          id_visum="'.dbres($link, $id_visum).'"
        ';
      $r = db_query($link, $sql);

      # mysql_result_as_array($result, $users);
      if (count($r) < 1) {
        $errors[] = t('No such user found in local database.');
        $view = 'login';
        break;
      }
      $user = reset($r);

      # this means user is logged in
      $_SESSION[SITE_SHORTNAME]['user'] = $user;

      # now we have a visum user id to match against our own database and then create a login, that's all that is needed

      header('Location: ./');
      # visum login end
    } else if ($logintype='local') {
      # try to find the user, did it exist in local db?
      $sql = '
        SELECT
          *
        FROM
          users
        WHERE
          username="'.dbres($link, $username).'" AND
          password=ENCRYPT("'.dbres($link, $password).'", "'.dbres($link, $password_salt).'")
        ';
      $r = db_query($link, $sql);

      if (count($r) < 1) {
        $errors[] = t('No such user found in local database.');
        $view = 'login';
        break;
      }
      $user = reset($r);

      # this means user is logged in
      $_SESSION[SITE_SHORTNAME]['user'] = $user;
      header('Location: ./');
    }

    break;

  case 'logout':
    if (!is_logged_in()) {
      # report_sysmessage(SYSMESSAGE_NOTICE, 'Redan utloggad.'); $view=''; break;
      break;
    }
    $_SESSION[SITE_SHORTNAME]['user'] = false;
    unset($_SESSION[SITE_SHORTNAME]['user']);
    break;

  case 'fix':
    die();
    if (!is_logged_in()) {
      # report_sysmessage(SYSMESSAGE_NOTICE, 'Redan utloggad.'); $view='';
      break;
    }

    $sql = '
      SELECT
        *
      FROM
        items
      ORDER BY
        location
      ';
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
        $sql = '
          SELECT
            id,
            title
          FROM
            locations
          WHERE
            title="'.dbres($link, $v).'"
          ';
        $loc = db_query($link, $sql);

        # was a location found?
        if (count($loc)) {
          # take the first one
          $loc = $loc[0];
        # was no location found?
        } else {
          # then insert the location
          $sql = '
            INSERT INTO locations (
              title
            ) VALUES(
              "' . dbres($link, $v) .'"
            )';
          $loc = db_query($link, $sql);
          $loc = array(
            'id' => db_insert_id($link),
            'title' => $v
          );
        }

        # get all matching relations based on id of location and item
        $sql = '
          SELECT
            *
          FROM
            relations_items_locations
          WHERE
            id_locations="'.dbres($link, $loc['id']).'" AND
            id_items="'.dbres($link, $id_items).'"
          ';
        $r = db_query($link, $sql);
        # no relation match?
        if (!count($r)) {
          # then insert the relation
          $sql = '
            INSERT INTO relations_items_locations (
              id_items,
              id_locations
            ) VALUES(
              '.(int)$id_items.',
              '.(int)$loc['id'].'
            )';
          $r = db_query($link, $sql);
          $valid_id_relations_items_locations[] = db_insert_id($link);
        } else {
          $valid_id_relations_items_locations[] = $r[0]['id'];
        }
      }

      # delete all invalid ones
      if (count($valid_id_relations_items_locations)) {
        $sql = '
          DELETE FROM
            relations_items_locations
          WHERE
            id_items="'.(int)$id_items.'" AND
            id NOT IN ('.implode(',', $valid_id_relations_items_locations).')
          ';
        db_query($link, $sql);
      }
    }

    break;
}
?>
