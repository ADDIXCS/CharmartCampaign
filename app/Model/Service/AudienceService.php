<?php
class AudienceService extends Object {
  public function __construct() {
    $this->Audience = ClassRegistry::init('Audience');
    $this->EntrySaveService = ClassRegistry::init('EntrySaveService');
  }
/**
 * オーディエンスが登録済みかのチェック
 */
  public function hasAudience($account, $entryType = null) {
    // ソーシャルアカウントでの応募状態をチェック
    $socialAccounts = [];
    // Facebookアカウント
    if($entryType != 'twitter' && $facebookId = $this->Facebook->getUser()) {
      $socialAccounts[] = [
        'Audience.entry_type' => 'facebook',
        'Audience.facebook_id' => $facebookId,
      ];
    }
    // Twitterアカウント
    if($entryType != 'facebook' && $twitterId = $this->Twitter->getUser()) {
      $socialAccounts[] = [
        'Audience.entry_type' => 'twitter',
        'Audience.twitter_id' => $twitterId,
      ];
    }
    if($socialAccounts) {
      if($audience = $this->Audience->find('first', [
        'conditions' => [
          'Audience.account_id' => $account['Account']['id'],
          'OR' => $socialAccounts,
        ],
        'fields' => $this->EntrySaveService->userFields,
      ])) {
        return $audience;
      }
    }
    // 上記で引っかからなかったらfalse
    return false;
  }
}

