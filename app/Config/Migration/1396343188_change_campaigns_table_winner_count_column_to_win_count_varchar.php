<?php
class ChangeCampaignsTableWinnerCountColumnToWinCountVarchar extends CakeMigration {

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
      'alter_field' => array(
        'campaigns' => array(
          'winner_count' => array('name' => 'win_count', 'type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        ),
      ),
    ),
    'down' => array(
      'alter_field' => array(
        'campaigns' => array(
          'win_count' => array('name' => 'winner_count', 'type' => 'integer', 'null' => false, 'default' => NULL),
        ),
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
