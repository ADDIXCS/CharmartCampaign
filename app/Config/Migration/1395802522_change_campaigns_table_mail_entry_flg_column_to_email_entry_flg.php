<?php
class ChangeCampaignsTableMailEntryFlgColumnToEmailEntryFlg extends CakeMigration {

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
					'email_entry_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'after' => 'sponsor_name'),
				),
			),
			'drop_field' => array(
				'campaigns' => array('mail_entry_flg',),
			),
		),
		'down' => array(
			'drop_field' => array(
				'campaigns' => array('email_entry_flg',),
			),
			'create_field' => array(
				'campaigns' => array(
					'mail_entry_flg' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
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
