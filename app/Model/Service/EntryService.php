<?php
class EntryService extends Object {
  public function __construct() {
    $this->Entry = ClassRegistry::init('Entry');
  }
/**
 * 応募済みかのチェック
 */
  public function isEntried($campaign, $entryType = null) {
    $campaign = $campaign['Campaign'];
    // テストモードの場合は未応募
    if($campaign['status'] == 'test') {
      return false;
    }
    // 1日1回応募の場合
    $conditionsDate = [];
    if(
      ($campaign['campaign_type'] == 'lottery' && $campaign['entry_limit']) ||
      ($campaign['campaign_type'] == 'coupon' && $campaign['coupon_used_flg'] && $campaign['entry_limit'])
    ) {
      $conditionsDate = [
        'created >=' => date('Y-m-d 00:00:00'),
      ];
    }
    // ソーシャルアカウントでの応募状態をチェック
    $baseConditions = [];
    // Facebookアカウント
    if($entryType != 'twitter' && $facebookId = $this->Facebook->getUser()) {
      $baseConditions[] = [
        'Entry.entry_type' => 'facebook',
        'Entry.facebook_id' => $facebookId,
      ];
    }
    // Twitterアカウント
    if($entryType != 'facebook' && $twitterId = $this->Twitter->getUser()) {
      $baseConditions[] = [
        'Entry.entry_type' => 'twitter',
        'Entry.twitter_id' => $twitterId,
      ];
    }
    // メールアドレス
    if($campaign['campaign_type'] == 'coupon' && ($entryType != 'twitter' && $entryType != 'facebook') && $email = CakeSession::read('entry_email')) {
      $baseConditions[] = [
        'Entry.entry_type' => 'email',
        'Entry.email' => $email,
      ];
    }
    // 診断メールアドレス
    if($campaign['campaign_type'] == 'shindan' && ($entryType != 'twitter' && $entryType != 'facebook') && $shindan_entry_id = CakeSession::read('shindan_entry_id')) {
      if($entried = $this->Entry->find('first', [
        'conditions' => [
          'Entry.id' => $shindan_entry_id,
        ] + $conditionsDate,
        'order' => 'Entry.created DESC',
      ])) {
        return $entried;
      } else {
        return false;
      }
    }
    if($baseConditions) {
      if($entried = $this->Entry->find('first', [
        'conditions' => [
          'Entry.campaign_id' => $campaign['id'],
          'OR' => $baseConditions,
        ] + $conditionsDate,
        'order' => 'Entry.created DESC',
      ])) {
        return $entried;
      }
    }
    // メールアドレスでの応募状態をチェック
    $emails = [];
    // フォームに入力されたメールアドレス
    if(!empty($this->Entry->data['Entry']['email'])) {
      $emails[] = $this->Entry->data['Entry']['email'];
    }
    // Facebookのメールアドレス
    if(
      ($facebookEmail = $this->Facebook->api('/me?fields=email')) &&
      !empty($facebookEmail['email'])
    ) {
      $emails[] = $facebookEmail['email'];
    }
    if($emails) {
      if($entried = $this->Entry->find('first', ['conditions' => [
        'Entry.campaign_id' => $campaign['id'],
        'OR' => array_map(function($email) { return ['Entry.email' => $email]; }, $emails),
      ] + $conditionsDate])) {
        return $entried;
      }
    }
    // 上記で引っかからなかったらfalse
    return false;
  }
}
