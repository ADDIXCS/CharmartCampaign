<?php
class EntrySaveService extends Object {
  public $userFields = [
    'id', 'last_name', 'first_name', 'last_kana', 'first_kana',
    'facebook_id', 'facebook_username', 'facebook_friend_count',
    'twitter_id', 'twitter_name', 'twitter_screen_name', 'twitter_friends_count', 'twitter_followers_count',
    'email', 'tel', 'gender', 'birthday', 'postcode', 'state', 'city', 'street',
  ];
  public function __construct() {
    $this->Entry = ClassRegistry::init('Entry');
  }
/**
 * 応募情報を保存する
 */
  public function saveEntry($data) {
    // 応募データを加工しやすいように一端変数に入れる
    $entry = $data['Entry'];
    // モデルのbind
    $this->_bindModel($entry['campaign_type']);
    // ソーシャルアカウントでの応募の場合のデータ整形
    if(in_array($entry['entry_type'], Entry::$socialEntryTypes)) {
      $socialProfile = $this->_getSocialProfile($entry['entry_type']);
      // 応募者データの取得
      $entrant = $this->_getEntrant($entry, $socialProfile);
      // オーディエンスデータの取得
      $audience = $this->_getAudience($entry, $socialProfile);
      // 応募時入力データ、既存のオーディエンスデータ、ソーシャルのユーザデータを結合
      $entry = $this->_mergeEntryData($entry, $socialProfile, $audience);
      // いいねデータのチェック
      list($entry, $data) = $this->_modifyLiked($entry, $data, $socialProfile);
    }
    // 保存
    // -------------------------
    // トランザクション開始
    $dataSource = $this->Entry->getDataSource();
    $dataSource->begin();
    // audienceの処理
    $audience_id = '';
    if(in_array($entry['entry_type'], Entry::$socialEntryTypes)) {
      // audienceがない場合に新規作成
      if(empty($audience)) {
        $this->Entry->Audience->create();
        if(!$this->Entry->Audience->save($entry)) {
          $dataSource->rollback();
          return false;
        }
        $audience_id = $this->Entry->Audience->getLastInsertId();
      } else {
        $audience_id = $audience['Audience']['id'];
        $data['Audience'] = $entry;
        $data['Audience']['id'] = $audience_id;
      }
      $entry['audience_id'] = $audience_id;
    }
    // 応募データ
    $data['Entry'] = $entry;
    // 応募者データ
    $data['Entrant'] = $entry;
    if(!empty($entrant)) {
      $data['Entrant']['id'] = $entrant['Entrant']['id'];
    }
    // 応募データの保存
    if(!$this->Entry->saveAssociated($data)) {
      $dataSource->rollback();
      return false;
    }
    // 投票コンテストの場合
    if($entry['campaign_type'] == 'vote') {
      $data['Vote']['entry_id'] = $this->Entry->getLastInsertId();
      $data['Vote']['user_agent'] = $entry['user_agent'];
      $data['Vote']['entrant_id'] = !empty($entrant)
        ? $entrant['Entrant']['id']
        : $this->Entry->Entrant->getLastInsertId();
      $data['Vote']['audience_id'] = $audience_id;
      if($entry['entry_type'] == 'facebook') {
        $data['Vote']['facebook_id'] = $entry['facebook_id'];
        $data['Vote']['facebook_username'] = $entry['facebook_username'];
      }
      if($entry['entry_type'] == 'twitter') {
        $data['Vote']['twitter_id'] = $entry['twitter_id'];
        $data['Vote']['twitter_screen_name'] = $entry['twitter_screen_name'];
      }
      $this->Entry->Vote = ClassRegistry::init('Vote');
      $this->Entry->Vote->create();
      if(!$this->Entry->Vote->save($data['Vote'])) {
        $dataSource->rollback();
        return false;
      }
    }
    // トランザクション終了
    $dataSource->commit();
    return true;
  }
/**
 * モデルのbind
 */
  protected function _bindModel($campaignType) {
    $this->Entry->bindModel(['belongsTo' => ['Entrant', 'Audience']], false);
    $this->Entry->bindModel(['hasOne' => ['Like']], false);
    switch($campaignType) {
      case 'shindan':
        $this->Entry->bindModel(['hasMany' => ['EntryAnswer']], false);
        break;
      default:
        $this->Entry->bindModel(['hasMany' => ['EntryEnquete']], false);
    }
  }
/**
 * ソーシャルアカウントのプロフィールデータ取得
 */
  protected function _getSocialProfile($entryType) {
    // SocialAccountServiceの初期設定
    // @todo FacebookとTwitterのセットしてるの、絶対よくない
    $this->SocialAccountService = ClassRegistry::init('SocialAccountService');
    $this->SocialAccountService->Facebook = $this->Facebook;
    $this->SocialAccountService->Twitter = $this->Twitter;
    switch($entryType) {
      case 'facebook':
        return $this->SocialAccountService->getFacebookProfile();
      case 'twitter':
        return $this->SocialAccountService->getTwitterProfile();
    }
  }
/**
 * 応募者データ取得
 */
  protected function _getEntrant($entry, $socialProfile) {
    return $this->Entry->Entrant->find('first', [
      'conditions' => [
        'campaign_id' => $entry['campaign_id'],
        $entry['entry_type'] . '_id' => $socialProfile[$entry['entry_type'] . '_id'],
      ],
      'fields' => $this->userFields,
    ]);
  }
/**
 * オーディエンスデータ取得
 */
  protected function _getAudience($entry, $socialProfile) {
    return $this->Entry->Audience->find('first', [
      'conditions' => [
        'account_id' => $entry['account_id'],
        $entry['entry_type'] . '_id' => $socialProfile[$entry['entry_type'] . '_id'],
      ],
      'fields' => $this->userFields,
    ]);
  }
/**
 * 応募時入力データ、既存のオーディエンスデータ、ソーシャルのユーザデータを結合
 */
  protected function _mergeEntryData($entry, $socialProfile, $audience) {
    switch($entry['entry_type']) {
      case 'facebook':
        $entry = Hash::filter($entry) + ['facebook_id' => $socialProfile['facebook_id']];
        if(array_key_exists('facebook_username', $socialProfile)) {
          $entry['facebook_username'] = $socialProfile['facebook_username'];
        }
        if(array_key_exists('facebook_friend_count', $socialProfile)) {
          $entry['facebook_friend_count'] = $socialProfile['facebook_friend_count'];
        }
        if($audience) {
          $entry += Hash::filter($audience['Audience']);
        }
        $entry += $socialProfile;
        break;
      case 'twitter':
        $entry = Hash::filter($entry) + $socialProfile;
        if($audience) {
          $entry += Hash::filter($audience['Audience']);
        }
        break;
    }
    // audienceのidがセットされるのでunset
    unset($entry['id']);
    return $entry;
  }
/**
 * いいね！フォローデータのチェック
 */
  protected function _modifyLiked($entry, $data, $socialProfile) {
    switch($entry['entry_type']) {
      case 'facebook':
        if($liked = $this->Entry->Like->find('first', ['conditions' => [
          'entry_id' => false,
          'facebook_id' => $socialProfile['facebook_id'],
          'campaign_id' => $entry['campaign_id'],
        ]])) {
          $entry['facebook_new_fan'] = true;
          $data['Like'] = [
            'id' => $liked['Like']['id']
          ];
        }
        break;
      case 'twitter':
        if(array_key_exists('Like', $data)) {
          $data['Like']['twitter_id'] = $socialProfile['twitter_id'];
        }
        break;
    }
    return [$entry, $data];
  }
}
