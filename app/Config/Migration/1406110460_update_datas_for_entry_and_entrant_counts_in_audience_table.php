<?php
class UpdateDatasForEntryAndEntrantCountsInAudienceTable extends CakeMigration {

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
      ini_set('memory_limit', '256M');
      $this->callback->out('entryデータのアップデートを開始');
      // 応募データ
      $Entry = ClassRegistry::init('Entry');
      $Entry->bindModel(array('belongsTo' => array('Campaign', 'Entrant')), false);
      $entries = $Entry->find('all');
      $entriesCount = count($entries);
      // モデルの設定
      $Entry->setCounterCache();
      $Entry->Entrant->setCounterCache();
      // トランザクション開始
      $dataSource = $Entry->getDataSource();
      $dataSource->begin();
      $i = 1;
      foreach($entries as $entry) {
        // 保存データの作成
        $entry['Entry']['campaign_type'] = $entry['Campaign']['campaign_type'];
        $entry['Entrant']['campaign_type'] = $entry['Campaign']['campaign_type'];
        unset($entry['Campaign']);
        // 応募者データの保存
        if(!$Entry->saveAssociated(Hash::filter($entry))) {
          $dataSource->rollback();
          return false;
        }
        $this->callback->out($i . '/' . $entriesCount . ' 完了');
        $i++;
      }
      $dataSource->commit();
    }
    return true;
  }
}
