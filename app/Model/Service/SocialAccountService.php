<?php
class SocialAccountService extends Object {
/**
 * Facebookのプロフィール情報を取得する
 */
  public function getFacebookProfile() {
    // Facebookのプロフィール情報取得
    if(!$me = $this->Facebook->api('/me?locale=ja&fields=birthday,first_name,gender,last_name,link,locale,name,timezone,updated_time,verified')) {
      throw new SorapsException('RequireSocialLogin');
    }
    // 必ず取得できるデータをセット
    $data = [
      'last_name'             => $me['last_name'],
      'first_name'            => $me['first_name'],
      'facebook_id'           => $me['id'],
    ];
    // メールアドレス
    if(array_key_exists('email', $me)) {
      $data['email'] = $me['email'];
    }
    // username
    if(array_key_exists('name', $me)) {
      $data['facebook_username'] = $me['name'];
    }
    // 性別
    if(array_key_exists('gender', $me)) {
      switch($me['gender']) {
        case 'male':
        case '男性':
          $data['gender'] = 'male';
          break;
        case 'female':
        case '女性':
          $data['gender'] = 'female';
          break;
      }
    }
    // 誕生日
    if(array_key_exists('birthday', $me)) {
      $birthday = explode('/', $me['birthday']);
      $data['birthday'] = sprintf('%04d-%02d-%02d', $birthday[2], $birthday[0], $birthday[1]);
    }
    // 住所
    if(array_key_exists('location', $me)) {
      if(preg_match('/[都道府県]/u', $me['location']['name'], $matches)) {
        $addr = explode($matches[0], $me['location']['name'], 2);
        $addr[0] .= $matches[0];
        $data['state'] = $addr[0];
        $data['city'] = trim($addr[1]);
      } else {
        $data['city'] = $me['location']['name'];
      }
    }
    // 友達の数
    if($friends = $this->Facebook->api('/me/friends')) {
      if(array_key_exists('summary', $friends)) {
        $data['facebook_friend_count'] = $friends['summary']['total_count'];
      }
    }
    return $data;
  }
/**
 * Twitterのプロフィール情報を取得する
 */
  public function getTwitterProfile() {
    if(!$me = $this->Twitter->getUserData()) {
      throw new SorapsException('RequireSocialLogin');
    }
    return [
      'twitter_id' => $me->id,
      'twitter_name' => $me->name,
      'twitter_screen_name' => $me->screen_name,
      'twitter_friends_count' => $me->friends_count,
      'twitter_followers_count' => $me->followers_count,
      'twitter_profile_image_url' => str_replace('https:', '', $me->profile_image_url_https),
    ];
  }
/**
 * いいね、フォロー済みかのチェック
 */
  public function isLiked($accountType, $client) {
    if($accountType == 'facebook') {
      $res = $this->Facebook->api('/me/likes/' . $client['Account']['facebook_id']);
      return !empty($res['data']);
    } elseif($accountType == 'twitter') {
      $res = (array) $this->Twitter->friendships_lookup([
        'screen_name' => $client['Account']['twitter_screen_name']
      ]);
      if(!empty($res[0])) {
        // フォローしているか同一ユーザの場合
        return in_array('following', $res[0]->connections) || $res[0]->id == $this->Twitter->getUser();
      }
    }
    return false;
  }
/**
 * シェア
 */
  public function share($accountType, $data) {
    if($accountType == 'facebook') {
      $postData = [];
      foreach(['message', 'link', 'picture', 'name', 'description'] as $param) {
        if(!empty($data[$param])) {
          $postData[$param] = $data[$param];
        }
      }
      $res = $this->Facebook->api('/me/feed', 'POST', $postData);
      if(!$res) {
        return false;
      }
    } elseif($accountType == 'twitter') {
      if(!empty($data['upload'])) {
        $res = $this->Twitter->statuses_updateWithMedia([
          'status'  => $data['message'],
          'media[]' => file_get_contents($data['picture']),
        ]);
      } else {
        $res = $this->Twitter->statuses_update([
          'status'  => $data['message'],
        ]);
      }
      if($res->httpstatus != 200) {
        return false;
      }
    } else {
      return false;
    }
    return true;
  }
}
