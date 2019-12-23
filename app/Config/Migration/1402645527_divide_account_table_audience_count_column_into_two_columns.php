<?php
class DivideAccountTableAudienceCountColumnIntoTwoColumns extends CakeMigration {

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
          'facebook_audience_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'campaign_count'),
          'twitter_audience_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'facebook_audience_count'),
        ),
      ),
      'drop_field' => array(
        'accounts' => array('audience_count',),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'accounts' => array('facebook_audience_count', 'twitter_audience_count',),
      ),
      'create_field' => array(
        'accounts' => array(
          'audience_count' => array('type' => 'integer', 'null' => false, 'default' => NULL),
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
    if($direction == 'up') {
      $this->callback->out('audience_countのアップデートを開始');
      $Account = ClassRegistry::init('Account');
      $Audience = ClassRegistry::init('Audience');
      $Audience->setCounterCache();
      $accounts = $Account->find('list');
      $accountsCount = count($accounts);
      $i = 1;
      foreach($accounts as $accountId => $value) {
        $Audience->updateCounterCache(array('account_id' => $accountId));
        $this->callback->out($i . '/' . $accountsCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
