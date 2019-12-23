<?php
class AccountService extends Object {
  public function __construct() {
    $this->Account = ClassRegistry::init('Account');
  }
/**
 * accountNameをもとにデータが存在しなければ例外を投げ、存在する場合はデータを返す
 *
 * @param string $accountName ログインID
 * @return array 見つかったアカウントデータ
 */
  public function requireByAccountName($accountName) {
    if(!$accountName || !$data = $this->Account->findByAccountName($accountName)) {
      throw new SorapsException('NotFound', $this->Account->_nameJa);
    }
    return $data;
  }
/**
 * 渡されたaccountNameより上位のアカウントでのログインチェック
 * accountNameから取得したアカウントのIDをもとに_requireParentsを返す
 *
 * @param string $accountName ログインID
 * @return array ログインアカウントから渡されたaccountNameのアカウントまでのアカウント一覧
 */
  public function requireParents($accountName) {
    $account = $this->requireByAccountName($accountName);
    return $this->_requireParents($account['Account']['id']);
  }
/**
 * 渡されたidをもとに上位のアカウントでのログインチェックと上位アカウント一覧を作成
 *
 * @param string $id ID
 * @param array $accounts 子アカウントの一覧
 * @return array ログインアカウントから渡されたidのアカウントまでのアカウント一覧
 */
  protected function _requireParents($id, $accounts = [])
  {
    $loginAccount = AuthComponent::user();
    $loginId = $loginAccount['id'];
    $account = $this->Account->requireById($id);
    // 親アカウントのリストを結合
    array_unshift($accounts, $account);
    // アカウントのidがログインIDと一致した場合
    if($account['Account']['id'] == $loginId) {
      return $accounts;
    }
    // アカウントの権限とログインアカウントの権限が一致した場合（これ以上さかのぼる必要はない）
    if($account['Account']['role'] == $loginAccount['role']) {
      throw new SorapsException('BadLoginRole');
    }
    // 先に上の条件ではじかれるからこれに一致することはないと思うけど念のため
    if($account['Account']['role'] == 'admin') {
      throw new SorapsException('BadLoginRole');
    }
    // 再帰呼び出し
    return self::_requireParents($account['Account']['parent_id'], $accounts);
  }
/**
 * OEM情報をセット
 */
  public function setOemInfo() {
    if($account = $this->Account->find('first', ['conditions' => [
      'oem_flg' => true,
      'oem_domain' => env('HTTP_HOST'),
    ]])) {
      $imageBase = env('HTTPS') ? 'https:' : 'http:';
      $imageBase .= '//s3-ap-northeast-1.amazonaws.com/' . Configure::read('aws.bucket');
      $imageBase .= '/accounts/' . $account['Account']['id'] . '/';
      Configure::write('service', [
        'name' => $account['Account']['oem_service_name'],
        'companyName' => $account['Account']['oem_company_name'],
        'companyNameEn' => $account['Account']['oem_company_name_en'],
        'twitterAccount' => $account['Account']['oem_twitter_account'],
        'logo' => $imageBase . $account['Account']['oem_logo'],
        'ogImage' => $imageBase . $account['Account']['oem_og_image'],
        'favicon' => $imageBase . $account['Account']['oem_favicon'],
        'touchIcon' => $imageBase . $account['Account']['oem_touch_icon'],
      ]);
      Configure::write('facebook', [
        'appId' => $account['Account']['oem_facebook_app_id'],
        'secret' => $account['Account']['oem_facebook_secret'],
      ]);
      Configure::write('twitter', [
        'consumerKey' => $account['Account']['oem_twitter_consumer_key'],
        'consumerSecret' => $account['Account']['oem_twitter_consumer_secret'],
      ]);
    }
  }
}
