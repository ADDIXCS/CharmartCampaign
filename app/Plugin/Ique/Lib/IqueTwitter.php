<?php
class IqueTwitter extends Object {
  // キャッシュデータ格納用
  protected $_user;
  protected $_userData;
/**
 * 初期設定
 */
  public function __construct() {
    parent::__construct();
    $consumerKey = Configure::read('twitter.consumerKey');
    $consumerSecret = Configure::read('twitter.consumerSecret');
    // インスタンスの生成
    $this->twitter = \Codebird\Codebird::getInstance();
    // コンシューマキーのセット
    $this->twitter->setConsumerKey($consumerKey, $consumerSecret);
    // 認証後のアクセスの場合
    if(!empty($_GET['oauth_verifier']) && CakeSession::read('soraps_tw_oauth_verify')) {
      // verifyフラグを削除
      CakeSession::delete('soraps_tw_oauth_verify');
      // リクエストトークンのセット
      $this->twitter->setToken(
        CakeSession::read('soraps_tw_request_token'),
        CakeSession::read('soraps_tw_request_token_secret')
      );
      // アクセストークンの取得
      $res = $this->twitter->oauth_accessToken([
        'oauth_verifier' => $_GET['oauth_verifier']
      ]);
      if($res->httpstatus == 200) {
        // アクセストークンをセッションに書き込み
        CakeSession::write('soraps_tw_access_token', $res->oauth_token);
        CakeSession::write('soraps_tw_access_token_secret', $res->oauth_token_secret);
        // ユーザデータの取得
        $this->_user = $res->user_id;
      }
    }
    // アクセストークンのセット
    $this->twitter->setToken(
      CakeSession::read('soraps_tw_access_token'),
      CakeSession::read('soraps_tw_access_token_secret')
    );
  }
/**
 * ユーザidを取得する
 */
  public function getUser() {
    if(isset($this->_user)) {
      // キャッシュデータを返す
      return $this->_user;
    } else {
      $userData = $this->getUserData();
      if($userData) {
        return $this->_user = $userData->id;
      } else {
        return $this->_user = false;
      }
    }
  }
/**
 * プロフィール画像のURLを取得する
 */
  public function getProfileImageUrl($userId, $size = null) {
    if(is_array($userId)) {
      // 100件以上ある場合の再帰処理
      if(count($userId) > 100) {
        return array_reduce(array_map(
          [$this, 'getProfileImageUrl'],
          array_chunk($userId, 100),
          [$size]
        ), function($carry, $item) { return (array) $carry + $item; });
      }
      $res = $this->twitter->users_lookup([
        'user_id' => implode(',', $userId),
      ]);
      $urls = [];
      if($res->httpstatus == 200) {
        unset($res->httpstatus);
        foreach($res as $data) {
          $url = str_replace('https:', '', $data->profile_image_url_https);
          if($size) {
            $url = str_replace('_normal', $size == 'original' ? '' : '_' . $size, $url);
          }
          $urls[$data->id_str] = $url;
        }
      }
      return $urls;
    } else {
      $res = $this->twitter->users_show([
        'user_id' => $userId,
      ]);
      $url = '';
      if($res->httpstatus == 200) {
        $url = str_replace('https:', '', $res->profile_image_url_https);
        if($size) {
          $url = str_replace('_normal', $size == 'original' ? '' : '_' . $size, $url);
        }
      }
      return $url;
    }
  }
/**
 * ユーザデータを取得する
 */
  public function getUserData($screenName = null) {
    if($screenName) {
      $res = $this->twitter->users_show([
        'screen_name' => $screenName,
      ]);
      if($res->httpstatus == 200) {
        return $res;
      }
      return false;
    } else {
      if(isset($this->_userData)) {
        // キャッシュデータを返す
        return $this->_userData;
      } else {
        $res = $this->twitter->account_verifyCredentials();
        if($res->httpstatus == 200) {
          return $this->_userData = $res;
        }
      }
      return $this->_userData = false;
    }
  }
/**
 * 認証用のURLを取得する
 * 認証済みの場合は$redirectUriがそのまま返る
 */
  public function getLoginUrl($redirectUri = null) {
    $redirectUri = Router::url($redirectUri, true);
    // トークンの削除
    $this->twitter->setToken(null, null);
    // リクエストトークンの取得
    $res = $this->twitter->oauth_requestToken([
      'oauth_callback' => $redirectUri
    ]);
    $this->twitter->setToken($res->oauth_token, $res->oauth_token_secret);
    CakeSession::write('soraps_tw_request_token', $res->oauth_token);
    CakeSession::write('soraps_tw_request_token_secret', $res->oauth_token_secret);
    CakeSession::write('soraps_tw_oauth_verify', true);
    return $this->twitter->oauth_authenticate();
  }
/**
 * フォローする
 */
  public function follow($screenName) {
    $res = $this->twitter->friendships_create([
      'screen_name' => $screenName,
    ]);
    if($res->httpstatus == 200 && property_exists($res, 'following') && !$res->following) {
      return true;
    }
    return false;
  }
/**
 * Codebirdインスタンスのメソッドを直接呼ぶ
 */
  public function __call($name, $arguments) {
    try {
      return call_user_func_array([$this->twitter, $name], $arguments);
    } catch(Exception $e) {
      return false;
    }
  }
}

