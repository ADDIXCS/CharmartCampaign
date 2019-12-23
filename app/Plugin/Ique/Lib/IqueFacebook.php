<?php
App::import('Facebook', 'FacebookV2p10');
class IqueFacebook extends Object {

/**
 * 初期設定
 */
  public function __construct() {
    parent::__construct();
    $this->facebook = new FacebookV2p10([
      'appId' => Configure::read('facebook.appId'),
      'secret' => Configure::read('facebook.secret'),
    ]);
  }
/**
 * Facebookインスタンスのメソッドを直接呼ぶ
 */
  public function __call($name, $arguments) {
    try {
      return call_user_func_array([$this->facebook, $name], $arguments);
    } catch(FacebookApiException $e) {
      return false;
    }
  }
}

