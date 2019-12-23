<?php
class AddColumnsToVotesTableForSocialInfo extends CakeMigration {

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
          'facebook_friend_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'facebook_username'),
          'twitter_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'twitter_id'),
          'twitter_friends_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'twitter_screen_name'),
          'twitter_followers_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'twitter_friends_count'),
          'twitter_profile_image_url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'twitter_followers_count'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'votes' => array('facebook_friend_count', 'twitter_name', 'twitter_friends_count', 'twitter_followers_count', 'twitter_profile_image_url',),
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
