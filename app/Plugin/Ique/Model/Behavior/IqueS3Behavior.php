<?php
class IqueS3Behavior extends ModelBehavior {
/**
 * 初期設定
 */
  public function setup(Model $model, $config = []) {
    $this->settings[$model->alias] = $config;
    $config = [
      'key' => Configure::read('aws.key'),
      'secret' => Configure::read('aws.secret'),
    ];
    $this->s3 = Aws\S3\S3Client::factory($config);
    $this->bucket = Configure::read('aws.bucket');
  }
/**
 * 保存前にファイルをアップロードする
 */
  public function beforeSave(Model $model, $options = []) {
    // モデルのid別のフォルダに保存するのでidは必須
    if(!$model->id) {
      return false;
    }
    foreach($this->settings[$model->alias] as $field => $fileName) {
      if(is_int($field)) {
        $field = $fileName;
      }
      $data = array_key_exists($field, $model->data[$model->alias])
        ? $model->data[$model->alias][$field]
        : '';
      if(is_array($data) && array_key_exists('tmp_name', $data) && file_exists($data['tmp_name'])) {
        // バリデーションのみの場合はアップロードしない
        if($options['validate'] === 'only') {
          continue;
        }
        // 拡張子の取得
        $ext = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
        // CakeのFileクラス
        $tmpFile = new File($data['tmp_name']);
        $tmpFile->open();
        try {
          // 拡張子が変わる場合もあるので削除してアップロード
          $this->s3->deleteMatchingObjects(
            $this->bucket,
            Inflector::tableize($model->alias) . '/' . $model->id . '/' . $fileName
          );
          $this->s3->upload(
            $this->bucket,
            Inflector::tableize($model->alias) . '/' . $model->id . '/' . $fileName . '.' . $ext,
            $tmpFile->handle,
            'public-read',
            ['params' => ['ContentType' => $tmpFile->mime()]]
          );
          // 保存用のファイル名をセット
          $model->data[$model->alias][$field] = $fileName . '.' . $ext . '?' . uniqid();
        } catch(Aws\S3\Exception\S3Exception $e) {
          // エラーメッセージの出し分け
          if(Configure::read('debug') > 0) {
            $model->validationErrors[$field][] = $e->getMessage();
          } else {
            $model->validationErrors[$field][] = 'ファイルのアップロードでエラーが発生しました';
          }
          return false;
        }
      } else {
        // アップロードされたファイルが無い場合削除処理
        if(!empty($model->data[$model->alias][$field . '_delete_flg'])) {
          try {
            $this->s3->deleteMatchingObjects(
              $this->bucket,
              Inflector::tableize($model->alias) . '/' . $model->id . '/' . $fileName
            );
          } catch(Aws\S3\Exception\S3Exception $e) {
            // エラーメッセージの出し分け
            if(Configure::read('debug') > 0) {
              $model->validationErrors[$field][] = $e->getMessage();
            } else {
              $model->validationErrors[$field][] = 'ファイルの削除でエラーが発生しました';
            }
            return false;
          }
          $model->data[$model->alias][$field] = '';
        } else {
          unset($model->data[$model->alias][$field]);
        }
      }
    }
    return true;
  }
/**
 * 削除前にファイルを削除する
 */
  public function beforeDelete(Model $model, $cascade = true) {
    if($model->Behaviors->enabled('SoftDelete') && $model->softDelete(null)) {
      return true;
    }
    try {
      $this->s3->deleteMatchingObjects(
        $this->bucket,
        Inflector::tableize($model->alias) . '/' . $model->id . '/'
      );
    } catch(Aws\S3\Exception\S3Exception $e) {
      return false;
    }
    return true;
  }
}
