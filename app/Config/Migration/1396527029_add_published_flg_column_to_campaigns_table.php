<?php
class AddPublishedFlgColumnToCampaignsTable extends CakeMigration {

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
					'published_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'campaign_type'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('published_flg',),
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
