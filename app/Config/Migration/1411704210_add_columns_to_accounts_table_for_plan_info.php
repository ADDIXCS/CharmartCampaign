<?php
class AddColumnsToAccountsTableForPlanInfo extends CakeMigration {

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
        'accounts' => array(
          'plan' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'twitter_screen_name'),
          'plan_prize_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'plan'),
          'plan_vote_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'plan_prize_flg'),
          'plan_lottery_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'plan_vote_flg'),
          'plan_shindan_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'plan_lottery_flg'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'accounts' => array('plan', 'plan_prize_flg', 'plan_vote_flg', 'plan_lottery_flg', 'plan_shindan_flg',),
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
      $Account = ClassRegistry::init('Account');
      if(!$Account->updateAll(
        array('Account.plan' => "'professional'"),
        array('Account.role' => 'client')
      )) {
        $this->callback->out('クライアントデータのアップデートに失敗');
        return false;
      }
      $this->callback->out('クライアントデータのアップデートに成功');
    }
    return true;
  }
}
