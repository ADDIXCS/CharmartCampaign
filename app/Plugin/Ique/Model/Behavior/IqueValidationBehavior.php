<?php
/**
 * デフォルトメッセージの定義とバリデーションルールの追加・上書き
 *
 * 参考
 * バリデーションメッセージをDryにしつつ国際化 - cakephperの日記(CakePHP, MongoDB)
 * http://d.hatena.ne.jp/cakephper/20090727/1248691184
 * CakePHP Behaviorでバリデーション周りの効率化を図る : エクスギア　Blog
 * http://www.exgear.jp/blog/2009/06/cakephp-behavior%E3%81%A7%E3%83%90%E3%83%AA%E3%83%87%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E5%91%A8%E3%82%8A%E3%81%AE%E5%8A%B9%E7%8E%87%E5%8C%96%E3%82%92%E5%9B%B3%E3%82%8B/
 */
class IqueValidationBehavior extends ModelBehavior {
/**
 * デフォルトのエラーメッセージ
 */
  public static $errorMessages = [
    'notEmpty'      => '必須項目です',
    'alphaNumeric'  => '半角英数字で入力してください',
    'numeric'       => '半角数字で入力してください',
    'boolean'       => 'データが不正です',
    'email'         => '正しいメールアドレスを入力してください',
    'phone'         => '正しい電話番号を入力してください',
    'postal'        => '正しい郵便番号を入力してください',
    'state'         => '正しい都道府県を入力してください',
    'date'          => '正しい日付を入力してください',
    'url'           => '正しいURLを入力してください',
    'between'       => '%2$d文字以上%3$d文字以内で入力してください',
    'range'         => '%2$d〜%3$dの数字を入力してください',
    'isUnique'      => 'この値は既に登録済みです',
    'inList'        => 'データが不正です',
    'minLength'     => '%2$d文字以上で入力してください',
    'maxLength'     => '%2$d文字以内で入力してください',
    'datetime'      => '正しい日付を入力してください',
    'uploadError'   => 'ファイルは必須です',
    'extension'     => '拡張子が不正です',
    'mimeType'      => 'MIME Typeが不正です',
    'fileSize'      => 'ファイルサイズが不正です',
    'imageFile'     => '画像ファイルを指定してください',
    'maxFileSize'   => '%2$s以下のファイルを指定してください',
    'hiragana'      => 'ひらがなで入力してください',
    'katakana'      => 'カタカナで入力してください',
    'username'      => '使用できない文字が含まれています',
    'usernameMark'  => '記号を最初と最後に入れることはできません',
  ];
/**
 * バリデーションルールの展開とエラーメッセージの設定
 */
  public function setup(Model $model, $config = []) {
    $this->setValidationRules($model);
  }
  public function setValidationRules(Model $model) {
    if(is_array($model->errorMessages)) {
      self::$errorMessages = am(self::$errorMessages, $model->errorMessages);
    }
    foreach($model->validate as &$rules) {
      $rulesTmp = [];
      foreach((array) $rules as $ruleKey => $rule) {
        // 簡易記述のバリデーションルールの処理
        if(is_string($rule)) {
          // 文字列のルールを配列化
          $rule = str_replace(' ', '', $rule);
          $rulesArr = explode('|', $rule);
          foreach($rulesArr as $ruleStr) {
            if(preg_match('/(.*?)\[(.*?)\]/', $ruleStr, $match)) {
              $ruleArr = [$match[1], explode(',', $match[2])];
            } else {
              $ruleArr = explode(',', $ruleStr);
            }
            $rulesTmp[$ruleArr[0]] = [
              'rule' => $ruleArr,
              //'required' => $ruleArr[0] == 'notEmpty' ? 'create' : false,
              'allowEmpty' => $ruleArr[0] == 'notEmpty' ? false : true,
            ];
          }
        } elseif(is_array($rule)) {
          $rulesTmp[$ruleKey] = $rule;
        }
      }
      // エラーメッセージのセット
      foreach($rulesTmp as &$ruleTmp) {
        $ruleName = is_array($ruleTmp['rule']) ? $ruleTmp['rule'][0] : $ruleTmp['rule'];
        if(!array_key_exists('message', $ruleTmp) && array_key_exists($ruleName, self::$errorMessages)) {
          $ruleTmp['message'] = vsprintf(self::$errorMessages[$ruleName], $ruleTmp['rule']);
        }
      }
      $rules = $rulesTmp;
    }
  }
/**
 * notEmptyルールを追加
 */
  public function addNotEmpty(Model $model, $field) {
    if(array_key_exists($field, $model->validate)) {
      $model->validate[$field] = am(['notEmpty' => [
        'rule' => 'notEmpty',
        'message' => self::$errorMessages['notEmpty'],
      ]], $model->validate[$field]);
    } else {
      $model->validate[$field]['notEmpty'] = [
        'rule' => 'notEmpty',
        'message' => self::$errorMessages['notEmpty'],
      ];
    }
  }
/**
 * multipleの場合のnotEmptyルールを追加
 */
  public function addNotEmptyMultiple(Model $model, $field) {
    $model->validate[$field]['multiple'] = [
      'rule' => ['multiple', ['min' => 1]],
      'message' => self::$errorMessages['notEmpty'],
    ];
  }
/**
 * デフォルトのalphaNumericだと全角文字がvalidになるので上書き
 */
  public static function alphaNumeric(Model $model, $check) {
    return preg_match('/^[a-zA-Z0-9]+$/', current($check));
  }
/**
 * デフォルトのrangeは超過、未満なので以上、以下に
 */
  public static function range(Model &$model, $check, $lower = null, $upper = null) {
    $check = current($check);
    if (!is_numeric($check)) {
      return false;
    }
    if (isset($lower) && isset($upper)) {
      return ($check >= $lower && $check <= $upper);
    }
    return is_finite($check);
  }
/**
 * デフォルトのemailだとRFCに沿わないau,docomoのアドレスがinvalidになるので上書き
 * 参考
 * HTML - メールアドレスを表す現実的な正規表現 - Qiita
 * http://qiita.com/sakuro/items/1eaa307609ceaaf51123
 */
  public static function email(Model $model, $check) {
    $pattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/';
    return preg_match($pattern, current($check));
  }
/**
 * デフォルトのphoneには日本の電話番号に対応していないので上書き
 * 参考
 * Emailと電話番号のバリデーション(またまた正規表現のお話) - 肉とビールとパンケーキ by @sotarok
 * http://sotarok.hatenablog.com/entry/20080115/1200460940
 * 電話番号チェックする正規表現(入力枠1個、ハイフン抜き) | 1000g
 * http://blog.1000k.net/2011/01/25/%E9%9B%BB%E8%A9%B1%E7%95%AA%E5%8F%B7%E3%83%81%E3%82%A7%E3%83%83%E3%82%AF%E3%81%99%E3%82%8B%E6%AD%A3%E8%A6%8F%E8%A1%A8%E7%8F%BE%E5%85%A5%E5%8A%9B%E6%9E%A01%E5%80%8B%E3%80%81%E3%83%8F%E3%82%A4%E3%83%95/
 * preg_match()正規表現チェッカー
 * http://okumocchi.jp/php/re.php
 */
  public static function phone(Model $model, $check) {
    // チェックするデータを取得
    $value = current($check);
    // ハイフン区切りの場合のそれぞれの桁数のチェック
    $pattern1 = '/^0[0-9]{1,4}-?[0-9]{1,4}-?[0-9]{3,4}$/';
    // 全体の文字数のチェック
    $pattern2 = '/^0[0-9]{9,10}$/';
    return preg_match($pattern1, $value) && preg_match($pattern2, str_replace('-', '', $value));
  }
/**
 * デフォルトのpostalには日本の郵便番号に対応していないので上書き
 */
  public static function postal(Model $model, $check) {
    // ハイフンがない場合にハイフンをつける
    $value = current($check);
    if(strlen($value) == 7 && preg_match('/^[0-9]+$/', $value)) {
      $value = substr($value, 0, 3) . '-' . substr($value, 3);
      $model->data[$model->alias][key($check)] = $value;
    }
    return preg_match('/^[0-9]{3}-[0-9]{4}$/', $value);
  }
/**
 * 都道府県のチェック
 */
  public static function state(Model &$model, $check) {
    return Validation::inList(current($check), Configure::read('states'));
  }
/**
 * アップロードファイルがない場合にtrueを返す
 */
  public static function extension(Model $model, $check, $extensions = ['gif', 'jpeg', 'png', 'jpg']) {
    // チェックするデータを取得
    $value = current($check);
    if(is_array($value) && array_key_exists('tmp_name', $value) && !$value['tmp_name']) {
      return true;
    }
    return Validation::extension($value, $extensions);
  }
  public static function mimeType(Model $model, $check, $mimeTypes = []) {
    // チェックするデータを取得
    $value = current($check);
    if(is_array($value) && array_key_exists('tmp_name', $value) && !$value['tmp_name']) {
      return true;
    }
    return Validation::mimeType($value, $mimeTypes);
  }
/**
 * 画像ファイルかどうか
 */
  public static function imageFile(Model $model, $check) {
    $extensions = ['gif', 'jpeg', 'png', 'jpg'];
    $mimeTypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'];
    return self::extension($model, $check, $extensions) && self::mimeType($model, $check, $mimeTypes);
  }
/**
 * デフォルトのエラーメッセージ用
 */
  public static function maxFileSize(Model $model, $check, $size = null) {
    return Validation::fileSize(current($check), '<=', $size);
  }
/**
 * ひらがな、カタカナかどうか
 * 参考
 * CakePHP Behaviorでバリデーション周りの効率化を図る : エクスギア　Blog
 * http://www.exgear.jp/blog/2009/06/cakephp-behavior%E3%81%A7%E3%83%90%E3%83%AA%E3%83%87%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E5%91%A8%E3%82%8A%E3%81%AE%E5%8A%B9%E7%8E%87%E5%8C%96%E3%82%92%E5%9B%B3%E3%82%8B/
 */
  public static function hiragana(Model $model, $check) {
    return preg_match('/^(?:\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93])+$/', current($check));
  }
  public static function katakana(Model $model, $check) {
    return preg_match('/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|ー)+$/', current($check));
  }
/**
 * ユーザ名として、半角英数字＋「-_.」を許可する
 */
  public static function username(Model &$model, $check) {
    if(!preg_match('/^[a-zA-Z0-9_\-\.]+$/', current($check))) {
      return false;
    } elseif(!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_\-\.]*[a-zA-Z0-9]$/', current($check))) {
      $model->validator()->getField(key($check))->getRule('username')->message = self::$errorMessages['usernameMark'];
      return false;
    } else {
      return true;
    }
  }
}
