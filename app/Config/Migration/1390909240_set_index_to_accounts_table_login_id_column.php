<?php
class SetIndexToAccountsTableLoginIdColumn extends CakeMigration {

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
      'alter_field' => array(
        'accounts' => array(
          'login_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        ),
      ),
      'create_field' => array(
        'accounts' => array(
          'indexes' => array(
            'login_id' => array('column' => 'login_id', 'unique' => 1),
          ),
        ),
      ),
    ),
    'down' => array(
      'alter_field' => array(
        'accounts' => array(
          'login_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        ),
      ),
      'drop_field' => array(
        'accounts' => array('indexes' => array('login_id')),
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
