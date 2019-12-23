<?php
$block = isset($block) ? $block : false;
echo $this->Html->btn(
  $campaign['Campaign']['item_name'] . '一覧を見る',
  $this->Soraps->isPreview() ? '#' : [
    'action' => 'items',
    'accountName' => $currentAccount['Account']['account_name'],
    'id' => $campaign['Campaign']['id'],
  ],
  ['type' => 'default', 'size' => 'lg', 'block' => $block]
);

