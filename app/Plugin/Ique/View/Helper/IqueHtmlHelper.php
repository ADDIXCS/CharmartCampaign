<?php
App::uses('BoostCakeHtmlHelper', 'BoostCake.View/Helper');

class IqueHtmlHelper extends BoostCakeHtmlHelper {
/**
 * パンくずリストにbootstrapのclassをつける
 */
  public function getCrumbList($options = [], $startText = false) {
    $defaults = ['class' => 'breadcrumb', 'firstClass' => '', 'lastClass' => 'active'];
    $options = array_merge($defaults, (array)$options);
    return parent::getCrumbList($options, $startText);
  }
/**
 * パンくずリストのリセット
 */
  public function resetCrumbs() {
    $this->_crumbs = [];
  }
/**
 * リンク
 */
  public function link($title, $url = null, $options = [], $confirmMessage = false) {
    if(is_array($options) && array_key_exists('icon', $options)) {
      $title = $this->icon($options['icon']) . ' ' . $title;
      $options['escapeTitle'] = false;
      unset($options['icon']);
    }
    return parent::link($title, $url, $options, $confirmMessage);
  }
/**
 * アイコン
 */
  public function icon($type = null, $size = 'lg', $fw = false, $options = []) {
    // 種類が指定されてなければ空の文字列を返す
    if(!$type) return;
    // サイズ
    $sizeClass = $size ? ' fa-' . $size : '';
    // 幅固定の処理
    $fwClass = $fw ? ' fa-fw' : '';
    return $this->tag('i', '', [
      'class' => 'fa fa-' . $type . $sizeClass . $fwClass,
    ] + $options);
  }
/**
 * ボタン
 */
  public function btn($title = null, $url = null, $btnOptions = [], $options = [], $confirmMessage = false) {
    // オプション設定のマージ
    $btnOptions = Hash::merge(
      ['type' => 'default', 'size' => null, 'block' => false, 'icon' => null],
      $btnOptions
    );
    // サイズ
    $sizeClass = $btnOptions['size'] ? ' btn-' . $btnOptions['size'] : '';
    // ブロック要素か
    $sizeClass .= $btnOptions['block'] ? ' btn-block' : '';
    // アイコン
    if($btnOptions['icon']) {
      $title = $this->icon($btnOptions['icon']) . ($title ? ' ' . $title : '');
    }
    // classの設定
    $options = $this->addClass($options, 'btn btn-' . $btnOptions['type'] . $sizeClass);
    // linkメソッドに渡すオプション
    $options = Hash::merge(
      ['escape' => false],
      $options
    );
    return parent::link($title, $url, $options, $confirmMessage) . "\n";
  }
}

