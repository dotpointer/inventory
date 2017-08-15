<?php

	# changelog
	# 2015-04-04 15:05:35 - Initial version
	# 2015-04-05 17:40:48
	# 2015-04-06 21:59:40 - putting it into use
	# 2015-04-08 22:08:23 - styling
	# 2015-04-10 22:04:36
	# 2015-04-11 22:51:20 - location, usage
	# 2015-04-12 14:29:13 - login
	# 2015-04-13 20:16:14 - text edits
	# 2015-04-21 19:44:56 - separating find parameters from the rest
	# 2015-04-24 11:54:49 - bugfix for inuse and status
	# 2015-04-25 18:24:08 - bugfix for status
	# 2015-05-06 22:13:53 - adding location logic
	# 2015-05-09 18:35:58 - adding location contents field
	# 2015-07-31 01:07:35 - hiding empty but really not empty rows on categories and locations
	# 2015-07-31 01:08:14
	# 2015-08-25 11:17:24
	# 2015-10-23 12:53:35 - adding batteries AA, AAA, C, D, E and 3R12
	# 2015-12-21 19:48:39 - find text bugfix
	# 2016-02-02 18:43:07 - minor fixes
	# 2016-03-09 00:41:58 - updating css
	# 2016-03-11 12:28:19 - cleanup, minor fixes
	# 2016-03-27 17:47:43 - adding files table
	# 2016-03-27 18:41:34 - adding images to locations
	# 2017-01-26 21:34:23 - adding materials column
	# 2017-02-01 18:31:36 - dotpointer domain edit
	# 2017-04-12 17:08:09 - adding quick button 100% cotton for material
	# 2017-05-13 15:28:45 - adding weight
	# 2017-05-13 17:49:28 - adding packlist
	# 2017-05-13 23:06:12 - adding packlist items
	# 2017-05-21 20:41:41 - adding packlist inuse

	# get required functionality
	require_once('include/functions.php');

	$acquired = isset($_REQUEST['acquired']) ? $_REQUEST['acquired'] : false;
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;
	$batteries_3r12 = isset($_REQUEST['batteries_3r12']) ? $_REQUEST['batteries_3r12'] : false;
	$batteries_aaa = isset($_REQUEST['batteries_aaa']) ? $_REQUEST['batteries_aaa'] : false;
	$batteries_aa = isset($_REQUEST['batteries_aa']) ? $_REQUEST['batteries_aa'] : false;
	$batteries_c = isset($_REQUEST['batteries_c']) ? $_REQUEST['batteries_c'] : false;
	$batteries_d = isset($_REQUEST['batteries_d']) ? $_REQUEST['batteries_d'] : false;
	$batteries_e = isset($_REQUEST['batteries_e']) ? $_REQUEST['batteries_e'] : false;
	$materials = isset($_REQUEST['materials']) ? $_REQUEST['materials'] : false;
	$category = isset($_REQUEST['category']) ? $_REQUEST['category'] : false;
	$contents = isset($_REQUEST['contents']) ? $_REQUEST['contents'] : false;
	$description = isset($_REQUEST['description']) ? $_REQUEST['description'] : false;
	$disposed = isset($_REQUEST['disposed']) ? $_REQUEST['disposed'] : false;
	$find = isset($_REQUEST['find']) ? $_REQUEST['find'] : false;
	$id_categories_find = isset($_REQUEST['id_categories_find']) ? $_REQUEST['id_categories_find'] : false;
	$id_categories = isset($_REQUEST['id_categories']) ? $_REQUEST['id_categories'] : false;
	$id_files = isset($_REQUEST['id_files']) ? $_REQUEST['id_files'] : false;
	$id_items = isset($_REQUEST['id_items']) ? $_REQUEST['id_items'] : false;
	$id_locations = isset($_REQUEST['id_locations']) ? $_REQUEST['id_locations'] : false;
	$id_packlists = isset($_REQUEST['id_packlists']) ? $_REQUEST['id_packlists'] : false;
	$id_packlist_items = isset($_REQUEST['id_packlist_items']) ? $_REQUEST['id_packlist_items'] : false;
	$id_relations_packlists_items = isset($_REQUEST['id_relations_packlists_items']) ? $_REQUEST['id_relations_packlists_items'] : false;
	$inuse = isset($_REQUEST['inuse']) ? $_REQUEST['inuse'] : false;
	$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 25;
	$location = isset($_REQUEST['location']) ? $_REQUEST['location'] : false;
	$packed = isset($_REQUEST['packed']) ? $_REQUEST['packed'] : false;
	$price = isset($_REQUEST['price']) ? $_REQUEST['price'] : false;
	$source = isset($_REQUEST['source']) ? $_REQUEST['source'] : false;
	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
	$status_find = isset($_REQUEST['status_find']) ? $_REQUEST['status_find'] : false;
	$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : false;
	$ticket = isset($_REQUEST['ticket']) ? $_REQUEST['ticket'] : false;
	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : false;
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : false;
	$watt = isset($_REQUEST['watt']) ? $_REQUEST['watt'] : false;
	$watt_max = isset($_REQUEST['watt_max']) ? $_REQUEST['watt_max'] : false;
	$weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : false;
	$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : false;

	# action management
	require_once('index-action.php');

	# view management
	require_once('index-view.php');

	# send user to login page if not logged in
	if (!is_logged_in()) {
		header('Location: http://www.<?php echo BASE_DOMAINNAME?>/?section=visum&id_sites='.ID_VISUM);
		die();
	}
?><html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>Inventarier<?php echo is_logged_in() && isset($item_amount[0], $item_amount[0]['amount']) ? ' - '.$item_amount[0]['amount'].' st' : ''?></title>
	<link rel="stylesheet" href="include/style.css" type="text/css" media="screen" />

	<script type="text/javascript" src="include/jquery-2.1.1.min.js"></script>
	<script type="text/javascript">
		var
			i = {
				action: '<?php echo $action?>',
				msg: {
					confirm_deletion: 'Är du säker på att du vill radera? Detta kan inte ångras.',
				},
				time_diff: <?php echo microtime(true) * 1000; ?>  - (new Date().getTime()),
				view: '<?php echo $view?>'
			};
	</script>
	<script type="text/javascript" src="include/load.js"></script>

</head>
<body>
	<ul>
		<li><a href="?view=index">Inventarier</a></li>
<?php 		if (is_logged_in()) { ?>
		<li><a href="?view=categories">Kategorier</a></li>
		<li><a href="?view=locations">Platser</a></li>
		<li><a href="?view=edit_item">Ny</a></li>
		<li><a href="?view=packlists">Packlistor</a></li>

		<li>
			<form action="?" method="post">
				<fieldset>
					<input type="hidden" name="view" value="index">
					<input type="text" class="text" name="find" value="<?php echo $find;?>" placeholder="Sök">
					<select name="id_categories_find">
						<option>-- Kategori</option>
<?php 		# walk categories one by one
			foreach ($categories_find as $k => $v) {
				$selected = (int)$id_categories_find === (int)$v['id'] ? ' selected="selected"' : '';

?>
						<option value="<?php echo $v['id'] ?>"<?php echo $selected?>><?php echo $v['title'] ?></option>
<?php
			} # eof-foreach-categories
?>
					</select>

<select name="status_find">
						<option>-- Status</option>
<?php 		# walk statuses one by one
			foreach ($statuses as $k => $v) {
				$selected = (int)$status_find === (int)$k ? ' selected="selected"' : '';
?>
						<option value="<?php echo $k ?>"<?php echo $selected?>><?php echo $v['text'] ?></option>
<?php
			} # eof-foreach-statuses
?>
					</select>

					<input type="submit" name="submit" class="submit" value="Sök">
					<br>
				</fieldset>
			</form>
		</li>
<?php		} # if-is-logged-in ?>
	</ul>
<?php
	# find out what view to show
	switch ($view) {
		default:
			if (!is_logged_in()) {
				print_login();
				break;
			}
			break;

		case 'edit_category': # to insert or update a category
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
	<h2>Redigera kategori<?php
		if (isset($category['id'])) {
			echo ' #'.$category['id'];
		}
?></h2>
	<form action="?action=insert_update_category" method="post">
		<fieldset>
			<input type="hidden" name="id_categories" value="<?php echo isset($category['id']) ? $category['id'] : ''?>">
			<label for="title">Titel</label><input class="text" type="text" name="title" value="<?php echo isset($category['title']) ? $category['title'] : ''?>"><br>
			<input class="submit" type="submit" name="submit" value="Spara">
		</fieldset>
	</form>
<?php
			break;

		case 'edit_item': # to insert or update an inventory item
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
	<h2>Redigera inventarie<?php

		if (isset($item['id'])) {
			echo ' #'.$item['id'];
		}

?></h2>
	<form action="?action=insert_update_item" method="post" enctype="multipart/form-data">
		<fieldset>
			<input type="hidden" name="MAX_FILE_SIZE" value="10737418240" />
			<input type="hidden" name="id_items" value="<?php echo isset($item['id']) ? $item['id'] : ''?>">
			<div class="row">
				<label for="title">Titel</label>
				<input class="text" type="text" name="title" value="<?php echo isset($item['title']) ? $item['title'] : ''?>">
			</div>
			<div class="row">
				<label for="description">Beskrivning</label>
				<textarea name="description"><?php echo isset($item['description']) ? $item['description'] : ''?></textarea>
				</div>
			<div class="row">
				<label for="batteries_aa">AA-batterier:</label>
				<input class="text" type="text" name="batteries_aa" value="<?php echo isset($item['batteries_aa']) ? $item['batteries_aa'] : ''?>">
			</div>
			<div class="row">
				<label for="batteries_aaa">AAA-batterier:</label>
				<input class="text" type="text" name="batteries_aaa" value="<?php echo isset($item['batteries_aaa']) ? $item['batteries_aaa'] : ''?>"></div>
			<div class="row">
				<label for="batteries_c">C-batterier:</label>
				<input class="text" type="text" name="batteries_c" value="<?php echo isset($item['batteries_c']) ? $item['batteries_c'] : ''?>">
			</div>
			<div class="row">
				<label for="batteries_d">D-batterier:</label>
				<input class="text" type="text" name="batteries_d" value="<?php echo isset($item['batteries_d']) ? $item['batteries_d'] : ''?>">
			</div>
			<div class="row">
				<label for="batteries_3r12">3R12-batterier:</label>
				<input class="text" type="text" name="batteries_3r12" value="<?php echo isset($item['batteries_3r12']) ? $item['batteries_3r12'] : ''?>">
			</div>
			<div class="row">
				<label for="batteries_e">(9V) E-batterier:</label>
				<input class="text" type="text" name="batteries_e" value="<?php echo isset($item['batteries_e']) ? $item['batteries_e'] : ''?>">
			</div>
			<div class="row">
				<label for="materials">Material:</label>
				<input class="text" type="text" name="materials" value="<?php echo isset($item['materials']) ? $item['materials'] : ''?>">
				<button id="button_material_100_cotton">100% bomull</button>
			</div>
			<div class="row">
				<label for="watt">Watt:</label>
				<input class="text" type="text" name="watt" value="<?php echo isset($item['watt']) ? $item['watt'] : ''?>">
			</div>
			<div class="row">
				<label for="watt_max">Watt max:</label>
				<input class="text" type="text" name="watt_max" value="<?php echo isset($item['watt_max']) ? $item['watt_max'] : ''?>">
			</div>
			<div class="row">
				<label for="weight">Vikt:</label>
				<input class="text" type="text" name="weight" value="<?php echo isset($item['weight']) ? $item['weight'] : ''?>">g
			</div>
			<div class="row">
				<label for="source">Fått från:</label>
				<input class="text" type="text" name="source" value="<?php echo isset($item['source']) ? $item['source'] : ''?>">
			</div>
			<div class="row">
				<label for="location">Placering:</label>
				<input class="text" type="text" name="location" value="<?php echo isset($item['location']) ? $item['location'] : ($location !== false ? $location : 'Uttervik')?>">
			</div>
			<div class="row">
				<label for="price">Inköpspris:</label>
				<input class="text" type="text" name="price" value="<?php echo isset($item['price']) ? $item['price'] : ''?>">
			</div>
			<div class="row">
				<label for="file">JPEG-bild</label>
				<input class="file" type="file" name="file">
<?php		if (isset($item['id_files']) && file_exists(FILE_DIR.$item['id_files'].'.jpg')) { ?>
				<a href="?view=file&amp;id_files=<?php echo $item['id_files']?>"><img src="?view=file&amp;type=thumbnail&amp;id_files=<?php echo $item['id_files']?>"></a><br><br>
				<input type="hidden" name="id_files" value="<?php echo isset($item['price']) ? $item['id_files'] : ''?>">
<?php 		} ?>
			</div>
			<div class="row">
				<label for="id_categories">Kategori</label>
				<select name="id_categories">
					<option>-- Kategori</option>
					<option value="-1">-- Ny kategori</option>
<?php
				# walk the categories
				foreach ($categories as $k => $v) {
					# is id_categories set on the item and that id matches this category id, then make it selected
					$selected = (((int)$id_categories === (int)$v['id']) || (isset($item['id_categories']) && (int)$v['id'] === (int)$item['id_categories'])) ? 'selected="selected"' : '';
					# print this item
?>

					<option value="<?php echo $v['id']?>"<?php echo $selected?>><?php echo $v['title']?></option>
<?php
				} # eof-foreach-categories
?>

				</select>
				<input class="text" id="category" name="category" placeholder="Ny kategori">
			</div>
			<div class="row">
				<label for="acquired">Datum förvärvad</label>
				<input class="text" type="text" name="acquired" value="<?php echo isset($item['acquired']) ? $item['acquired'] : ''?>">
				<button id="button_aquired_date">Sätt datum</button>
			</div>
			<div class="row">
				<label for="disposed">Datum avyttrad</label>
				<input class="text" type="text" name="disposed" value="<?php echo isset($item['disposed']) ? $item['disposed'] : ''?>">
				<button id="button_disposed_date">Sätt datum</button>
			</div>
			<div class="row">
				<label for="status">Status:</label>
				<select name="status">
<?php
				# walk statuses
				foreach ($statuses as $k => $v) {
					$selected = isset($item['status']) && (int)$k === (int)$item['status'] ? ' selected="selected"' : ($status!== false && (int)$status === (int)$k ? 'selected="selected"' : '');
					# print option
?>
					<option value="<?php echo $k;?>"<?php echo $selected?>><?php echo $v['text']?></option>
<?php
				}
?>
				</select>
				<button id="button_status_sale">Säljes</button>
				<button id="button_status_sold">Såld</button>
		</div>


			<div class="row">
				<label for="inuse">Används:</label>
				<select name="inuse">
				<option value="<?php echo $k;?>"<?php echo $selected?>>-- Används</option>
	<?php
					# walk inuse
					foreach ($usage as $k => $v) {
						$selected = isset($item['inuse']) && (int)$k === (int)$item['inuse'] ? ' selected="selected"' : ($inuse !== false && (int)$inuse === (int)$k ? 'selected="selected"' : '');
						# print option
	?>
					<option value="<?php echo $k;?>"<?php echo $selected?>><?php echo $v?></option>
	<?php
					}
	?>

				</select>
			</div>
			<div class="row">
				<label for="view">Nästa sida:</label>
				<select name="view">
					<option value="index"<?php echo $view==='edit_item' ? ' selected="selected"' : ''?>>Visa redigerad inventarie</option>
					<option value="edit_item"<?php echo $view==='edit_item' ? ' selected="selected"' : ''?>>Ny inventarie</option>
				</select>
			</div>
			<input class="submit" type="submit" name="submit" value="Spara">
		</fieldset>
	</form>
<?php
			break;

		case 'edit_location': # to insert or update a location
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
	<h2>Redigera plats<?php
		if (isset($location['id'])) {
			echo ' #'.$location['id'];
		}
?></h2>
	<form action="?action=insert_update_location" method="post" enctype="multipart/form-data">
		<fieldset>
			<input type="hidden" name="view" value="locations">
			<input type="hidden" name="id_locations" value="<?php echo isset($location['id']) ? $location['id'] : ''?>">
			<input type="hidden" name="MAX_FILE_SIZE" value="10737418240" />

			<div class="row">
				<label for="title">Titel</label><input class="text" type="text" name="title" value="<?php echo isset($location['title']) ? $location['title'] : ''?>"><br>
			</div>
			<div class="row">
				<label for="contents">Innehåll</label><input class="text" type="text" name="contents" value="<?php echo isset($location['contents']) ? $location['contents'] : ''?>"><br>
			</div>
			<div class="row">
				<label for="file">JPEG-bild</label>
				<input class="file" type="file" name="file">
<?php		if (isset($item['id_files']) && file_exists(FILE_DIR.$item['id_files'].'.jpg')) { ?>
				<a href="?view=file&amp;id_files=<?php echo $item['id_files']?>"><img src="?view=file&amp;type=thumbnail&amp;id_files=<?php echo $item['id_files']?>"></a><br><br>
				<input type="hidden" name="id_files" value="<?php echo isset($item['price']) ? $item['id_files'] : ''?>">
<?php 		} ?>
			</div>
			<div class="row">
				<input class="submit" type="submit" name="submit" value="Spara">
			</div>
		</fieldset>
	</form>
<?php
			break;

		case 'edit_packlist': # to insert or update a packlist
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
	<h2>Redigera packlista<?php
		if (isset($packlist['id'])) {
			echo ' #'.$packlist['id'];
		}
?></h2>
	<form action="?action=insert_update_packlist" method="post">
		<fieldset>
			<input type="hidden" name="view" value="packlists">
			<input type="hidden" name="id_packlists" value="<?php echo isset($packlist['id']) ? $packlist['id'] : ''?>">

			<div class="row">
				<label for="title">Titel</label><input class="text" type="text" name="title" value="<?php echo isset($packlist['title']) ? $packlist['title'] : ''?>"><br>
			</div>
			<div class="row">
				<input class="submit" type="submit" name="submit" value="Spara">
			</div>
		</fieldset>
	</form>
<?php
			break;

		case 'index': # to list items
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
		<h2>Inventarieförteckning<?php
			if (isset($category) && is_array($category)) {
				echo ' för kategori '.$category['title'];
			}

			if (isset($location) && is_array($location)) {
				echo ' för platsen '.$location['title'];
			}

		?></h2>
		<p>Sökningen resulterade i <?php echo $items_amount; ?> träffar.</p>

		<p class="browse">
			<a href="?view=index&amp;find=<?php echo $find?>&amp;id_locations=<?php echo $id_locations?>&amp;id_categories_find=<?php echo $id_categories_find?>&amp;status_find=<?php echo $status_find?>&amp;start=<?php echo ($start-$limit) > 0 ? $start-$limit : 0 ?>&amp;limit=<?php echo $limit?>">&lt;&lt; <?php echo $start ?></a>
			 -
			<a href="?view=index&amp;find=<?php echo $find?>&amp;id_locations=<?php echo $id_locations?>&amp;id_categories_find=<?php echo $id_categories_find?>&amp;status_find=<?php echo $status_find?>&amp;start=<?php echo $start+$limit?>&amp;limit=<?php echo $limit?>"><?php echo count($items) < (int)$start+(int)$limit && $start < count($items) ? count($items) : (int)$start+(int)$limit;  ?> &gt;&gt;</a>

		</p>

		<table>
			<thead>
				<tr>
					<th class="image">Bild</th>
					<th>Beskrivning</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
<?php 		foreach ($items as $k => $v) { ?>
				<tr>
					<td class="image">
						<div class="image"><div class="id"><a href="?view=file&amp;id_items=<?php echo $v['id']?>">#<?php echo $v['id']?></a></div><?php
					if (file_exists(THUMBNAIL_DIR.$v['id_files'].'.jpg')) {
						?><a href="?view=file&amp;id_files=<?php echo $v['id_files']?>"><img src="?view=file&amp;type=thumbnail&amp;id_files=<?php echo $v['id_files']?>"></a><?php
					} else {
						?><img src="images/nophoto_320x240.png"><?php
					}
					?>
						</div>
					</td>
					<td class="description">
						<h4><a href="?view=index&amp;id_items=<?php echo $v['id']?>">#<?php echo $v['id']?> - <?php echo $v['title']?></a></h4>
						<p><?php echo strlen(trim($v['description'])) ? nl2br($v['description']) : 'Ingen beskrivning.'; ?></p>
						<p class="subdata">
							Fått från: <?php echo $v['source']?>
							<br>
							Placering: <?php foreach ($v['locations'] as $loc_key => $loc) {

							if ($loc_key) {
								echo ' + ';
							}

							?><a href="?view=index&amp;id_locations=<?php echo $loc['id_locations']?>"><?php echo $loc['title'] ?></a><?php
						}
						?>
							<?php if ($v['watt']) { ?>
							<br>
							Watt: <?php echo $v['watt']?>-<?php echo $v['watt_max']?>
							<?php } ?>

							<?php if ($v['weight']) { ?>
							<br>
							Vikt: <?php echo $v['weight']?>g
							<?php } ?>

							<?php if ($v['batteries_aa']) { ?>
							<br>
							AA-batterier: <?php echo $v['batteries_aa']?> st
							<?php } ?>

							<?php if ($v['batteries_aaa']) { ?>
							<br>
							AAA-batterier: <?php echo $v['batteries_aaa']?> st
							<?php } ?>

							<?php if ($v['batteries_c']) { ?>
							<br>
							C-batterier: <?php echo $v['batteries_c']?> st
							<?php } ?>

							<?php if ($v['batteries_d']) { ?>
							<br>
							D-batterier: <?php echo $v['batteries_d']?> st
							<?php } ?>

							<?php if ($v['batteries_e']) { ?>
							<br>
							(9V) E-batterier: <?php echo $v['batteries_e']?> st
							<?php } ?>

							<?php if ($v['batteries_3r12']) { ?>
							<br>
							3R12-batterier: <?php echo $v['batteries_3r12']?> st
							<?php } ?>

							<?php if ($v['materials']) { ?>
							<br>
							Material: <?php echo $v['materials']?>
							<?php } ?>

							<form class="form_add_item_to_packlist">
								<div>
									<label>Lägg till packlista</label>
									<input type="hidden" value="<?php echo $v['id'] ?>" name="id_items" >
									<select>
										<?php foreach($packlists as $vx) { ?>
										<option value="<?php echo $vx['id']; ?>"><?php echo $vx['title']; ?></option>
										<?php } ?>
									</select>
									<input type="submit" value="OK" >
								</div>
							</form>
						</p>
					</td>
					<td>
						<div class="status <?php echo $statuses[$v['status']]['name']; ?>"><?php echo $statuses[$v['status']]['text']; ?></div>

						<div class="inuse inuse<?php echo $v['inuse']; ?>"><?php echo $usage[$v['inuse']]; ?></div>
						<br><br>

						<a href="?view=edit_item&amp;id_items=<?php echo $v['id']?>">Redigera</a>
					</td>
				</tr>
<?php
			} # eof-foreach-items
			reset($items);
?>
			</tbody>
		</table>
		<p class="browse">
			<a href="?view=index&amp;find=<?php echo $find?>&amp;id_locations=<?php echo $id_locations?>&amp;id_categories_find=<?php echo $id_categories_find?>&amp;status_find=<?php echo $status_find?>&amp;start=<?php echo ($start-$limit) > 0 ? $start-$limit : 0 ?>&amp;limit=<?php echo $limit?>">&lt;&lt; <?php echo $start ?></a>
			 -
 			<a href="?view=index&amp;find=<?php echo $find?>&amp;id_locations=<?php echo $id_locations?>&amp;id_categories_find=<?php echo $id_categories_find?>&amp;status_find=<?php echo $status_find?>&amp;start=<?php echo $start+$limit?>&amp;limit=<?php echo $limit?>"><?php echo count($items) < (int)$start+(int)$limit && $start < count($items) ? count($items) : (int)$start+(int)$limit;  ?> &gt;&gt;</a>

		</p>
<?php
			break;

		case 'categories': # to list item categories
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
		<h2>Kategorier</h2>
		<p class="actions">
			<a href="?view=edit_category">Ny kategori</a>
		</p>
		<table>
			<thead>
				<tr>
					<th class="id">#</th>
					<th>Kategori</th>
					<th class="counter">Objekt</th>
					<th>Användning</th>
					<th class="manage">Hantera</th>
				</tr>
			</thead>
<?php 		# walk categories one by one
			$x = 0;
			foreach ($categories as $k => $v) {
				$x += $v['item_amount'];
			}
?>

			<tbody>
<?php 		# walk categories one by one
			$total_amounts = array();
			$total_total_amounts = 0;
			foreach ($categories as $k => $v) {

				$i=0;
				$amounts = array();
				$total = 0;
				# walk to find in-use values
				while(isset($v['inuse'.$i.'_amount'])) {
					# skip zero values
					if ((int)$v['inuse'.$i.'_amount'] <=0) {
						$i++;
						continue;
						}
					$amounts[$i] = (int)$v['inuse'.$i.'_amount'];
					if (!isset($total_amounts[$i])) $total_amounts[$i] = 0;
					$total_amounts[$i] +=  (int)$v['inuse'.$i.'_amount'];

					$total += $amounts[$i];
					$total_total_amounts += $amounts[$i];
					++$i;

				}

				# is the item total above zero, but no available - then there are no active items, go next
				# note that this differ from a location that has no items at all - it is unused
				if ($total > 0 && (int)$v['item_amount'] < 1) continue;

				if ($total < 1) continue;

				# var_dump($v['id'], $total, (int)$v['item_amount']);

?>
				<tr>
					<td>
						<?php echo $v['id']; ?>
					</td>
					<td>
						<a href="?view=index&amp;id_categories_find=<?php echo $v['id'] ?>"><?php echo $v['title'] ?></a>
					</td>
					<td>
						<?php echo $v['item_amount'] ?> st
					</td>
					<td class="progressbar">
						<div class="inuse_progressbars">
<?php


						foreach ($amounts as $k => $v) {
							?><div title="<?php echo $usage[$k]?>" class="inuse_progressbar<?php echo $k?>" style="width: <?php echo ($v / $total) * 100; ?>%"><?php echo $v ?> (<?php echo round(($v / $total) * 100,2); ?>%)</div><?php
						}


?>
					</td>

					<td class="manage">
						<a href="?view=edit_category&amp;id_categories=<?php echo $v['id'] ?>">Redigera</a>
					</td>

				</tr>
<?php
			} # eof-foreach-categories
?>
			</tbody>

<tfoot>
				<tr>
					<td>Totalt</td>
					<td></td>
					<td><?php echo $x; ?> st</td>
					<td class="progressbar">
						<div class="inuse_progressbars">

<?php
						foreach ($total_amounts as $k => $v) {
							?><div title="<?php echo $usage[$k]?>" class="inuse_progressbar<?php echo $k?>" style="width: <?php echo ($v / $total_total_amounts) * 100; ?>%"><?php echo $v ?> (<?php echo round(($v / $total_total_amounts) * 100,2); ?>%)</div><?php
						}
?>
						</div>

					</td>
					<td></td>
				</tr>
			</tfoot>
		</table>
<?php
			break;

		case 'locations': # to list item locations
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
		<h2>Platser<div class="action"><a href="?view=edit_location">Ny</a></div></h2>
		<table>
			<thead>
				<tr>
					<th class="image">Bild</th>
					<th>Plats</th>
					<th>Innehåll</th>
					<th class="counter">Objekt</th>
					<th>Användning</th>
					<th class="manage">Hantera</th>
				</tr>
			</thead>
<?php 		# walk locations one by one
			$x = 0;
			foreach ($locations as $k => $v) {
				$x += $v['item_amount'];
			}
?>
			<tfoot>
				<tr>
					<td>Totalt</td>
					<td></td>
					<td></td>
					<td><?php echo $x; ?> st</td>
					<td>
					<td></td>
				</tr>
			</tfoot>
			<tbody>
<?php 		# walk locations one by one
			foreach ($locations as $k => $v) {


				$i=0;
				$amounts = array();
				$total = 0;
				# walk to find in-use values
				while(isset($v['inuse'.$i.'_amount'])) {
					# skip zero values
					if ((int)$v['inuse'.$i.'_amount'] <=0) {
						$i++;
						continue;
						}
					$amounts[$i] = (int)$v['inuse'.$i.'_amount'];

					$total += $amounts[$i];
												++$i;

				}

				# is the item total above zero, but no available - then there are no active items, go next
				# note that this differ from a location that has no items at all - it is unused
				if ($total > 0 && (int)$v['item_amount'] < 1) continue;

?>
				<tr>
					<td class="image">
						<div class="image"><!--<div class="id"><a href="?view=file&amp;id_items=<?php echo $v['id']?>">#<?php echo $v['id']?></a>--></div><?php
					if (file_exists(THUMBNAIL_DIR.$v['id_files'].'.jpg')) {
						?><a href="?view=file&amp;id_files=<?php echo $v['id_files']?>"><img src="?view=file&amp;type=thumbnail&amp;id_files=<?php echo $v['id_files']?>"></a><?php
					} else {
						?><img src="images/nophoto_320x240.png"><?php
					}
					?>
						</div>
					</td>
					<td>
						<a href="?view=index&amp;id_locations=<?php echo $v['id'] ?>"><?php echo $v['title'] ?></a>
					</td>
					<td>
						<?php echo $v['contents'] ?>
					</td>
					<td>
						<?php echo $v['item_amount'] ?> st
					</td>
					<td class="progressbar">
						<div class="inuse_progressbars">
<?php


						foreach ($amounts as $k2 => $v2) {
							?><div title="<?php echo $usage[$k2]?>" class="inuse_progressbar<?php echo $k2?>" style="width: <?php echo ($v2 / $total) * 100; ?>%"><?php echo $v2 ?> (<?php echo round(($v2 / $total) * 100,2); ?>%)</div><?php
						}


?>
					</td>

					<td class="manage">
					<?php if ((int)$v['item_amount'] < 1) { ?>
						<a href="?action=delete_location&amp;id_locations=<?php echo $v['id'] ?>&view=locations">Radera</a>
					<?php } ?>

						<a href="?view=edit_location&amp;id_locations=<?php echo $v['id'] ?>">Redigera</a>

					</td>

				</tr>
<?php
			} # eof-foreach-locations
?>
			</tbody>
		</table>
<?php
			break;

		case 'packlist': # to list items from a packlist
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
		<h2>Packlista <?php echo $packlist['title']; ?> <a href="?view=edit_packlist&amp;id_packlists=<?php echo $packlist['id']; ?>">Redigera</a></div></h2>
		<table>
			<thead>
				<tr>
					<th>Packad</th>
					<th>Objekt</th>
					<th>Användning</th>
					<th>Vikt</th>
					<th></th>
				</tr>
			</thead>
<?php 		# walk packlist items one by one
			$x = 0;
			foreach ($items as $k => $v) {
				$x += $v['weight'];
			}
?>

			<tfoot>
				<tr>
					<td></td>
					<td>Totalt</td>
					<td></td>
					<td><?php echo $x; ?>g</td>
					<td><?php echo count($items); ?> st</td>
				</tr>
			</tfoot>
			<tbody>
<?php 		# walk items in packlist one by one
			foreach ($items as $k => $v) {
?>
				<tr>
					<td class="<?php echo (int)$v['packed'] ? 'packed' : 'unpacked'?>">
						<input
							data-packlist-item="<?php echo (int)$v['packlist_item']?>"
<?php
				if (!(int)$v['packlist_item']) {
?>
							data-id-relations-packlists-items="<?php echo (int)$v['id_relations_packlists_items']?>"
<?php
				} else {
?>
							data-id-packlist-items="<?php echo (int)$v['id_packlist_items']?>"
<?php			} ?>
							type="checkbox"
							value="1"<?php echo (int)$v['packed'] ? ' checked' : ''?>>
					</td>
					<td>
<?php
				if (!(int)$v['packlist_item']) { ?>
						<a href="?view=index&id_items=<?php echo $v['id_items'] ?>"><?php echo $v['title'] ?></a>
<?php			} else {
					echo $v['title'];
				}
?>
					</td>
					<td>
						<select
							name="inuse<?php echo (int)$v['inuse']?>"
							data-packlist-item="<?php echo (int)$v['packlist_item']?>"
<?php
				if (!(int)$v['packlist_item']) {
?>
							data-id-relations-packlists-items="<?php echo (int)$v['id_relations_packlists_items']?>"
<?php
				} else {
?>
							data-id-packlist-items="<?php echo (int)$v['id_packlist_items']?>"
<?php			} ?>>
							<option value="<?php echo $k;?>"<?php echo $selected?>>-- Används</option>
<?php
					# walk inuse
					foreach ($usage as $k2 => $v2) {
						$selected = isset($v['inuse']) && (int)$k2 === (int)$v['inuse'] ? ' selected="selected"' : '';
						# print option
?>
							<option value="<?php echo $k2;?>"<?php echo $selected?>><?php echo $v2?></option>
<?php
					}
?>
						</select>
					</td>
					<td>
						<?php echo $v['weight'] ?>g
					</td>
					<td class="manage">
<?php
						# is this a regular item
				if (!(int)$v['packlist_item']) {
?>
						<a href="?action=delete_relation_packlists_items&amp;id_relations_packlists_items=<?php echo $v['id_relations_packlists_items'] ?>&view=packlist&id_packlists=<?php echo $packlist['id'] ?>" class="confirm">Radera</a>
<?php
				# or is this a packlist item
				} else {
?>
						<a href="?" class="edit_packlist_item" data-id-packlist-items="<?php echo $v['id_packlist_items'] ?>" data-weight="<?php echo $v['weight'] ?>" data-title="<?php echo $v['title'] ?>">Redigera</a>
						<a href="?action=delete_packlist_item&amp;id_packlist_items=<?php echo $v['id_packlist_items'] ?>&view=packlist&id_packlists=<?php echo $packlist['id'] ?>" class="confirm">Radera</a>
<?php
				}
?>
					</td>
				</tr>
<?php
			} # eof-foreach-packlist
?>
			</tbody>
		</table>

		<h2>Lägg till/redigera objekt utanför inventariedatabasen <a href="#" id="a_form_packlist_item_reset">Rensa</a></h2>
		<form action="?action=insert_update_packlist_item" method="post" id="form_edit_packlist_item">
			<fieldset>
				<input type="hidden" name="view" value="packlist">
				<input type="hidden" name="id_packlists" value="<?php echo isset($packlist['id']) ? $packlist['id'] : ''?>">
				<input type="hidden" name="id_packlist_items" value="0">

				<div class="row">
					<label>#</label><span class="value" id="span_id_packlist_items">Nytt objekt</span><br>
					<label for="title">Titel</label><input class="text" type="text" name="title" value=""><br>
					<label for="weight">Vikt</label><input class="text" type="text" name="weight" value=""><br>
				</div>
				<div class="row">
					<input class="submit" type="submit" name="submit" value="Spara">
				</div>
			</fieldset>
		</form>
<?php
			break;

		case 'packlists': # to list item packlists
			if (!is_logged_in()) {
				print_login();
				break;
			}
?>
		<h2>Packlistor<div class="action"><a href="?view=edit_packlist">Ny</a></div></h2>
		<table>
			<thead>
				<tr>
					<th>Namn</th>
					<th>Objekt</th>
					<th>Vikt</th>
					<th></th>
				</tr>
			</thead>
<?php 		# walk packlists one by one
			$x = 0;
			foreach ($packlists as $k => $v) {
				$x += $v['item_amount'];
			}
?>
			<tbody>
<?php 		# walk packlists one by one
			foreach ($packlists as $k => $v) {

?>
				<tr>
					<td>
						<a href="?view=packlist&amp;id_packlists=<?php echo $v['id'] ?>"><?php echo $v['title'] ?></a>
					</td>
					<td>
						<?php echo $v['item_amount'] ?> st
					</td>
					<td class="counter">
						<?php echo $v['weight'] ?>g
					</td>
					<td class="manage">
						<a href="?action=delete_packlist&amp;id_packlists=<?php echo $v['id'] ?>&view=packlists" class="confirm">Radera</a>
						<a href="?view=edit_packlist&amp;id_packlists=<?php echo $v['id'] ?>">Redigera</a>
					</td>
				</tr>
<?php
			} # eof-foreach-packlists
?>
			</tbody>
		</table>
<?php
			break;

	}
?>
</body>
</html>
