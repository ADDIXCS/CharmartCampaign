<?php
App::uses('View', 'View');

/**
 * Jade部分はHamlViewを参考に作成
 * https://github.com/TiuTalk/haml
 */
class IqueJadeView extends View {
  public static $extension = 'jade';
  protected $_cacheFolder = 'views';
/**
 * コンストラクタ
 */
  public function __construct(Controller $controller = null) {
    $this->Jade = new Jade\Jade();
    $controller->ext = '.' . self::$extension;
    return parent::__construct($controller);
  }
/**
 * jadeのレンダリング
 */
  protected function _renderJade($file) {
    return $this->Jade->render($file);
  }
/**
 * キャッシュファイルの名前を返す
 */
  protected function _cacheFileName() {
    return CACHE . $this->_cacheFolder . DS . uniqid('jade_') . '.ctp';
  }
/**
 * ビューの作成
 */
  protected function _createRenderedView($file) {
    $content = $this->_renderJade($file);

    $tmpFile = new File($this->_cacheFileName(), true);
    $tmpFile->write($content);
    $tmpFile->close();

    return $tmpFile->pwd();
  }
/**
 * ビューファイルの削除
 */
  protected function _deleteRenderedView($tmpFile) {
    $file = new File($tmpFile);
    return $file->delete();
  }
/**
 * ビューのレンダリング処理
 */
  protected function _evaluate($viewFile, $dataForView) {
    $file = new File($viewFile);
    if ($file->ext() != self::$extension) {
      return parent::_evaluate($viewFile, $dataForView);
    }

    $file = $this->_createRenderedView($viewFile);
    $content = parent::_evaluate($file, $dataForView);
    $this->_deleteRenderedView($file);

    return $content;
  }
/**
 * ビューファイルのパスを取得
 * テーマ機能を使ったときに共通のjadeよりテーマのctpを優先させるようにViewのメソッドを上書き
 */
  protected function _getViewFileName($name = null) {
    $subDir = null;

    if ($this->subDir !== null) {
      $subDir = $this->subDir . DS;
    }

    if ($name === null) {
      $name = $this->view;
    }
    $name = str_replace('/', DS, $name);
    list($plugin, $name) = $this->pluginSplit($name);

    if (strpos($name, DS) === false && $name[0] !== '.') {
      $name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
    } elseif (strpos($name, DS) !== false) {
      if ($name[0] === DS || $name[1] === ':') {
        if (is_file($name)) {
          return $name;
        }
        $name = trim($name, DS);
      } elseif ($name[0] === '.') {
        $name = substr($name, 3);
      } elseif (!$plugin || $this->viewPath !== $this->name) {
        $name = $this->viewPath . DS . $subDir . $name;
      }
    }
    $paths = $this->_paths($plugin);
    $exts = $this->_getExtensions();
    foreach ($paths as $path) {
      foreach ($exts as $ext) {
        if (file_exists($path . $name . $ext)) {
          return $path . $name . $ext;
        }
      }
    }
    $defaultPath = $paths[0];

    if ($this->plugin) {
      $pluginPaths = App::path('plugins');
      foreach ($paths as $path) {
        if (strpos($path, $pluginPaths[0]) === 0) {
          $defaultPath = $path;
          break;
        }
      }
    }
    throw new MissingViewException(['file' => $defaultPath . $name . $this->ext]);
  }
  protected function _getLayoutFileName($name = null) {
    if ($name === null) {
      $name = $this->layout;
    }
    $subDir = null;

    if ($this->layoutPath !== null) {
      $subDir = $this->layoutPath . DS;
    }
    list($plugin, $name) = $this->pluginSplit($name);
    $paths = $this->_paths($plugin);
    $file = 'Layouts' . DS . $subDir . $name;

    $exts = $this->_getExtensions();
    foreach ($paths as $path) {
      foreach ($exts as $ext) {
        if (file_exists($path . $file . $ext)) {
          return $path . $file . $ext;
        }
      }
    }
    throw new MissingLayoutException(['file' => $paths[0] . $file . $this->ext]);
  }
}

