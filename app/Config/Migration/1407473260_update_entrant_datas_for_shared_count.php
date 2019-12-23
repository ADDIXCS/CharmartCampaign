<?php
class UpdateEntrantDatasForSharedCount extends CakeMigration {

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
    ),
    'down' => array(
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
      $this->callback->out('entryデータのアップデートを開始');
      // entrantデータを作成したマイグレーションのレコードを取得
      $SchemaMigration = ClassRegistry::init('SchemaMigration');
      $migration = $SchemaMigration->findByClass('UpdateDatasForEntrantData');
      // entryデータの取得
      $Entry = ClassRegistry::init('Entry');
      $entries = $Entry->find('all', array('conditions' => array(
        'Entry.created >' => $migration['SchemaMigration']['created'],
        'OR' => array(
          array('Entry.entry_type' => 'facebook', 'Entry.facebook_shared' => true),
          array('Entry.entry_type' => 'twitter', 'Entry.twitter_shared' => true),
        ),
      )));
      $Entry->setCounterCache();
      $entriesCount = count($entries);
      $i = 1;
      foreach($entries as $entry) {
        $Entry->updateCounterCache(array('entrant_id' => $entry['Entry']['entrant_id']));
        $this->callback->out($i . '/' . $entriesCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
