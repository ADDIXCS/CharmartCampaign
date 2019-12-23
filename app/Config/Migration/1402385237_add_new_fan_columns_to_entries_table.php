<?php
class AddNewFanColumnsToEntriesTable extends CakeMigration {

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
				'entries' => array(
					'facebook_new_fan' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'facebook_shared'),
					'twitter_new_fan' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'twitter_shared'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'entries' => array('facebook_new_fan', 'twitter_new_fan',),
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
