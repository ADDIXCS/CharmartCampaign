<?php
class AddEntryShareFlgColumnToCampaignsTableForEditEntry extends CakeMigration {

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
					'entry_share_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'twitter_fangate_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('entry_share_flg',),
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
