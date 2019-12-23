<?php
class AddFacebookSharedAndTwitterSharedColumnToEntriesTable extends CakeMigration {

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
				'entries' => array(
					'facebook_shared' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'facebook_friend_count'),
					'twitter_shared' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'twitter_profile_image_url'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'entries' => array('facebook_shared', 'twitter_shared',),
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
