<?php
$this->Html->resetCrumbs();
if(empty($campaignsEdit)) {
  // アカウント
  if(isset($parentAccounts)) {
    $i = 1;
    foreach($parentAccounts as $_parentAccount) {
      $parentAccount = $_parentAccount['Account'];
      if($parentAccount['role'] != 'admin') {
        $this->Html->addCrumb(
          h($parentAccount['screen_name']),
          [
            'controller' => $parentAccount['role'] != 'client' ? 'accounts' : 'dashboard',
            'action' => 'index',
            'accountName' => $parentAccount['account_name']
          ],
          $i == 1 ? ['icon' => 'home'] : ['escape' => false]
        );
        $i++;
      }
    }
  }
  // オーディエンス
  if($this->viewPath == 'Audiences' && $this->action != 'admin_index') {
    $this->Html->addCrumb(
      'オーディエンス一覧',
      [
        'controller' => 'audiences',
        'action' => 'index',
        'accountName' => $currentAccount['Account']['account_name'],
      ],
      ['escape' => false]
    );
  }
  // キャンペーン
  if(isset($campaign) && $this->action != 'admin_view') {
    $this->Html->addCrumb(
      h($campaign['Campaign']['title']),
      [
        'controller' => 'campaigns',
        'action' => 'view',
        'accountName' => $currentAccount['Account']['account_name'],
        'id' => $campaign['Campaign']['id'],
      ],
      ['escape' => false]
    );
  }
  // タイトル
  $this->Html->addCrumb(h($title_for_layout));
  echo $this->Html->getCrumbList();
} else {
  $currentEditType = $this->request->param('editType');
  foreach(CampaignValue::getEditTypes($campaign) as $key => $val) {
    $this->Html->addCrumb(
      $val,
      [
        'action' => 'edit',
        'accountName' => $currentAccount['Account']['account_name'],
        'id' => $campaign['Campaign']['id'],
        'editType' => $key,
      ],
      ['class' => $currentEditType == $key ? 'current' : false]
    );
  }
  $this->Html->addCrumb('');
  echo $this->Html->getCrumbList(['class' => 'breadcrumb breadcrumb-arrow']);
}
