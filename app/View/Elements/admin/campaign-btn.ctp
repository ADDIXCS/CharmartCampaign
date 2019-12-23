<?php
$_campaign = $campaign;
$campaign = $_campaign['Campaign'];
if(array_key_exists('Account', $_campaign)) {
  $accountName = $_campaign['Account']['account_name'];
} else {
  $accountName = $currentAccount['Account']['account_name'];
}

// ボタンのサイズ
if(in_array(
  $this->Soraps->pageId(),
  ['campaigns-index', 'dashboard-index', 'admin-campaigns']
)) {
  $size = 'sm';
} else {
  $size = null;
}

// ボタンの開始
echo '<div class="section">';
// 編集中かどうか
if($campaign['status'] != 'editing') {
  if($campaign['status'] != 'stopped') {
    // published_flg切り替え
    echo '<div class="btn-group">';
    $statuses = [
      ['label' => '非公開', 'btnOption' => ['icon' => 'stop']],
      ['label' => '公開中', 'btnOption' => ['type' => 'primary', 'icon' => 'play']],
      ['label' => 'テストモード', 'btnOption' => ['icon' => 'pause']],
    ];
    echo $this->Html->btn(
      $statuses[$campaign['published_flg']]['label'] . ' <span class="caret"></span>',
      '#',
      $statuses[$campaign['published_flg']]['btnOption'] + ['size' => $size],
      ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown']
    );
    echo '<ul class="dropdown-menu">';
    if($campaign['published_flg'] !== '1') {
      echo '<li>' . $this->Form->postLink(
        $this->Html->icon($statuses[1]['btnOption']['icon'], 'lg', true) . ' 公開する',
        [
          'controller' => 'campaigns',
          'action' => 'publish',
          'accountName' => $accountName,
          'id' => $campaign['id'],
        ],
        [
          'data' => ['published_flg' => 1, 'redirectAction' => $this->action],
          'escape' => false
        ],
        '公開しますか？'
      ) . '</li>';
    }
    if($campaign['published_flg'] !== '0') {
      echo '<li>' . $this->Form->postLink(
        $this->Html->icon($statuses[0]['btnOption']['icon'], 'lg', true) . ' 非公開にする',
        [
          'controller' => 'campaigns',
          'action' => 'publish',
          'accountName' => $accountName,
          'id' => $campaign['id'],
        ],
        [
          'data' => ['published_flg' => 0, 'redirectAction' => $this->action],
          'escape' => false
        ],
        '非公開にしますか？'
      ) . '</li>';
    }
    if($campaign['published_flg'] !== '2') {
      echo '<li>' . $this->Form->postLink(
        $this->Html->icon($statuses[2]['btnOption']['icon'], 'lg', true) . ' テストモードにする',
        [
          'controller' => 'campaigns',
          'action' => 'publish',
          'accountName' => $accountName,
          'id' => $campaign['id'],
        ],
        [
          'data' => ['published_flg' => 2, 'redirectAction' => $this->action],
          'escape' => false
        ],
        'テストモードにしますか？'
      ) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo ' ';
  }
  // インサイト
  echo $this->Html->btn(
    'インサイト',
    [
      'controller' => 'campaigns',
      'action' => 'view',
      'accountName' => $accountName,
      'id' => $campaign['id'],
    ],
    ['type' => 'success', 'size' => $size, 'icon' => 'bar-chart-o']
  );
  // 応募一覧
  echo $this->Html->btn(
    $campaign['campaign_type'] == 'lancers' ? '投票者一覧' : '応募一覧',
    [
      'controller' => 'entries',
      'action' => 'index',
      'accountName' => $accountName,
      'id' => $campaign['id'],
    ],
    ['type' => 'success', 'size' => $size, 'icon' => 'users']
  );
  // キャンペーンの種類に応じて表示
  switch($campaign['campaign_type']) {
    case 'shindan':
      echo $this->Html->btn(
        '診断結果',
        [
          'controller' => 'campaigns',
          'action' => 'results',
          'accountName' => $accountName,
          'id' => $campaign['id'],
        ],
        ['type' => 'success', 'size' => $size, 'icon' => 'inbox']
      );
      break;
    case 'contest':
    case 'vote':
    case 'lancers':
      echo $this->Html->btn(
        '投票数',
        [
          'controller' => 'campaigns',
          'action' => 'votes',
          'accountName' => $accountName,
          'id' => $campaign['id'],
        ],
        ['type' => 'success', 'size' => $size, 'icon' => 'inbox']
      );
      break;
  }
  // アンケートを使用している場合に表示
  if($_campaign['Enquete']) {
    echo $this->Html->btn(
      'アンケート結果',
      [
        'controller' => 'campaigns',
        'action' => 'enquetes',
        'accountName' => $accountName,
        'id' => $campaign['id'],
      ],
      ['type' => 'success', 'size' => $size, 'icon' => 'pencil-square-o']
    );
  }
  // csvダウンロード
  echo $this->Html->btn(
    'CSVダウンロード',
    [
      'controller' => 'entries',
      'action' => 'csv',
      'accountName' => $accountName,
      'id' => $campaign['id'],
    ],
    ['type' => 'success', 'size' => $size, 'icon' => 'download']
  );
  if($campaign['status'] != 'stopped') {
    // 編集
    echo '<div class="btn-group">';
    echo $this->Html->btn(
      '編集 <span class="caret"></span>',
      '#',
      ['type' => 'warning', 'size' => $size, 'icon' => 'pencil'],
      ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown']
    );
    echo '<ul class="dropdown-menu">';
    foreach(CampaignValue::getEditTypes($_campaign) as $key => $val) {
      echo '<li>' . $this->Html->link(
        $val,
        [
          'controller' => 'campaigns',
          'action' => 'edit',
          'accountName' => $accountName,
          'id' => $campaign['id'],
          'editType' => $key,
        ]
      ) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo ' ';
  }
  // 削除
  // echo $this->Html->btn(
  //   '削除',
  //   '#',
  //   ['type' => 'danger disabled', 'size' => $size, 'icon' => 'trash-o']
  // );
  // キャンペーン表示
  echo $this->Html->btn(
    'キャンペーン表示',
    [
      'controller' => 'campaign_view',
      'action' => 'top',
      'accountName' => $accountName,
      'id' => $campaign['id'],
      'admin' => false,
    ],
    ['type' => 'primary', 'size' => $size, 'icon' => 'external-link'],
    ['target' => '_blank']
  );
} else {
  if($campaign['status'] != 'stopped') {
    echo $this->Html->btn(
      '編集',
      [
        'controller' => 'campaigns',
        'action' => 'view',
        'accountName' => $accountName,
        'id' => $campaign['id'],
      ],
      ['type' => 'warning', 'size' => $size, 'icon' => 'pencil']
    );
  }
}
echo '</div>';
