<?php
class SorapsCsvBehavior extends ModelBehavior {
  protected $_handle;
  protected $_enquetes = [];
  protected $_header = [
    'id' => [
      '="ID"',
    ],
    'common' => [
      '姓', '名', 'せい', 'めい', 'アカウントタイプ', 'メールアドレス',
      '電話番号', '性別', '生年月日', '年齢', '郵便番号', '都道府県', '郡市区', 'それ以降の住所',
    ],
    'commonAudience' => [
      '参加回数', '投票回数', 'シェア回数', 'エンゲージメントスコア',
    ],
    'facebook' => ['FacebookID', 'Facebookユーザ名', 'Facebook友達数'],
    'facebookEntry' => [
      'Facebook新規いいね！有無', 'Facebookシェア有無', 'Facebookシェア内容',
    ],
    'twitter' => [
      'TwitterID', 'Twitterユーザ名', 'Twitterフォロー数', 'Twitterフォロワー数',
    ],
    'twitterEntry' => [
      'Twitter新規フォロー有無', 'Twitterシェア有無', 'Twitterシェア内容',
    ],
    'date' => [
      '応募日時',
    ],
  ];
/**
 * setup
 */
  public function setup(Model $model) {
    $this->_handle = fopen('php://temp', 'r+b');
  }
/**
 * CSVデータの生成
 */
  public function getCsvData(Model $model, $users) {
    fputcsv($this->_handle, $this->_getHeaders($model));
    // データ行
    foreach($users as $_user) {
      $user = $_user[$model->alias];
      $line = [$user['id']];
      if($model->alias == 'Audience') {
        $line = am($line, [
          $user['entry_count'], $user['vote_count'], $user['shared_count'], $user['engagement'],
        ]);
      }
      $line = am($line, [
        $user['last_name'], $user['first_name'],
        $user['last_kana'], $user['first_kana'],
        $user['entry_type'], $user['email'],
        $user['tel'], $user['gender_ja'], $user['birthday'], $user['age'],
        $user['postcode'], $user['state'], $user['city'], $user['street'],
      ]);
      $line = am($line, [
        '="' . $user['facebook_id'] . '"',
        $user['facebook_username'], $user['facebook_friend_count'],
      ]);
      if($model->alias == 'Entry') {
        $line = am($line, [
          $user['facebook_new_fan'], $user['facebook_shared'], $user['facebook_shared_message'],
        ]);
      }
      $line = am($line, [
        '="' . $user['twitter_id'] . '"',
        $user['twitter_name'] . ($user['twitter_screen_name']
          ? '(' . $user['twitter_screen_name'] . ')'
          : ''),
        $user['twitter_friends_count'], $user['twitter_followers_count'],
      ]);
      if($model->alias == 'Entry') {
        $line = am($line, [
          $user['twitter_new_fan'], $user['twitter_shared'], $user['twitter_shared_message'],
        ]);
      }
      foreach($this->_enquetes as $enquete) {
        $enqueteData = '';
        if(array_key_exists($enquete['id'], $_user['EntryEnquete'])) {
          if($enquete['type'] != 'check') {
            $enqueteData = $_user['EntryEnquete'][$enquete['id']];
          } else {
            $enqueteData = implode(',', $_user['EntryEnquete'][$enquete['id']]);
          }
        }
        $line[] = $enqueteData;
      }
      $line = am($line, [
        $user['created'],
      ]);
      fputcsv($this->_handle, $line);
    }
    rewind($this->_handle);
    return mb_convert_encoding(stream_get_contents($this->_handle), 'SJIS-win');
  }
/**
 * アンケートデータのセット
 */
  public function setEnquetes(Model $model, $enquetes) {
    $this->_enquetes = $enquetes;
  }
/**
 * 応募、オーディエンスの見出し
 */
  protected function _getHeaders(Model $model) {
    $header = [];
    switch($model->alias) {
      case 'Entry':
        $header = am(
          $this->_header['id'],
          $this->_header['common'],
          $this->_header['facebook'],
          $this->_header['facebookEntry'],
          $this->_header['twitter'],
          $this->_header['twitterEntry']
        );
        foreach($this->_enquetes as $enquete) {
          $header[] = $enquete['text'];
        }
        $header = am(
          $header,
          $this->_header['date']
        );
        break;
      case 'Audience':
        $header = am(
          $this->_header['id'],
          $this->_header['commonAudience'],
          $this->_header['common'],
          $this->_header['facebook'],
          $this->_header['twitter'],
          $this->_header['date']
        );
        break;
    }
    return $header;
  }
}
