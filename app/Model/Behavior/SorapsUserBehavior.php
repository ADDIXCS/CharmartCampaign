<?php
class SorapsUserBehavior extends ModelBehavior {
/**
 * setup
 */
  public function setup(Model $model) {
    // バリデーションルールのセット
    $model->validate = [
      'last_kana' => 'hiragana',
      'first_kana' => 'hiragana',
      'email' => 'email',
      'tel' => 'phone',
      'gender' => 'inList[male,female]',
      'birthday' => 'date',
      'postcode' => 'postal',
      'state' => 'state',
    ];
    $model->setValidationRules();
  }
/**
 * 名前のバリデーションメッセージをセット
 */
  public function afterValidate(Model $model) {
    if(array_key_exists('last_name', $model->validationErrors)) {
      $model->validationErrors['name'] = $model->validationErrors['last_name'];
    }
    if(array_key_exists('first_name', $model->validationErrors)) {
      $model->validationErrors['name'] = $model->validationErrors['first_name'];
    }
    if(array_key_exists('last_kana', $model->validationErrors)) {
      $model->validationErrors['kana'] = $model->validationErrors['last_kana'];
    }
    if(array_key_exists('first_kana', $model->validationErrors)) {
      $model->validationErrors['kana'] = $model->validationErrors['first_kana'];
    }
  }
/**
 * データの整形
 * リレーション先のモデルからはafterFindが呼ばれないので、
 * 単独で呼び出せるように別名のメソッドを定義
 */
  public function afterFind(Model $model, $results) {
    return $this->modifySorapsUserData($model, $results);
  }
  public function modifySorapsUserData(Model $model, $results) {
    $now = date('Ymd');
    foreach($results as &$_result) {
      if(is_array($_result) && array_key_exists($model->alias, $_result)) {
        $result = $_result[$model->alias];
        // フルネーム
        if(array_key_exists('last_name', $result) && array_key_exists('first_name', $result)) {
          if($result['last_name'] || $result['first_name']) {
            $result['full_name'] = $result['last_name'] . ' ' . $result['first_name'];
          } else {
            $result['full_name'] = '氏名登録なし';
          }
        }
        // 年齢の計算
        if(array_key_exists('birthday', $result)) {
          if($result['birthday']) {
            $age = floor(($now - date('Ymd', strtotime($result['birthday']))) / 10000);
            $result['age'] = $age;
          } else {
            $result['age'] = '';
          }
        }
        // 性別の日本語化
        if(array_key_exists('gender', $result)) {
          if($result['gender'] == 'male') {
            $result['gender_ja'] = '男性';
          } elseif($result['gender'] == 'female') {
            $result['gender_ja'] = '女性';
          } else {
            $result['gender_ja'] = '';
          }
        }
        // シェアしたかどうか、新規いいね／フォローかどうかの統合（Entryモデルの場合のみ）
        if($model->alias == 'Entry' && array_key_exists('entry_type', $result)) {
          if(
            array_key_exists($result['entry_type'] . '_shared', $result) &&
            $result[$result['entry_type'] . '_shared']
          ) {
            $result['shared'] = true;
          } else {
            $result['shared'] = false;
          }
          if(
            array_key_exists($result['entry_type'] . '_new_fan', $result) &&
            $result[$result['entry_type'] . '_new_fan']
          ) {
            $result['new_fan'] = true;
          } else {
            $result['new_fan'] = false;
          }
        }
        $_result[$model->alias] = $result;
      }
    }
    return $results;
  }
/**
 * Twitterのプロフィール画像をセット
 */
  public function addTwitterProfileImageUrl(Model $model, $users) {
    $urls = $model->Twitter->getProfileImageUrl(
      Hash::extract($users, '{n}.' . $model->alias . '.twitter_id')
    );
    foreach($users as &$user) {
      $twitterId = $user[$model->alias]['twitter_id'];
      if(array_key_exists($twitterId, $urls)) {
        $user[$model->alias]['twitter_profile_image_url'] = $urls[$twitterId];
      }
    }
    return $users;
  }
}
