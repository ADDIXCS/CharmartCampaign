<?php
App::uses('AccountValue', 'Model/Value');
class Account extends AppModel {
  protected $_nameJa = 'アカウント';
  public $actsAs = [
    'Ique.IqueS3' => [
      'oem_logo' => 'logo',
      'oem_og_image' => 'og_image',
      'oem_favicon' => 'favicon',
      'oem_touch_icon' => 'touch_icon',
    ]
  ];
/**
 * バリデーションルール
 */
  public $validate = [
    'screen_name' => 'notEmpty|maxLength,20',
    'account_name' => 'notEmpty|username|between,3,20|isUnique',
    'password' => 'notEmpty|alphaNumeric|between,4,20',
    'oem_flg' => 'boolean',
    'facebook_id' => 'numeric',
    'facebook_page_url' => [
      'validFacebookPageUrl' => [
        'rule' => 'validFacebookPageUrl',
        'message' => 'FacebookページURLが正しくありません',
        'allowEmpty' => true,
      ],
    ],
    'twitter_id' => 'numeric',
    'twitter_screen_name' => [
      'validTwitterScreenName' => [
        'rule' => 'validTwitterScreenName',
        'message' => 'Twitterユーザ名が正しくありません',
        'allowEmpty' => true,
      ],
    ],
  ];
/**
 * 正しいFacebookページのURLかどうか
 * 正しい場合、idをdataにセットする
 */
  public function validFacebookPageUrl($check) {
    $facebookPage = $this->Facebook->api('/' . array_pop(explode('/', current($check))) . '?fields=id,link');
    if(!empty($facebookPage['id'])) {
      $this->data[$this->alias]['facebook_id'] = $facebookPage['id'];
      return true;
    }
    return false;
  }
/**
 * 正しいTwitterのユーザ名かどうか
 * 正しい場合、idをdataにセットする
 */
  public function validTwitterScreenName($check) {
    $twitterUser = $this->Twitter->users_show(['screen_name' => current($check)]);
    if(!empty($twitterUser->id)) {
      $this->data[$this->alias]['twitter_id'] = $twitterUser->id;
      $this->data[$this->alias][key($check)] = trim($this->data[$this->alias][key($check)]);
      return true;
    }
    return false;
  }
/**
 * プランのバリデーションを追加
 */
  public function beforeValidate($options = []) {
    $this->validate['plan'][] = [
      'rule' => ['inList', array_keys(AccountValue::$plans)],
    ];
    return parent::beforeValidate($options);
  }
/**
 * パスワードのunsetと日本語role、子roleを追加
 *
 * @param array $results findメソッドの結果
 * @return array データを処理した$results
 */
  public function afterFind($results) {
    foreach($results as &$result) {
      if(array_key_exists('Account', $result)) {
        // XXX: unsetすると、ログイン処理時にもパスワードがなくなる
        // unset($result['Account']['password']);
        if(array_key_exists('role', $result['Account'])) {
          $role = $result['Account']['role'];
          $result['Account']['role_ja']       = AccountValue::getRoleJa($role);
          $result['Account']['child_role']    = AccountValue::getChildRole($role);
          $result['Account']['child_role_ja'] = AccountValue::getChildRoleJa($role);
        }
        if(array_key_exists('plan', $result['Account'])) {
          $result['Account']['plan_ja'] = AccountValue::getPlanJa($result['Account']['plan']);
        }
      }
    }
    return parent::afterFind($results);
  }
/**
 * 保存前の処理
 *
 * @return boolean true
 * @todo passwordの部分を変数にしたい（Componentの設定をModel内で呼ぶ奇麗な方法があれば）
 */
  public function beforeSave() {
    // パスワードの暗号化をする
    if(array_key_exists('password', $this->data[$this->alias])) {
      $passwordHasher = new BlowfishPasswordHasher();
      $this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
    }
    // ソーシャルアカウント情報がない場合にidを空にする（後から削除した場合用）
    if(empty($this->data[$this->alias]['facebook_page_url'])) {
      $this->data[$this->alias]['facebook_id'] = '';
    }
    if(empty($this->data[$this->alias]['twitter_screen_name'])) {
      $this->data[$this->alias]['twitter_id'] = '';
    }
    return parent::beforeSave();
  }
}
