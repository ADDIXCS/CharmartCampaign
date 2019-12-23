<?php
App::uses('AssetDispatcher.php', 'Routing/Filter');

class SorapsAssetDispatcher extends AssetDispatcher {
/**
 * テーマのフォルダ構造を二層として扱う
 */
  protected function _getAssetFile($url) {
    $parts = explode('/', $url);
    if ($parts[0] === 'theme') {
      $themeName = $parts[1] . '/' . $parts[2];
      unset($parts[0], $parts[1], $parts[2]);
      $fileFragment = implode(DS, $parts);
      $path = App::themePath($themeName) . 'webroot' . DS;
      return $path . $fileFragment;
    }
    return parent::_getAssetFile($url);
  }
}
