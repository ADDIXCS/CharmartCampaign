<?php
$entried = !empty($entried) ? $entried['Entry'] : false;
$btnOptions = ['type' => 'primary'];
$shareMessage = '';
$afterInput = '';
// ボタンの種類とシェアの文言の設定
if($entried) {
  switch($entried['entry_type']) {
    case 'facebook':
      $btnOptions = ['type' => 'facebook', 'icon' => 'facebook-square'];
      break;
    case 'twitter':
      $btnOptions = ['type' => 'twitter', 'icon' => 'twitter'];
      // シェアの文言
      switch($campaign['Campaign']['campaign_type']) {
        case $campaign['Campaign']['campaign_type'] == 'contest' && $item:
          $shareMessage = sprintf(
            '【%s】に「%s」を投稿しました。' . "\n",
            $campaign['Campaign']['title'],
            $item['Item']['title']
          );
          break;
        case 'shindan':
          $shareMessage = sprintf(
            '【%s】 %sさんの診断結果は%sでした。' . "\n",
            $campaign['Campaign']['title'],
            $entried['twitter_screen_name'],
            $result['Result']['title']
          );
          break;
        default:
          $shareMessage = sprintf(
            '【%s】に参加しました。' . "\n",
            $campaign['Campaign']['title']
          );
      }
      if($campaign['Campaign']['campaign_type'] == 'contest' && $item) {
        $shareMessage .= $this->Html->url([
          'action' => 'items',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['Campaign']['id'],
          'itemId' => $item['Item']['id'],
        ], true);
      } else {
        $shareMessage .= $this->Html->url([
          'action' => 'top',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['Campaign']['id'],
        ], true);
      }
      $afterInput = '<p id="js-share-left" class="share-left"><span></span> /140</p>';
      break;
  }
}
// ボタンのデフォルトラベルの設定
switch($campaign['Campaign']['campaign_type']) {
  case 'shindan':
    $btnLabel = '結果をシェアする';
    break;
  default:
    $btnLabel = 'キャンペーンをシェアする';
}

if($entried['entry_type'] != 'facebook') {
  echo $this->Form->input('shareMessage', [
    'type' => 'textarea',
    'label' => false,
    'class' => 'form-control',
    'div' => 'form-group',
    'id' => 'js-share-message-' . ($entried ? $entried['entry_type'] : ''),
    'value' => $shareMessage,
    'placeholder' => 'シェアするコメントを入力ください。',
    'afterInput' => $afterInput,
  ]);
}

echo '<div class="btn-shadow">' . $this->Html->btn(
  $this->Soraps->customText('share_btn_label', $btnLabel),
  '#',
  $btnOptions + ['size' => 'lg', 'block' => true],
  ['id' => 'js-btn-share']
) . '</div>';
// シェア用のjs
echo $this->element('campaigns/js-share');
