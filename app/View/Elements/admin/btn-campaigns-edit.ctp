<?php
$saveBtnText = '保存';
$saveBtnIcon = 'save';
if($campaign['Campaign']['status'] == 'editing') {
  switch(true) {
    case $this->request->param('editType') == 'items':
    case $this->request->param('editType') == 'detail':
      break;
    default:
      $saveBtnText = '保存して次のステップへ';
      $saveBtnIcon = 'arrow-right';
  }
}
echo $this->Form->button($this->Html->icon($saveBtnIcon, 'lg', true) . $saveBtnText, [
  'type' => 'submit',
  'class' => 'btn btn-primary',
]);
echo $this->Html->btn(
  'キャンセル',
  [
    'controller' => 'campaigns',
    'action' => 'view',
    'accountName' => $currentAccount['Account']['account_name'],
    'id' => $campaign['Campaign']['id'],
  ]
);
if($this->request->param('editType') == 'items' && $items) {
  echo $this->Html->btn(
    '次のステップへ',
    [
      'action' => 'edit',
      'accountName' => $currentAccount['Account']['account_name'],
      'id' => $campaign['Campaign']['id'],
      'editType' => 'page-items',
    ],
    ['type' => 'primary', 'icon' => 'arrow-right']
  );
}
