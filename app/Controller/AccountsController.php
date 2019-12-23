<?php
class AccountsController extends AppController {
  public $libs = ['Facebook' => 'Ique.IqueFacebook', 'Twitter' => 'Ique.IqueTwitter'];
/**
 * 子アカウント一覧
 */
  public function admin_index($accountName) {
    // 支配者とクライアントの子アカウント一覧にはアクセスできない
    $this->_requireHasChildAccount();
    // 支配者ログインの場合、削除済みのアカウントも表示する
    if($this->loginAccount['role'] == 'admin') {
      $this->Account->softDelete(false);
    }
    // 子アカウント取得
    $accounts = $this->Paginator->paginate(
      'Account',
      ['parent_id' => $this->currentAccount['Account']['id']]
    );
    // 変数セット
    $this->set('accounts', $accounts);
  }
/**
 * 子アカウント追加
 */
  public function admin_add($accountName) {
    // 支配者とクライアントの子アカウントは作れない
    $this->_requireHasChildAccount();
    // 権限チェック（管理者はクライアントしか作成できない）
    $this->_requireEditableRole();
    // 保存処理
    if($this->request->is('post')) {
      $this->request->data['Account']['parent_id'] = $this->currentAccount['Account']['id'];
      $this->request->data['Account']['role'] = $this->currentAccount['Account']['child_role'];
      $this->Account->create();
      if($this->Account->save($this->request->data)) {
        $this->Session->setSuccessFlash('アカウントを追加しました');
        $this->redirect(['action' => 'index', 'accountName' => $accountName]);
      } else {
        $this->Session->setDangerFlash('アカウント追加に失敗しました');
      }
    }
    // ビューの指定
    $this->render('admin_form');
  }
/**
 * アカウント編集
 */
  public function admin_edit($accountName) {
    // 権限チェック（管理者はクライアントしか編集できない）
    $this->_requireEditableRole();
    // 保存処理
    if($this->request->is('put')) {
      // パスワードが未入力の場合はバリデーションしないためにunsetする
      if(!$this->request->data['Account']['password']) {
        unset($this->request->data['Account']['password']);
      }
      $this->Account->id = $this->currentAccount['Account']['id'];
      if($this->Account->save($this->request->data)) {
        $this->Session->setSuccessFlash('アカウントを編集しました');
        $this->redirect(['action' => 'index', 'accountName' => $this->parentAccount['Account']['account_name']]);
      } else {
        // putリクエストにするためにidをセットする
        $this->request->data['Account']['id'] = $this->currentAccount['Account']['id'];
        $this->Session->setDangerFlash('アカウント編集に失敗しました');
      }
    } else {
      $this->request->data = $this->currentAccount;
      unset($this->request->data['Account']['password']);
    }
    // ビューの指定
    $this->render('admin_form');
  }
/**
 * アカウント削除
 */
  public function admin_delete($accountName) {
    // 権限チェック（管理者はクライアントしか削除できない）
    $this->_requireEditableRole();
    // 削除処理
    if($this->request->is('post')) {
      if($this->Account->delete($this->currentAccount['Account']['id'])) {
        $this->Session->setSuccessFlash('アカウントを削除しました');
        $this->redirect(['action' => 'index', 'accountName' => $this->parentAccount['Account']['account_name']]);
      } else {
        $this->Session->setDangerFlash('アカウント削除に失敗しました');
        $this->redirect(['action' => 'index', 'accountName' => $this->parentAccount['Account']['account_name']]);
      }
    } else {
      throw new SorapsException('BadRequestMethod');
    }
  }
/**
 * 子アカウント情報にアクセスできることを保証する
 * 支配者とクライアントの子アカウント情報にはアクセスできない
 */
  protected function _requireHasChildAccount() {
    if(in_array($this->currentAccount['Account']['role'], ['admin', 'client'])) {
      throw new SorapsException('RequireAccessibleChildAccount');
    }
  }
/**
 * 編集可能であることを保証する
 * 管理者はクライアント情報しか編集できない
 */
  protected function _requireEditableRole() {
    if($this->action == 'admin_add' && $this->currentAccount['Account']['child_role'] == 'client') {
      $this->Auth->requireMasterLogin();
    } elseif(in_array($this->action, ['admin_edit', 'admin_delete']) && $this->currentAccount['Account']['role'] == 'client') {
      $this->Auth->requireMasterLogin();
    } else {
      $this->Auth->requireAdminLogin();
    }
  }
}


