<?php
switch($key) {
  case 'name':
  case 'kana':
    $requiredKey = ($this->action == 'vote' ? 'input_vote_' : 'input_') . $key;
    echo $this->Form->input($key, [
      'type' => 'name',
      'label' => [
        'text' => $label,
        'required' => $campaign['Campaign'][$requiredKey] == 2 ? true : false,
      ],
      'kana' => $key == 'kana',
    ]);
    break;
  case 'gender':
    echo $this->Form->input($key, [
      'type' => 'radio',
      'label' => $label,
      'options' => ['male' => '男性', 'female' => '女性'],
    ]);
    break;
  case 'birthday':
    echo $this->Form->input($key, [
      'type' => 'date',
      'label' => $label,
      'minYear' => 1940,
      'maxYear' => date('Y'),
      'default' => false,
    ]);
    break;
  case 'state':
    echo $this->Form->input($key, [
      'type' => 'select',
      'label' => $label,
      'options' => array_combine(Configure::read('states'), Configure::read('states')),
      'empty' => '選択してください',
      'inline' => true,
    ]);
    break;
  case 'email':
  case 'tel':
  case 'postcode':
    echo $this->Form->input($key, [
      'type' => 'text',
      'label' => $label,
      'placeholder' => '半角で入力',
      'inline' => $key === 'postcode',
    ]);
    break;
  default:
    echo $this->Form->input($key, [
      'type' => 'text',
      'label' => $label,
    ]);
}
