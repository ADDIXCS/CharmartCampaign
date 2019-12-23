<?php
class AddColumnsToCampaignsTableForEntryInfo extends CakeMigration {

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
					'mail_entry_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'after' => 'sponsor_name'),
					'facebook_entry_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'after' => 'mail_entry_flg'),
					'facebook_fangate_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'after' => 'facebook_entry_flg'),
					'twitter_entry_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'after' => 'facebook_fangate_flg'),
					'twitter_fangate_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'after' => 'twitter_entry_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('mail_entry_flg', 'facebook_entry_flg', 'facebook_fangate_flg', 'twitter_entry_flg', 'twitter_fangate_flg',),
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
