<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
  public $actsAs = [
    'Ique.IqueValidation',
    'Utils.SoftDelete' => ['priority' => 99],
  ];
/**
 * データの新規作成時のidにuniqid()をセットする
 *
 * @param array $data 保存するデータ
 * @param $filterKey 不明
 */
  public function create($data = [], $filterKey = false) {
    // counterCacheの設定
    $this->setCounterCache();
    // $dataにnullが渡ってくることがある
    return parent::create(array_merge(['id' => uniqid()], (array) $data), $filterKey);
  }
/**
 * SoftDeleteBehaviorを正しく動作させるため、Modelのメソッドを上書き
 *
 * @param array $id データのID
 * @return boolean
 */
  public function exists($id = null) {
    if ($this->Behaviors->attached('SoftDelete')) {
      return $this->existsAndNotDeleted($id);
    } else {
      return parent::exists($id);
    }
  }
/**
 * SoftDeleteBehaviorを正しく動作させるため、Modelのメソッドを上書き
 *
 * @param array $id データのID
 * @param $cascade 不明
 * @return boolean
 */
  public function delete($id = null, $cascade = true) {
    // counterCacheの設定
    $this->setCounterCache();
    $result = parent::delete($id, $cascade);
    if ($result === false && $this->Behaviors->enabled('SoftDelete')) {
      // CounterCache
      $softDeleteParam = $this->softDelete(null);
      $this->softDelete(false);
      $this->updateCounterCache();
      $this->softDelete($softDeleteParam);
      return (bool)$this->field('deleted', ['deleted' => 1]);
    }
    return $result;
  }
/**
 * SoftDeleteBehaviorを使用した際、
 * アソシエーション先のModelでも論理削除済みのデータを返さないように
 */
  public function beforeFind($query) {
    foreach($this->hasMany as $alias => $modelInfo) {
      if($this->$alias->Behaviors->attached('SoftDelete')) {
        if(empty($modelInfo['conditions'])) {
          $this->hasMany[$alias]['conditions'] = [$alias . '.deleted !=' => 1];
        } else {
          $this->hasMany[$alias]['conditions'] = array_merge(
            $modelInfo['conditions'],
            [$alias . '.deleted !=' => 1]
          );
        }
      }
      $this->$alias->beforeFind($query);
    }
    return parent::beforeFind($query);
  }
/**
 * データが存在しなければ例外を投げ、存在する場合はデータを返す
 *
 * @param string $type 取得するデータの種類
 * @param array $query 検索条件
 * @return array 見つかったデータ
 */
  public function requireData($type = 'first', $query = []) {
    if(!$data = $this->find($type, $query)) {
      throw new SorapsException('NotFound', $this->_nameJa);
    }
    return $data;
  }
/**
 * データが存在しなければ例外を投げ、存在する場合はデータを返す
 *
 * @param string $id データのid
 * @return array 見つかったデータ
 */
  public function requireById($id = null) {
    $id = $id ? $id : $this->getID();
    if(!$id || !$data = $this->findById($id)) {
      throw new SorapsException('NotFound', $this->_nameJa);
    }
    return $data;
  }
/**
 * SoftDeleteBehaviorを使用している場合に正しく動作させるため、Modelのメソッドを上書き
 *
 * @param array $fields フィールド
 * @param boolean $or 検索条件
 * @return boolean
 */
  public function isUnique($fields, $or = true) {
    if($this->Behaviors->attached('SoftDelete')) {
      $softDeleteParam = $this->softDelete(null);
      $this->softDelete(false);
    }
    $result = parent::isUnique($fields, $or);
    if($this->Behaviors->attached('SoftDelete')) {
      $this->softDelete($softDeleteParam);
    }
    return $result;
  }
/**
 * counterCache用のモデルのbind
 * それぞれの子モデルで上書きする
 */
  public function setCounterCache() {
  }
}
