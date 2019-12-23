<?php
class AddEntryAndEntrantCountLotteryColumnsToAudiencesTable extends CakeMigration {

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
				'audiences' => array(
					'entry_count_lottery' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count_vote'),
					'entrant_count_lottery' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entrant_count_vote'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'audiences' => array('entry_count_lottery', 'entrant_count_lottery',),
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
