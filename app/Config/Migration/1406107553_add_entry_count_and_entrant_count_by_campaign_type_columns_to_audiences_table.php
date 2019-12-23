<?php
class AddEntryCountAndEntrantCountByCampaignTypeColumnsToAudiencesTable extends CakeMigration {

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
					'entry_count_prize' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count'),
					'entry_count_vote' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count_prize'),
					'entry_count_shindan' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count_vote'),
					'entrant_count_prize' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count_shindan'),
					'entrant_count_vote' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entrant_count_prize'),
					'entrant_count_shindan' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entrant_count_vote'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'audiences' => array('entry_count_prize', 'entry_count_vote', 'entry_count_shindan', 'entrant_count_prize', 'entrant_count_vote', 'entrant_count_shindan',),
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
