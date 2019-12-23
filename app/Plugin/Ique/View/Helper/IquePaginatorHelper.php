<?php
App::uses('BoostCakePaginatorHelper', 'BoostCake.View/Helper');

class IquePaginatorHelper extends BoostCakePaginatorHelper {
/**
 * urlの形式を上書き
 */
  public function beforeRender($viewFile) {
    parent::beforeRender($viewFile);
    $checkParams = ['accountName', 'id', 'listId'];
    $passParams = [];
    foreach($checkParams as $checkParam) {
      if(array_key_exists($checkParam, $this->request->params)) {
        $passParams[$checkParam] = $this->request->params[$checkParam];
      }
    }
    $this->options['url'] = $passParams;
    if (!empty($this->request->query)) {
      $this->options['url']['?'] = $this->request->query;
    }
  }
/**
 * デフォルトのオプションを設定
 */
  public function pagination($options = []) {
    $default = [
      'ul' => 'pagination',
      'modulus' => 9,
      'currentClass' => 'active',
    ];
    $options += $default;
    return parent::pagination($options);
  }
/**
 * activeのデザインが適用されなかったので上書き
 */
  public function numbers($options = []) {
    $return = parent::numbers($options);
    return preg_replace('@<li class="active">(.*?)</li>@', '<li class="active"><span>\1</span></li>', $return);
  }
/**
 * sort
 */
  public function sort($key, $title = null, $options = []) {
    $options = array_merge(['url' => ['page' => 1], 'model' => null], $options);
    $options['escape'] = false;
    // デフォルトの並び順を多い順に
    $options['direction'] = isset($options['direction']) ? $options['direction'] : 'desc';
    // 三角アイコン
    if($this->sortKey($options['model']) == $key) {
      $options = $this->addClass($options, 'active');
      $title .= $this->Html->icon('sort-' . $this->sortDir($options['model']), 'lg', true);
    } else {
      $title .= $this->Html->icon('sort', 'lg', true);
    }
    return parent::sort($key, $title, $options);
  }
/**
 * 表示件数リンク
 */
  public function limit($title = null, $limit = 20, $options = []) {
    if($this->param('limit') == $limit) {
      $options = $this->addClass($options, 'active');
    }
    return parent::link($title, ['page' => 1, 'limit' => $limit], $options);
  }
}
