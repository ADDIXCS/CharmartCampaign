<?php
class UpdateDatasForEntrantData extends CakeMigration {

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
      $Entry->bindModel(array('hasMany' => array('Vote')), false);
      $entries = $Entry->find('all');
      $entriesCount = count($entries);
      // モデルの設定
      $Entry->bindModel(array('belongsTo' => array('Entrant')), false);
      $Entry->setCounterCache();
      $Entry->Entrant->setCounterCache();
      $Entry->Entrant->Audience->virtualFields = array();
      $Vote = ClassRegistry::init('Vote');
      $Vote->setCounterCache();
      // トランザクション開始
      $dataSource = $Entry->getDataSource();
      $dataSource->begin();
      $i = 1;
      foreach($entries as $entry) {
        // 投票データの取得
        $votes = $entry['Vote'];
        unset($entry['Vote']);
        // 応募者データの取得
        $entrant = array();
        if($entry['Entry']['entry_type'] == 'facebook') {
          $entrant = $Entry->Entrant->find('first', array('conditions' => array(
            'Entrant.campaign_id' => $entry['Entry']['campaign_id'],
            'Entrant.facebook_id' => $entry['Entry']['facebook_id'],
          )));
        }
        if($entry['Entry']['entry_type'] == 'twitter') {
          $entrant = $Entry->Entrant->find('first', array('conditions' => array(
            'Entrant.campaign_id' => $entry['Entry']['campaign_id'],
            'Entrant.twitter_id' => $entry['Entry']['twitter_id'],
          )));
        }
        // 保存データの作成
        unset($entry['Entry']['entrant_id']);
        unset($entry['Entry']['vote_count']);
        if(!empty($entrant)) {
          $entry['Entrant'] = array('id' => $entrant['Entrant']['id']) + $entry['Entry'];
        } else {
          $entry['Entrant'] = $entry['Entry'];
          unset($entry['Entrant']['id']);
        }
        // 応募者データの保存
        if(!$Entry->saveAssociated(Hash::filter($entry))) {
          $dataSource->rollback();
          return false;
        }
        $this->callback->out($i . '/' . $entriesCount . ' 完了');
        $i++;
        // 投票データにentrant_idを設定
        if($votes) {
          $this->callback->out('  voteデータのアップデート');
          $entrant_id = !empty($entrant)
            ? $entrant['Entrant']['id']
            : $Entry->Entrant->getLastInsertId();
          $votesCount = count($votes);
          $j = 1;
          foreach($votes as $vote) {
            $Vote->id = $vote['id'];
            if(!$Vote->save(array(
              'entrant_id' => $entrant_id,
              'modified' => false,
            ))) {
              $dataSource->rollback();
              return false;
            }
            $this->callback->out('  ' . $j . '/' . $votesCount . ' 完了');
            $j++;
          }
        }
      }
      $dataSource->commit();
    }
    return true;
  }
}
