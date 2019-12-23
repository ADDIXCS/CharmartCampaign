<?php
class ChangeCampaignsTableTypeColumnToCampaignType extends CakeMigration {

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
      'rename_field' => array(
        'campaigns' => array(
          'type' => 'campaign_type',
        ),
      ),
    ),
    'down' => array(
      'rename_field' => array(
        'campaigns' => array(
          'campaign_type' => 'type',
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
