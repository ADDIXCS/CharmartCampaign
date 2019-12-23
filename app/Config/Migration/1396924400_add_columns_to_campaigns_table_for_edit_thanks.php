<?php
class AddColumnsToCampaignsTableForEditThanks extends CakeMigration {

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
					'thanks_message_text_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'entry_share_flg'),
					'thanks_message_text' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'thanks_message_text_flg'),
					'share_btn_text_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'thanks_message_text'),
					'share_btn_text' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'share_btn_text_flg'),
					'share_double_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'share_btn_text'),
					'thanks_bnr_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'share_double_flg'),
					'thanks_bnr' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'thanks_bnr_flg'),
					'thanks_bnr_url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'thanks_bnr'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('thanks_message_text_flg', 'thanks_message_text', 'share_btn_text_flg', 'share_btn_text', 'share_double_flg', 'thanks_bnr_flg', 'thanks_bnr', 'thanks_bnr_url',),
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
