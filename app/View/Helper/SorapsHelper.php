<?php
class SorapsHelper extends AppHelper {
/**
 * 管理者か支配者ログインかどうか
 */
  public function isMasterLogin() {
    $loginAccount = AuthComponent::user();
    return in_array($loginAccount['role'], ['admin', 'master']);
  }
  public static function isAdminLogin()
  {
    $loginAccount = AuthComponent::user();
    return $loginAccount['role'] == 'admin';
  }
/**
 * title要素
 */
  public function title() {
    $title = '';
    // ページタイトルは管理画面か静的ページのときだけ
    if($this->request->prefix == 'admin' || $this->action == 'pages') {
      $title .= $this->_View->getVar('title_for_layout');// . ' | ';
    }
    // キャンペーン名とアカウント名
    $campaign = $this->_View->getVar('campaign');
    $currentAccount = $this->_View->getVar('currentAccount');
    if($campaign && $this->pageId() != 'campaigns-view') {
      $campaign = $campaign['Campaign'];
      // キャンペーン名は管理画面か非公開でないときだけ
      if(
        $this->request->prefix == 'admin' ||
        !in_array($campaign['status'], ['stopped', 'editing', 'closed'])
      ) {
        // アイテム詳細ページ、投票ページの場合はアイテム名
        if(
          in_array($this->_View->action, ['items', 'vote']) &&
          $item = $this->_View->getVar('item')
        ) {
          $title .= $item['Item']['title'];// . ' | ';
        }
        // キャンペーン名
        $title .= $campaign['title'];// . ' | ';
      }
    } elseif($currentAccount) {
      $title .= $currentAccount['Account']['screen_name'];// . ' | ';
    }
    // $title .= Configure::read('service.name');
    return $title;
  }
/**
 * description
 */
  public function description() {
    $description = '';
    if(!$campaign = $this->_View->getVar('campaign')) {
      return $description;
    }
    $campaign = $campaign['Campaign'];
    // 非公開の場合はreturn
    if(in_array($campaign['status'], ['stopped', 'editing', 'closed'])) {
      return $description;
    }
    // アイテム詳細ページ、投票ページの場合はアイテム名
    if(
      in_array($this->_View->action, ['items', 'vote']) &&
      $item = $this->_View->getVar('item')
    ) {
      if($campaign['campaign_type'] == 'lancers') {
        $description = 'この作品、とても良いと思いませんか？みなさんも、この作品に投票して応援をお願いします！';
      } else {
        $description = $item['Item']['description'];
      }
    }
    // キャンペーン概要
    if(!$description && $campaign) {
      $description = $campaign['summary'];
    }
    return String::truncate(str_replace(["\r\n", "\r", "\n"], ' ', strip_tags($description)), 130);
  }
/**
 * body要素のidを生成する
 */
  public function pageId($campaignView = false) {
    $controller = strtolower($this->_View->viewPath);
    $action = $this->_View->action;
    // キャンペーン画面の場合
    if($campaignView) {
      $controller = 'campaignview';
      if($this->isPreview()) {
        if($this->request->param('editType') == 'detail') {
          $action = 'top';
        } else {
          $action = str_replace('page-', '', $this->request->param('editType'));
        }
      }
    }
    // 応募確認画面の場合
    if($this->_View->action == 'entry' && $this->_View->getVar('isConfirm')) {
      $action = 'entry-confirm';
    }
    // 投票確認画面の場合
    if($this->_View->action == 'vote' && $this->_View->getVar('isConfirm')) {
      $action = 'vote-confirm';
    }
    return $controller . '-' . str_replace('admin_', '', $action);
  }
/**
 * 新規作成・編集中のアカウントタイプを返す
 */
  public function getEditingAccountType() {
    $currentAccount = $this->_View->getVar('currentAccount');
    if($this->action == 'admin_add') {
      return $currentAccount['Account']['child_role'];
    } elseif($this->action == 'admin_edit') {
      return $currentAccount['Account']['role'];
    }
  }
/**
 * キャンペーンビューで使用するデータのセット
 */
  public function setCampaignData($campaign) {
    $this->campaign = $campaign;
  }
/**
 * プレビューページかどうか
 */
  public function isPreview($content = false) {
    if($content && $this->request->param('editType') == 'detail') {
      return false;
    }
    return $this->action == 'admin_edit';
  }
/**
 * キャンペーンビューで使用するデータの取り出し
 */
  public function text($fieldName) {
    // テーマカラーの場合のみ特別な処理
    if($fieldName == 'theme_color') {
      if($this->request->param('editType') == 'detail') {
        return '{{theme_color}}';
      } else {
        return $this->campaign['theme_color'];
      }
    }
    if($this->isPreview(true)) {
      return '{{' . $fieldName . '}}';
    } else {
      return $this->campaign[$fieldName];
    }
  }
  public function textarea($fieldName) {
    if($this->isPreview(true)) {
      return '<span ng-bind-html="' . $fieldName . '|nl2br"></span>';
    } else {
      return nl2br($this->campaign[$fieldName]);
    }
  }
  public function customText($fieldName, $default) {
    if($this->isPreview(true)) {
      $opt = '<span ng-show="' . $fieldName . '_flg">{{' . $fieldName . '}}</span>';
      $opt .= '<span ng-show="!' . $fieldName . '_flg">' . $default . '</span>';
    } elseif($this->isDisplay($fieldName . '_flg')) {
      $opt = $this->campaign[$fieldName];
    } else {
      $opt = $default;
    }
    return $opt;
  }
  public function resource($fieldName) {
    if($this->isPreview(true)) {
      return '{{' . $fieldName . '|trusted}}';
    } else {
      return $this->campaign[$fieldName];
    }
  }
/**
 * キャンペーン画面で使用する日付データの作成
 */
  public function date($fieldName) {
    if($this->isPreview(true)) {
      return '{{' . $fieldName . '.year}}/{{' . $fieldName . '.month}}/{{' . $fieldName . '.day}}';
    } else {
      return date('Y/n/j', strtotime($this->campaign[$fieldName]));;
    }
  }
/**
 * 表示非表示の判別のためにプレビューページかデータが存在する場合trueを返す
 */
  public function isDisplay($fieldName, $reverse = false) {
    $dataFlg = $reverse ? !$this->campaign[$fieldName] : $this->campaign[$fieldName];
    return $this->isPreview() || $dataFlg;
  }
}

