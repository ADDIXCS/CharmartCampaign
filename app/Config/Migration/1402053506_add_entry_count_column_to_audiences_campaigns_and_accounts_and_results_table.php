<?php
class AddEntryCountColumnToAudiencesCampaignsAndAccountsAndResultsTable extends CakeMigration {

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
          'entry_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'parent_id'),
        ),
        'audiences' => array(
          'entry_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'account_id'),
        ),
        'campaigns' => array(
          'entry_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'account_id'),
        ),
        'results' => array(
          'entry_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'campaign_id'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'accounts' => array('entry_count',),
        'audiences' => array('entry_count',),
        'campaigns' => array('entry_count',),
        'results' => array('entry_count',),
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
      $this->callback->out('entry_countのアップデートを開始');
      $Entry = ClassRegistry::init('Entry');
      $Entry->setCounterCache();
      $entries = $Entry->find('list');
      $entriesCount = count($entries);
      $i = 1;
      foreach($entries as $entryId => $value) {
        $Entry->save(array('id' => (string) $entryId, 'modified' => false));
        $this->callback->out($i . '/' . $entriesCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
