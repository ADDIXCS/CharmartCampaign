<?php
class EnqueteOption extends AppModel {
/**
 * バリデーションルール
 */
  public $validate = [
    'text' => 'notEmpty',
  ];
}

