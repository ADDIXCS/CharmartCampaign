<?php
class ChangeCampaignsTableHeaderImageToTopImage extends CakeMigration {

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
          'header_image_flg' => 'top_image_flg',
          'header_image' => 'top_image',
        ),
      ),
    ),
    'down' => array(
      'rename_field' => array(
        'campaigns' => array(
          'top_image_flg' => 'header_image_flg',
          'top_image' => 'header_image',
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
