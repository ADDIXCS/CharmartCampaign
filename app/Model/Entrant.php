<?php
class Entrant extends AppModel {
  public $actsAs = ['SorapsUser'];
/**
 * リレーション先のモデルとして呼び出された際にSorapsUserBehaviorのafterFindを適用する
 */
  public function afterFind($results, $primary = false) {
    if($primary) {
      return parent::afterFind($results, $primary);
    } else {
      return $this->Behaviors->dispatchMethod(
        $this,
        'modifySorapsUserData',
        [parent::afterFind($results, $primary)]
      );
    }
  }
/**
 * counterCache用のモデルのbind
 */
  public function setCounterCache() {
    $entrantCounterScope = ['entrant_count' => ['Entrant.deleted' => false]];
    $audienceEntrantCounterScope = [
      'entrant_count_prize' => ['Entrant.campaign_type' => 'prize', 'Entrant.deleted' => false],
      'entrant_count_contest' => ['Entrant.campaign_type' => 'contest', 'Entrant.deleted' => false],
      'entrant_count_vote' => ['Entrant.campaign_type' => 'vote', 'Entrant.deleted' => false],
      'entrant_count_coupon' => ['Entrant.campaign_type' => 'coupon', 'Entrant.deleted' => false],
      'entrant_count_lottery' => ['Entrant.campaign_type' => 'lottery', 'Entrant.deleted' => false],
      'entrant_count_shindan' => ['Entrant.campaign_type' => 'shindan', 'Entrant.deleted' => false],
    ];
    $this->bindModel(['belongsTo' => [
      'Campaign' => ['counterCache' => $entrantCounterScope],
      'Audience' => ['counterCache' => $audienceEntrantCounterScope],
    ]], false);
  }
}
