<?php
class SorapsAuthComponent extends AuthComponent {
  public $settings = [
    'loginRedirect' => ['controller' => 'admin', 'action' => 'index'],
    'logoutRedirect' => ['controller' => 'login', 'action' => 'login'],
    'loginAction' => ['controller' => 'login', 'action' => 'login'],
    'authError' => false,
    'flash' => [
      'element' => 'alert',
      'key' => 'flash',
      'params' => [
        'plugin' => 'BoostCake',
        'class' => 'alert-danger',
      ],
    ],
    'authenticate' => [
      'Form' => [
        'fields' => [
          'username' => 'account_name',
          'password' => 'password'
        ],
        'userModel' => 'Account',
        'passwordHasher' => 'Blowfish',
      ],
    ],
  ];
  public function __construct(ComponentCollection $collection, $settings = []) {
    parent::__construct($collection, am($this->settings, $settings));
  }
/**
 * 管理者か支配者ログインでない場合に例外を投げる
 */
  public function requireMasterLogin() {
    if(!$this->isMasterLogin()) {
      throw new SorapsException('BadLoginRole');
    }
  }
  public function requireAdminLogin() {
    if(!$this->isAdminLogin()) {
      throw new SorapsException('BadLoginRole');
    }
  }
  public function isMasterLogin() {
    $loginAccount = $this->user();
    return in_array($loginAccount['role'], ['admin', 'master']);
  }
  public function isAdminLogin() {
    $loginAccount = $this->user();
    return $loginAccount['role'] === 'admin';
  }
}
