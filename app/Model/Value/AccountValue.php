<?php
App::uses('CampaignValue', 'Model/Value');
class AccountValue extends Object {
/**
 * 権限
 */
  public static $roles = [
    'admin' => [
      'ja' => '支配者',
      'child' => 'master',
    ],
    'master' => [
      'ja' => '管理者',
      'child' => 'agent',
    ],
    'agent' => [
      'ja' => '代理店',
      'child' => 'client',
    ],
    'client' => [
      'ja' => 'クライアント',
    ],
  ];
/**
 * 契約プラン
 */
  public static $plans = [
    'stop' => [
      'name' => '契約停止',
    ],
    'basic' => [
      'name' => 'ベーシックプラン',
      'campaigns' => ['prize'],
    ],
    'shop' => [
      'name' => '店舗誘導プラン',
      'campaigns' => ['prize', 'coupon', 'lottery'],
    ],
    'professional' => [
      'name' => 'プロフェッショナルプラン',
      'campaigns' => ['prize', 'contest', 'vote', 'coupon', 'lottery', 'shindan'],
    ],
    'custom' => [
      'name' => 'カスタムプラン',
    ],
  ];
/**
 * 日本語role、子roleを取得
 *
 * @param string $role roleの英語名
 * @return string それぞれの処理結果
 */
  public static function getRoleJa($role) {
    return self::$roles[$role]['ja'];
  }
  public static function getChildRole($role) {
    return $role != 'client' ? self::$roles[$role]['child'] : '';
  }
  public static function getChildRoleJa($role) {
    return $role != 'client' ? self::$roles[self::getChildRole($role)]['ja'] : '';
  }
/**
 * 日本語planを取得
 */
  public static function getPlanJa($plan) {
    if(!array_key_exists($plan, self::$plans)) {
      return '';
    }
    return self::$plans[$plan]['name'];
  }
}
