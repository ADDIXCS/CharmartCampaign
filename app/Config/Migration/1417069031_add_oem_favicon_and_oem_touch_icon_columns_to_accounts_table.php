<?php
class AddOemFaviconAndOemTouchIconColumnsToAccountsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
  public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
  public $migration = array(
    'up' => array(
      'create_field' => array(
        'accounts' => array(
          'oem_favicon' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'oem_og_image'),
          'oem_touch_icon' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'oem_favicon'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'accounts' => array('oem_favicon', 'oem_touch_icon',),
      ),
    ),
  );

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
  public function before($direction) {
    return true;
  }

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
  public function after($direction) {
    return true;
  }
}