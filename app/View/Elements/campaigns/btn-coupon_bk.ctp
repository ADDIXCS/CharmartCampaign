<?php
$btnOptions = ['type' => 'primary', 'size' => 'lg', 'block' => true];
switch(true) {
  // 未応募の場合
  case !$entried:
    echo $this->Html->btn(
      '使用できません',
      '#',
      $btnOptions,
      ['class' => 'disabled']
    );
    break;
  // 使用済みの場合
  case $entried['Entry']['coupon_used']:
    echo $this->Html->btn(
      '使用済み',
      '#',
      $btnOptions,
      ['class' => 'disabled']
    );
    break;
  default:
    echo $this->Html->btn(
      'クーポンを使用する',
      '#',
      $btnOptions,
      ['id' => 'js-btn-coupon']
    );
}
