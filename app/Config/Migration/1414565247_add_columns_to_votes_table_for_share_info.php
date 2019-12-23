<?php
class AddColumnsToVotesTableForShareInfo extends CakeMigration {

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
        'votes' => array(
          'facebook_shared' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'facebook_username'),
          'facebook_shared_message' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'facebook_shared'),
          'twitter_shared' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'twitter_screen_name'),
          'twitter_shared_message' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'twitter_shared'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'votes' => array('facebook_shared', 'facebook_shared_message', 'twitter_shared', 'twitter_shared_message',),
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