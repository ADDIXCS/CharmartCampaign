<?php
class AdminController extends AppController {
/**
 * ログイン後にアクセスされたり、トップへ戻るリンク先として使われる
 */
  public function admin_index() {
    $this->redirectIndex();
  }
/**
 * 開催中のキャンペーン一覧
 */
  public function admin_campaigns() {
    $this->_initMasterPage();
    // キャンペーン一覧取得
    $this->_mergeUses([
      'uses' => ['Campaign', 'CampaignAddDataService']
    ]);
    $this->Paginator->settings = am($this->Paginator->settings, [
      'conditions' => [
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
    $this->Campaign->bindModel(['belongsTo' => ['Account']]);
    $campaigns = $this->Paginator->paginate('Campaign');
    $campaigns = $this->CampaignAddDataService->addStatus($campaigns);
    $campaigns = $this->CampaignAddDataService->addListData($campaigns);
    // 変数セット
    $this->set('campaigns', $campaigns);
  }
/**
 * 更新履歴
 */
  public function admin_changelog() {
    $this->_initMasterPage();
  }
/**
 * 管理者権限以上でしかアクセスできないページの初期処理
 */
  protected function _initMasterPage() {
    // アカウント制限
    $this->Auth->requireMasterLogin();
    // パンくず用に管理者アカウントをセットする
    $this->_mergeUses(['uses' => ['AccountService']]);
    $this->parentAccounts = [$this->AccountService->requireByAccountName('master')];
  }
}

