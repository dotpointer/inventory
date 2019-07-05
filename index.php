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
  # 2018-02-19 20:08:00 - adding packlist from and to and copy packlist
  # 2018-02-22 22:21:00 - adding packlist item relation comment
  # 2018-03-10 22:03:00 - adjusting packlist listings
  # 2018-03-14 23:02:00 - adding criteria handling
  # 2018-03-14 23:44:00 - adding criteria handling continued
  # 2018-03-15 00:47:00 - adding criteria handling continued
  # 2018-03-15 02:30:00 - translations
  # 2018-04-08 12:42:10 - adding location history
  # 2018-04-09 12:12:00 - cleanup
  # 2018-04-13 22:57:00 - adding packlist column for days
  # 2018-04-13 23:50:00 - adding packlist notes
  # 2018-04-19 14:49:00 - updating criteria and packlist addition forms
  # 2018-04-24 19:07:00 - adding created and updated to item index
  # 2018-05-04 23:58:00 - adding risk materials
  # 2018-06-24 17:59:00 - adding local login
  # 2018-06-25 18:58:00 - adding local user management and multi user support
  # 2018-06-26 16:04:00 - adding error handling
  # 2018-06-27 14:51:00 - adding translations json array
  # 2018-06-27 18:14:00 - updating jquery from version 2.1.1 to 3.3.1, removing editusers array check, updating formatting
  # 2018-07-02 19:30:00 - bugfix, image upload was disabled
  # 2018-07-16 16:52:44
  # 2018-07-19 18:00:02 - indentation change, tab to 2 spaces
  # 2018-12-20 18:42:00 - moving translation to Base translate
  # 2019-02-27 18:35:00 - adding from and to dates to packlist view
  # 2019-02-27 19:12:00 - adding json error output
  # 2019-04-04 18:40:32 - mobile mode changes

  # get required functionality
  require_once('include/functions.php');
  start_translations(dirname(__FILE__).'/include/locales/');

  init_constants();

  $add_to_new_packlists = isset($_REQUEST['add_to_new_packlists']) ? $_REQUEST['add_to_new_packlists'] : false;
  $acquired = isset($_REQUEST['acquired']) ? $_REQUEST['acquired'] : false;
  $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;
  $batteries_3r12 = isset($_REQUEST['batteries_3r12']) ? $_REQUEST['batteries_3r12'] : false;
  $batteries_aaa = isset($_REQUEST['batteries_aaa']) ? $_REQUEST['batteries_aaa'] : false;
  $batteries_aa = isset($_REQUEST['batteries_aa']) ? $_REQUEST['batteries_aa'] : false;
  $batteries_c = isset($_REQUEST['batteries_c']) ? $_REQUEST['batteries_c'] : false;
  $batteries_d = isset($_REQUEST['batteries_d']) ? $_REQUEST['batteries_d'] : false;
  $batteries_e = isset($_REQUEST['batteries_e']) ? $_REQUEST['batteries_e'] : false;
  $materialrisk = isset($_REQUEST['materialrisk']) ? $_REQUEST['materialrisk'] : false;
  $materials = isset($_REQUEST['materials']) ? $_REQUEST['materials'] : false;
  $category = isset($_REQUEST['category']) ? $_REQUEST['category'] : false;
  $comment = isset($_REQUEST['comment']) ? $_REQUEST['comment'] : false;
  $confirm = isset($_REQUEST['confirm']) ? $_REQUEST['confirm'] : false;
  $contents = isset($_REQUEST['contents']) ? $_REQUEST['contents'] : false;
  $description = isset($_REQUEST['description']) ? $_REQUEST['description'] : false;
  $disposed = isset($_REQUEST['disposed']) ? $_REQUEST['disposed'] : false;
  $find = isset($_REQUEST['find']) ? $_REQUEST['find'] : false;
  $format = isset($_REQUEST['format']) ? $_REQUEST['format'] : false;
  $from = isset($_REQUEST['from']) ? $_REQUEST['from'] : false;
  $id_categories_find = isset($_REQUEST['id_categories_find']) ? $_REQUEST['id_categories_find'] : false;
  $id_categories = isset($_REQUEST['id_categories']) ? $_REQUEST['id_categories'] : false;
  $id_criterias = isset($_REQUEST['id_criterias']) ? $_REQUEST['id_criterias'] : false;
  $id_files = isset($_REQUEST['id_files']) ? $_REQUEST['id_files'] : false;
  $id_items = isset($_REQUEST['id_items']) ? $_REQUEST['id_items'] : false;
  $id_locations = isset($_REQUEST['id_locations']) ? $_REQUEST['id_locations'] : false;
  $id_packlists = isset($_REQUEST['id_packlists']) ? $_REQUEST['id_packlists'] : false;
  $id_packlists_from = isset($_REQUEST['id_packlists_from']) ? $_REQUEST['id_packlists_from'] : false;
  $id_packlists_to = isset($_REQUEST['id_packlists_to']) ? $_REQUEST['id_packlists_to'] : false;
  $id_packlist_items = isset($_REQUEST['id_packlist_items']) ? $_REQUEST['id_packlist_items'] : false;
  $id_relations_criterias_items = isset($_REQUEST['id_relations_criterias_items']) ? $_REQUEST['id_relations_criterias_items'] : false;
  $id_relations_packlists_items = isset($_REQUEST['id_relations_packlists_items']) ? $_REQUEST['id_relations_packlists_items'] : false;
  $id_users= isset($_REQUEST['id_users']) ? $_REQUEST['id_users'] : false;
  $interval_days = isset($_REQUEST['interval_days']) ? $_REQUEST['interval_days'] : false;
  $inuse = isset($_REQUEST['inuse']) ? $_REQUEST['inuse'] : false;
  $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 25;
  $location = isset($_REQUEST['location']) ? $_REQUEST['location'] : false;
  $logintype = isset($_REQUEST['logintype']) ? $_REQUEST['logintype'] : false;
  $notes = isset($_REQUEST['notes']) ? $_REQUEST['notes'] : false;
  $packed = isset($_REQUEST['packed']) ? $_REQUEST['packed'] : false;
  $price = isset($_REQUEST['price']) ? $_REQUEST['price'] : false;
  $source = isset($_REQUEST['source']) ? $_REQUEST['source'] : false;
  $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
  $status_find = isset($_REQUEST['status_find']) ? $_REQUEST['status_find'] : false;
  $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : false;
  $ticket = isset($_REQUEST['ticket']) ? $_REQUEST['ticket'] : false;
  $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : false;
  $to = isset($_REQUEST['to']) ? $_REQUEST['to'] : false;
  $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : false;
  $watt = isset($_REQUEST['watt']) ? $_REQUEST['watt'] : false;
  $watt_max = isset($_REQUEST['watt_max']) ? $_REQUEST['watt_max'] : false;
  $weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : false;
  $view = isset($_REQUEST['view']) ? $_REQUEST['view'] : false;
  $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : false;
  $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : false;
  $password_retype = isset($_REQUEST['password_retype']) ? $_REQUEST['password_retype'] : false;

  # action management
  require_once('index-action.php');

  # view management
  require_once('index-view.php');

  # if json
  if ($format === 'json') {
    if (count($errors)) {
      die(json_encode(array(
        'status' => false,
        'data' => array(
          'errors' => $errors
        )
      )));
    }
    die();
  }
?><html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title><?php echo t('Inventory') ?><?php echo is_logged_in() && isset($item_amount[0], $item_amount[0]['amount']) ? ' - '.$item_amount[0]['amount'].' st' : ''?></title>
  <link rel="stylesheet" href="include/style.css" type="text/css" media="screen" />

  <script type="text/javascript" src="include/jquery-3.3.1.min.js"></script>
  <script type="text/javascript">
    var
      i = {
        action: '<?php echo $action?>',
        msg: <?php echo json_encode(get_translation_texts(), true); ?>,
        time_diff: <?php echo microtime(true) * 1000; ?>  - (new Date().getTime()),
        view: '<?php echo $view?>'
      };
  </script>
  <script type="text/javascript" src="include/load.js"></script>

</head>
<body>
<?php
  if (is_logged_in()) {
?>
  <ul class="menu">
    <li class="menuitem"><a href="?view=index"><?php echo t('Inventory') ?></a></li>
<?php 		if (is_logged_in()) { ?>
    <li class="menuitem"><a href="?view=categories"><?php echo t('Categories') ?></a></li>
    <li class="menuitem"><a href="?view=locations"><?php echo t('Locations') ?></a></li>
    <li class="menuitem"><a href="?view=edit_item"><?php echo t('New') ?></a></li>
    <li class="menuitem"><a href="?view=packlists"><?php echo t('Packlists') ?></a></li>
    <li class="menuitem"><a href="?view=users"><?php echo t('Users') ?></a></li>
    <li class="menuitem"><a href="?action=logout&amp;view=login"><?php echo t('Logout') ?></a></li>

    <li>
      <form action="?" method="post">
        <fieldset>
          <input type="hidden" name="view" value="index">
          <input type="text" class="text" name="find" value="<?php echo $find;?>" placeholder="Sök">
          <select name="id_categories_find">
            <option>-- <?php echo t('Category') ?></option>
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
            <option>-- <?php echo t('Status') ?></option>
<?php 		# walk statuses one by one
      foreach ($statuses as $k => $v) {
        $selected = (int)$status_find === (int)$k ? ' selected="selected"' : '';
?>
            <option value="<?php echo $k ?>"<?php echo $selected?>><?php echo $v['text'] ?></option>
<?php
      } # eof-foreach-statuses
?>
          </select>

          <input type="checkbox" class="checkbox" id="materialrisk" name="materialrisk" value="1"<?php echo $materialrisk ? ' checked="checked" ' : '' ?>/>
          <label for="materialrisk"><?php echo t('Risk materials') ?></label>
          <input type="submit" name="submit" class="submit" value="<?php echo t('Search') ?>">
          <br>
        </fieldset>
      </form>
    </li>
<?php		} # if-is-logged-in ?>
  </ul>
<?php
  }

  # are there errors, then print them
  if (count($errors)) {
?>
  <p class="error">
<?php
    foreach ($errors as $k => $v) {
      if ($k) {
        echo ' ';
      }
      echo $v;
    }
?>
  </p>
<?php
  }

  # find out what view to show
  switch ($view) {
    default:
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
      break;

    case 'edit_category': # to insert or update a category
      if (!is_logged_in()) {
        print_login($username);
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
      <label for="title"><?php echo t('Title') ?></label><input class="text" type="text" name="title" value="<?php echo isset($category['title']) ? $category['title'] : ''?>"><br>
      <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
    </fieldset>
  </form>
<?php
      break;

    case 'edit_criteria': # to insert or update a criteria
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
  <h2><?php echo t('Edit criteria') ?><?php
    if (isset($criteria['id'])) {
      echo ' #'.$criteria['id'];
    }
?></h2>
  <form action="?action=insert_update_criteria" method="post">
    <fieldset>
      <input type="hidden" name="view" value="criterias">
      <input type="hidden" name="id_criterias" value="<?php echo isset($criteria['id']) ? $criteria['id'] : ''?>">

      <div class="row">
        <label for="title"><?php echo t('Title') ?></label>
        <input class="text" type="text" name="title" value="<?php echo isset($criteria['title']) ? $criteria['title'] : ''?>"><br>
      </div>

      <div class="row">
        <label for="title"><?php echo t('Day interval') ?></label>
        <input class="text" type="number" name="interval_days" value="<?php echo isset($criteria['interval_days']) ? $criteria['interval_days'] : ''?>"><br>
      </div>

      <div class="row">
        <label for="title"><?php echo t('Add to new packlists') ?></label>
        <select name="add_to_new_packlists">
          <option value="0"><?php echo t('No') ?></option>
          <option value="1"<?php echo isset($criteria['add_to_new_packlists']) && (int)$criteria['add_to_new_packlists'] ? ' selected="selected' : ''?>><?php echo t('Yes') ?></option>
        </select>
        <br>
      </div>

      <div class="row">
        <input class="submit" type="submit" name="submit" value="<?php t('Save') ?>">
      </div>
    </fieldset>
  </form>
<?php
      break;

    case 'edit_item': # to insert or update an inventory item
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
  <h2><?php echo t('Edit inventory') ?><?php

    if (isset($item['id'])) {
      echo ' #'.$item['id'];
    }

?></h2>
  <form action="?action=insert_update_item" method="post" enctype="multipart/form-data">
    <fieldset>
      <input type="hidden" name="MAX_FILE_SIZE" value="10737418240" />
      <input type="hidden" name="id_items" value="<?php echo isset($item['id']) ? $item['id'] : ''?>">
      <div class="row">
        <label for="title"><?php echo t('Title') ?></label>
        <input class="text" type="text" name="title" value="<?php echo isset($item['title']) ? $item['title'] : ''?>">
      </div>
      <div class="row">
        <label for="description"><?php echo t('Description') ?></label>
        <textarea name="description"><?php echo isset($item['description']) ? $item['description'] : ''?></textarea>
        </div>
      <div class="row">
        <label for="batteries_aa"><?php echo t('AA batteries') ?>:</label>
        <input class="text" type="text" name="batteries_aa" value="<?php echo isset($item['batteries_aa']) ? $item['batteries_aa'] : ''?>">
      </div>
      <div class="row">
        <label for="batteries_aaa"><?php echo t('AAA batteries') ?>:</label>
        <input class="text" type="text" name="batteries_aaa" value="<?php echo isset($item['batteries_aaa']) ? $item['batteries_aaa'] : ''?>"></div>
      <div class="row">
        <label for="batteries_c"><?php echo t('C batteries') ?>:</label>
        <input class="text" type="text" name="batteries_c" value="<?php echo isset($item['batteries_c']) ? $item['batteries_c'] : ''?>">
      </div>
      <div class="row">
        <label for="batteries_d"><?php echo t('D batteries') ?>:</label>
        <input class="text" type="text" name="batteries_d" value="<?php echo isset($item['batteries_d']) ? $item['batteries_d'] : ''?>">
      </div>
      <div class="row">
        <label for="batteries_3r12"><?php echo t('3R12 batteries') ?>:</label>
        <input class="text" type="text" name="batteries_3r12" value="<?php echo isset($item['batteries_3r12']) ? $item['batteries_3r12'] : ''?>">
      </div>
      <div class="row">
        <label for="batteries_e">(9V) <?php echo t('E batteries') ?>:</label>
        <input class="text" type="text" name="batteries_e" value="<?php echo isset($item['batteries_e']) ? $item['batteries_e'] : ''?>">
      </div>
      <div class="row">
        <label for="materials"><?php echo t('Material') ?>:</label>
        <input class="text" type="text" name="materials" value="<?php echo isset($item['materials']) ? $item['materials'] : ''?>">
        <button id="button_material_100_cotton">100% <?php echo t('cotton') ?></button>
      </div>
      <div class="row">
        <label for="watt"><?php echo t('Watts') ?>:</label>
        <input class="text" type="text" name="watt" value="<?php echo isset($item['watt']) ? $item['watt'] : ''?>">
      </div>
      <div class="row">
        <label for="watt_max"><?php echo t('Watts max') ?>:</label>
        <input class="text" type="text" name="watt_max" value="<?php echo isset($item['watt_max']) ? $item['watt_max'] : ''?>">
      </div>
      <div class="row">
        <label for="weight"><?php echo t('Weight') ?>:</label>
        <input class="text" type="text" name="weight" value="<?php echo isset($item['weight']) ? $item['weight'] : ''?>">
        <span class="unit">g</span>
      </div>
      <div class="row">
        <label for="source"><?php echo t('Got from') ?>:</label>
        <input class="text" type="text" name="source" value="<?php echo isset($item['source']) ? $item['source'] : ''?>">
      </div>
      <div class="row">
        <label for="location"><?php echo t('Location') ?>:</label>
        <input class="text" type="text" name="location" value="<?php echo isset($item['location']) ? $item['location'] : ($location !== false ? $location : 'Uttervik')?>">
      </div>
      <div class="row">
        <label for="price"><?php echo t('Buying price') ?>:</label>
        <input class="text" type="text" name="price" value="<?php echo isset($item['price']) ? $item['price'] : ''?>">
      </div>
      <div class="row">
        <label for="file"><?php echo t('JPEG image') ?></label>
<?php		if (file_exists(MAGICK_PATH.'convert')) { ?>
        <input class="file" type="file" name="file">
<?php		} else { ?>
        <span class="value"><?php echo t('ImageMagick is not installed, please install it to enable image uploads.'); ?></span>
<?php		} ?>
<?php		if (isset($item['id_files']) && file_exists(FILE_DIR.$item['id_files'].'.jpg')) { ?>
        <a href="?view=file&amp;id_files=<?php echo $item['id_files']?>"><img src="?view=file&amp;type=thumbnail&amp;id_files=<?php echo $item['id_files']?>"></a><br><br>
        <input type="hidden" name="id_files" value="<?php echo isset($item['price']) ? $item['id_files'] : ''?>">
<?php 		} ?>
      </div>
      <div class="row">
        <label for="id_categories"><?php echo t('Category') ?></label>
        <select name="id_categories">
          <option>-- <?php echo t('Category') ?></option>
          <option value="-1">-- <?php echo t('New category') ?></option>
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
        <input class="text" id="category" name="category" placeholder="<?php echo t('New category') ?>">
      </div>
      <div class="row">
        <label for="acquired"><?php echo t('Date acquired') ?></label>
        <input class="text" type="text" name="acquired" value="<?php echo isset($item['acquired']) ? $item['acquired'] : ''?>">
        <button id="button_aquired_date"><?php echo t('Set date') ?></button>
      </div>
      <div class="row">
        <label for="disposed"><?php echo t('Date disposed') ?></label>
        <input class="text" type="text" name="disposed" value="<?php echo isset($item['disposed']) ? $item['disposed'] : ''?>">
        <button id="button_disposed_date"><?php echo t('Set date') ?></button>
      </div>
      <div class="row">
        <label for="status"><?php echo t('Status') ?>:</label>
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
        <button id="button_status_sale"><?php echo t('To be sold') ?></button>
        <button id="button_status_sold"><?php echo t('Sold') ?></button>
    </div>


      <div class="row">
        <label for="inuse"><?php echo t('In use') ?>:</label>
        <select name="inuse">
        <option value="<?php echo $k;?>"<?php echo $selected?>>-- <?php echo t('In use') ?></option>
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
        <label for="view"><?php echo t('Next page') ?>:</label>
        <select name="view">
          <option value="index"<?php echo $view==='edit_item' ? ' selected="selected"' : ''?>><?php echo t('Show edited inventory') ?></option>
          <option value="edit_item"<?php echo $view==='edit_item' ? ' selected="selected"' : ''?>><?php echo t('New inventory') ?></option>
        </select>
      </div>
      <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
    </fieldset>
  </form>
<?php
      break;

    case 'edit_location': # to insert or update a location
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
  <h2><?php echo t('Edit location'); ?><?php
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
        <label for="title"><?php echo t('Title') ?></label><input class="text" type="text" name="title" value="<?php echo isset($location['title']) ? $location['title'] : ''?>"><br>
      </div>
      <div class="row">
        <label for="contents"><?php echo t('Contents') ?></label><input class="text" type="text" name="contents" value="<?php echo isset($location['contents']) ? $location['contents'] : ''?>"><br>
      </div>
      <div class="row">
        <label for="file"><?php echo t('JPEG image') ?></label>
        <input class="file" type="file" name="file">
<?php		if (isset($item['id_files']) && file_exists(FILE_DIR.$item['id_files'].'.jpg')) { ?>
        <a href="?view=file&amp;id_files=<?php echo $item['id_files']?>"><img src="?view=file&amp;type=thumbnail&amp;id_files=<?php echo $item['id_files']?>"></a><br><br>
        <input type="hidden" name="id_files" value="<?php echo isset($item['price']) ? $item['id_files'] : ''?>">
<?php 		} ?>
      </div>
      <div class="row">files
        <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
      </div>
    </fieldset>
  </form>
<?php
      break;

    case 'edit_packlist': # to insert or update a packlist
      if (!is_logged_in()) {
        print_login($username);
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
        <label for="title"><?php echo t('Title') ?></label>
        <input class="text" type="text" name="title" value="<?php echo isset($packlist['title']) ? $packlist['title'] : ''?>"><br>
      </div>
      <div class="row">
        <label for="title"><?php echo t('Date from') ?></label>
        <input class="text" type="date" name="from" value="<?php echo isset($packlist['from']) ? substr($packlist['from'], 0, strpos($packlist['from'], ' ')) : ''?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required>
        <br>
      </div>
      <div class="row">
        <label for="title"><?php echo t('Date to') ?></label>
        <input class="text" type="date" name="to" value="<?php echo isset($packlist['to']) ? substr($packlist['to'], 0, strpos($packlist['to'], ' ')) : ''?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required>
        <br>
      </div>

      <div class="row">
        <label for="id_packlists_from"><?php echo t('Copy from packlist') ?></label>
        <select name="id_packlists_from">
          <option value="0">-- <?php echo t('Do not copy') ?></option>
          <?php foreach($packlists_copy as $vx) { ?>
          <option value="<?php echo $vx['id']; ?>"><?php echo $vx['title']; ?></option>
          <?php } ?>
        </select>
        <br>
      </div>

      <div class="row">
        <label for="id_packlists_from"><?php echo t('Criterias') ?></label>
        <div class="selectbox_left">
          <div class="subheader"><?php echo t('Available') ?>:</div>
          <select multiple id="select_criterias_available">
            <?php foreach ($criterias_available as $ca) { ?>
            <option value="<?php echo $ca['id'] ?>"><?php echo $ca['title'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="selectbox_center">
          <br>
          <button id="button_criterias_remove">&lt;&lt;</button>
          <br>
          <button id="button_criterias_add">&gt;&gt;</button>
        </div>
        <div class="selectbox_right">
          <div class="subheader"><?php echo t('Selected') ?>:</div>
          <select multiple id="select_criterias_selected">
            <?php foreach ($criterias_selected as $cs) { ?>
            <option value="<?php echo $cs['id'] ?>"><?php echo $cs['title'] ?></option>
            <?php } ?>
          </select>
          <div id="hidden_selected_criterias">
            <?php foreach ($criterias_selected as $cs) { ?>
            <input type="hidden" value="<?php echo $cs['id'] ?>" name="id_criterias[]">
            <?php } ?>
          </div>
        </div>
        <br>
      </div>

      <div class="row">
        <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
      </div>
    </fieldset>
  </form>
<?php
      break;


    case 'edit_relation_packlists_items': # to insert or update a packlist
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
  <h2><?php echo t('Edit packlist object relation') ?><?php
?></h2>
  <form action="?view=packlist&id_packlists=<?php echo isset($relation['id_packlists']) ? $relation['id_packlists'] : ''?>" method="post">
    <fieldset>
      <input type="hidden" name="action" value="update_relation_packlists_items">
      <input type="hidden" name="id_relations_packlists_items" value="<?php echo isset($relation['id_relation_packlists_items']) ? $relation['id_relation_packlists_items'] : ''?>">

      <div class="row">
        <label for="title"><?php echo t('Object') ?></label>
        <span class="value"><?php echo $relation['title']; ?></span><br>
      </div>
      <div class="row">
        <label for="title"><?php echo t('Comment') ?></label>
        <input class="text" type="text" name="comment" value="<?php echo $relation['comment'] ?>">
        <br>
      </div>
      <div class="row">
        <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
      </div>
    </fieldset>
  </form>
<?php
      break;

    case 'edit_user': # to insert or update a standalone user
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
  <h2><?php echo isset($user['id']) ? t('Edit user').' #'.$user['id'] : t('Add user'); ?></h2>
<?php
      if (isset($user, $user['id_visum']) && $user['id_visum'] !== '0') {
?>
  <p>
    <?php t('This is a Visum user, it cannot be edited locally.'); ?>
  </p>
  <p>
    <a href="?view=users"><?php t('Back to user list.'); ?></a>
  </p>
<?php
        break;
      }
?>
  <form action="?action=insert_update_user" method="post">
    <fieldset>
      <input type="hidden" name="view" value="users">
      <input type="hidden" name="id_users" value="<?php echo isset($user['id']) ? $user['id'] : ''?>">

      <div class="row">
        <label for="username"><?php echo t('Username') ?></label>
        <input class="text" type="text" name="username" value="<?php echo isset($user['username']) ? $user['username'] : ''?>"><br>
      </div>

      <div class="row">
        <label for="password"><?php echo t('Password') ?></label>
        <input class="text" type="password" name="password" value="">
<?php
      if (isset($user['id'])) {
?>
        <i><?php echo t('Leave password fields blank to keep current password.') ?></i>
<?php
      }
?>
        <br>
      </div>

      <div class="row">
        <label for="password_retype"><?php echo t('Password again') ?></label>
        <input class="text" type="password" name="password_retype" value="">
        <br>
      </div>

      <div class="row">
        <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
      </div>
    </fieldset>
  </form>
<?php
      break;
    case 'location_history':
?>
    <table>
      <thead>
        <tr>
          <th><?php echo t('Location') ?></th>
          <th><?php echo t('Date') ?></th>
        </tr>
      </thead>
      <tbody>
<?php 		foreach ($location_history as $k => $v) { ?>
        <tr>
          <td><?php echo implode(json_decode($v['title']), ' / '); ?></td>
          <td><?php echo $v['created']?></td>
        </tr>
<?php		} ?>
      </tbody>
    </table>
<?php

      break;

    case 'index': # to list items
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
    <h2><?php echo t('Inventory list') ?><?php
      if (isset($category) && is_array($category)) {
        echo ' '.t('for category').' '.$category['title'];
      }

      if (isset($location) && is_array($location)) {
        echo ' '.t('for location').' '.$location['title'];
      }

    ?></h2>
    <p>
      <?php echo t('The search resulted in') ?> <?php echo $items_amount; ?> <?php echo t('hits') ?>.
      <?php if ($materialrisk) {
        $tmp = array();
        foreach ($mothmaterials as $v) {
          $tmp[] = t($v);
        }
      ?>
      <?php echo t('Filtered on these risk materials').': '.(implode(', ', $tmp)); ?>.
      <?php } ?>
    </p>

    <p class="browse">
      <a href="?view=index&amp;find=<?php echo $find?>&amp;id_locations=<?php echo $id_locations?>&amp;id_categories_find=<?php echo $id_categories_find?>&amp;status_find=<?php echo $status_find?>&amp;start=<?php echo ($start-$limit) > 0 ? $start-$limit : 0 ?>&amp;limit=<?php echo $limit?>">&lt;&lt; <?php echo $start ?></a>
       -
      <a href="?view=index&amp;find=<?php echo $find?>&amp;id_locations=<?php echo $id_locations?>&amp;id_categories_find=<?php echo $id_categories_find?>&amp;status_find=<?php echo $status_find?>&amp;start=<?php echo $start+$limit?>&amp;limit=<?php echo $limit?>"><?php echo count($items) < (int)$start+(int)$limit && $start < count($items) ? count($items) : (int)$start+(int)$limit;  ?> &gt;&gt;</a>

    </p>

    <table>
      <thead>
        <tr>
          <th class="image"><?php echo t('Image') ?></th>
          <th><?php echo t('Description') ?></th>
          <th><?php echo t('Status') ?></th>
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
            <p><?php echo strlen(trim($v['description'])) ? nl2br($v['description']) : t('No description.'); ?></p>
            <p class="subdata">
              <?php echo t('Got from') ?>: <?php echo $v['source']?>
              <br>
              <?php echo t('Location') ?>: <?php foreach ($v['locations'] as $loc_key => $loc) {

              if ($loc_key) {
                echo ' + ';
              }

              ?><a href="?view=index&amp;id_locations=<?php echo $loc['id_locations']?>"><?php echo $loc['title'] ?></a><?php
            }
            ?>
              <?php if ($v['watt']) { ?>
              <br>
              <?php echo t('Watts') ?>: <?php echo $v['watt']?>-<?php echo $v['watt_max']?>
              <?php } ?>

              <?php if ($v['weight']) { ?>
              <br>
              <?php echo t('Weight') ?>: <?php echo $v['weight']?>g
              <?php } ?>

              <?php if ($v['batteries_aa']) { ?>
              <br>
              <?php echo t('AA batteries') ?>: <?php echo $v['batteries_aa']?> st
              <?php } ?>

              <?php if ($v['batteries_aaa']) { ?>
              <br>
              <?php echo t('AAA batteries') ?>: <?php echo $v['batteries_aaa']?> st
              <?php } ?>

              <?php if ($v['batteries_c']) { ?>
              <br>
              <?php echo t('C batteries') ?>: <?php echo $v['batteries_c']?> st
              <?php } ?>

              <?php if ($v['batteries_d']) { ?>
              <br>
              <?php echo t('D batteries') ?>: <?php echo $v['batteries_d']?> st
              <?php } ?>

              <?php if ($v['batteries_e']) { ?>
              <br>
              (9V) <?php echo t('E batteries') ?>: <?php echo $v['batteries_e']?> st
              <?php } ?>

              <?php if ($v['batteries_3r12']) { ?>
              <br>
              <?php echo t('3R12 batteries') ?>: <?php echo $v['batteries_3r12']?> st
              <?php } ?>

              <?php if ($v['materials']) { ?>
              <br>
              <?php echo t('Materials') ?>: <?php echo $v['materials']?>
              <?php } ?>

              <form class="form_add_item_to_packlist">
                <div>
                  <label><?php echo t('Add to packlist') ?></label>
                  <input type="hidden" value="<?php echo $v['id'] ?>" name="id_items" >
                  <select>
                    <option>-- <?php echo t('Choose') ?></option>
                    <optgroup label="<?php echo t('Coming') ?>">
                      <?php
                      foreach($packlists as $vx) {
                        if (strtotime($vx['from']) > time()) {
                      ?>
                      <option value="<?php echo $vx['id']; ?>"><?php echo $vx['title']; ?></option>
                      <?php
                        }
                      }
                      ?>
                    </optgroup>
                    <optgroup label="<?php echo t('Current') ?>">
                      <?php
                      foreach($packlists as $vx) {
                        if (strtotime($vx['from']) <= time() && strtotime($vx['to']) >= time()) {
                      ?>
                      <option value="<?php echo $vx['id']; ?>"><?php echo $vx['title']; ?></option>
                      <?php
                        }
                      }
                      ?>
                    </optgroup>
                    <optgroup label="<?php echo t('Ended') ?>">
                      <?php
                      foreach($packlists as $vx) {
                        if (strtotime($vx['to']) <= time()) {
                      ?>
                      <option value="<?php echo $vx['id']; ?>"><?php echo $vx['title']; ?></option>
                      <?php
                        }
                      }
                      ?>
                    </optgroup>
                  </select>
                  <input type="submit" value="<?php echo t('Save') ?>" >
                </div>
              </form>

              <form class="form_add_item_to_criteria">
                <div>
                  <label><?php echo t('Add to criteria') ?></label>
                  <input type="hidden" value="<?php echo $v['id'] ?>" name="id_items" >
                  <select>
                    <option>-- <?php echo t('Choose') ?></option>
                    <?php foreach($criterias as $vx) { ?>
                    <option value="<?php echo $vx['id']; ?>"><?php echo $vx['title']; ?></option>
                    <?php } ?>
                  </select>
                  <input type="submit" value="<?php echo t('Save') ?>" >
                </div>
              </form>
              <br><br>
              <i><?php echo t('Created').' '.$v['created'].', '.t('updated').' '.$v['updated'] ?>.</i>
            </p>
          </td>
          <td>
            <div class="status <?php echo $statuses[$v['status']]['name']; ?>"><?php echo $statuses[$v['status']]['text']; ?></div>

            <div class="inuse inuse<?php echo $v['inuse']; ?>"><?php echo $usage[$v['inuse']]; ?></div>
            <br><br>

            <a href="?view=edit_item&amp;id_items=<?php echo $v['id']?>"><?php echo t('Edit') ?></a>
            <br>
            <a href="?view=location_history&amp;id_items=<?php echo $v['id']?>"><?php echo t('Location history') ?></a>
          </td>
        </tr>
<?php
      } # eof-foreach-items

      if (isset($items) && is_array($items)) reset($items);
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
        print_login($username);
        break;
      }
?>
    <h2><?php echo t('Categories') ?></h2>
    <p class="actions">
      <a href="?view=edit_category">Ny kategori</a>
    </p>
    <table>
      <thead>
        <tr>
          <th class="id">#</th>
          <th><?php echo t('Category') ?></th>
          <th class="counter"><?php echo t('Object') ?></th>
          <th><?php echo t('Usage') ?></th>
          <th class="manage"><?php echo t('Manage') ?></th>
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
            foreach ($amounts as $k2 => $v2) {
              ?><div title="<?php echo $usage[$k2]?>" class="inuse_progressbar<?php echo $k2?>" style="width: <?php echo ($v2 / $total) * 100; ?>%"><?php echo $v2 ?> (<?php echo round(($v2 / $total) * 100,2); ?>%)</div><?php
            }
?>
          </td>

          <td class="manage">
            <a href="?view=edit_category&amp;id_categories=<?php echo $v['id'] ?>"><?php echo t('Edit') ?></a>
          </td>

        </tr>
<?php
      } # eof-foreach-categories
?>
      </tbody>
      <tfoot>
        <tr>
          <td><?php echo t('Totally') ?></td>
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
    case 'criteria': # to show a criteria
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
    <h2><?php echo t('Criteria') ?> <?php echo $criteria['title']; ?> <a href="?view=edit_criteria&amp;id_criterias=<?php echo $criteria['id']; ?>"><?php echo t('Edit') ?></a></div></h2>

    <h3><?php echo t('Summary') ?></h3>
    <ul>
      <li>
        <?php echo t('Day interval') ?>: <?php echo $criteria['interval_days'] ?>
      </li>
      <li>
        <?php echo t('Add to new packlists') ?>: <?php echo (int)$criteria['add_to_new_packlists'] ? 'Ja' : 'Nej' ?>
      </li>
    </ul>
    <br>

    <h3><?php echo t('Objects required by the criteria') ?></h3>
    <table>
      <thead>
        <tr>
          <th><?php echo t('Object') ?>a</th>
          <th></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td><?php echo t('Totally') ?></td>
          <td><?php echo count($items); ?> <?php echo t('pcs') ?></td>
        </tr>
      </tfoot>
      <tbody>
<?php 		# walk items in criteria one by one
      foreach ($items as $k => $v) {
?>
        <tr>
          <td>
            <a href="?view=index&id_items=<?php echo $v['id_items'] ?>"><?php echo $v['title'] ?></a>
          </td>
          <td class="manage">
            <a href="?action=delete_relation_criterias_items&amp;id_relations_criterias_items=<?php echo $v['id_relations_criterias_items'] ?>&view=criteria&id_criterias=<?php echo $criteria['id'] ?>" class="confirm"><?php echo t('Remove') ?></a>
          </td>
        </tr>
<?php
      } # eof-foreach-criteria
?>
      </tbody>
    </table>
<?php
      break;
    case 'locations': # to list item locations
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
    <h2>Platser<div class="action"><a href="?view=edit_location"><?php echo t('New') ?></a></div></h2>
    <table>
      <thead>
        <tr>
          <th class="image"><?php echo t('Image') ?></th>
          <th><?php echo t('Location') ?></th>
          <th><?php echo t('Contents') ?></th>
          <th class="counter"><?php echo t('Objects') ?></th>
          <th><?php echo t('Usage') ?></th>
          <th class="manage"><?php echo t('Manage') ?></th>
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
          <td><?php echo t('Totally') ?></td>
          <td></td>
          <td></td>
          <td><?php echo $x; ?> <?php echo t('pcs') ?></td>
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
            <a href="?action=delete_location&amp;id_locations=<?php echo $v['id'] ?>&view=locations"><?php echo t('Remove') ?></a>
          <?php } ?>
            <a href="?view=edit_location&amp;id_locations=<?php echo $v['id'] ?>"><?php echo t('Edit') ?></a>
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
        print_login($username);
        break;
      }
?>
    <h2>
      <?php echo t('Packlist') ?> <?php echo $packlist['title']; ?>
      <div class="action">
        <a href="?view=edit_packlist&amp;id_packlists=<?php echo $packlist['id']; ?>"><?php echo t('Edit') ?></a>
      </div>
    </h2>
    <p>
      <?php echo date('Y-m-d', strtotime($packlist['from'])); ?> - <?php echo date('Y-m-d', strtotime($packlist['to'])); ?>
    </p>
<?php
    if ($criterias) {
?>
    <h3><?php echo t('Criterias') ?></h3>
    <ul>
<?php
      foreach ($criterias as $criteria) {
?>
      <li>
<?php
          echo $criteria['title'] ?> x <?php echo $criteria['multiplier'];

          # are there missing items in the packlist
          if ($criteria['missing_items']) {
?>
            <br>
            <span class="warning"><?php echo t('The following is missing in the packlist') ?>:</span>
            <?php

            foreach ($criteria['missing_items'] as $k => $v) {
              if ($k) {
                ?>, <?php
              }
              ?>
              <a href="?view=index&id_items=<?php echo $v['id_items'] ?>"><?php echo $v['title'] ?></a>
              <?php
            }
          }
?>
      </li>
<?php
      }
?>
    </ul>
    <br>
<?php
    }
?>
    <form action="?action=update_packlist_notes" method="post" id="form_update_packlist_notes">
      <fieldset>
        <input type="hidden" name="id_packlists" value="<?php echo $packlist['id'] ?>">
        <div class="row">
          <label for="notes"><?php echo t('Notes') ?></label>
          <textarea name="notes"><?php echo isset($packlist['notes']) ? $packlist['notes'] : ''?></textarea>
        </div>
        <div class="row">
          <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
        </div>
      </fieldset>
    </form>
    <table>
      <thead>
        <tr>
          <th><?php echo t('Packed') ?></th>
          <th><?php echo t('Object') ?></th>
          <th><?php echo t('Usage') ?></th>
          <th><?php echo t('Weight') ?></th>
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
          <td><?php echo t('Totally') ?></td>
          <td></td>
          <td><?php echo $x; ?>g</td>
          <td><?php echo count($items); ?> <?php echo t('pcs') ?></td>
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
<?php
          if (strlen($v['relation_comment'])) {
?>
            <br>
            &nbsp;<i><?php echo $v['relation_comment'] ?></i>
<?php
          }
        } else {
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
              <option value="<?php echo $k;?>"<?php echo $selected?>>-- <?php echo t('Usage') ?></option>
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
            <a href="?view=edit_relation_packlists_items&amp;id_relations_packlists_items=<?php echo $v['id_relations_packlists_items'] ?>"><?php echo t('Edit') ?></a>
            <a href="?action=delete_relation_packlists_items&amp;id_relations_packlists_items=<?php echo $v['id_relations_packlists_items'] ?>&view=packlist&id_packlists=<?php echo $packlist['id'] ?>" class="confirm"><?php echo t('Remove') ?></a>
<?php
        # or is this a packlist item
        } else {
?>
            <a href="?" class="edit_packlist_item" data-id-packlist-items="<?php echo $v['id_packlist_items'] ?>" data-weight="<?php echo $v['weight'] ?>" data-title="<?php echo $v['title'] ?>"><?php echo t('Edit') ?></a>
            <a href="?action=delete_packlist_item&amp;id_packlist_items=<?php echo $v['id_packlist_items'] ?>&view=packlist&id_packlists=<?php echo $packlist['id'] ?>" class="confirm"><?php echo t('Remove') ?></a>
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

    <h2><?php echo t('Add/edit object outside of the inventory database') ?> <a href="#" id="a_form_packlist_item_reset"><?php echo t('Clear') ?></a></h2>
    <form action="?action=insert_update_packlist_item" method="post" id="form_edit_packlist_item">
      <fieldset>
        <input type="hidden" name="view" value="packlist">
        <input type="hidden" name="id_packlists" value="<?php echo isset($packlist['id']) ? $packlist['id'] : ''?>">
        <input type="hidden" name="id_packlist_items" value="0">

        <div class="row">
          <label>#</label><span class="value" id="span_id_packlist_items"><?php echo t('New object') ?></span><br>
          <label for="title"><?php echo t('Title') ?></label><input class="text" type="text" name="title" value=""><br>
          <label for="weight"><?php echo t('Weight') ?></label><input class="text" type="text" name="weight" value=""><br>
        </div>
        <div class="row">
          <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
        </div>
      </fieldset>
    </form>
<?php
      break;

    case 'packlists': # to list item packlists
      if (!is_logged_in()) {
        print_login($username);
        break;
      }

      $packlisttype = array(
        0 => t('Coming'),
        1 => t('Current'),
        2 => t('Ended')
      );

      for ($f = 0; $f < 3; $f +=1 ) {

?>
    <h2>
      <?php echo t('Packlists') ?> - <?php echo $packlisttype[$f];

        if ($f === 0) { ?>
      <div class="action"><a href="?view=edit_packlist"><?php echo t('New') ?></a></div>
      <div class="action"><a href="?view=criterias"><?php echo t('Criterias') ?></a>&nbsp;</div>
<?php
        }
?>
    </h2>
    <table>
      <thead>
        <tr>
          <th><?php echo t('Name') ?></th>
          <th><?php echo t('From') ?></th>
          <th><?php echo t('To') ?></th>
          <th><?php echo t('Days') ?></th>
          <th><?php echo t('Object') ?></th>
          <th><?php echo t('Weight') ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
<?php 		# walk packlists one by one
        foreach ($packlists as $k => $v) {
          # coming, but from field is less than or equal to now
          if ($f === 0 && strtotime($v['from']) <= time()) {
            continue;
          }

          # current, but from field is after now or to field is before now
          if ($f === 1 && (strtotime($v['from']) > time() || strtotime($v['to']) < time())) {
            continue;
          }

          # passed, but to field is more than or equal to now
          if ($f === 2 && strtotime($v['to']) >= time()) {
            continue;
          }

          $date1 = new DateTime(substr($v['from'], 0, strpos($v['from'], ' ')));
          $date2 = new DateTime(substr($v['to'], 0, strpos($v['to'], ' ')));

          $daysdiff = $date2->diff($date1)->format("%a") + 1;
?>
        <tr>
          <td>
            <a href="?view=packlist&amp;id_packlists=<?php echo $v['id'] ?>"><?php echo $v['title'] ?></a>
          </td>
          <td>
            <?php echo substr($v['from'], 0, strpos($v['from'], ' ')) ?>
          </td>
          <td>
            <?php echo substr($v['to'], 0, strpos($v['to'], ' ')) ?>
          </td>
          <td>
            <?php echo $daysdiff ?>
          </td>
          <td>
            <?php echo $v['item_amount'] ?> <?php echo t('pcs') ?>
          </td>
          <td class="counter">
            <?php echo $v['weight'] ?>g
          </td>
          <td class="manage">
            <a href="?action=delete_packlist&amp;id_packlists=<?php echo $v['id'] ?>&view=packlists" class="confirm"><?php echo t('Remove') ?></a>
            <a href="?view=edit_packlist&amp;id_packlists=<?php echo $v['id'] ?>"><?php echo t('Edit') ?></a>
          </td>
        </tr>
<?php
        } # eof-foreach-packlists
?>
      </tbody>
    </table>
<?php
      }
      break;

    case 'criterias': # to list criterias
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
    <h2>
      <?php echo t('Criterias') ?>
      <div class="action"><a href="?view=edit_criteria"><?php echo t('New') ?></a></div>
    </h2>
    <table>
      <thead>
        <tr>
          <th><?php echo t('Name') ?></th>
          <th><?php echo t('Day interval') ?></th>
          <th><?php echo t('Add to new packlists') ?></th>
          <th><?php echo t('Objects') ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>

<?php			# walk criterias one by one
        foreach ($criterias as $k => $v) {
?>
        <tr>
          <td>
            <a href="?view=criteria&amp;id_criterias=<?php echo $v['id'] ?>"><?php echo $v['title'] ?></a>
          </td>
          <td>
            <?php echo $v['interval_days'] ?>
          </td>
          <td>
            <?php echo (int)$v['add_to_new_packlists'] ? t('Yes') : t('No') ?>
          </td>
          <td class="counter">
            <?php echo $v['item_amount'] ?> st
          </td>
          <td class="manage">
            <a href="?action=delete_criteria&amp;id_criterias=<?php echo $v['id'] ?>&view=criterias" class="confirm"><?php echo t('Remove') ?></a>
            <a href="?view=edit_criteria&amp;id_criterias=<?php echo $v['id'] ?>"><?php echo t('Edit') ?></a>
          </td>
        </tr>
<?php
        }
?>
      </tbody>
    </table>
<?php
      break;

    case 'users': # to list users
      if (!is_logged_in()) {
        print_login($username);
        break;
      }
?>
    <h2>
      <?php echo t('Users') ?>
      <div class="action"><a href="?view=edit_user"><?php echo t('New') ?></a></div>
    </h2>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th><?php echo t('Username') ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>

<?php			# walk users one by one
        foreach ($users as $k => $v) {
?>
        <tr>
          <td>
            <?php echo $v['id'] ?>
          </td>
          <td>
            <?php
          if ($v['id_visum'] === "0" ) { ?>
              <a href="?view=user&amp;id_users=<?php echo $v['id'] ?>"><?php echo $v['username']; ?></a>
            <?php
          } else {
                echo $v['nickname'];
          }
            ?>
          </td>
          <td class="manage">
            <?php if ($v['id_visum'] === "0" ) { ?>
              <a href="?action=delete_user&amp;id_users=<?php echo $v['id'] ?>&view=users" class="confirm"><?php echo t('Remove') ?></a>
              <a href="?view=edit_user&amp;id_users=<?php echo $v['id'] ?>"><?php echo t('Edit') ?></a>
            <?php } ?>
          </td>
        </tr>
<?php
        }
?>
      </tbody>
    </table>
<?php
      break;
  }
?>
</body>
</html>
