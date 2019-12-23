<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package    app.Controller
 * @link    http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
  public $uses = ['Account', 'AccountValue', 'AccountService'];
  public $components = [
    'Session' => ['className' => 'Ique.IqueSession'],
    'Paginator' => [
      'className' => 'Ique.IquePaginator',
      'maxLimit' => 200,
      'paramType' => 'querystring',
    ],
    'Auth' => ['className' => 'SorapsAuth'],
    'DebugKit.Toolbar' => ['forceEnable' => true, 'panels' => ['history' => false]],
  ];
  public $viewClass = 'Soraps';
  public $helpers = [
    'Session',
    'Html' => ['className' => 'Ique.IqueHtml'],
    'Form' => ['className' => 'Ique.IqueForm'],
    'Paginator' => ['className' => 'Ique.IquePaginator'],
    'S3' => ['className' => 'Ique.IqueS3'],
    'Soraps',
  ];
  public function beforeFilter() {
    // OEM情報の設定
    $this->AccountService->setOemInfo();
    // メンテナンスモードのチェック
    if(Configure::read('maintenance')) {
      // DebugKitの無効化
      $this->Toolbar->enabled = false;
      $this->Toolbar->initialize($this);
      $this->render('/Root/maintenance', 'campaigns');
      $this->response->send();
      exit;
    }
    // ライブラリファイルの読み込み
    if (is_array($this->libs)) {
      // FacebookSDKのsession_startよりも先に呼ぶ
      CakeSession::start();
      foreach ($this->libs as $alias => $libClass) {
        list($plugin, $libClass) = pluginSplit($libClass, true);
        App::uses($libClass, $plugin . 'Lib');
        $alias = is_string($alias) ? $alias : $libClass;
        $this->{$alias} = new $libClass();
        // ビューへのセット
        $this->set($alias, $this->{$alias});
        // モデルへのセット
        foreach($this->uses as $model) {
          $this->{$model}->{$alias} = $this->{$alias};
        }
      }
    }
    // ログインアカウントをセット
    $this->loginAccount = $this->Auth->user();
    $this->set('loginAccount', $this->loginAccount);
    // 管理者、支配者ログインの場合はデバッグモードにする
    if(in_array($this->loginAccount['role'], ['admin', 'master'])) {
      Configure::write('debug', 2);
    }
    // debugモードが0の場合、DebugKitを無効にする
    if(!Configure::read('debug')) {
      $this->Toolbar->enabled = false;
      $this->Toolbar->initialize($this);
    }
    // 管理画面の処理
    if($this->request->prefix == 'admin') {
      $this->layout = 'admin';
      // アカウント関連の処理
      if(array_key_exists('accountName', $this->request->params)) {
        // ログインアカウント以下のアカウントかチェック
        $this->parentAccounts = $this->AccountService->requireParents($this->request->params['accountName']);
        // 閲覧中のアカウント取得
        $this->currentAccount = array_pop($this->parentAccounts);
        // 親アカウントの取得
        $this->parentAccount = $this->parentAccounts
          ? array_pop($this->parentAccounts)
          : $this->currentAccount;
        // array_popで取得したアカウントを祖先アカウント一覧に再セット
        if($this->parentAccount != $this->currentAccount) {
          $this->parentAccounts[] = $this->parentAccount;
        } elseif($this->currentAccount['Account']['role'] == 'client') {
          $this->parentAccount = $this->Account->requireData('first', ['conditions' => [
            'id' => $this->currentAccount['Account']['parent_id'],
            'role' => 'agent',
          ]]);
        }
        $this->parentAccounts[] = $this->currentAccount;
      }
    }
    // キャンペーン表面の処理
    else {
      $this->Auth->allow();
      $this->layout = 'campaigns';
      if($this->name == 'CampaignView') {
        $this->currentAccount = $this->AccountService->requireByAccountName($this->request->params['accountName']);
        $this->parentAccount = $this->Account->requireData('first', ['conditions' => [
          'id' => $this->currentAccount['Account']['parent_id'],
          'role' => 'agent',
        ]]);
      }
    }
  }
/**
 * 変数のセット
 */
  public function beforeRender() {
    $this->set('parentAccounts', $this->parentAccounts);
    $this->set('currentAccount', $this->currentAccount);
    $this->set('parentAccount', $this->parentAccount);
    $this->set('campaign', $this->campaign);
  }
/**
 * 閲覧状態に応じた初期ページにリダイレクトさせる
 * $messageがある場合はエラーとしてalertをセットする
 */
  public function redirectIndex($message = null) {
    if($message) {
      $this->Session->setDangerFlash($message);
    }
    if(array_key_exists('admin', $this->request->params) && $this->request->params['admin']) {
      // 管理画面の場合
      $loginAccount = $this->Auth->user();
      $accountName = $loginAccount['account_name'];
      // ログインアカウントが取得できない場合はログインページへ
      if(!$loginAccount) {
        $this->redirect([
          'controller' => 'login',
          'action' => 'login',
        ]);
      }
      // ログインアカウントの権限に応じたページへ
      if($loginAccount['role'] == 'client') {
        $this->redirect([
          'controller' => 'dashboard',
          'action' => 'index',
          'accountName' => $accountName,
        ]);
      } else {
        $this->redirect([
          'controller' => 'accounts',
          'action' => 'index',
          'accountName' => $accountName == 'admin' ? 'master' : $accountName,
        ]);
      }
    } else {
      // 表面の場合
      $this->_mergeUses(['uses' => ['Campaign']]);
      $client = $this->Account->find('first', ['conditions' => [
        'account_name' => $this->request->params['accountName'],
        'role' => 'client',
        'plan !=' => 'stop',
      ]]);
      if(
        $client &&
        $this->Campaign->find('first', ['conditions' => [
          'id' => $this->request->param('id'),
          'account_id' => $client['Account']['id'],
        ]])
      ) {
        $this->redirect([
          'controller' => 'campaign_view',
          'action' => 'top',
          'accountName' => $this->request->params['accountName'],
          'id' => $this->request->params['id']
        ]);
      } else {
        $this->redirect([
          'controller' => 'root',
          'action' => 'index',
        ]);
      }
    }
  }
/**
 * クライアントアカウント以下に紐づくデータを閲覧することを保証する
 * ダッシュボードやオーディエンス情報など
 */
  protected function _requireClient() {
    if($this->currentAccount['Account']['role'] != 'client') {
      throw new SorapsException('RequireClientAccount');
    }
    // 契約停止中クライアントの情報は管理者しか見られない
    if($this->currentAccount['Account']['plan'] == 'stop') {
      $this->Auth->requireMasterLogin();
    }
    // OEM代理店の場合のドメインチェックとリダイレクト処理
    if($this->request->prefix == 'admin' && $this->Auth->isMasterLogin()) {
      return;
    }
    $agent = $this->parentAccount['Account'];
    if($agent['oem_flg']) {
      if($agent['oem_domain'] !== env('HTTP_HOST')) {
        $this->redirect('//' . $agent['oem_domain'] . env('REQUEST_URI'));
      }
    } else {
      if(Configure::read('defaultDomain') !== env('HTTP_HOST')) {
        $this->redirect('//' . Configure::read('defaultDomain') . env('REQUEST_URI'));
      }
    }
  }
/**
 * キャンペーン情報を準備する
 */
  protected function _requireCampaign() {
    $this->campaign = $this->Campaign->requireData('first', ['conditions' => [
      'Campaign.id' => $this->request->params['id'],
      'Campaign.account_id' => $this->currentAccount['Account']['id'],
    ]]);
    $this->campaign = $this->CampaignAddDataService->addStatus($this->campaign, $this->currentAccount);
  }
/**
 * CSVダウンロード
 */
  protected function _csvDownload($data, $filename = 'soraps.csv') {
    $this->response->type('csv');
    // ファイル名
    $filename = str_replace([' ', '　'], '_', $filename);
    $this->response->download($filename);
    // コンテンツボディ
    $this->response->body($data);
    // レスポンスを返す
    $this->response->send();
    $this->_stop();
  }
}
