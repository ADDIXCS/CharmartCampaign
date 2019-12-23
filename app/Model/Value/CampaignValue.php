<?php
class CampaignValue extends Object {
/**
 * キャンペーンの種類
 */
  public static $types = ['prize', 'contest', 'vote', 'coupon', 'lottery', 'shindan', 'lancers'];
/**
 * 各キャンペーンのデータ
 */
  public static $prize = [
    'name' => '懸賞',
    'editTypes' => [
      'page-top' => 'キャンペーントップ',
      'page-entry' => '応募ページ',
      'enquetes' => 'アンケート',
      'page-finish' => '応募完了ページ',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => 'キャンペーンに応募する',
    'entryActionName' => '応募',
  ];
  public static $contest = [
    'name' => '投稿コンテスト',
    'editTypes' => [
      'page-top' => '投稿コンテストトップ',
      'page-items' => 'アイテム一覧',
      'page-entry' => '応募ページ',
      'enquetes' => 'アンケート',
      'page-finish' => '応募完了ページ',
      'page-vote' => '投票ページ',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => '投稿する',
    'entryActionName' => '投稿',
  ];
  public static $vote = [
    'name' => '投票コンテスト',
    'editTypes' => [
      'page-top' => '投票コンテストトップ',
      'items' => 'アイテム登録',
      'page-items' => 'アイテム一覧',
      'page-entry' => '応募ページ',
      'enquetes' => 'アンケート',
      'page-finish' => '応募完了ページ',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => '投票する',
    'entryActionName' => '投票',
  ];
  public static $coupon = [
    'name' => 'クーポン',
    'editTypes' => [
      'page-top' => 'キャンペーントップ',
      'page-entry' => '応募ページ',
      'enquetes' => 'アンケート',
      'page-finish' => '応募完了ページ',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => 'クーポンを取得する',
    'entryActionName' => 'クーポン取得',
  ];
  public static $lottery = [
    'name' => 'スピードくじ',
    'editTypes' => [
      'gifts' => '景品登録',
      'page-top' => 'キャンペーントップ',
      'page-entry' => '応募ページ',
      'enquetes' => 'アンケート',
      'page-finish' => '応募完了ページ',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => 'スピードくじを引く',
    'entryActionName' => 'くじを引く',
  ];
  public static $shindan = [
    'name' => '診断',
    'editTypes' => [
      'page-top' => '診断トップ',
      'questions' => '設問・回答登録',
      'results' => '結果登録',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => '診断する',
    'entryActionName' => '診断',
  ];
  public static $lancers = [
    'name' => 'ランサーズ連携コンテスト',
    'editTypes' => [
      'page-top' => 'コンテストトップ',
      'detail' => '詳細設定',
    ],
    'entryBtnDefaultText' => '投票する',
    'entryActionName' => '投票',
  ];
/**
 * 入力項目の種類
 */
  public static $inputs = [
    'name' => '氏名',
    'kana' => 'ふりがな',
    'email' => 'メールアドレス',
    'tel' => '電話番号',
    'gender' => '性別',
    'birthday' => '生年月日',
    'postcode' => '郵便番号',
    'state' => '都道府県',
    'city' => '郡市区',
    'street' => 'それ以降の住所',
  ];
/**
 * 利用可能なキャンペーンの種類を返す
 */
  public static function getAvailableTypes($client) {
    $client = $client['Account'];
    switch($client['plan']) {
      case 'stop':
        return [];
        break;
      case 'custom':
        $plans = [];
        foreach(self::$types as $type) {
          if($client['plan_' . $type . '_flg']) {
            $plans[] = $type;
          }
        }
        return $plans;
        break;
      default:
        return AccountValue::$plans[$client['plan']]['campaigns'];
    }
  }
/**
 * キャンペーン編集の種類から編集ステップの数字を返す
 */
  public static function getEditStep($campaign, $editType) {
    foreach(array_keys(self::getEditTypes($campaign)) as $key => $val) {
      if($val == $editType) {
        return $key + 1;
      }
    }
    throw new SorapsException('BadEditType');
  }
/**
 * キャンペーン編集の数字から編集の種類を返す
 */
  public static function getEditType($campaign, $editStep) {
    $editTypes = array_keys(self::getEditTypes($campaign));
    return $editTypes[$editStep - 1];
  }
/**
 * キャンペーンの種類から編集の一覧を返す
 * ビューから呼ばれることもあるためstaticに定義
 */
  public static function getEditTypes($campaign) {
    return CampaignValue::${$campaign['Campaign']['campaign_type']}['editTypes'];
  }
}
