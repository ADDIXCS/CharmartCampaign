<?php
class UpdateVoteDatasByAudienceData extends CakeMigration {

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
      $this->callback->out('voteデータのアップデートを開始');
      // キャンペーンデータ（投票コンテストの投票データは除く）
      $Campaign = ClassRegistry::init('Campaign');
      $campaigns = $Campaign->find('list', array('conditions' => array(
        'campaign_type' => array('contest', 'lancers'),
      )));
      // 投票データ
      $Vote = ClassRegistry::init('Vote');
      $EntrySaveService = ClassRegistry::init('EntrySaveService');
      $Vote->bindModel(array('belongsTo' => array('Audience' => array(
        'fields' => $EntrySaveService->userFields,
      ))), false);
      $votes = $Vote->find('all', array('conditions' => array(
        'campaign_id' => array_keys($campaigns),
      )));
      $votesCount = count($votes);
      // トランザクション開始
      $dataSource = $Vote->getDataSource();
      $dataSource->begin();
      $i = 1;
      foreach($votes as $vote) {
        // 保存データの作成
        unset($vote['Vote']['facebook_friend_count']);
        unset($vote['Vote']['twitter_friends_count']);
        unset($vote['Vote']['twitter_followers_count']);
        $data = Hash::filter($vote['Vote']) + Hash::filter($vote['Audience']);
        // データの保存
        if(!$Vote->save($data)) {
          $dataSource->rollback();
          return false;
        }
        $this->callback->out($i . '/' . $votesCount . ' 完了');
        $i++;
      }
      $dataSource->commit();
    }
    return true;
  }
}
