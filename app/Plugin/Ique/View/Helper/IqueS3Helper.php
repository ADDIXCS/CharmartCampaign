<?php
/**
 * UploadPackを参考に作成
 * https://github.com/szajbus/uploadpack
 */
class IqueS3Helper extends AppHelper {
  public $helpers = ['Html'];
  public $region = 'us-east-1';

/**
 * S3上の画像を直接表示
 */
  public function image($data, $path, $options = []) {
    $url = $this->url($data, $path);
    return $url ? $this->Html->image($url, $options) : '';
  }
/**
 * S3上の画像をリサイズサーバを通して表示
 */
  public function resized($data, $path, $size, $options = []) {
    $url = $this->url($data, $path, $size);
    return $url ? $this->Html->image($url, $options) : '';
  }
/**
 * ファイルのURLを生成
 */
  public function url($data, $path, $size = '') {
    list($model, $field) = explode('.', $path);
    // 渡されるデータがモデルでラップされてない場合があるのでデータの整形
    if(array_key_exists($model, $data)) {
      $id = $data[$model]['id'];
      $filename = $data[$model][$field];
    } else {
      $id = $data['id'];
      $filename = $data[$field];
    }
    // データの存在チェック
    if(!$filename) {
      return;
    }
    // bucket名/table名/id/拡張子無しのファイル名
    $filePath = Configure::read('aws.bucket') . '/' . Inflector::tableize($model) . '/';
    $filePath .= $id . '/' . pathinfo($filename, PATHINFO_FILENAME);
    // 拡張子の取得
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    // サイズ指定の有無によるURLの出し分け
    if($size) {
      $ctx = hash_init('sha256', HASH_HMAC, Configure::read('aws.secret'));
      hash_update($ctx, $filePath . '/' . $size);
      $url = 'http://image.soraps.jp/';
      $url .= $this->resizeEndpoint . $filePath . '/' . $size . '/' . hash_final($ctx) . '.' . $ext;
    } else {
      $this->region = Configure::read('aws.region');
      $url = $this->getPublicAccessBaseUrl();
      $url .= $this->S3Endpoint . $filePath . '.' . $ext;
    }
    return $url;
  }

  protected function getPublicAccessBaseUrl() {
      switch($this->region) {
          case 'us-east-1':
              return 'https://s3.amazonaws.com/';
              break;
          case 'ap-northeast-1':
              return 'https://s3-ap-northeast-1.amazonaws.com/';
              break;
          default:
              return 'https://s3.amazonaws.com/';
              break;
      }
  }
}
