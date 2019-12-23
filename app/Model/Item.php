<?php
class Item extends AppModel {
  public $actsAs = [
    'Ique.IqueS3' => ['image']
  ];
/**
 * バリデーションルール
 */
  public $validate = [
    'title' => 'notEmpty|maxLength,50',
    'image' => [
      'imageFile|maxFileSize,2MB',
      'uploadError' => [
        'rule' => 'uploadError',
        'on' => 'create',
      ],
    ],
    'nickname' => 'notEmpty|maxLength,50',
  ];
/**
 * beforeValidate
 */
  public function beforeValidate($options = []) {
    if(array_key_exists('image', $this->data[$this->alias])) {
      $image = $this->data[$this->alias]['image'];
      // リサイズ
      if(is_array($image) && array_key_exists('tmp_name', $image) && file_exists($image['tmp_name'])) {
        ini_set('memory_limit', '256M');
        // 画像の取得
        $imagine = new Imagine\Gd\Imagine();
        $imagine->setMetadataReader(new Imagine\Image\Metadata\ExifMetadataReader());
        $tmpImage = $imagine->open($image['tmp_name']);
        // 回転（リサイズしてから回転すると小さくなる）
        $autoRotateFilter = new Imagine\Filter\Basic\Autorotate();
        $tmpImage = $autoRotateFilter->apply($tmpImage);
        // リサイズ
        $tmpImage = $tmpImage->thumbnail(new Imagine\Image\Box(810, 810));
        // データの上書き
        $this->data[$this->alias]['image'] = $this->_tmpfile($tmpImage->get(
          str_replace('image/', '', $image['type']),
          ['png_compression_level' => 6, 'jpeg_quality' => 75] // デフォルト値
        ));
      }
      // DataURIスキームの場合
      if(is_string($image) && preg_match('/^data:image\/([a-z]+);base64,/', $image, $match)) {
        // データの抽出
        $encodedFile = str_replace($match[0], '', $image);
        // データの上書き
        $this->data[$this->alias]['image'] = $this->_tmpfile(base64_decode($encodedFile));
      }
    }
    return parent::beforeValidate($options);
  }
/**
 * afterFind
 */
  public function afterFind($results) {
    foreach($results as &$result) {
      if(array_key_exists('Item', $result)) {
        if(array_key_exists('image', $result['Item'])) {
          if(get_class($this->getDataSource()) == 'Lancers' && is_string($result['Item']['image'])) {
            $result['Item']['image'] = explode(',', $result['Item']['image']);
          }
        }
      }
    }
    return parent::afterFind($results);
  }
/**
 * VoteModelをbindする
 */
  public function bindVoted($requestData = []) {
    // ソーシャルアカウントでの投票状態をチェック
    $accounts = [];
    // Facebookアカウント
    if($facebookId = $this->Facebook->getUser()) {
      $accounts[] = [
        'Voted.entry_type' => 'facebook',
        'Voted.facebook_id' => $facebookId,
      ];
    }
    // Twitterアカウント
    if($twitterId = $this->Twitter->getUser()) {
      $accounts[] = [
        'Voted.entry_type' => 'twitter',
        'Voted.twitter_id' => $twitterId,
      ];
    }
    // メールアドレス
    if(!empty($requestData['Vote']['email'])) {
      $accounts[] = [
        'Voted.entry_type' => 'email',
        'Voted.email' => $requestData['Vote']['email'],
      ];
    }
    $this->bindModel(['hasMany' => [
      'Voted' => [
        'className' => 'Vote',
        'conditions' => [
          'Voted.created >=' => date('Y-m-d 00:00:00'),
          'OR' => $accounts ? $accounts : ['id' => false],
        ],
      ],
    ]], false);
  }
/**
 * 一時ファイルを作成して、情報を返す
 */
  protected function _tmpfile($data) {
    // 一時ファイルの作成（メソッドが終わってもファイルが消えないようにプロパティとして定義）
    $this->_handle = tmpfile();
    fwrite($this->_handle, $data);
    $handleInfo = stream_get_meta_data($this->_handle);
    // 一時ファイルの読み込み
    $tmpFile = new File($handleInfo['uri']);
    $fileInfo = $tmpFile->info();
    // 拡張子
    $ext = str_replace('image/', '', $fileInfo['mime']);
    $ext = $ext == 'jpeg' ? 'jpg' : $ext;
    return [
      'name' => 'image.' . $ext,
      'type' => $fileInfo['mime'],
      'tmp_name' => $handleInfo['uri'],
      'error' => 0,
      'size' => $fileInfo['filesize'],
    ];
  }
}



