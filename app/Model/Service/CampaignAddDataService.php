<?php
class CampaignAddDataService extends Object {
/**
 * キャンペーンの開催状態をセット
 */
  public function addStatus($campaigns, $client = null) {
    if(array_key_exists('Campaign', $campaigns)) {
      $campaigns['Campaign'] = $this->_addStatus($campaigns['Campaign'], $client);
    } else {
      foreach($campaigns as &$campaign) {
        // 開催中のキャンペーン一覧ページ用
        if(array_key_exists('Account', $campaign)) {
          $client['Account'] = $campaign['Account'];
        }
        $campaign['Campaign'] = $this->_addStatus($campaign['Campaign'], $client);
      }
    }
    return $campaigns;
  }
  protected function _addStatus($campaign, $client) {
    if(array_key_exists('published_flg', $campaign)) {
      $CampaignEditService = ClassRegistry::init('CampaignEditService');
      switch(true) {
        case !in_array($campaign['campaign_type'], CampaignValue::getAvailableTypes($client)):
          $campaign['status'] = 'stopped';
          $campaign['status_ja'] = '停止';
          break;
        case !$CampaignEditService->isEditFinished(['Campaign' => $campaign]):
          $campaign['status'] = 'editing';
          $campaign['status_ja'] = '作成中';
          break;
        case !$campaign['published_flg']:
          $campaign['status'] = 'closed';
          $campaign['status_ja'] = '非公開';
          break;
        case $campaign['published_flg'] == 2:
          $campaign['status'] = 'test';
          $campaign['status_ja'] = 'テストモード';
          break;
        case time() < strtotime($campaign['start_date']):
          $campaign['status'] = 'prepared';
          $campaign['status_ja'] = '準備中';
          break;
        case !$campaign['end_date_flg'] && time() > strtotime($campaign['end_date']):
          $campaign['status'] = 'finished';
          $campaign['status_ja'] = '終了';
          break;
        case (
          $campaign['campaign_type'] == 'coupon' && $campaign['coupon_limit'] &&
          $campaign['entry_count'] >= $campaign['coupon_limit']
        ):
          $campaign['status'] = 'stockout';
          $campaign['status_ja'] = '在庫切れ';
          break;
        default:
          $campaign['status'] = 'published';
          $campaign['status_ja'] = '開催中';
      }
    }
    return $campaign;
  }
/**
 * キャンペーン一覧用のデータを集計
 */
  public function addListData($campaigns) {
    foreach($campaigns as &$campaign) {
      $campaign['Campaign']['total_reach_count'] = 0;
      foreach($campaign['Entry'] as $entry) {
        if($entry['shared']) {
          switch($entry['entry_type']) {
            case 'facebook':
              $campaign['Campaign']['total_reach_count'] += $entry['facebook_friend_count'];
              break;
            case 'twitter':
              $campaign['Campaign']['total_reach_count'] += $entry['twitter_followers_count'];
              break;
          }
        }
      }
    }
    return $campaigns;
  }
/**
 * インサイト用のデータを集計
 */
  public function addInsightData($_campaign) {
    // デバイス判別用オブジェクトの初期化
    ini_set('memory_limit', '256M');
    $this->browscap = new phpbrowscap\Browscap(TMP . 'browscap');
    $this->browscap->doAutoUpdate = false;
    $this->mobileDetect = new Mobile_Detect();
    // 変数のフラット化
    $campaign = $_campaign['Campaign'];
    $entrants = $_campaign['Entrant'];
    // データ格納用変数定義
    $campaign = $this->_addDefaultValue($campaign);
    $campaign['total_entrants_count']    = count($entrants);
    // 参加データのループ開始
    foreach($entrants as $entrant) {
      // 参加者数
      $campaign[$entrant['entry_type'] . '_entrants_count']++;
      // 各種参加者データの追加
      $campaign = $this->_addGenderAge($campaign, $entrant);
      $campaign = $this->_addFriendCount($campaign, $entrant);
      $campaign = $this->_addState($campaign, $entrant);
      // 参加データの処理
      foreach($entrant['Entry'] as $entry) {
        // 参加数
        $campaign['total_entries_count']++;
        $campaign[$entry['entry_type'] . '_entries_count']++;
        if(!empty($entry['shared'])) {
          // シェア数
          $campaign['total_shared_count']++;
          $campaign[$entry['entry_type'] . '_shared_count']++;
          // リーチ数
          $campaign = $this->_addReach($campaign, $entry);
        }
        // 参加時間
        $campaign['time'][date('G', strtotime($entry['created']))]++;
        // 環境
        $campaign = $this->_addEnv($campaign, $entry);
      }
    }
    // データを多い順に並び替え
    arsort($campaign['state']);
    arsort($campaign['env']['desktop']['os']);
    arsort($campaign['env']['desktop']['browser']);
    arsort($campaign['env']['tablet']['os']);
    arsort($campaign['env']['tablet']['browser']);
    arsort($campaign['env']['mobile']['os']);
    arsort($campaign['env']['mobile']['browser']);
    $_campaign['Campaign'] = $campaign;
    return $_campaign;
  }
/**
 * 集計用のデフォルトの値をセット
 */
  protected function _addDefaultValue($campaign) {
    return $campaign + [
      'total_entries_count'     => 0,
      'facebook_entries_count'  => 0,
      'twitter_entries_count'   => 0,
      'email_entries_count'     => 0,
      'facebook_entrants_count' => 0,
      'twitter_entrants_count'  => 0,
      'email_entrants_count'    => 0,
      'total_shared_count'      => 0,
      'facebook_shared_count'   => 0,
      'twitter_shared_count'    => 0,
      'total_reach_count'       => 0,
      'facebook_reach_count'    => 0,
      'twitter_reach_count'     => 0,
      'gender_age_count' => [
        'male'    => array_fill_keys([10, 20, 30, 40, 50, 60], 0),
        'female'  => array_fill_keys([10, 20, 30, 40, 50, 60], 0),
        'unknown' => 0,
      ],
      'time'                    => array_fill(0, 24, 0),
      'facebook_friend_count'   => array_fill_keys([10, 100, 500, 1000, 1001], 0),
      'twitter_followers_count' => array_fill_keys([10, 100, 500, 1000, 1001], 0),
      'state'                   => [],
      'env' => array_fill_keys(
        ['desktop', 'mobile', 'tablet'],
        ['count' => 0, 'os' => [], 'browser' => []]
      ),
    ];
  }
/**
 * 性別、年齢
 */
  protected function _addGenderAge($campaign, $entrant) {
    if(in_array($entrant['gender'], ['male', 'female'])) {
      switch(true) {
        case !$entrant['age']:
          $campaign['gender_age_count']['unknown']++;
          break;
        case $entrant['age'] < 10:
          $campaign['gender_age_count'][$entrant['gender']][10]++;
          break;
        case $entrant['age'] >= 70:
          $campaign['gender_age_count'][$entrant['gender']][60]++;
          break;
        default:
          $campaign['gender_age_count'][$entrant['gender']][floor($entrant['age'] / 10) * 10]++;
      }
    } else {
      $campaign['gender_age_count']['unknown']++;
    }
    return $campaign;
  }
/**
 * Facebookの友達、Twitterのフォロワーの数
 */
  protected function _addFriendCount($campaign, $entrant) {
    $friendCountKeys = [
      'facebook' => 'facebook_friend_count',
      'twitter' => 'twitter_followers_count',
    ];
    if(in_array($entrant['entry_type'], Entry::$socialEntryTypes)) {
      switch(true) {
        case $entrant[$friendCountKeys[$entrant['entry_type']]] <= 10:
          $campaign[$friendCountKeys[$entrant['entry_type']]][10]++;
          break;
        case $entrant[$friendCountKeys[$entrant['entry_type']]] <= 100:
          $campaign[$friendCountKeys[$entrant['entry_type']]][100]++;
          break;
        case $entrant[$friendCountKeys[$entrant['entry_type']]] <= 500:
          $campaign[$friendCountKeys[$entrant['entry_type']]][500]++;
          break;
        case $entrant[$friendCountKeys[$entrant['entry_type']]] <= 1000:
          $campaign[$friendCountKeys[$entrant['entry_type']]][1000]++;
          break;
        default:
          $campaign[$friendCountKeys[$entrant['entry_type']]][1001]++;
      }
    }
    return $campaign;
  }
/**
 * 都道府県
 */
  protected function _addState($campaign, $entrant) {
    if($entrant['state']) {
      if(array_key_exists($entrant['state'], $campaign['state'])) {
        $campaign['state'][$entrant['state']]++;
      } else {
        $campaign['state'][$entrant['state']] = 1;
      }
    }
    return $campaign;
  }
/**
 * リーチ数
 */
  protected function _addReach($campaign, $entry) {
    switch($entry['entry_type']) {
      case 'facebook':
        $campaign['total_reach_count']    += $entry['facebook_friend_count'];
        $campaign['facebook_reach_count'] += $entry['facebook_friend_count'];
        break;
      case 'twitter':
        $campaign['total_reach_count']   += $entry['twitter_followers_count'];
        $campaign['twitter_reach_count'] += $entry['twitter_followers_count'];
        break;
    }
    return $campaign;
  }
/**
 * 閲覧環境
 */
  protected function _addEnv($campaign, $entry) {
    if(!empty($entry['user_agent'])) {
      $browser = $this->browscap->getBrowser($entry['user_agent']);
      if($browser->isTablet || $this->mobileDetect->isTablet($entry['user_agent'])) {
        $envDevice = 'tablet';
      } elseif($browser->isMobileDevice) {
        $envDevice = 'mobile';
      } else {
        $envDevice = 'desktop';
      }
      $campaign['env'][$envDevice]['count']++;
      foreach(['os', 'browser'] as $envType) {
        $envInfo = $envType == 'os' ? $browser->Platform : $browser->Browser;
        if(array_key_exists(
          $envInfo,
          $campaign['env'][$envDevice][$envType]
        )) {
          $campaign['env'][$envDevice][$envType][$envInfo]++;
        } else {
          $campaign['env'][$envDevice][$envType][$envInfo] = 1;
        }
      }
    }
    return $campaign;
  }
}
