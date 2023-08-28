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
  # 2017-05-13 15:28:36 - adding weight
  # 2017-05-13 17:49:40 - adding packlist
  # 2017-05-21 20:42:15 - adding packlist inuse
  # 2017-08-15 18:12:20 - getting visum base domain name from config
  # 2018-02-19 20:08:00 - adding packlist from and to and copy packlist
  # 2018-02-22 22:21:00 - adding packlist item relation comment
  # 2018-03-14 23:02:00 - adding criterias handling
  # 2018-03-14 23:44:00 - adding criterias handling continued
  # 2018-03-15 02:48:23 - translations
  # 2018-04-08 11:31:01 - adding location history
  # 2018-04-09 11:56:00 - cleanup
  # 2018-04-13 23:49:00 - adding packlist notes
  # 2018-05-04 23:56:00 - adding risk materials
  # 2018-05-05 00:02:00 - adding risk materials continued
  # 2018-05-05 13:53:00 - adding risk materials continued
  # 2018-06-24 17:57:00 - adding local login
  # 2018-06-25 18:56:00 - minor html correction
  # 2018-06-26 16:03:00 - adding error handling
  # 2018-06-27 18:09:00 - bugfix, database name was set in the file, adding setup info
  # 2018-07-19 18:00:01 - indentation change, tab to 2 spaces
  # 2018-12-20 18:42:00 - moving translation to Base translate
  # 2023-08-28 17:31:00 - updating visum url

  define('SITE_SHORTNAME', 'inventory');

  if (!file_exists(dirname(__FILE__).'/setup.php')) {
    header('Content-Type: text/plain');
?>
  Welcome. It appears that the setup file in include/setup.php is not present. Please go to the include
  directory and copy the example file setup-example.php to setup.php. Then edit the setup.php to fit your
  system setup. Thanks for using this software.
<?php
    die();
  }

  require_once('setup.php');
  require_once('base3.php');
  require_once('base.translate.php');

  /*
    1 = own
    2 = sold
    3 = given away
    4 = dumped
  */

  $link = db_connect();
  # mysql_set_charset('utf8', $link);

  $errors = array();

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
    STATUS_OWN => array('text' => 'Own', 'name' => 'own'),
    STATUS_OWNSELL => array('text' => 'Selling', 'name' => 'ownsell'),
    STATUS_SOLD => array('text' => 'Sold', 'name' => 'sold'),
    STATUS_GIVENAWAY =>  array('text' => 'Given away', 'name' => 'givenaway'),
    STATUS_DUMPED =>  array('text' => 'Dumped', 'name' => 'dumped'),
    STATUS_WISHED => array('text' => 'Wished', 'name' => 'wished'),
    STATUS_WISHED_SKIPPED => array('text' => 'Wished - dumped', 'name' => 'wishedskipped'),
  );

  define('USAGE_FREQUENT', 4);
  define('USAGE_SOMETIMES', 3);
  define('USAGE_SELDOM', 2);
  define('USAGE_NEVER', 1);
  define('USAGE_UNKNOWN', 0);

  $usage = array(
    USAGE_FREQUENT => 'Frequent',
    USAGE_SOMETIMES => 'Sometimes',
    USAGE_SELDOM => 'Seldom',
    USAGE_NEVER => 'Never',
    USAGE_UNKNOWN => 'Unknown'
  );

  # materials that may get attacked by clothes moths
  $mothmaterials = array(
    'kashmir',
    'lamb wool',
    'leather',
    'skin',
    'wool',
    'woollen',
    'yarn'
  );

  function copy_packlist($link, $id_packlists_from, $id_packlists_to) {

    # copy packlist item relations
    $sql = 'SELECT * FROM relations_packlists_items WHERE id_packlists="'.dbres($link, $id_packlists_from).'"';
    $r = db_query($link, $sql);

    foreach ($r as $k => $item) {
      unset($item['id'], $item['packed'], $item['inuse']);
      $item['id_packlists'] = $id_packlists_to;
      $item['created'] = date('Y-m-d H:i:s');

      # check that relation is not there before
      $sql = 'SELECT * FROM relations_packlists_items WHERE id_packlists="'.dbres($link, $id_packlists_to).'" AND id_items="'.dbres($link, $item['id_items']).'"';
      $r_insert = db_query($link, $sql);
      if (count($r_insert)) {
        continue;
      }

      # insert packlist relation
      $iu = dbpia($link, 	$item);
      $sql = 'INSERT INTO relations_packlists_items ('.implode(',', array_keys($iu)).') VALUES('.implode(',', $iu).')';
      # echo $sql."\n";
      $r_insert = db_query($link, $sql);
    }

    # copy packlist items
    $sql = 'SELECT * FROM packlist_items WHERE id_packlists="'.dbres($link, $id_packlists_from).'"';
    $r = db_query($link, $sql);

    foreach ($r as $k => $item) {
      unset($item['id'], $item['updated'], $item['packed'], $item['inuse']);
      $item['id_packlists'] = $id_packlists_to;
      $item['created'] = date('Y-m-d H:i:s');

      # check that item is not there before
      $sql = 'SELECT * FROM packlist_items WHERE id_packlists="'.dbres($link, $id_packlists_to).'" AND title="'.dbres($link, $item['title']).'" AND weight="'.dbres($link, $item['weight']).'"';
      $r_insert = db_query($link, $sql);
      if (count($r_insert)) {
        continue;
      }

      # insert packlist item
      $iu = dbpia($link, 	$item);
      $sql = 'INSERT INTO packlist_items ('.implode(',', array_keys($iu)).') VALUES('.implode(',', $iu).')';
      # echo $sql."\n";
      $r_insert = db_query($link, $sql);
    }
  }

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

  function print_login($username) {
?>
  <h2>Logga in</h2>
  <form action="?action=login&amp;logintype=local" method="post">
    <fieldset>
      <label for="username"><?php echo t('Username') ?></label>
      <input class="text" type="text" name="username" value="<?php echo $username?>"><br>
      <label for="password"><?php echo t('Password') ?></label>
      <input class="text" type="password" name="password" value=""><br>
      <input class="submit" type="submit" name="submit" value="<?php echo t('Save') ?>">
    </fieldset>
  </form>
<?php
    if (defined('ID_VISUM') && constant('ID_VISUM') !== false) {
?>
  <p class="login">
    <a href="//<?php echo BASE_DOMAINNAME?>/?section=visum&id_sites=<?php echo ID_VISUM?>"><?php echo t('Login with Visum here.') ?><a/>
  </p>
<?php
    }

    return true;
  }

  if (!file_exists(MAGICK_PATH.'convert')) {
    $errors[] = t('ImageMagick is not installed.');
  }

  function init_constants() {
    global $statuses, $usage;

    $statuses = array(
      STATUS_OWN => array('text' => t('Own'), 'name' => 'own'),
      STATUS_OWNSELL => array('text' => t('Selling'), 'name' => 'ownsell'),
      STATUS_SOLD => array('text' => t('Sold'), 'name' => 'sold'),
      STATUS_GIVENAWAY =>  array('text' => t('Given away'), 'name' => 'givenaway'),
      STATUS_DUMPED =>  array('text' => t('Dumped'), 'name' => 'dumped'),
      STATUS_WISHED => array('text' => t('Wished'), 'name' => 'wished'),
      STATUS_WISHED_SKIPPED => array('text' => t('Wished - dumped'), 'name' => 'wishedskipped'),
    );

    $usage = array(
      USAGE_FREQUENT => t('Frequent'),
      USAGE_SOMETIMES => t('Sometimes'),
      USAGE_SELDOM => t('Seldom'),
      USAGE_NEVER => t('Never'),
      USAGE_UNKNOWN => t('Unknown')
    );

  }

  # --- end of translations
  function validate_user($s) {
    if (preg_match('/[^A-Za-z0-9]/', $s)) return false;
    if (strlen($s) < 3) return false;
    if (strlen($s) > 16) return false;
    return true;
  }

  function validate_pass($s) {
    # at least 6 chars
    if (strlen($s) < 6) return false;

    # must contain a-z
    if (!preg_match('/^.*([a-z]).*$/', $s, $m)) return false;
    if (!preg_match('/^.*([0-9]).*$/', $s, $m)) return false;
    return true;
  }
?>
