<?php
class AddColumnsToAccountsTableForSoftDelete extends CakeMigration {

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
					'deleted' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'role'),
					'deleted_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'after' => 'deleted'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'accounts' => array('deleted', 'deleted_date',),
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
