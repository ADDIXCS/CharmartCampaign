<?php
class AddColumnsToCampaignsTableForEditTop extends CakeMigration {

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
					'header_image_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'end_date'),
					'gift_image_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'gift_name'),
					'gift_image' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'gift_image_flg'),
					'announce_date_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'win_count'),
					'announce_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'after' => 'announce_date_flg'),
					'sponsor_url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'sponsor_name'),
					'sponsor_contact' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'sponsor_url'),
					'notice' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'sponsor_contact'),
					'entry_btn_text_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'notice'),
					'entry_btn_text' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'entry_btn_text_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('header_image_flg', 'gift_image_flg', 'gift_image', 'announce_date_flg', 'announce_date', 'sponsor_url', 'sponsor_contact', 'notice', 'entry_btn_text_flg', 'entry_btn_text',),
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
