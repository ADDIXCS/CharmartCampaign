<?php
class Audience extends AppModel {
  public $actsAs = ['Search.Searchable', 'SorapsUser', 'SorapsCsv'];
  public $virtualFields = [
    'friend_count' => 'facebook_friend_count + twitter_followers_count',
    'engagement' => '
      entrant_count_prize   * 20 +
      entrant_count_contest * 50 +
      entrant_count_vote    * 15 +
      entrant_count_coupon  * 20 +
      entrant_count_lottery * 20 +
      entrant_count_shindan * 15 +
      (entry_count_contest - entrant_count_contest) * 30 +
      (entry_count_coupon  - entrant_count_coupon)  * 5 +
      (entry_count_lottery - entrant_count_lottery) * 5 +
      (entry_count_shindan - entrant_count_shindan) * 3 +
      shared_count * 10 +
      (vote_count - entrant_count_vote) * 3
    ',
  ];
  public $filterArgs = [
    'entry_type' => ['type' => 'value'],
    'gender' => ['type' => 'value'],
    'age_min' => ['type' => 'query', 'method' => 'searchAgeRange'],
    'age_max' => ['type' => 'query', 'method' => 'searchAgeRange'],
    'state' => ['type' => 'value'],
    'campaign' => [
      'type' => 'subquery', 'method' => 'searchCampaign', 'field' => 'Audience.id'
    ],
    'entry_count_min' => ['type' => 'value', 'field' => 'Audience.entry_count >='],
    'entry_count_max' => ['type' => 'value', 'field' => 'Audience.entry_count <='],
    'friend_count_min' => ['type' => 'value', 'field' => 'Audience.friend_count >='],
    'friend_count_max' => ['type' => 'value', 'field' => 'Audience.friend_count <='],
    'past_date_min' => ['type' => 'query', 'method' => 'searchPastDateRange'],
    'past_date_max' => ['type' => 'query', 'method' => 'searchPastDateRange'],
    'engagement_min' => ['type' => 'value', 'field' => 'Audience.engagement >='],
    'engagement_max' => ['type' => 'value', 'field' => 'Audience.engagement <='],
    'name' => [
      'type' => 'like',
      'field' => ['Audience.last_name', 'Audience.first_name'],
    ],
  ];
/**
 * 年齢での検索
 */
  public function searchAgeRange($data = []) {
    $query = [];
    if(array_key_exists('age_min', $data)) {
      $query['Audience.birthday <='] = date('Y-m-d', strtotime((-$data['age_min']) . ' year'));
    }
    if(array_key_exists('age_max', $data)) {
      $query['Audience.birthday >'] = date('Y-m-d', strtotime((-$data['age_max'] - 1) . ' year'));
    }
    return $query;
  }
/**
 * 経過日数での検索
 */
  public function searchPastDateRange($data = []) {
    $query = [];
    if(array_key_exists('past_date_min', $data)) {
      $query['Audience.modified <'] = date('Y-m-d', strtotime((-$data['past_date_min'] + 1) . ' day'));
    }
    if(array_key_exists('past_date_max', $data)) {
      $query['Audience.modified >='] = date('Y-m-d', strtotime((-$data['past_date_max']) . ' day'));
    }
    return $query;
  }
/**
 * 参加キャンペーンでの検索
 */
  public function searchCampaign($data = []) {
    $this->bindModel(['hasMany' => ['Entry']]);
    $this->Entry->Behaviors->attach('Search.Searchable');
    $query = $this->Entry->getQuery('all', [
      'conditions' => ['campaign_id' => $data['campaign']],
      'fields' => ['audience_id'],
    ]);
    return $query;
  }
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
    $counterScope = [
      'facebook_audience_count' => [
        'Audience.entry_type' => 'facebook',
        'Audience.deleted' => false,
      ],
      'twitter_audience_count' => [
        'Audience.entry_type' => 'twitter',
        'Audience.deleted' => false,
      ],
    ];
    $this->bindModel(['belongsTo' => [
      'Account' => ['counterCache' => $counterScope],
    ]], false);
  }
}
