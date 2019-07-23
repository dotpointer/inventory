<?php

# changelog
# 2015-04-06 22:05:21
# 2015-04-09 09:34:17
# 2015-04-10 22:04:43
# 2015-04-11 22:51:39 - location, usage
# 2015-04-12 14:36:30 - visum login
# 2015-04-13 12:20:00 - edit_item, bugfix, cannot edit sold items
# 2015-04-28 14:38:00 - adding location
# 2015-05-06 22:14:06 - adding location logic
# 2015-05-07 18:26:42 - dropping items.location field
# 2015-05-09 18:36:18 - adding location contents field
# 2015-10-23 13:03:52 - adding batteries, aa, aaa, c, d, 9v and 3R12
# 2016-03-09 00:41:22 - updating css
# 2016-03-27 17:47:26 - adding files table
# 2016-03-27 18:41:06 - adding images to locations
# 2017-01-26 21:32:04 - adding materials column
# 2017-05-13 15:30:51 - adding weight
# 2017-05-13 17:50:02 - adding packlist
# 2017-05-13 23:06:59 - adding packlist items
# 2017-05-21 20:42:30 - adding packlist inuse
# 2018-02-19 20:08:00 - adding packlist from and to and copy packlist
# 2018-02-22 22:21:00 - adding packlist item relation comment
# 2018-03-14 23:02:00 - adding criteria handling
# 2018-03-14 23:44:00 - adding criteria handling continued
# 2018-03-15 00:47:00 - adding criteria handling continued
# 2018-04-08 12:34:39 - adding location history
# 2018-04-09 12:12:00 - cleanup
# 2018-04-13 23:50:00 - adding packlist notes
# 2018-05-04 23:58:00 - adding risk materials
# 2018-06-25 18:58:00 - adding local user management and multi user support
# 2018-06-26 16:04:00 - adding error handling
# 2018-06-27 18:12:00 - bugfixes in sql queries
# 2018-07-19 18:00:02 - indentation change, tab to 2 spaces
# 2019-07-23 20:15:00 - adding unpacked status

if (!isset($view)) die();

if (is_logged_in()) {
  $categories_find = db_query($link, '
    SELECT
      *
    FROM
      categories
    WHERE
      id_users="'.dbres($link, get_logged_in_user('id')).'"
    ORDER BY
      title
  ');

  $item_amount = db_query($link, '
    SELECT
      COUNT(id) AS amount
    FROM
      items
    WHERE
      status<='.STATUS_OWNSELL.' AND
      id_users="'.dbres($link, get_logged_in_user('id')).'"
    '
  );
}

# check out what view we have
switch ($view) {
  case 'edit_category': # to edit a category
    if (!is_logged_in()) break;

    $category = false;

    # is item id specified?
    if ($id_categories) {
      # try to get that item
      $categories = db_query($link, '
        SELECT
          *
        FROM
          categories
        WHERE
          id="'.dbres($link, $id_categories).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
      # was there any matching items?
      if (count($categories)) {
        # then take the first of it
        $category = $categories[0];
      }
    }
    break;

  case 'edit_criteria': # to edit a criteria
    if (!is_logged_in()) break;

    $criteria = false;

    # is item id specified?
    if ($id_criterias) {
      # try to get that item
      $criterias = db_query($link, '
        SELECT
          *
        FROM
          criterias
        WHERE
          id="'.dbres($link, $id_criterias).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
      # was there any matching items?
      if (count($criterias)) {
        # then take the first of it
        $criteria = $criterias[0];
      }
    }

    break;

  case 'edit_item': # to edit an item
    if (!is_logged_in()) break;

    $categories = db_query($link, '
      SELECT
        *
      FROM
        categories
      WHERE
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ORDER BY
        title
      ');

    $item = false;

    # is item id specified?
    if ($id_items) {
      # try to get that item
      $items = db_query($link, '
        SELECT
          *
        FROM
          items
        WHERE
          id="'.dbres($link, $id_items).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');
      # was there any matching items?
      if (count($items)) {
        # then take the first of it
        $item = $items[0];
      }
    }

    # was there an item?
    if ($item) {

      # try to get the locations of the item
      $locations = db_query($link, '
        SELECT
          l.title
        FROM
          relations_items_locations AS r,
          locations AS l
        WHERE
          r.id_locations = l.id AND
          r.id_items="'.dbres($link, $id_items).'" AND
          r.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
          l.id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
        # was there any matching locations?
        if (count($locations)) {
          # walk the locations
          foreach ($locations as $k => $v) {
            # simplify the position
            $locations[$k] = $locations[$k]['title'];
          }

          # merge the locations into a string
          $item['location'] = implode(' + ', $locations);
        }

    }
    break;

  case 'edit_location': # to edit a location
    if (!is_logged_in()) break;

    $location = false;

    # is item id specified?
    if ($id_locations) {
      # try to get that item
      $locations = db_query($link, '
        SELECT
          *
        FROM
          locations
        WHERE
          id="'.dbres($link, $id_locations).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
      # was there any matching items?
      if (count($locations)) {
        # then take the first of it
        $location = $locations[0];
      }
    }
    break;

  case 'edit_packlist': # to edit a packlist
    if (!is_logged_in()) break;

    $packlist = false;

    # is item id specified?
    if ($id_packlists) {
      # try to get that item
      $packlists = db_query($link, '
        SELECT
          *
        FROM
          packlists
        WHERE
          id="'.dbres($link, $id_packlists).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');
      # was there any matching items?
      if (count($packlists)) {
        # then take the first of it
        $packlist = $packlists[0];

        $criterias_selected =  db_query($link, '
          SELECT
            id_criterias AS id
          FROM
            relations_criterias_packlists
          WHERE
            id_packlists="'.dbres($link, $id_packlists).'" AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          ');
      }
      $criterias_available = db_query($link, '
        SELECT
          *
        FROM
          criterias
        WHERE
          id NOT IN (
            SELECT
              id_criterias
            FROM
              relations_criterias_packlists
            WHERE
              id_packlists="'.dbres($link, $id_packlists).'" AND
              id_users="'.dbres($link, get_logged_in_user('id')).'"
        )');
      $criterias_selected =  db_query($link, '
        SELECT
          rcp.id_criterias AS id,
          c.title
        FROM
          relations_criterias_packlists AS rcp
          LEFT JOIN criterias AS c ON rcp.id_criterias = c.id AND c.id_users="'.dbres($link, get_logged_in_user('id')).'"
        WHERE
          rcp.id_packlists="'.dbres($link, $id_packlists).'" AND
          rcp.id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');

      # sort by from, because some trips may not know the to-date
      $packlists_copy = db_query($link, '
        SELECT
          *
        FROM
          packlists
        WHERE
          NOT id="'.dbres($link, $id_packlists).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ORDER BY
          `from` DESC
        ');
    } else {
      $criterias_available = db_query($link, '
        SELECT
          *
        FROM
          criterias
        WHERE
          add_to_new_packlists=0 AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
      $criterias_selected = db_query($link, '
        SELECT
          *
        FROM
          criterias
        WHERE
          add_to_new_packlists=1 AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');
      # sort by from, because some trips may not know the to-date
      $packlists_copy = db_query($link, '
        SELECT
          *
        FROM
          packlists
        WHERE
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ORDER BY
          `from` DESC
        ');
    }

    break;

  case 'edit_relation_packlists_items': # to update a packlist relation

    $relation = false;

    $sql = 'SELECT
          rpi.id AS id_relation_packlists_items,
          rpi.id_items,
          rpi.id_packlists,
          i.title,
          rpi.comment
        FROM
          relations_packlists_items AS rpi
            LEFT JOIN items AS i ON i.id = rpi.id_items AND i.id_users="'.dbres($link, get_logged_in_user('id')).'"
        WHERE
          rpi.id="'.dbres($link, $id_relations_packlists_items).'" AND
          rpi.id_users="'.dbres($link, get_logged_in_user('id')).'"

    ';

    # try to get that item
    $relations = db_query($link, $sql);
    # was there any matching items?
    if (count($relations)) {
      # then take the first of it
      $relation = $relations[0];
    }

    break;
  case 'edit_user': # to edit a user
    if (!is_logged_in()) break;
    $user = false;
    # is item id specified?
    if ($id_users) {
      # try to get that item
      $users = db_query($link, '
        SELECT
          *
        FROM
          users
        WHERE
          id="'.dbres($link, $id_users).'"
      ');
      # was there any matching items?
      if (count($users)) {
        # then take the first of it
        $user = $users[0];
      }
    }
    break;

  case 'category': # to display a category
    if (!is_logged_in()) break;
    $category = db_query($link, '
      SELECT
        *
      FROM
        categories
      WHERE
        id="'.dbres($link, $id_categories).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');
    break;

  case 'categories': # to display a list of categories
    if (!is_logged_in()) break;
    $categories = db_query($link, '
      SELECT
        c.id,
        c.title,
        itemcount.amount AS item_amount,
        IFNULL(inuse0.amount, 0) AS inuse0_amount,
        IFNULL(inuse1.amount, 0) AS inuse1_amount,
        IFNULL(inuse2.amount, 0) AS inuse2_amount,
        IFNULL(inuse3.amount, 0) AS inuse3_amount,
        IFNULL(inuse4.amount, 0) AS inuse4_amount
      FROM
        categories AS c
        LEFT JOIN (
          SELECT
            COUNT(id) AS amount,
            id_categories
          FROM
            items
          WHERE
            status<='.STATUS_OWNSELL.' AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          GROUP BY
            id_categories
        ) AS itemcount ON c.id = itemcount.id_categories
        LEFT JOIN (
          SELECT
            COUNT(id) AS amount,
            id_categories
          FROM
            items
          WHERE
            status<='.STATUS_OWNSELL.' AND
            inuse=0 AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          GROUP BY
            id_categories
        ) AS inuse0 ON c.id = inuse0.id_categories
        LEFT JOIN (
          SELECT
            COUNT(id) AS amount,
            id_categories
          FROM
            items
          WHERE
            status<='.STATUS_OWNSELL.' AND
            inuse=1 AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          GROUP BY
            id_categories
        ) AS inuse1 ON c.id = inuse1.id_categories
        LEFT JOIN (
          SELECT
            COUNT(id) AS amount,
            id_categories
          FROM
            items
          WHERE
            status<='.STATUS_OWNSELL.' AND
            inuse=2 AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          GROUP BY
            id_categories
        ) AS inuse2 ON c.id = inuse2.id_categories
        LEFT JOIN (
          SELECT
            COUNT(id) AS amount,
            id_categories
          FROM
            items
          WHERE
            status<='.STATUS_OWNSELL.' AND
            inuse=3 AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          GROUP BY
            id_categories
        ) AS inuse3 ON c.id = inuse3.id_categories
        LEFT JOIN (
          SELECT
            COUNT(id) AS amount,
            id_categories
          FROM
            items
          WHERE
            status<='.STATUS_OWNSELL.' AND
            inuse=4 AND
            id_users="'.dbres($link, get_logged_in_user('id')).'"
          GROUP BY
            id_categories
        ) AS inuse4 ON c.id = inuse4.id_categories
      ORDER BY title');
    break;

  case 'criteria': # to display a category
    if (!is_logged_in()) break;
    $criterias = db_query($link, '
      SELECT
        *
      FROM
        criterias
      WHERE
        id="'.dbres($link, $id_criterias).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
    ');

    if (!count($criterias)) {
      $errors[] = 'Criteria not found';
      $view = 'criterias';
      break;
    }

    $criteria = reset($criterias);

    $items = db_query($link, '
      SELECT
        i.id AS id_items,
        rpi.id AS id_relations_criterias_items,
        i.title,
        0 AS criteria_item
      FROM
        items AS i,
        relations_criterias_items AS rpi
      WHERE
        i.id = rpi.id_items AND
        rpi.id_criterias = "'.dbres($link, $id_criterias).'" AND
        i.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
        rpi.id_users="'.dbres($link, get_logged_in_user('id')).'"
      ORDER BY
        title
      ');

    break;

  case 'file': # to get a file
    if (!is_logged_in()) break;

    # make sure id is specified
    if (!$id_files || !is_numeric($id_files)) {
      $errors[] = t('File ID must be specified.');
      $view = 'index';
      break;
    }

    # make sure it is ours
    $sql = '
      SELECT
        id
      FROM
        files
      WHERE
        id="'.dbres($link, $id_files).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    if (!count(db_query($link, $sql))) {
      $errors[] = t('Could not find the file, maybe it is not yours.');
      $view = 'index';
      break;
    }

    # get thumbnail?
    if ($type === 'thumbnail') {

      $fullpath = THUMBNAIL_DIR.(int)$id_files.'.jpg';
    # or get regular file?
    } else {
      $fullpath = FILE_DIR.(int)$id_files.'.jpg';
    }

    # make sure file exists
    if (!file_exists($fullpath)) {
      $errors[] = t('File not found.');
      $view = 'index';
      break;
    }

    # output header - JPEG data
    header('Content-Disposition: inline; filename='.basename($fullpath));
    header('Content-Type: image/jpeg');
    header('Content-Length: '.filesize($fullpath));

    # print file
    readfile($fullpath);
    die();

  case 'index': # to list items
    if (!is_logged_in()) break;

    $where = array();

    # is category id specified?
    if ($id_categories_find && is_numeric($id_categories_find)) {

      # make a where clause about it
      $where[] = '(i.id_categories="'.dbres($link, $id_categories_find).'")';

      # get categories as this will be printed
      $r = db_query($link, '
        SELECT
          *
        FROM
          categories
        WHERE
          id="'.dbres($link, $id_categories_find).'" AND
          id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
      $category = count($r) ? $r[0] : false;

    }

    # is item specified?
    if ($id_items && is_numeric($id_items)) {
      # make a where clause about it
      $where[] = 'id="'.dbres($link, $id_items).'"';
    }

    # is the material risk checked?
    if ($materialrisk) {
      $tmp = array();
      foreach ($mothmaterials as $k => $v) {
        $tmp[$k] = 'LOWER(materials) LIKE "% '.t($v).'%"';
      }

      $where[] = '('.implode(' OR ', $tmp).')';
    }

    # is status specified?
    if ($status_find !== false && is_numeric($status_find)) {
      # make a where clause about it
      $where[] = 'i.status="'.dbres($link, $status_find).'"';
    }	else {
      # default to own and own+sell
      $where[] = 'i.status<='.STATUS_OWNSELL;
    }

    # is find specified?

    if ($find) {

      # split it by the words
      $findwords = explode(' ', $find);

      # walk those words
      foreach ($findwords as $k => $v) {
        # check this word against title, description etc.
        $where[] = ' (LOWER(i.title) LIKE "%'.dbres($link, strtolower($v)).'%" OR LOWER(i.description) LIKE "%'.dbres($link, strtolower($v)).'%")';
      }
    }

    # base query - get all
    $sql = 'SELECT
          i.acquired,
          i.batteries_aa,
          i.batteries_aaa,
          i.batteries_c,
          i.batteries_d,
          i.batteries_e,
          i.batteries_3r12,
          i.materials,
          i.created,
          i.description,
          i.disposed,
          i.id,
          i.id_categories,
          i.id_files,
          i.inuse,
          i.price,
          i.source,
          i.status,
          i.title,
          i.updated,
          i.watt,
          i.watt_max,
          i.weight
        FROM items AS i';

    if ($id_locations) {
      $sql .= ', relations_items_locations AS r';
      $where[] = '(r.id_items = i.id AND r.id_locations="'.dbres($link, $id_locations).'")';

      # get location info
      $r = db_query($link, 'SELECT * FROM locations WHERE id="'.dbres($link, $id_locations).'"');
      $location = count($r) ? $r[0] : false;
    }

    $where[] = '(i.id_users="'.dbres($link, get_logged_in_user('id')).'")';

    # is there a where clause?
    if (count($where)) {
      $sql .= ' WHERE '.implode($where, ' AND ');
    }

    # run query
    $items = db_query($link, $sql);

    $items_amount = count($items);

    # add limitation of amount of hits
    $sql .= ' LIMIT '.dbres($link, (int)$start).', '.dbres($link, (int)$limit);

    # run query again
    $items = db_query($link, $sql);

    foreach ($items as $k => $v) {
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
          r.id_locations = l.id AND
          r.id_items="'.dbres($link, $v['id']).'" AND
          r.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
          l.id_users="'.dbres($link, get_logged_in_user('id')).'"
        ');
        # was there any matching locations?
      $items[$k]['locations'] = $locations;
    }

    # sort by from, because some trips may not know the to-date
    $packlists = db_query($link, '
      SELECT
        *
      FROM
        packlists
      WHERE
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ORDER BY
        `from` DESC
    ');

    $criterias = db_query($link, '
      SELECT
        *
      FROM
        criterias
      WHERE
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');

  #	die($sql);
    break;

  case 'location_history':
    if (!is_logged_in()) break;
    $sql = '
      SELECT
        *
      FROM
        location_history
      WHERE
        id="'.dbres($link, $id_items).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ';
    $location_history = db_query($link, $sql);
    break;

  case 'locations': # to display a list of locations
    if (!is_logged_in()) break;
    $sql = '
      SELECT
        l.id,
        l.id_files,
        l.title,
        l.contents,
        itemcount.amount AS item_amount,
        IFNULL(inuse0.amount, 0) AS inuse0_amount,
        IFNULL(inuse1.amount, 0) AS inuse1_amount,
        IFNULL(inuse2.amount, 0) AS inuse2_amount,
        IFNULL(inuse3.amount, 0) AS inuse3_amount,
        IFNULL(inuse4.amount, 0) AS inuse4_amount
      FROM
        locations AS l
        LEFT JOIN (
          SELECT
            COUNT(ir.id) AS amount,
            ir.id_locations,
            ir.status
          FROM
            (
              SELECT
                items.id,
                relations_items_locations.id_locations,
                items.status
              FROM
                items,
                relations_items_locations
              WHERE
                items.id = relations_items_locations.id_items AND
                items.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
                relations_items_locations.id_users="'.dbres($link, get_logged_in_user('id')).'"
            ) AS ir
          WHERE
            ir.status<='.STATUS_OWNSELL.'
          GROUP BY
            ir.id_locations
        ) AS itemcount ON l.id = itemcount.id_locations
        LEFT JOIN (
          SELECT
            COUNT(ir.id) AS amount,
            ir.id_locations,
            ir.inuse
          FROM
            (
              SELECT
                items.id,
                relations_items_locations.id_locations,
                items.inuse,
                items.status
              FROM
                items,
                relations_items_locations
              WHERE
                items.id = relations_items_locations.id_items AND
                items.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
                relations_items_locations.id_users="'.dbres($link, get_logged_in_user('id')).'"
              ) AS ir
          WHERE
            ir.inuse=0
          GROUP BY
            ir.id_locations
        ) AS inuse0 ON l.id = inuse0.id_locations
        LEFT JOIN (
          SELECT
            COUNT(ir.id) AS amount,
            ir.id_locations,
            ir.inuse
          FROM
            (
              SELECT
                items.id,
                relations_items_locations.id_locations,
                items.inuse,
                items.status
              FROM
                items,
                relations_items_locations
              WHERE
                items.id = relations_items_locations.id_items AND
                items.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
                relations_items_locations.id_users="'.dbres($link, get_logged_in_user('id')).'"
            ) AS ir
          WHERE
            ir.inuse=1
          GROUP BY
            ir.id_locations
        ) AS inuse1 ON l.id = inuse1.id_locations
        LEFT JOIN (
          SELECT
            COUNT(ir.id) AS amount,
            ir.id_locations,
            ir.inuse
          FROM
            (
              SELECT
                items.id,
                relations_items_locations.id_locations,
                items.inuse,
                items.status
              FROM
                items,
                relations_items_locations
              WHERE
                items.id = relations_items_locations.id_items AND
                items.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
                relations_items_locations.id_users="'.dbres($link, get_logged_in_user('id')).'"
            ) AS ir
          WHERE
            ir.inuse=2
          GROUP BY
            ir.id_locations

        ) AS inuse2 ON l.id = inuse2.id_locations
        LEFT JOIN (
          SELECT
            COUNT(ir.id) AS amount,
            ir.id_locations,
            ir.inuse
          FROM
            (
              SELECT
                items.id,
                relations_items_locations.id_locations,
                items.inuse,
                items.status
              FROM
                items,
                relations_items_locations
              WHERE
                items.id = relations_items_locations.id_items AND
                items.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
                relations_items_locations.id_users="'.dbres($link, get_logged_in_user('id')).'"
            ) AS ir
          WHERE
            ir.inuse=3
          GROUP BY
            ir.id_locations
        ) AS inuse3 ON l.id = inuse3.id_locations
        LEFT JOIN (
          SELECT
            COUNT(ir.id) AS amount,
            ir.id_locations,
            ir.inuse
          FROM
            (
              SELECT
                items.id,
                relations_items_locations.id_locations,
                items.inuse,
                items.status
              FROM
                items,
                relations_items_locations
              WHERE
                items.id = relations_items_locations.id_items AND
                items.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
                relations_items_locations.id_users="'.dbres($link, get_logged_in_user('id')).'"
            ) AS ir
          WHERE
            ir.inuse=4
          GROUP BY
            ir.id_locations
        ) AS inuse4 ON l.id = inuse4.id_locations
      WHERE
        l.id_users="'.dbres($link, get_logged_in_user('id')).'"
      ORDER BY l.title';
      $locations = db_query($link, $sql);
    break;

  case 'packlist': # to display a category
    if (!is_logged_in()) break;
    $packlists = db_query($link, '
      SELECT
        *
      FROM
        packlists
      WHERE
        id="'.dbres($link, $id_packlists).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      ');

    if (!count($packlists)) {
      $errors[] = t('Packlist not found');
      $view = 'packlists';
      break;
    }

    $packlist = reset($packlists);

    $items = db_query($link, '
      SELECT
        i.id AS id_items,
        rpi.id AS id_relations_packlists_items,
        rpi.comment AS relation_comment,
        i.title,
        i.weight,
        0 AS packlist_item,
        rpi.packed,
        rpi.unpacked,
        rpi.inuse
      FROM
        items as i,
        relations_packlists_items AS rpi
      WHERE
        i.id = rpi.id_items	AND
        rpi.id_packlists = "'.dbres($link, $id_packlists).'" AND
        i.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
        rpi.id_users="'.dbres($link, get_logged_in_user('id')).'"
      ORDER BY
        title
      ');

    # list of items only in this packlist
    $packlist_items = db_query($link, '
      SELECT
        id AS id_packlist_items,
        title,
        weight,
        1 AS packlist_item,
        packed,
        unpacked,
        inuse
      FROM
        packlist_items
      WHERE
        id_packlists="'.dbres($link, $id_packlists).'" AND
        id_users="'.dbres($link, get_logged_in_user('id')).'"
      '
    );

    # walk packlist items and add to items
    foreach ($packlist_items as $item) {
      $items[] = $item;
    }

    $criterias = db_query($link, '
      SELECT
        rcp.id_criterias,
        rcp.id_packlists,
        c.title,
        c.interval_days
      FROM
        criterias AS c,
        relations_criterias_packlists AS rcp
      WHERE
        c.id=rcp.id_criterias AND
        rcp.id_packlists = "'.dbres($link, $id_packlists).'" AND
        c.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
        rcp.id_users="'.dbres($link, get_logged_in_user('id')).'"
    ');

    if (count($criterias)) {

      $days = ((strtotime($packlist['to']) - strtotime($packlist['from']))  / (60 * 60 * 24)) + 1;

      foreach ($criterias as $k => $v) {

        $criterias[$k]['multiplier'] = 0;
        $criterias[$k]['missing_items'] = array();

        $daycounter = 0;
        # walk the days in the packlist
        for ($i=1; $i <= $days; $i++) {

          $daycounter += 1;

          # has the daycounter reached the criteria day interval
          if ($daycounter >= (int)$v['interval_days']) {
            # increment the multiplier
            $criterias[$k]['multiplier'] += 1;
            # reset the day counter
            $daycounter = 0;
          }
        }

        # is this criteria used according to days
        if ($criterias[$k]['multiplier']) {

          # find items in the criteria that may not be in the packlist
          $criterias[$k]['missing_items'] = db_query($link, '
            SELECT
              i.id AS id_items,
              i.title
            FROM
              items AS i,
              relations_criterias_items AS rci
            WHERE
              i.id = rci.id_items AND 
              rci.id_criterias="'.dbres($link, $criterias[$k]['id_criterias']).'" AND
              i.id NOT IN (
                SELECT
                  id_items
                FROM
                  relations_packlists_items
                WHERE
                  id_packlists="'.dbres($link, $id_packlists).'" AND
                  id_users="'.dbres($link, get_logged_in_user('id')).'"
              ) AND
              i.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
              rci.id_users="'.dbres($link, get_logged_in_user('id')).'"
            ');
        }
      }
    }

    break;

  case 'packlists': # to display packlists
    if (!is_logged_in()) break;

    $sql = 'SELECT
          p.from,
          p.id,
          p.title,
          p.to,
          irpi.item_amount AS item_amount,
          irpi.weight AS weight
        FROM
          packlists AS p
          LEFT JOIN (
            SELECT
              id_packlists,
              COUNT(i.id) AS item_amount,
              SUM(i.weight) AS weight
            FROM
              items AS i,
              relations_packlists_items AS rpi
            WHERE
              i.id = rpi.id_items AND
              i.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
              rpi.id_users="'.dbres($link, get_logged_in_user('id')).'"
            GROUP BY
              rpi.id_packlists
          ) AS irpi ON irpi.id_packlists = p.id AND p.id_users="'.dbres($link, get_logged_in_user('id')).'"
        WHERE
          p.id_users="'.dbres($link, get_logged_in_user('id')).'"
        ORDER BY
          p.from DESC
        ';

    $packlists = db_query($link, $sql);
    break;

  case 'criterias': # to display criterias
    if (!is_logged_in()) break;

    $sql = 'SELECT
          c.id,
          c.title,
          c.interval_days,
          c.add_to_new_packlists,
          IFNULL(irci.item_amount, 0) AS item_amount
        FROM
          criterias AS c
          LEFT JOIN (
            SELECT
              id_criterias,
              COUNT(i.id) AS item_amount
            FROM
              items AS i,
              relations_criterias_items AS rci
            WHERE
              i.id = rci.id_items AND
              i.id_users="'.dbres($link, get_logged_in_user('id')).'" AND
              rci.id_users="'.dbres($link, get_logged_in_user('id')).'"
            GROUP BY
              rci.id_criterias
          ) AS irci ON irci.id_criterias = c.id AND c.id_users="'.dbres($link, get_logged_in_user('id')).'"
        ORDER BY
          c.id DESC
        ';

    $criterias = db_query($link, $sql);
    break;

  case 'users': # to display a list of users
    if (!is_logged_in()) break;
    $users = db_query($link, '
      SELECT
        id,
        id_visum,
        nickname,
        username
      FROM
        users
      ORDER BY id');
    break;
}

?>
