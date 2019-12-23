<?php
class ChangeAccountsTableSomeColumnsName extends CakeMigration {

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
      'rename_field' => array(
        'accounts' => array(
          'name' => 'screen_name',
          'login_id' => 'account_name',
          'login_pass' => 'password',
        ),
      ),
      'create_field' => array(
        'accounts' => array(
          'indexes' => array(
            'account_name' => array('column' => 'account_name', 'unique' => 1),
          ),
        ),
      ),
      'drop_field' => array(
        'accounts' => array('indexes' => array('login_id')),
      ),
    ),
    'down' => array(
      'rename_field' => array(
        'accounts' => array(
          'screen_name' => 'name',
          'account_name' => 'login_id',
          'password' => 'login_pass',
        ),
      ),
      'create_field' => array(
        'accounts' => array(
          'indexes' => array(
            'login_id' => array('column' => 'login_id', 'unique' => 1),
          ),
        ),
      ),
      'drop_field' => array(
        'accounts' => array('indexes' => array('account_name')),
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
