<?php
class Answer extends AppModel {
/**
 * バリデーションルール
 */
  public $validate = [
    'text' => 'notEmpty',
    'point' => 'notEmpty|numeric',
  ];
}

