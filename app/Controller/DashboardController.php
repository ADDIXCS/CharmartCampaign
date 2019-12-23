<?php
class DashboardController extends AppController {
  public $uses = [
    'Campaign',
    'CampaignValue',
    'CampaignAddDataService'
  ];
  public function beforeFilter() {
    parent::beforeFilter();
    // クライアント以下のアクセスかチェック
    $this->_requireClient();
  }
/**
 * ダッシュボード
 */
  public function admin_index($accountName) {
    // キャンペーン一覧取得
    $this->Paginator->settings = am($this->Paginator->settings, [
      'conditions' => [
        'account_id' => $this->currentAccount['Account']['id'],
        'campaign_type' => CampaignValue::getAvailableTypes($this->currentAccount),
        'published_flg' => 1,
        'start_date <=' => date('Y-m-d H:i:s'),
        'OR' => [
          'end_date >=' => date('Y-m-d H:i:s'),
          'end_date_flg' => 1,
        ],
      ],
      'order' => ['start_date' => 'desc'],
    ]);
    $this->Campaign->bindModel(['hasMany' => ['Entry', 'Enquete']]);
    $campaigns = $this->Paginator->paginate('Campaign');
    $campaigns = $this->CampaignAddDataService->addStatus($campaigns, $this->currentAccount);
    $campaigns = $this->CampaignAddDataService->addListData($campaigns);
    // 変数セット
    $this->set('campaigns', $campaigns);
  }
}

