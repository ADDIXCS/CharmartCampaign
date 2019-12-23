<?php
class AddColumnsToCampaignsToCampaignsTableForShindanCounts extends CakeMigration {

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
				'campaigns' => array(
					'questions_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'finish_bnr_url'),
					'answers_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'questions_count'),
					'results_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'answers_count'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('questions_count', 'answers_count', 'results_count',),
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
