<?php
class AddColumnsToAccountsAndAudiencesTableForContest extends CakeMigration {

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
					'plan_contest_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'plan_prize_flg'),
				),
				'audiences' => array(
					'entry_count_contest' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count_prize'),
					'entrant_count_contest' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entrant_count_prize'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'accounts' => array('plan_contest_flg',),
				'audiences' => array('entry_count_contest', 'entrant_count_contest',),
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
