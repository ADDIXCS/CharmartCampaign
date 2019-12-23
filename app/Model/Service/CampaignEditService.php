<?php
class CampaignEditService extends Object {
  public function __construct() {
    $this->Campaign = ClassRegistry::init('Campaign');
  }
/**
 * キャンペーン編集
 */
  public function editCampaign($data, $editType) {
    // トランザクション開始
    $dataSource = $this->Campaign->getDataSource();
    $dataSource->begin();
    // 複数の子データの削除処理
    $data = $this->_deleteChildData($data, $editType);
    // 保存処理
    if($this->Campaign->saveAssociated($data, ['deep' => true])) {
      $dataSource->commit();
      return true;
    } else {
      $dataSource->rollback();
      return false;
    }
  }
/**
 * 複数の子データの削除処理
 */
  protected function _deleteChildData($data, $editType) {
    if(in_array($editType, ['enquetes', 'gifts', 'questions', 'results'])) {
      $modelName = Inflector::classify($editType);
      if(!array_key_exists($modelName, $data)) {
        return $data;
      }
      $this->Campaign->$modelName->softDelete(false);
      foreach($data[$modelName] as $i => $object) {
        // 削除フラグチェック
        if($object['delete_flg']) {
          if(!empty($object['id'])) {
            $this->Campaign->$modelName->delete($object['id']);
          }
          unset($data[$modelName][$i]);
        }
        // さらに子データのある場合
        if(in_array($editType, ['enquetes', 'questions'])) {
          $childModels = ['questions' => 'Answer', 'enquetes' => 'EnqueteOption'];
          $childModelName = $childModels[$editType];
          if(!array_key_exists($childModelName, $object)) {
            continue;
          }
          $this->Campaign->$modelName->$childModelName->softDelete(false);
          foreach($object[$childModelName] as $j => $objectChild) {
            if($object['delete_flg'] || $objectChild['delete_flg']) {
              if(!empty($objectChild['id'])) {
                $this->Campaign->$modelName->$childModelName->delete($objectChild['id']);
              }
              unset($data[$modelName][$i][$childModelName][$j]);
            }
          }
        }
      }
    }
    return $data;
  }
/**
 * キャンペーン編集のステップにアクセスできるかどうか
 */
  public function isAccessibleEditType($campaign, $editType) {
    if(!in_array($editType, array_keys(CampaignValue::getEditTypes($campaign)))) {
      throw new SorapsException('BadEditType');
    }
    $editStep = CampaignValue::getEditStep($campaign, $editType);
    if($editStep <= $campaign['Campaign']['edit_step'] + 1) {
      return true;
    } else {
      return false;
    }
  }
/**
 * キャンペーン編集が完了したかどうか
 */
  public function isEditFinished($campaign) {
    return count(CampaignValue::getEditTypes($campaign)) == $campaign['Campaign']['edit_step'];
  }
}
