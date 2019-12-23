<?php
App::uses('Entry', 'Model');
$campaign = $campaign['Campaign'];
$entryBtns = [];
$btnOptionsMain = [
  'type' => isset($type) ? $type : 'default',
  'size' => isset($size) ? $size : 'lg',
  'block' => isset($block) ? $block : true,
];
// キャンペーンが開催中かどうか
if(!$this->Soraps->isPreview() && !in_array($campaign['status'], ['published', 'test'])) {
  echo $this->Html->btn(
    'キャンペーン終了',
    '#',
    $btnOptionsMain,
    ['class' => 'disabled']
  );
  return;
}
foreach(Entry::$entryTypes as $entryType) {
  // 応募可能な場合
  if($campaign[$entryType . '_entry_flg']) {
    switch($campaign['campaign_type']) {
      case isset($vote):
      case 'lancers':
        $link = [
          'action' => 'vote',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['id'],
          'entryType' => $entryType,
          '?' => ['item' => $item['Item']['id']],
        ];
        break;
      case 'vote':
        $link = [
          'action' => 'entry',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['id'],
          'entryType' => $entryType,
          '?' => ['item' => $item['Item']['id']],
        ];
        break;
      case 'shindan':
        $link = [
          'action' => $entryType == 'email' ? 'entry' : 'questions',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['id'],
          'entryType' => $entryType,
        ];
        break;
      default:
        $link = [
          'action' => 'entry',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['id'],
          'entryType' => $entryType,
        ];
    }
    // 応募方法に応じた変数の設定
    switch($entryType) {
      case 'email':
        $label = 'E-mail';
        $icon = 'envelope-square';
        $btnIcon = 'envelope';
        $btnType = ['default' => 'default', 'primary' => 'primary'];
        break;
      case 'facebook':
        $label = 'Facebook';
        $icon = 'facebook-square';
        $btnIcon = 'facebook-square';
        $btnType = ['default' => 'default-facebook', 'primary' => 'facebook'];
        // アプリ認証リンクの設定
        if(!$this->Soraps->isPreview()) {
          $link = Router::url([
              'controller' => 'oauth',
              'action' => 'facebookLogin',
              '?' => ['redirect_uri' => Router::url($link)]
          ]);
        }
        break;
      case 'twitter':
        $label = 'Twitter';
        $icon = 'twitter-square';
        $btnIcon = 'twitter';
        $btnType = ['default' => 'default-twitter', 'primary' => 'twitter'];
        // アプリ認証リンクの設定
        if(!$this->Soraps->isPreview() && !$Twitter->getUser()) {
          $link = Router::url([
            'controller' => 'oauth',
            'action' => 'twitterLogin',
            '?' => ['redirect_uri' => Router::url($link)]
          ]);
        }
        break;
    }
    $entryBtns[] = [
      'link' => $link,
      'label' => $label,
      'icon' => $icon,
      'btnIcon' => $btnIcon,
      'btnType' => $btnType,
    ];
  }
}
// 応募方法が一つの場合、メインボタンのデザインを変更
if(count($entryBtns) === 1) {
  $btnOptionsMain['icon'] = $entryBtns[0]['btnIcon'];
  $btnOptionsMain['type'] = $entryBtns[0]['btnType'][$btnOptionsMain['type']];
}
// ボタンのアクションを決定
$actionType = 'entry';
if($campaign['campaign_type'] == 'vote') {
  if(!empty($item['Voted'])) {
    $actionType = 'entried';
  } elseif(!empty($entried)) {
    $actionType = 'vote';
  }
} elseif($campaign['campaign_type'] == 'lancers' || isset($vote)) {
  if(!empty($item['Voted'])) {
    $actionType = 'entried';
  }
} elseif(!empty($entried)) {
  $actionType = 'entried';
}
switch($actionType) {
  // 応募済み、投票済みの場合
  case 'entried':
    if($campaign['campaign_type'] == 'coupon' || $campaign['campaign_type'] == 'lottery') {
      $btnText = $campaign['campaign_type'] == 'coupon' ? '取得済みクーポンを見る' : '抽選済み結果を見る';
      echo $this->Html->btn(
        $btnText,
        [
          'action' => 'finish',
          'accountName' => $currentAccount['Account']['account_name'],
          'id' => $campaign['id'],
        ],
        $btnOptionsMain
      );
    } else {
      $btnText =
        in_array($campaign['campaign_type'], ['vote', 'lancers']) || isset($vote)
          ? '投票済み'
          : '応募済み';
      echo $this->Html->btn(
        $btnText,
        '#',
        $btnOptionsMain,
        ['class' => 'disabled']
      );
    }
    break;
  // ajaxでの投票ボタン
  case 'vote';
    $entryBtnDefaultText = CampaignValue::${$campaign['campaign_type']}['entryBtnDefaultText'];
    echo $this->Html->btn(
      '投票する',
      '#',
      $btnOptionsMain,
      $this->Soraps->isPreview()
        ? []
        : ['class' => 'js-btn-vote', 'id' => 'item-' . $item['Item']['id']]
    );
    break;
  // デフォルトの応募ボタン
  case 'entry';
    $entryBtnDefaultText = CampaignValue::${$campaign['campaign_type']}['entryBtnDefaultText'];
    if(count($entryBtns) === 1) {
      echo $this->Html->btn(
        isset($vote)
          ? '投票する'
          : $this->Soraps->customText('entry_btn_label', $entryBtnDefaultText),
        $this->Soraps->isPreview() ? '#' : $entryBtns[0]['link'],
        $btnOptionsMain,
        ['class' => 'js-btn-entry']
      );
    } else {
      $modalId = in_array($campaign['campaign_type'], ['vote', 'lancers']) || isset($vote)
        ? '-' . $item['Item']['id']
        : '';
      echo $this->Html->btn(
        isset($vote)
          ? '投票する'
          : $this->Soraps->customText('entry_btn_label', $entryBtnDefaultText),
        '#',
        $btnOptionsMain,
        ['data-toggle' => 'modal', 'data-target' => '#js-modal-btns-entry' . $modalId]
      );
      // 応募方法のモーダル
      if(!$this->Soraps->isPreview()) {
        $this->start('modal');
        echo '<div class="modal modal-btns-entry" id="js-modal-btns-entry' . $modalId . '">';
        echo '<div class="modal-dialog"><div class="modal-content">';
        echo '<div class="modal-body">';
        echo '<button class="close" data-dismiss="modal">&times;</button>';
        echo '<p class="text-center">どのアカウントで参加しますか？</p>';
        echo '<div class="row">';
        foreach($entryBtns as $entryBtn) {
          echo '<div class="col-xs-' . (12 / count($entryBtns)) . '">';
          echo $this->Html->link(
            $this->Html->icon($entryBtn['icon']),
            $entryBtn['link'],
            ['class' => 'js-btn-entry', 'escape' => false]
          );
          echo '<p>' . $entryBtn['label'] . '</p>';
          echo '</div>';
        }
        echo '</div>'; // row
        echo '</div>'; // modal-body
        echo '</div></div>'; // modal-content, modal-dialog
        echo '</div>'; // modal
        $this->end();
      }
    }
    break;
}
