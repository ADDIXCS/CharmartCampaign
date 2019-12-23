<?php
class Enquete extends AppModel {
/**
 * バリデーションルール
 */
  public $validate = [
    'text' => 'notEmpty',
    'type' => 'notEmpty|inList[text,textarea,select,radio,check]',
    'order' => 'notEmpty|inList[static,random]',
    'required_flg' => 'boolean',
  ];
}

