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

if (!isset($view)) die();

$categories_find = db_query($link, 'SELECT * FROM categories ORDER BY title');

$item_amount = db_query($link, 'SELECT COUNT(id) AS amount FROM items WHERE status<='.STATUS_OWNSELL);

# check out what view we have
switch ($view) {

	case 'edit_category': # to edit a category
		if (!is_logged_in()) break;

		$category = false;

		# is item id specified?
		if ($id_categories) {
			# try to get that item
			$categories = db_query($link, 'SELECT * FROM categories WHERE id="'.dbres($link, $id_categories).'"');
			# was there any matching items?
			if (count($categories)) {
				# then take the first of it
				$category = $categories[0];
			}
		}
		break;

	case 'edit_item': # to edit an item
		if (!is_logged_in()) break;

		$categories = db_query($link, 'SELECT * FROM categories ORDER BY title');

		$item = false;

		# is item id specified?
		if ($id_items) {
			# try to get that item
			$items = db_query($link, 'SELECT * FROM items WHERE id="'.dbres($link, $id_items).'"');
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
					r.id_locations = l.id
					AND
					r.id_items="'.dbres($link, $id_items).'"
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
			$locations = db_query($link, 'SELECT * FROM locations WHERE id="'.dbres($link, $id_locations).'"');
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
			$packlists = db_query($link, 'SELECT * FROM packlists WHERE id="'.dbres($link, $id_packlists).'"');
			# was there any matching items?
			if (count($packlists)) {
				# then take the first of it
				$packlist = $packlists[0];
			}
			# sort by from, because some trips may not know the to-date
			$packlists_copy = db_query($link, 'SELECT * FROM packlists WHERE NOT id="'.dbres($link, $id_packlists).'" ORDER BY `from` DESC');
		} else {
			# sort by from, because some trips may not know the to-date
			$packlists_copy = db_query($link, 'SELECT * FROM packlists ORDER BY `from` DESC');
		}

		break;

	case 'category': # to display a category
		if (!is_logged_in()) break;
		$category = db_query($link, 'SELECT * FROM categories WHERE id="'.dbres($link, $id_categories).'"');
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
					SELECT COUNT(id) AS amount, id_categories FROM items WHERE status<='.STATUS_OWNSELL.' GROUP BY id_categories
				) AS itemcount ON c.id = itemcount.id_categories
				LEFT JOIN (
					SELECT COUNT(id) AS amount, id_categories FROM items WHERE status<='.STATUS_OWNSELL.' AND inuse=0 GROUP BY id_categories
				) AS inuse0 ON c.id = inuse0.id_categories
				LEFT JOIN (
					SELECT COUNT(id) AS amount, id_categories FROM items WHERE status<='.STATUS_OWNSELL.' AND inuse=1 GROUP BY id_categories
				) AS inuse1 ON c.id = inuse1.id_categories
				LEFT JOIN (
					SELECT COUNT(id) AS amount, id_categories FROM items WHERE status<='.STATUS_OWNSELL.' AND inuse=2 GROUP BY id_categories
				) AS inuse2 ON c.id = inuse2.id_categories
				LEFT JOIN (
					SELECT COUNT(id) AS amount, id_categories FROM items WHERE status<='.STATUS_OWNSELL.' AND inuse=3 GROUP BY id_categories
				) AS inuse3 ON c.id = inuse3.id_categories
				LEFT JOIN (
					SELECT COUNT(id) AS amount, id_categories FROM items WHERE status<='.STATUS_OWNSELL.' AND inuse=4 GROUP BY id_categories
				) AS inuse4 ON c.id = inuse4.id_categories
			ORDER BY title');
		break;

	case 'file': # to get a file
		if (!is_logged_in()) break;

		# make sure id is specified
		if (!$id_files || !is_numeric($id_files)) die('File ID must be specified.');

		# get thumbnail?
		if ($type === 'thumbnail') {

			$fullpath = THUMBNAIL_DIR.(int)$id_files.'.jpg';
		# or get regular file?
		} else {
			$fullpath = FILE_DIR.(int)$id_files.'.jpg';
		}

		# make sire file exists
		if (!file_exists($fullpath)) die('File not found.');

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
			$r = db_query($link, 'SELECT * FROM categories WHERE id="'.dbres($link, $id_categories_find).'"');
			$category = count($r) ? $r[0] : false;

		}

		# is item specified?
		if ($id_items && is_numeric($id_items)) {
			# make a where clause about it
			$where[] = 'id="'.dbres($link, $id_items).'"';
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
					r.id_locations = l.id
					AND
					r.id_items="'.dbres($link, $v['id']).'"
				');
				# was there any matching locations?
			$items[$k]['locations'] = $locations;
		}

		# sort by from, because some trips may not know the to-date
		$packlists = db_query($link, 'SELECT * FROM packlists ORDER BY `from` DESC');

	#	die($sql);
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
						(SELECT items.id, relations_items_locations.id_locations, items.status FROM items, relations_items_locations WHERE items.id = relations_items_locations.id_items) AS ir
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
						(SELECT items.id, relations_items_locations.id_locations, items.inuse, items.status FROM items, relations_items_locations WHERE items.id = relations_items_locations.id_items) AS ir
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
						(SELECT items.id, relations_items_locations.id_locations, items.inuse, items.status FROM items, relations_items_locations WHERE items.id = relations_items_locations.id_items) AS ir
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
						(SELECT items.id, relations_items_locations.id_locations, items.inuse, items.status FROM items, relations_items_locations WHERE items.id = relations_items_locations.id_items) AS ir
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
						(SELECT items.id, relations_items_locations.id_locations, items.inuse, items.status FROM items, relations_items_locations WHERE items.id = relations_items_locations.id_items) AS ir
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
						(SELECT items.id, relations_items_locations.id_locations, items.inuse, items.status FROM items, relations_items_locations WHERE items.id = relations_items_locations.id_items) AS ir
					WHERE
						ir.inuse=4
					GROUP BY
						ir.id_locations

				) AS inuse4 ON l.id = inuse4.id_locations
			ORDER BY l.title';
			# die($sql);

			$locations = db_query($link, $sql);

		break;

	case 'packlist': # to display a category
		if (!is_logged_in()) break;
		$packlists = db_query($link, 'SELECT * FROM packlists WHERE id="'.dbres($link, $id_packlists).'"');

		if (!count($packlists)) die('Packlist not found');
		$packlist = reset($packlists);

		$items = db_query($link, '
			SELECT
				i.id AS id_items,
				rpi.id AS id_relations_packlists_items,
				i.title,
				i.weight,
				0 AS packlist_item,
				rpi.packed,
				rpi.inuse
			FROM
				items as i,
				relations_packlists_items AS rpi
			WHERE
				i.id = rpi.id_items
				AND
				rpi.id_packlists = "'.dbres($link, $id_packlists).'"
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
				inuse
			FROM packlist_items
			WHERE id_packlists="'.dbres($link, $id_packlists).'"'
		);

		# walk packlist items and add to items
		foreach ($packlist_items as $item) {
			$items[] = $item;
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
							i.id = rpi.id_items
						GROUP BY
							rpi.id_packlists
					) AS irpi ON irpi.id_packlists = p.id
				ORDER BY
					p.from DESC
				';

		$packlists = db_query($link, $sql);
		break;

}

?>
