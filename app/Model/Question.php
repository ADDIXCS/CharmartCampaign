<?php
class Question extends AppModel {
  public $actsAs = [
    'Ique.IqueS3' => ['image']
  ];
/**
 * バリデーションルール
 */
  public $validate = [
    'image' => [
      'imageFile|maxFileSize,2MB',
      'validImageOrText' => [
        'rule' => 'validImageOrText',
        'message' => '画像かテキストは必須です',
      ],
    ],
    'text' => [
      'validImageOrText' => [
        'rule' => 'validImageOrText',
        'message' => '画像かテキストは必須です',
        'allowEmpty' => true,
      ],
    ],
    'type' => 'notEmpty|inList[radio,checkbox]',
    'order' => 'notEmpty|inList[static,random]',
  ];
/**
 * 画像かテキストが必須
 */
  public function validImageOrText($check) {
    $data = $this->data[$this->alias];
    return $data['text'] || Validation::uploadError($data['image']);
  }
}

