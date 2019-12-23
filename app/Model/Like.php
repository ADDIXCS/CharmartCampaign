<?php
class Like extends AppModel {
/**
 * counterCache用のモデルのbind
 */
  public function setCounterCache() {
    $counterScope = [
      'facebook_new_fan_count' => ['Like.entry_type' => 'facebook', 'Like.deleted' => false],
      'twitter_new_fan_count' => ['Like.entry_type' => 'twitter', 'Like.deleted' => false],
    ];
    $this->bindModel(['belongsTo' => [
      'Campaign' => ['counterCache' => $counterScope],
      'Account' => ['counterCache' => $counterScope],
    ]], false);
  }
}

