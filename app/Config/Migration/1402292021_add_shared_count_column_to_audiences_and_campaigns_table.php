<?php
class AddSharedCountColumnToAudiencesAndCampaignsTable extends CakeMigration {

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
        'audiences' => array(
          'shared_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count'),
        ),
        'campaigns' => array(
          'shared_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'entry_count'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'audiences' => array('shared_count',),
        'campaigns' => array('shared_count',),
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
      $this->callback->out('shared_countのアップデートを開始');
      $Entry = ClassRegistry::init('Entry');
      $Entry->setCounterCache();
      $this->callback->out('audiencesテーブル');
      $Audience = ClassRegistry::init('Audience');
      $audiences = $Audience->find('list');
      $audiencesCount = count($audiences);
      $i = 1;
      foreach($audiences as $audienceId => $value) {
        $Entry->updateCounterCache(array('audience_id' => $audienceId));
        $this->callback->out($i . '/' . $audiencesCount . ' 完了');
        $i++;
      }
      $this->callback->out('campaignsテーブル');
      $Campaign = ClassRegistry::init('Campaign');
      $campaigns = $Campaign->find('list');
      $campaignsCount = count($campaigns);
      $i = 1;
      foreach($campaigns as $campaignId => $value) {
        $Entry->updateCounterCache(array('campaign_id' => $campaignId));
        $this->callback->out($i . '/' . $campaignsCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
