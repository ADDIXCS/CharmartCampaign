<?php
class EntriesController extends AppController {
  public $uses = [
    'Campaign',
    'CampaignAddDataService',
    'Entry',
    'Audience',
    'Vote',
    'EntryEnqueteService',
  ];
  public $libs = ['Twitter' => 'Ique.IqueTwitter'];
  public function beforeFilter() {
    parent::beforeFilter();
    // クライアント以下のアクセスかチェック
    $this->_requireClient();
    // キャンペーンデータの取得
    if($this->action != 'admin_delete') {
      $this->Campaign->bindModel(['hasMany' => ['Enquete']], false);
      $this->Campaign->Enquete->bindModel(['hasMany' => ['EnqueteOption']], false);
      $this->Campaign->recursive = 2;
    }
    $this->_requireCampaign();
  }
/**
 * 応募一覧
 */
  public function admin_index($accountName, $id) {
    // アンケート結果のbind
    $this->Entry->bindModel(['hasMany' => ['EntryEnquete']], false);
    // スピードくじの場合のModelのbind
    if($this->campaign['Campaign']['campaign_type'] == 'lottery') {
      $this->Entry->bindModel(['belongsTo' => ['Gift']], false);
    }
    // entryデータの取得
    $this->Paginator->settings['limit'] = 50;
    if($this->campaign['Campaign']['campaign_type'] == 'lancers') {
      $this->Vote->bindModel(['belongsTo' => ['Audience']], false);
      $this->Vote->virtualFields['count'] = 'COUNT(*)';
      $this->Paginator->settings['order'] = 'Vote.created DESC';
      $this->Paginator->settings['group'] = 'Vote.audience_id';
      $this->Paginator->settings['fields'] = [
        'Audience.*',
        'Vote.count',
      ];
      $entries = $this->Paginator->paginate(
        'Vote',
        ['Vote.campaign_id' => $id]
      );
      $entries = $this->Audience->addTwitterProfileImageUrl($entries);
    } else {
      $this->Paginator->settings['order'] = 'Entry.created DESC';
      $entries = $this->Paginator->paginate(
        'Entry',
        ['Entry.campaign_id' => $id]
      );
      $entries = $this->Entry->addTwitterProfileImageUrl($entries);
    }
    $entries = $this->EntryEnqueteService->modifyEnqueteData($entries, $this->campaign['Enquete']);
    $this->set('entries', $entries);
  }
/**
 * 応募削除
 */
  public function admin_delete($accountName, $id, $entryId) {
    // 削除処理
    if($this->request->is('post')) {
      if($this->Entry->delete($entryId)) {
        $this->Session->setSuccessFlash('応募を削除しました');
        $this->redirect(['action' => 'index', 'accountName' => $accountName, 'id' => $id]);
      } else {
        $this->Session->setDangerFlash('応募削除に失敗しました');
        $this->redirect(['action' => 'index', 'accountName' => $accountName, 'id' => $id]);
      }
    } else {
      throw new SorapsException('BadRequestMethod');
    }
  }
/**
 * CSVダウンロード
 */
  public function admin_csv($accountName, $id) {
    // Entryデータの取得
    $this->Entry->bindModel(['hasMany' => ['EntryEnquete']], false);
    $entries = $this->Entry->findAllByCampaignId($id);
    $entries = $this->EntryEnqueteService->modifyEnqueteData($entries, $this->campaign['Enquete']);
    // CSVの生成、レスポンス
    $this->Entry->setEnquetes($this->campaign['Enquete']);
    $this->_csvDownload(
      $this->Entry->getCsvData($entries),
      date('Ymd') . '_' . $this->campaign['Campaign']['title'] . '_応募一覧.csv'
    );
  }
}

