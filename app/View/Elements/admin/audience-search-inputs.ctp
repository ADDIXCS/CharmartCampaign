<?php
echo $this->Form->input('entry_type', [
  'type' => 'select',
  'label' => 'アカウント',
  'multiple' => 'checkbox',
  'class' => 'checkbox-inline',
  'options' => ['facebook' => 'Facebook', 'twitter' => 'Twitter'],
]);
echo $this->Form->input('gender', [
  'type' => 'select',
  'label' => '性別',
  'multiple' => 'checkbox',
  'class' => 'checkbox-inline',
  'options' => ['male' => '男性', 'female' => '女性'],
]);
echo $this->Form->input('age', [
  'type' => 'range',
  'label' => '年齢',
]);
echo $this->Form->input('state', [
  'type' => 'select',
  'label' => '都道府県',
  'options' => array_combine(Configure::read('states'), Configure::read('states')),
  'empty' => '選択してください',
  'inline' => true,
]);
echo $this->Form->input('entry_count', [
  'type' => 'range',
  'label' => '参加回数',
]);
echo $this->Form->input('friend_count', [
  'type' => 'range',
  'label' => '友達／フォロワー数',
]);
echo $this->Form->input('past_date', [
  'type' => 'range',
  'label' => '経過日数',
]);
echo $this->Form->input('engagement', [
  'type' => 'range',
  'label' => 'エンゲージメント',
]);
echo $this->Form->input('name', [
  'type' => 'text',
  'label' => '名前',
]);
echo $this->Form->input('campaign', [
  'type' => 'select',
  'label' => '参加キャンペーン',
  'options' => $campaigns,
  'empty' => '選択してください',
  'inline' => true,
  'class' => 'form-control input-large',
  'helpBlock' => '結果を表示するのに時間がかかります',
]);

