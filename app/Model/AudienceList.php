<?php
class AudienceList extends AppModel {
/**
 * バリデーションルール
 */
  public $validate = [
    'name' => 'notEmpty',
    'criteria' => [
      'notEmpty' => [
        'rule' => 'notEmpty',
        'message' => '検索条件を指定してください',
      ],
    ],
  ];
}
