<?php
class AudiencesController extends AppController {
  public $uses = ['Audience', 'AudienceList'];
  public $libs = ['Twitter' => 'Ique.IqueTwitter'];
  public $components = ['Search.Prg' => [
    'commonProcess' => [
      'keepPassed' => false,
      'allowedParams' => ['accountName'],
      'excludedParams' => ['sort', 'direction', 'limit'],
      'paramType' => 'querystring',
      'filterEmpty' => true,
    ]
  ]];
  public function beforeFilter() {
    parent::beforeFilter();
    // クライアント以下のアクセスかチェック
    $this->_requireClient();
    // Paginatorの設定を変更
    $this->Paginator->settings['limit'] = 50;
    $this->Paginator->settings['order'] = 'modified DESC';
  }
/**
 * オーディエンス一覧
 */
  public function admin_index($accountName) {
    $this->Prg->commonProcess();
    $this->_setListVars($this->Audience->parseCriteria($this->Prg->parsedParams()));
  }
/**
 * リスト表示
 */
  public function admin_lists($accountName, $listId) {
    // リスト
    $list = $this->AudienceList->requireData('first', ['conditions' => [
      'id' => $listId,
      'account_id' => $this->currentAccount['Account']['id'],
    ]]);
    $this->set('list', $list);
    // 検索条件の抽出
    list(, $querystr) = explode('?', $list['AudienceList']['criteria']);
    parse_str($querystr, $queryArgs);
    // 変数のセット
    $this->_setListVars($this->Audience->parseCriteria($queryArgs));
    $this->request->data['Audience'] = $queryArgs;
    $this->request->data['AudienceList'] = $list['AudienceList'];
  }
/**
 * リストの追加
 */
  public function admin_add_list($accountName) {
    // 保存処理
    if($this->request->is('post')) {
      $this->AudienceList->create();
      if($this->AudienceList->save($this->_createAudienceListData())) {
        $this->Session->setSuccessFlash('リストを追加しました');
        $this->redirect([
          'action' => 'lists',
          'accountName' => $accountName,
          'listId' => $this->AudienceList->getLastInsertId(),
        ]);
      } else {
        $this->Session->setDangerFlash('リスト追加に失敗しました');
        $this->redirect([
          'action' => 'index',
          'accountName' => $accountName,
          '?' => Hash::filter($parsedParams),
        ]);
      }
    }
  }
/**
 * リストの編集
 */
  public function admin_edit_list($accountName, $listId) {
    // 保存処理
    if($this->request->is('post')) {
      // リストの存在チェック
      $list = $this->AudienceList->requireData('first', ['conditions' => [
        'id' => $listId,
        'account_id' => $this->currentAccount['Account']['id'],
      ]]);
      // 保存
      $this->AudienceList->id = $listId;
      if($this->AudienceList->save($this->_createAudienceListData())) {
        $this->Session->setSuccessFlash('リストを編集しました');
      } else {
        $this->Session->setDangerFlash('リスト追加に失敗しました');
      }
      $this->redirect([
        'action' => 'lists',
        'accountName' => $accountName,
        'listId' => $listId,
      ]);
    }
  }
/**
 * オーディエンス詳細
 */
  public function admin_view($accountName, $audienceId) {
    $this->_mergeUses(['uses' => ['Entry']]);
    $this->Audience->bindModel(['hasMany' => ['Entry' => [
      'order' => 'created DESC',
    ]]], false);
    $this->Entry->bindModel(['belongsTo' => ['Campaign']], false);
    $this->Audience->recursive = 2;
    $audience = $this->Audience->requireData('first', ['conditions' => [
      'id' => $audienceId,
      'account_id' => $this->currentAccount['Account']['id'],
    ]]);
    $this->set('audience', $audience);
  }
/**
 * CSVダウンロード
 */
  public function admin_csv($accountName) {
    $audiences = $this->Audience->findAllByAccountId($this->currentAccount['Account']['id']);
    $this->_csvDownload(
      $this->Audience->getCsvData($audiences),
      date('Ymd') . '_' . $this->currentAccount['Account']['screen_name'] . '_オーディエンス一覧.csv'
    );
  }
/**
 * オーディエンス一覧、リスト表示
 */
  protected function _setListVars($criteria) {
    // オーディエンス一覧
    $audiences = $this->Paginator->paginate(
      'Audience',
      am(
        $criteria,
        ['account_id' => $this->currentAccount['Account']['id']]
      )
    );
    $audiences = $this->Audience->addTwitterProfileImageUrl($audiences);
    $this->set('audiences', $audiences);
    // リスト一覧
    $audienceLists = $this->AudienceList->findAllByAccountId(
      $this->currentAccount['Account']['id']
    );
    // キャンペーン
    $this->_mergeUses(['uses' => ['Campaign']]);
    $campaigns = $this->Campaign->find('list', ['conditions' => [
      'account_id' => $this->currentAccount['Account']['id'],
    ]]);
    $this->set(compact('audiences', 'audienceLists', 'campaigns'));
  }
/**
 * オーディエンス一覧、リスト表示
 */
  protected function _createAudienceListData() {
    // presetFormの課程でAudienceListが失われるので退避
    $_requestData = $this->request->data;
    // 検索条件の抽出
    $this->request->query = $this->request->data['Audience'];
    $this->Prg->presetForm(['model' => 'Audience', 'paramType' => 'querystring']);
    $parsedParams = $this->Prg->parsedParams();
    // データを返す
    return ['AudienceList' => [
      'account_id' => $this->currentAccount['Account']['id'],
      'name' => $_requestData['AudienceList']['name'],
      'criteria' => Router::queryString(Hash::filter($parsedParams)),
    ]];
  }
}

