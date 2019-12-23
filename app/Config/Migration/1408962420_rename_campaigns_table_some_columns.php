<?php
class RenameCampaignsTableSomeColumns extends CakeMigration {

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
          'entry_btn_text_flg' => 'entry_btn_label_flg',
          'entry_btn_text' => 'entry_btn_label',
          'finish_message_text_flg' => 'finish_message_flg',
          'finish_message_text' => 'finish_message',
          'share_btn_text_flg' => 'share_btn_label_flg',
          'share_btn_text' => 'share_btn_label',
        ),
      ),
    ),
    'down' => array(
      'rename_field' => array(
        'campaigns' => array(
          'entry_btn_label_flg' => 'entry_btn_text_flg',
          'entry_btn_label' => 'entry_btn_text',
          'finish_message_flg' => 'finish_message_text_flg',
          'finish_message' => 'finish_message_text',
          'share_btn_label_flg' => 'share_btn_text_flg',
          'share_btn_label' => 'share_btn_text',
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
