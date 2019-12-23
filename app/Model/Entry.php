<?php
class Entry extends AppModel {
  public $actsAs = [
    'SorapsUser',
    'SorapsCsv',
    'Ique.IqueS3' => ['contest_image'],
  ];
/**
 * 応募方法の種類
 */
  public static $entryTypes = ['email', 'facebook', 'twitter'];
  public static $socialEntryTypes = ['facebook', 'twitter'];
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
    $entryCounterScope = ['entry_count' => ['Entry.deleted' => false]];
    $audienceEntryCounterScope = [
      'entry_count' => ['Entry.deleted' => false],
      'entry_count_prize' => ['Entry.campaign_type' => 'prize', 'Entry.deleted' => false],
      'entry_count_contest' => ['Entry.campaign_type' => 'contest', 'Entry.deleted' => false],
      'entry_count_vote' => ['Entry.campaign_type' => 'vote', 'Entry.deleted' => false],
      'entry_count_coupon' => ['Entry.campaign_type' => 'coupon', 'Entry.deleted' => false],
      'entry_count_lottery' => ['Entry.campaign_type' => 'lottery', 'Entry.deleted' => false],
      'entry_count_shindan' => ['Entry.campaign_type' => 'shindan', 'Entry.deleted' => false],
    ];
    $shareCounterScope = ['shared_count' => [
      'OR' => [
        ['Entry.entry_type' => 'facebook', 'Entry.facebook_shared' => true],
        ['Entry.entry_type' => 'twitter', 'Entry.twitter_shared' => true],
      ],
      'Entry.deleted' => false,
    ]];
    $couponUsedCounterScope = [
      'coupon_used_count' => ['Entry.coupon_used' => true, 'Entry.deleted' => false],
    ];
    $this->bindModel(['belongsTo' => [
      'Entrant'  => ['counterCache' => $entryCounterScope + $shareCounterScope],
      'Audience' => ['counterCache' => $audienceEntryCounterScope + $shareCounterScope],
      'Campaign' => [
        'counterCache' => $entryCounterScope + $shareCounterScope + $couponUsedCounterScope,
      ],
      'Account'  => ['counterCache' => $entryCounterScope],
      'Gift'     => ['counterCache' => $entryCounterScope],
      'Result'   => ['counterCache' => $entryCounterScope],
    ]], false);
  }
}

