<?php
class AddInputColumnsToCampaignsTable extends CakeMigration {

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
          'input_name' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'original_design_flg'),
          'input_kana' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_name'),
          'input_tel' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_kana'),
          'input_gender' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_tel'),
          'input_birthday' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_gender'),
          'input_postcode' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_birthday'),
          'input_state' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_postcode'),
          'input_city' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_state'),
          'input_street' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_city'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'campaigns' => array('input_name', 'input_kana', 'input_tel', 'input_gender', 'input_birthday', 'input_postcode', 'input_state', 'input_city', 'input_street',),
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
    if($direction == 'up') {
      $Campaign = ClassRegistry::init('Campaign');
      if(!$Campaign->updateAll(array(
        'input_name'     => 1,
        'input_kana'     => 1,
        'input_tel'      => 1,
        'input_gender'   => 1,
        'input_birthday' => 1,
        'input_postcode' => 1,
        'input_state'    => 1,
        'input_city'     => 1,
        'input_street'   => 1,
      ), array('Campaign.campaign_type !=' => 'shindan'))) {
        $this->callback->out('campaignデータのアップデートに失敗');
        return false;
      }
      $this->callback->out('campaignデータのアップデートに成功');
    }
    return true;
  }
}
