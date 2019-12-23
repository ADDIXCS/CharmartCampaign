<?php
class Result extends AppModel {
  public $actsAs = [
    'Ique.IqueS3' => ['image']
  ];
/**
 * バリデーションルール
 */
  public $validate = [
    'point_min' => [
      'notEmpty|numeric',
      'validPointRange' => [
        'rule' => 'validPointRange',
        'message' => '配点の範囲が不正です',
      ],
    ],
    'point_max' => [
      'notEmpty|numeric',
      'validPointRange' => [
        'rule' => 'validPointRange',
        'message' => '配点の範囲が不正です',
      ],
    ],
    'image' => 'imageFile|maxFileSize,2MB',
    'title' => 'notEmpty',
  ];
/**
 * 配点のmaxがmin以上か
 */
  public function validPointRange($check) {
    $data = $this->data[$this->alias];
    return $data['point_min'] <= $data['point_max'];
  }
/**
 * 配点のバリデーションメッセージをセット
 */
  public function afterValidate() {
    if(array_key_exists('point_min', $this->validationErrors)) {
      $this->validationErrors['validate_points'] = $this->validationErrors['point_min'];
      unset($this->validationErrors['point_min']);
    }
    if(array_key_exists('point_max', $this->validationErrors)) {
      $this->validationErrors['validate_points'] = $this->validationErrors['point_max'];
      unset($this->validationErrors['point_max']);
    }
  }
}


