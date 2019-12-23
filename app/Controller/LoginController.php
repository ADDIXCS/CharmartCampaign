<?php
class LoginController extends AppController {
/**
 * ログイン
 */
  public function admin_login() {
    if($this->request->is('post')) {
      if($this->Auth->login()) {
        $this->redirect($this->Auth->redirectUrl());
      } else {
        $this->Auth->flash('ログインIDまたはパスワードが不正です');
      }
    }
  }
/**
 * ログアウト
 */
  public function admin_logout() {
    $this->redirect($this->Auth->logout());
  }
}

