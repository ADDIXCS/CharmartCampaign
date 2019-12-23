<?php
class AddColumnsToEntriesTableAndAudiencesTableForBackendData extends CakeMigration {

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
        'audiences' => array(
          'facebook_friend_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'facebook_username'),
          'user_agent' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'street'),
        ),
        'entries' => array(
          'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'campaign_id'),
          'facebook_friend_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'facebook_username'),
          'user_agent' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'street'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'audiences' => array('facebook_friend_count', 'user_agent',),
        'entries' => array('account_id', 'facebook_friend_count', 'user_agent',),
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
