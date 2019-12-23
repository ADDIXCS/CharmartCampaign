<?php
App::uses('BoostCakeFormHelper', 'BoostCake.View/Helper');

class IqueFormHelper extends BoostCakeFormHelper {
  public $helpers = ['Html', 'S3'];
  // 確認画面
  public $isConfirm = false;
  // BoostCakeFormHelperで空にされるので退避
  protected $_inputDefaultsTmp;
  // _getInput内で使用できるように退避
  protected $_optionsTmp;
  // インラインになるinputType
  protected $_inlineInputTypes = ['name', 'time', 'date', 'datetime', 'file', 'image'];
  // horizonatalフォームかどうか
  protected $_horizontal = false;
  // AngularJSを使うか
  protected $_angular = false;
/**
 * 確認画面かどうかの設定
 */
  public function __construct(View $view, $settings = []) {
    parent::__construct($view, $settings);
    if($view->getVar('isConfirm')) {
      $this->isConfirm = true;
    }
  }
/**
 * デフォルトの設定
 */
  public function create($model = null, $options = []) {
    // オプション設定のマージ
    $options = Hash::merge(
      [
        'inputDefaults' => [
          'div' => 'form-group',
          'class' => 'form-control',
          'label' => ['class' => 'col-sm-2 control-label'],
          'wrapInput' => 'col-sm-4',
        ],
        'class' => 'form-horizontal',
        'novalidate' => true,
        'angular' => false,
      ],
      $options
    );
    // horizontalフォームかどうか
    $this->_horizontal = strpos($options['class'], 'form-horizontal') !== false;
    // AngularJSを使うかどうか
    $this->_angular = $options['angular'];
    unset($options['angular']);
    return parent::create($model, $options);
  }
/**
 * 必須ラベル
 */
  public function label($fieldName = null, $text = null, $options = []) {
    $inputData = parent::_initInputField($fieldName, $options);
    if($fieldName && array_key_exists('required', $inputData) && $inputData['required']) {
      $text = $this->Html->tag('span', '必須', ['class' => 'label label-danger']) . ' ' . $text;
    }
    return parent::label($fieldName, $text, $options);
  }
/**
 * 独自オプション
 */
  public function input($fieldName, $options = []) {
    $options += [
      'beforeInput' => '',
      'afterInput' => '',
    ];
    // AngularJSのオプション設定
    if($this->_angular) {
      // モデルのデータを取得
      $inputData = parent::_initInputField($fieldName);
      // モデルのデータがない場合にデフォルトの値を設定
      if(is_null($inputData['value']) && array_key_exists('default', $options)) {
        $inputData['value'] = $options['default'];
      }
      $options['ng-model'] = $fieldName;
      if($inputData['value']) {
        if($options['type'] == 'checkbox') {
          $options['ng-init'] = $fieldName . '=true';
        } elseif(is_string($inputData['value'])) {
          $options['ng-init'] = $fieldName . '=\'' . addslashes($inputData['value']) . '\'';
        }
      } elseif($options['type'] == 'radio') {
        $options['ng-init'] = $fieldName . '=false';
      }
    }
    // インラインヘルプ
    if(array_key_exists('helpInline', $options)) {
      $options['beforeInput'] = '<div class="form-inline">' . $options['beforeInput'];
      $options['afterInput'] .= ' <span class="control-label">' . $options['helpInline'] . '</span></div>';
    }
    // ブロックヘルプ
    if(array_key_exists('helpBlock', $options)) {
      $options['afterInput'] .= '<span class="help-block">' . $options['helpBlock'] . '</span>';
    }
    // labelの内容をbeforeに設定する
    if($options['type'] == 'radio') {
      if(array_key_exists('label', $options)) {
        $options['before'] = $this->label($fieldName, $options['label'], $this->_inputDefaults['label']);
      }
    }
    // インラインフォームになるものにカラム幅を設定
    if(in_array($options['type'], $this->_inlineInputTypes) || !empty($options['inline'])) {
      if(!array_key_exists('wrapInput', $options)) {
        $colWidth = 12 - preg_replace('/[^0-9]/', '', $this->_inputDefaults['label']['class']);
        $options['wrapInput'] = preg_replace('/col-sm-[0-9]+/', 'col-sm-' . $colWidth, $this->_inputDefaults['wrapInput']);
      }
    }
    // BoostCakeFormHelperで空にされるので退避
    $this->_inputDefaultsTmp = $this->_inputDefaults;
    // _getInput内で使用できるように退避
    $this->_optionsTmp = $options;
    return parent::input($fieldName, $options);
  }
/**
 * bootstrapでのデザイン
 */
  public function radio($fieldName, $options = [], $attributes = []) {
    $attributes['legend'] = false;
    if($attributes['class'] == 'form-control') {
      $attributes['class'] = 'radio-inline';
    }
    unset($attributes['label']);
    return parent::radio($fieldName, $options, $attributes);
  }
/**
 * 姓名のフィールドを返す
 */
  public function name($fieldName, $options = []) {
    if(empty($options['kana'])) {
      $lastNameLabel = '姓';
      $firstNameLabel = '名';
    } else {
      $lastNameLabel = 'せい';
      $firstNameLabel = 'めい';
    }
    unset($options['kana']);
    $opt = '<span class="form-control-name">';
    $opt .= '<span class="control-label">' . $lastNameLabel . '</span> ';
    $opt .= parent::text('last_' . $fieldName, $options);
    $opt .= '</span>';
    $opt .= ' ';
    $opt .= '<span class="form-control-name">';
    $opt .= '<span class="control-label">' . $firstNameLabel . '</span> ';
    $opt .= parent::text('first_' . $fieldName, $options);
    $opt .= '</span>';
    return $opt;
  }
/**
 * bootstrapでのデザイン
 */
  public function file($fieldName, $options = []) {
    $opt = parent::file($fieldName, $options);
    $opt .= parent::checkbox($fieldName . '_delete_flg', ['class' => 'hide']);
    $opt .= $this->button($this->Html->icon('file-o', 'lg', true) . 'ファイル選択', [
      'type' => 'button',
      'class' => 'btn btn-default btn-file js-btn-file',
    ]);
    // 削除ボタン
    if(!is_array($this->_optionsTmp['label']) || empty($this->_optionsTmp['label']['required'])) {
      // モデルのデータを取得
      $inputData = parent::_initInputField($fieldName);
      $deleteBtnClass = $inputData['value'] ? '' : ' hide';
      $opt .= $this->button($this->Html->icon('trash-o', 'lg', true), [
        'type' => 'button',
        'class' => 'btn btn-default btn-file-delete js-btn-file-delete' . $deleteBtnClass,
      ]);
    }
    return $opt;
  }
/**
 * accept属性の設定
 */
  public function image($fieldName, $options = []) {
    // オプションの初期値を設定
    $options += ['preview' => true];
    $opt = '';
    if($options['preview']) {
      // モデルのデータを取得
      $inputData = parent::_initInputField($fieldName);
      // idを取得
      $fieldNameArr = explode('.', $fieldName);
      array_pop($fieldNameArr);
      array_push($fieldNameArr, 'id');
      $idData = parent::_initInputField(implode('.', $fieldNameArr));
      if(is_string($inputData['value']) && $inputData['value'] && $idData['value']) {
        $opt .= '<p class="image-preview">' . $this->S3->image(
          ['id' => $idData['value'], 'image' => $inputData['value']],
          $this->model() . '.image'
        ) . '</p>';
      }
    }
    unset($options['preview']);
    return $opt . $this->file($fieldName, $options + ['accept' => 'image/*']);
  }
/**
 * インラインでの表示とデフォルトの値
 */
  public function dateTime($fieldName, $dateFormat, $timeFormat, $options = []) {
    // オプション
    $options += [
      'minYear' => 2014,
      'maxYear' => date('Y', strtotime('+2 year')),
      'default' => date('Y-m-d 00:00'),
      'multiLines' => false,
    ];
    // AngularJSのオプション設定
    $angularOptions = [];
    $types = $dateFormat ? ['Y' => 'year', 'm' => 'month', 'd' => 'day'] : [];
    $types += $timeFormat ? ['H' => 'hour', 'i' => 'min'] : [];
    foreach($types as $key => $type) {
      if($this->_angular) {
        // モデルのデータを取得
        $inputData = $this->_dateTimeSelected($type, $fieldName, ['value' => null]);
        // 取得データに合わせたng-init用の値の設定
        if($type == 'year' && (strlen($inputData['value']) > 4 || $inputData['value'] === 'now')) {
          $inputData['value'] = date_create($inputData['value'])->format($key);
        } elseif(strlen($inputData['value']) > 2) {
          $inputData['value'] = date_create($inputData['value'])->format($key);
        } elseif(!$inputData['value'] && array_key_exists('default', $options)) {
          $inputData['value'] = date_create($options['default'])->format($key);
        } elseif($inputData['value'] === false) {
          $inputData['value'] = null;
        }
        $angularOptions[$type] = [
          'ng-model' => $fieldName . '.' . $type,
          'ng-init' => $fieldName . '.' . $type . '=\'' . $inputData['value'] . '\'',
        ];
      } else {
        $angularOptions[$type] = [];
      }
    }
    // 出力htmlの生成開始
    $opt = '';
    if($dateFormat) {
      // 年
      $opt .= parent::year($fieldName, $options['minYear'], $options['maxYear'], [
        'default' => $options['default'] ? date('Y', strtotime($options['default'])) : false,
        'orderYear' => 'asc',
        'class' => $options['class'],
      ] + $angularOptions['year']);
      $opt .= ' <span class="control-label">年</span> ';
      // 月
      $opt .= parent::month($fieldName, [
        'default' => $options['default'] ? date('m', strtotime($options['default'])) : false,
        'monthNames' => false,
        'class' => $options['class'],
      ] + $angularOptions['month']);
      $opt .= ' <span class="control-label">月</span> ';
      // 日
      $opt .= parent::day($fieldName, [
        'default' => $options['default'] ? date('d', strtotime($options['default'])) : false,
        'class' => $options['class'],
      ] + $angularOptions['day']);
      $opt .= ' <span class="control-label">日</span> ';
    }
    if($timeFormat) {
      // 2行に分ける処理
      if($options['multiLines']) {
        $opt .= '</div><div class="form-inline">';
      }
      // 時
      $opt .= parent::hour($fieldName, true, [
        'default' => date('H', strtotime($options['default'])),
        'class' => $options['class'],
      ] + $angularOptions['hour']);
      $opt .= ' <span class="control-label">時</span> ';
      // 分
      $opt .= parent::minute($fieldName, [
        'default' => date('i', strtotime($options['default'])),
        'class' => $options['class'],
      ] + $angularOptions['min']);
      $opt .= ' <span class="control-label">分</span>';
    }
    return $opt;
  }
/**
 * 検索用範囲指定
 */
  public function range($fieldName, $options = []) {
    $options = $this->addClass($options, 'input-mini');
    $opt = '<div class="form-inline">';
    $opt .= parent::text($fieldName . '_min', $options);
    $opt .= '<span class="control-label"> 〜 </span> ';
    $opt .= parent::text($fieldName . '_max', $options);
    $opt .= '</div>';
    return $opt;
  }
/**
 * 他のボタンと並べたときのために最後に改行を入れる
 */
  public function button($title, $options = []) {
    return parent::button($title, $options) . "\n";
  }
/**
 * postLinkのボタン化
 */
  public function postBtn($title, $url = null, $btnOptions = [], $options = [], $confirmMessage = false) {
    // オプション設定のマージ
    $btnOptions = Hash::merge(
      ['type' => 'default', 'size' => null, 'icon' => null],
      $btnOptions
    );
    // サイズ
    $sizeClass = $btnOptions['size'] ? ' btn-' . $btnOptions['size'] : '';
    // アイコン
    if($btnOptions['icon']) {
      $title = $this->Html->icon($btnOptions['icon']) . ($title ? ' ' . $title : '');
    }
    // linkメソッドに渡すオプション
    $options = Hash::merge(
      ['class' => 'btn btn-' . $btnOptions['type'] . $sizeClass, 'escape' => false],
      $options
    );
    return parent::postLink($title, $url, $options, $confirmMessage) . "\n";
  }
/**
 * 確認画面の場合の処理
 * インラインの処理
 */
  protected function _getInput($args) {
    extract($args);
    if($this->isConfirm) {
      $inputData = parent::_initInputField($fieldName);
      switch ($type) {
        case 'radio':
          $opt = $inputData['value'] ? $radioOptions[$inputData['value']] : '';
          $opt .= $this->hidden($fieldName);
          break;
        case 'checkbox':
          $opt = $inputData['value'] ? $this->_optionsTmp['label']['text'] : '';
          $opt .= $this->hidden($fieldName);
          break;
        case 'select':
          if(is_array($inputData['value'])) {
            $values = [];
            foreach($inputData['value'] as $value) {
              $values[] = $options['options'][$value];
            }
            $opt = implode(', ', $values);
            $args['options']['class'] = 'hide';
            $opt .= FormHelper::_getInput($args);
          } else {
            $opt = $inputData['value'] ? $options['options'][$inputData['value']] : '';
            $opt .= $this->hidden($fieldName);
          }
          break;
        case 'date':
          $opt = '';
          if(array_sum($inputData['value'])) {
            $opt .= vsprintf('%d年%d月%d日', $inputData['value']);
          }
          $opt .= $this->hidden($fieldName . '.year');
          $opt .= $this->hidden($fieldName . '.month');
          $opt .= $this->hidden($fieldName . '.day');
          break;
        case 'name':
          $inputDataLast = parent::_initInputField('last_' . $fieldName);
          $inputDataFirst = parent::_initInputField('first_' . $fieldName);
          $opt = $inputDataLast['value'] . ' ' . $inputDataFirst['value'];
          $opt .= $this->hidden('last_' . $fieldName);
          $opt .= $this->hidden('first_' . $fieldName);
          break;
        case 'image':
          $opt = '';
          if(
            is_array($inputData['value']) &&
            array_key_exists('tmp_name', $inputData['value']) &&
            file_exists($inputData['value']['tmp_name'])
          ) {
            $tmpFile = new File($inputData['value']['tmp_name']);
            $encodedFile = 'data:' . $tmpFile->mime() . ';base64,' . base64_encode($tmpFile->read());
            $opt .= '<img src="' . $encodedFile . '">';
            $opt .= $this->hidden($fieldName, ['value' => $encodedFile]);
          }
          return $opt;
        default:
          $opt = nl2br($inputData['value']);
          $opt .= $this->hidden($fieldName);
      }
      $colWidth = 12 - preg_replace('/[^0-9]/', '', $this->_inputDefaultsTmp['label']['class']);
      return '<div class="col-sm-' . $colWidth . ' form-control-static">' . $opt . '</div>';
    }
    // インラインフォームになるものをdivで囲う
    if(in_array($type, $this->_inlineInputTypes) || !empty($options['inline'])) {
      unset($args['options']['inline']);
      return '<div class="form-inline">' . parent::_getInput($args) . '</div>';
    } else {
      return parent::_getInput($args);
    }
  }
}

