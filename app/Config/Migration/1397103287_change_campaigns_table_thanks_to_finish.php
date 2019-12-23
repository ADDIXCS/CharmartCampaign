<?php
class ChangeCampaignsTableThanksToFinish extends CakeMigration {

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
          'thanks_message_text_flg' => 'finish_message_text_flg',
          'thanks_message_text' => 'finish_message_text',
          'thanks_bnr_flg' => 'finish_bnr_flg',
          'thanks_bnr' => 'finish_bnr',
          'thanks_bnr_url' => 'finish_bnr_url',
        ),
      ),
    ),
    'down' => array(
      'rename_field' => array(
        'campaigns' => array(
          'finish_message_text_flg' => 'thanks_message_text_flg',
          'finish_message_text' => 'thanks_message_text',
          'finish_bnr_flg' => 'thanks_bnr_flg',
          'finish_bnr' => 'thanks_bnr',
          'finish_bnr_url' => 'thanks_bnr_url',
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
