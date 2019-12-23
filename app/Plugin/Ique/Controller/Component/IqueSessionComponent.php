<?php
class IqueSessionComponent extends SessionComponent {
/**
 * bootstrapのelementを使用したalertを生成する
 */
  public function setBsFlash($message, $type = 'info') {
    parent::setFlash($message, 'alert', [
      'plugin' => 'BoostCake',
      'class' => 'alert-' . $type,
    ]);
  }
/**
 * それぞれのalertのエイリアス
 */
  public function setSuccessFlash($message) {
    $this->setBsFlash($message, 'success');
  }
  public function setDangerFlash($message) {
    $this->setBsFlash($message, 'danger');
  }
}
