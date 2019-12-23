<?php
class AddAgeLimitColumnToCampaignsTableForEditDetail extends CakeMigration {

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
					'age_limit' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 2, 'after' => 'thanks_bnr_url'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('age_limit',),
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
