<?php
class ChangeEntriesTableAndAudiencesTableBirthdayColumnToAllowNull extends CakeMigration {

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
				'audiences' => array(
					'birthday' => array('type' => 'date', 'null' => true, 'default' => NULL),
				),
				'entries' => array(
					'birthday' => array('type' => 'date', 'null' => true, 'default' => NULL),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'audiences' => array(
					'birthday' => array('type' => 'date', 'null' => false, 'default' => NULL),
				),
				'entries' => array(
					'birthday' => array('type' => 'date', 'null' => false, 'default' => NULL),
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
