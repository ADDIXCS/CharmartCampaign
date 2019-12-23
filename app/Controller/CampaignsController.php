<?php
class CampaignsController extends AppController {
  public $uses = [
    'Campaign',
    'CampaignValue',
    'CampaignEditService',
    'CampaignAddDataService'
  ];
  public $libs = ['Facebook' => 'Ique.IqueFacebook', 'Twitter' => 'Ique.IqueTwitter'];
  public function beforeFilter() {
    parent::beforeFilter();
    // クライアント以下のアクセスかチェック
    $this->_requireClient();
    // モデルのbind
    $this->_bindModel();
    // ボタン表示用にアンケートデータのbind
    if(!in_array($this->action, ['admin_add', 'admin_edit', 'admin_publish'])) {
      $this->Campaign->bindModel(['hasMany' => ['Enquete']], false);
    }
    // キャンペーンデータの取得
    if(!in_array($this->action, ['admin_index', 'admin_add'])) {
      $this->_requireCampaign();
    }
  }
/**
 * キャンペーン一覧
 */
  public function admin_index($accountName) {
    // 支配者ログインの場合、削除済みのキャンペーンも表示する
    if($this->loginAccount['role'] == 'admin') {
      $this->Campaign->softDelete(false);
    }
    // キャンペーン一覧取得
    $this->Paginator->settings = am($this->Paginator->settings, [
      'conditions' => ['account_id' => $this->currentAccount['Account']['id']],
      'order' => ['start_date' => 'desc'],
    ]);
    $campaigns = $this->Paginator->paginate('Campaign');
    $campaigns = $this->CampaignAddDataService->addStatus($campaigns, $this->currentAccount);
    $campaigns = $this->CampaignAddDataService->addListData($campaigns);
    // 変数セット
    $this->set('campaigns', $campaigns);
  }
/**
 * キャンペーントップ（インサイト）
 */
  public function admin_view($accountName, $id) {
    // 編集が完了していない場合編集ページへリダイレクト
    if(!$this->CampaignEditService->isEditFinished($this->campaign)) {
      $this->redirect([
        'action' => 'edit',
        'accountName' => $accountName,
        'id' => $id,
        'editType' => CampaignValue::getEditType($this->campaign, $this->campaign['Campaign']['edit_step'] + 1),
      ]);
    }
    if($this->campaign['Campaign']['campaign_type'] == 'lancers') {
      $this->_mergeUses(['uses' => ['Vote']]);
      $this->Vote->bindModel(['belongsTo' => ['Audience']]);
      $this->Vote->virtualFields['count'] = 'COUNT(*)';
      $this->Vote->Audience->bindModel(['hasMany' => ['Entry' => [
        'className' => 'Vote',
        'conditions' => ['campaign_id' => $id],
      ]]]);
      $this->Vote->recursive = 2;
      $votes = $this->Vote->find('all', [
        'conditions' => ['campaign_id' => $id],
        'group' => ['Vote.audience_id'],
        'fields' => ['Audience.*', 'Vote.audience_id', 'Vote.count'],
      ]);
      $entrants = [];
      foreach($votes as $vote) {
        $entrants[] = $vote['Audience']['Audience'];
      }
      $this->campaign['Entrant'] = $entrants;
    }
    // インサイト用データを集計
    $this->campaign = $this->CampaignAddDataService->addInsightData($this->campaign);
  }
/**
 * キャンペーン作成
 */
  public function admin_add($accountName, $campaignType) {
    // キャンペーンの種類のチェック
    if(!in_array($campaignType, CampaignValue::getAvailableTypes($this->currentAccount))) {
      throw new SorapsException('BadCampaignType');
    }
    // 保存処理
    if($this->request->is('post')) {
      $this->request->data['Campaign']['account_id'] = $this->currentAccount['Account']['id'];
      $this->request->data['Campaign']['campaign_type'] = $campaignType;
      $this->request->data['Campaign']['published_flg'] = 2;
      $this->Campaign->create();
      if($this->Campaign->save($this->request->data)) {
        $this->Session->setSuccessFlash('キャンペーンを作成しました');
        $this->redirect([
          'action' => 'edit',
          'accountName' => $accountName,
          'id' => $this->Campaign->getLastInsertID(),
          'editType' => CampaignValue::getEditType($this->request->data, 1),
        ]);
      } else {
        $this->Session->setDangerFlash('キャンペーン作成に失敗しました');
      }
    }
  }
/**
 * キャンペーン編集
 */
  public function admin_edit($accountName, $id, $editType, $objectId = null) {
    // 契約で利用不可のキャンペーンの場合キャンペーン一覧に移動
    // キャンペーントップだと、編集中の場合リダイレクトループになる
    if($this->campaign['Campaign']['status'] == 'stopped') {
      $this->redirect([
        'action' => 'index',
        'accountName' => $accountName,
      ]);
    }
    // 編集可能なステップでない場合リダイレクト
    if(!$this->CampaignEditService->isAccessibleEditType($this->campaign, $editType)) {
      $this->redirect([
        'action' => 'edit',
        'accountName' => $accountName,
        'id' => $id,
        'editType' => CampaignValue::getEditType($this->campaign, $this->campaign['Campaign']['edit_step'] + 1),
      ]);
    }
    $editStep = CampaignValue::getEditStep($this->campaign, $editType);
    // putリクエストの処理
    if($this->request->is('put')) {
      // キャンペーンidのセット
      $this->request->data['Campaign']['id'] = $this->campaign['Campaign']['id'];
      // 編集ステップのセット
      if($editStep > $this->campaign['Campaign']['edit_step']) {
        $this->request->data['Campaign']['edit_step'] = $editStep;
        // isEditFinishedに渡すとき用
        $this->campaign['Campaign']['edit_step'] = $editStep;
      }
      // アイテム編集の場合
      if($objectId) {
        $this->request->data['Item']['id'] = $objectId;
      }
      if($this->CampaignEditService->editCampaign($this->request->data, $editType)) {
        if($editType != 'items') {
          $this->Session->setSuccessFlash('キャンペーンを編集しました');
          // 全ての編集が完了しているかどうかによってステップの出し分け
          if($this->CampaignEditService->isEditFinished($this->campaign)) {
            $this->redirect([
              'action' => 'view',
              'accountName' => $accountName,
              'id' => $id,
            ]);
          } else {
            $this->redirect([
              'action' => 'edit',
              'accountName' => $accountName,
              'id' => $id,
              'editType' => CampaignValue::getEditType($this->campaign, $editStep + 1),
            ]);
          }
        } else {
          if($objectId) {
            $this->Session->setSuccessFlash('アイテムを編集しました');
          } else {
            $this->Session->setSuccessFlash('アイテムを追加しました');
          }
          $this->redirect([
            'action' => 'edit',
            'accountName' => $accountName,
            'id' => $id,
            'editType' => 'items',
          ]);
        }
      } else {
        // putリクエストにするためにidをセットする
        $this->request->data['Campaign']['id'] = $this->campaign['Campaign']['id'];
        // プレビュー用の画像ファイルをセット
        if($editType == 'items') {
          $this->request->data['Item']['image'] = $this->campaign['Item']['image'];
        }
        $this->Session->setDangerFlash('キャンペーン編集に失敗しました');
      }
    } else {
      $this->request->data = $this->campaign;
    }
    // キャンペーンの種類に応じたデータの取得
    switch($this->campaign['Campaign']['campaign_type']) {
      case 'contest':
        switch($editType) {
          // ピックアップアイテム
          case 'page-top':
          case 'detail':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->set('items', $this->Item->find('all', [
              'conditions' => ['campaign_id' => $id, 'unpublished_flg' => false],
              'limit' => 4,
              'order' => 'rand()',
            ]));
            break;
          // アイテム一覧
          case 'page-items':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->Item->bindModel(['belongsTo' => ['Entry']]);
            $this->Paginator->settings['limit'] = 8;
            $this->Paginator->settings['order'] = 'Item.created desc';
            $this->set('items', $this->Paginator->paginate(
              'Item',
              ['Item.campaign_id' => $id, 'Item.unpublished_flg' => false]
            ));
            break;
          // プレビュー用のアイテム
          case 'page-finish':
          case 'page-vote':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->set('item', $this->Item->find('first', [
              'conditions' => ['campaign_id' => $id, 'unpublished_flg' => false],
              'order' => 'created DESC',
            ]));
            break;
        }
        break;
      case 'vote':
        switch($editType) {
          // ピックアップアイテム
          case 'page-top':
          case 'detail':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->set('items', $this->Item->find('all', [
              'conditions' => ['campaign_id' => $id],
              'limit' => 4,
              'order' => 'rand()',
            ]));
            break;
          // 投票対象のアイテム
          case 'page-entry':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->set('item', $this->Item->findByCampaignId($id));
            break;
          // アイテム一覧
          case 'items':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->set('items', $this->Item->findAllByCampaignId($id));
            break;
          case 'page-items':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->Paginator->settings['limit'] = 8;
            $this->set('items', $this->Paginator->paginate(
              'Item',
              ['campaign_id' => $id]
            ));
            break;
        }
        break;
      case 'lottery':
        switch($editType) {
          // 景品名、当選数のデフォルト用
          case 'page-top':
            $this->_mergeUses(['uses' => ['Gift']]);
            $this->set('gifts', $this->Gift->findAllByCampaignId($id));
            break;
          // プレビュー用の景品
          case 'page-finish':
            $this->_mergeUses(['uses' => ['Gift']]);
            $this->set('gift', $this->Gift->findByCampaignId($id));
            break;
        }
        break;
      case 'lancers':
        switch($editType) {
          // ピックアップアイテム
          case 'page-top':
          case 'detail':
            $this->_mergeUses(['uses' => ['Item']]);
            $this->Item->setDataSource('lancers');
            $this->set('items', $this->Item->find('all', [
              'conditions' => ['lancers_url' => $this->campaign['Campaign']['lancers_url']],
              'limit' => 4,
              'order' => 'rand()',
            ]));
            break;
        }
        break;
    }
  }
/**
 * 公開フラグ変更
 */
  public function admin_publish($accountName, $id) {
    if($this->request->is('post')) {
      $data = ['published_flg' => (int) $this->request->data['published_flg']];
      $this->Campaign->id = $this->campaign['Campaign']['id'];
      if($this->Campaign->save($data)) {
        switch($this->request->data['published_flg']) {
          case 0:
            $this->Session->setSuccessFlash('キャンペーンを非公開にしました');
            break;
          case 1:
            $this->Session->setSuccessFlash('キャンペーンを公開しました');
            break;
          case 2:
            $this->Session->setSuccessFlash('キャンペーンをテストモードにしました');
            break;
        }
        // Facebookのogタグの再読み込み
        $this->Facebook->api('/', 'POST', [
          'id' => Router::url([
            'controller' => 'campaign_view',
            'action' => 'top',
            'accountName' => $accountName,
            'id' => $this->campaign['Campaign']['id'],
            'admin' => false,
          ], true),
          'scrape' => true,
        ]);
      } else {
        $this->Session->setDangerFlash('キャンペーン編集に失敗しました');
      }
      if($this->request->data['redirectAction'] == 'admin_index') {
        $this->redirect(['action' => 'index', 'accountName' => $accountName]);
      } else {
        $this->redirect(['action' => 'view', 'accountName' => $accountName, 'id' => $id]);
      }
    } else {
      throw new SorapsException('BadRequestMethod');
    }
  }
/**
 * アイテムの公開フラグ変更
 */
  public function admin_publish_item($accountName, $id, $itemId) {
    if($this->request->is('post')) {
      $this->_mergeUses(['uses' => ['Item']]);
      $item = $this->Item->requireData('first', ['conditions' => [
        'id' => $itemId,
        'campaign_id' => $id,
      ]]);
      $data = ['unpublished_flg' => !$item['Item']['unpublished_flg']];
      $this->Item->id = $item['Item']['id'];
      if($this->Item->save($data)) {
        if($data['unpublished_flg']) {
          $this->Session->setSuccessFlash('アイテムを非公開にしました');
        } else {
          $this->Session->setSuccessFlash('アイテムを公開しました');
        }
      } else {
        $this->Session->setDangerFlash('アイテム編集に失敗しました');
      }
      $this->redirect([
        'action' => 'votes',
        'accountName' => $accountName,
        'id' => $id,
      ]);
    } else {
      throw new SorapsException('BadRequestMethod');
    }
  }
/**
 * アイテム削除
 */
  public function admin_delete_item($accountName, $id, $itemId) {
    if($this->request->is('post')) {
      $this->_mergeUses(['uses' => ['Item']]);
      $item = $this->Item->requireData('first', ['conditions' => [
        'id' => $itemId,
        'campaign_id' => $id,
      ]]);
      $this->Item->softDelete(false);
      // 投票数が0の場合しか削除できない
      if(!$item['Item']['vote_count'] && $this->Item->delete($itemId)) {
        $this->Session->setSuccessFlash('アイテムを削除しました');
      } else {
        $this->Session->setDangerFlash('アイテム削除に失敗しました');
      }
      $this->redirect([
        'action' => 'edit',
        'accountName' => $accountName,
        'id' => $id,
        'editType' => 'items',
      ]);
    } else {
      throw new SorapsException('BadRequestMethod');
    }
  }
/**
 * 診断結果
 */
  public function admin_results($accountName, $id) {
  }
/**
 * 投票数
 */
  public function admin_votes($accountName, $id) {
    if ($itemId = $this->request->query('csv')) {
      $this->_mergeUses(['uses' => ['Vote']]);
      $votes = $this->Vote->findAllByItemId($itemId);
      if ( ! $votes) {
        exit();
      }
      $_handle = fopen('php://temp', 'r+b');
      $headers = array_keys($votes[0]['Vote']);
      fputcsv($_handle, $headers);
      foreach ($votes as $vote) {
        fputcsv($_handle, am($vote['Vote']));
      }
      rewind($_handle);
      $this->_csvDownload(
        mb_convert_encoding(stream_get_contents($_handle), 'SJIS-win'),
        date('Ymd') . '_' . $itemId . '_投票一覧.csv'
      );
      exit();
    }
    $this->_mergeUses(['uses' => ['Item']]);
    $this->Paginator->settings['order'] = 'Item.vote_count DESC';
    $this->set('items', $this->Paginator->paginate(
      'Item',
      ['Item.campaign_id' => $id]
    ));
  }
/**
 * アンケート結果
 */
  public function admin_enquetes($accountName, $id) {
  }
/**
 * キャンペーンを取得する際のモデルのbind
 */
  protected function _bindModel() {
    // actionに応じてモデルをbind
    switch($this->action) {
      case 'admin_index':
        $this->Campaign->bindModel(['hasMany' => ['Entry']]);
        break;
      case 'admin_view':
        $this->Campaign->bindModel(['hasMany' => ['Entrant']]);
        $this->Campaign->Entrant->bindModel(['hasMany' => ['Entry']]);
        $this->Campaign->recursive = 2;
        break;
      case 'admin_results':
        $this->_mergeUses(['uses' => ['Question']]);
        $this->Campaign->bindModel(['hasMany' => [
          'Question',
          'Result' => [
            'order' => 'entry_count DESC',
          ],
        ]], false);
        $this->Question->bindModel(['hasMany' => ['Answer' => [
          'order' => 'entry_count DESC',
        ]]], false);
        $this->Campaign->recursive = 2;
        break;
      case 'admin_enquetes':
        $this->Campaign->bindModel(['hasMany' => ['Enquete']], false);
        $this->Campaign->Enquete->bindModel(['hasMany' => [
          'EnqueteOption' => ['order' => 'entry_count DESC'],
          'EntryEnquete' => ['order' => 'created DESC', 'limit' => 10],
        ]], false);
        $this->Campaign->recursive = 2;
        break;
    }
    // キャンペーン編集の種類に応じてモデルをbind
    switch($this->request->param('editType')) {
      case 'enquetes':
        $this->Campaign->bindModel(['hasMany' => ['Enquete']], false);
        $this->Campaign->Enquete->bindModel(['hasMany' => ['EnqueteOption']], false);
        $this->Campaign->recursive = 2;
        break;
      case 'items':
        if($objectId = $this->request->param('objectId')) {
          $this->Campaign->bindModel(['hasOne' => ['Item' => [
            'conditions' => ['Item.id' => $objectId],
          ]]], false);
        } elseif($this->request->is('put')) {
          $this->Campaign->bindModel(['hasOne' => ['Item']], false);
        }
        break;
      case 'gifts':
        $this->Campaign->bindModel(['hasMany' => ['Gift']], false);
        break;
      case 'questions':
        $this->Campaign->bindModel(['hasMany' => ['Question']], false);
        $this->Campaign->Question->bindModel(['hasMany' => ['Answer']], false);
        $this->Campaign->recursive = 2;
        break;
      case 'results':
        $this->_mergeUses(['uses' => ['Question', 'Answer', 'Result']]);
        $this->Campaign->bindModel(['hasMany' => ['Question', 'Result']], false);
        $this->Question->bindModel(['hasMany' => ['Answer']], false);
        $this->Campaign->recursive = 2;
        break;
    }
  }
}
