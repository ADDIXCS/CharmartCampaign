<?php
class AddOemFlgColumnToAccountsTableAndChangeDomainToOemDomain extends CakeMigration {

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
          'oem_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'role'),
        ),
      ),
      'rename_field' => array(
        'accounts' => array(
          'domain' => 'oem_domain',
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'accounts' => array('oem_flg',),
      ),
      'rename_field' => array(
        'accounts' => array(
          'oem_domain' => 'domain',
        ),
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
