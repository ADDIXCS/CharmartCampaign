<?php
App::uses('IqueJadeView', 'Ique.View');

class SorapsView extends IqueJadeView {
/**
 * キャンペーン周りのレンダリング
 */
  public function render($view = null, $layout = null) {
    // キャンペーン編集と表面のデフォルトのビューの場合
    if((
      $this->request->param('controller') == 'campaign_view' ||
      $this->Soraps->pageId() == 'campaigns-edit'
    )) {
      $prefix = $this->request->param('admin') ? 'admin_edit_' : '';
      if($view) {
        $viewType = $view;
      } else {
        $viewType = $this->request->param('admin')
          ? $this->request->param('editType')
          : $this->action;
      }
      $campaignType = Hash::get($this->viewVars, 'campaign.Campaign.campaign_type');
      try {
        $view = $prefix . $viewType . '_' . $campaignType;
        return parent::render($view, $layout);
      } catch(MissingViewException $e) {
        $view = $prefix . $viewType;
        return parent::render($view, $layout);
      }
    }
    return parent::render($view, $layout);
  }
/**
 * プレビューのレンダリング
 * 表面のbodyにつくidでラップして返す
 */
  public function renderPreview() {
    $this->hasRendered = false;
    switch($this->request->param('editType')) {
      case 'detail':
        $viewType = 'top';
        break;
      default:
        $viewType = str_replace('page-', '', $this->request->param('editType'));
    }
    $campaignType = Hash::get($this->viewVars, 'campaign.Campaign.campaign_type');
    try {
      $view = '/CampaignView/' . $viewType . '_' . $campaignType;
      return parent::render($view, false);
    } catch(MissingViewException $e) {
      $view = '/CampaignView/' . $viewType;
      return parent::render($view, false);
    }
  }
}

