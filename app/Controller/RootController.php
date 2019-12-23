<?php
class RootController extends AppController {
  public $uses = [];
  public $libs = ['Facebook' => 'Ique.IqueFacebook', 'Twitter' => 'Ique.IqueTwitter'];
/**
 * 表面トップ
 * 今の所用途なし
 */
  public function index() {
  }
/**
 * 静的ページの表示
 */
  public function pages($pageName) {
    $this->render($pageName);
  }
/**
 * twitter認証ページへリダイレクト
 */
  public function twitteroauth() {
    $this->redirect($this->Twitter->getLoginUrl($this->request->query('redirect_uri')));
  }
}
