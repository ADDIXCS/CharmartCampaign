<?php
class CreateGiftsTable extends CakeMigration {

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
			'create_table' => array(
				'gifts' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entry_count' => array('type' => 'integer', 'null' => false, 'default' => NULL),
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'image' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'win_count' => array('type' => 'integer', 'null' => false, 'default' => NULL),
					'win_probability' => array('type' => 'integer', 'null' => false, 'default' => NULL),
					'win_daily' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'deleted' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
					'deleted_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'gifts'
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
