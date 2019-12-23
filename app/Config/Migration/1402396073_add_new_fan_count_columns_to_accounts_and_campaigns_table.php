<?php
class AddNewFanCountColumnsToAccountsAndCampaignsTable extends CakeMigration {

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
					'facebook_new_fan_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count'),
					'twitter_new_fan_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'facebook_new_fan_count'),
				),
				'campaigns' => array(
					'facebook_new_fan_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count'),
					'twitter_new_fan_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'facebook_new_fan_count'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'accounts' => array('facebook_new_fan_count', 'twitter_new_fan_count',),
				'campaigns' => array('facebook_new_fan_count', 'twitter_new_fan_count',),
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
