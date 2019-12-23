<?php
class ChartHelper extends AppHelper {
  protected $_campaign;
  protected $_colors = [
    'gray'     => '#626262',
    'red'      => '#e83217',
    'green'    => '#3cb145',
    'blue'     => '#0071be',
    'yellow'   => '#f8b308',
    'purple'   => '#552a8d',
    'pink'     => '#ff5da7',
    'facebook' => '#4e69a2',
    'twitter'  => '#55acee',
  ];
/**
 * キャンペーンのデータを取得
 */
  public function __construct(View $view, $settings = []) {
    parent::__construct($view, $settings);
    $campaign = $view->get('campaign');
    $this->_campaign = $campaign['Campaign'];
  }
/**
 * 性別
 */
  public function gender() {
    $maleCount = array_sum($this->_campaign['gender_age_count']['male']);
    $femaleCount = array_sum($this->_campaign['gender_age_count']['female']);
    $unknownCount = $this->_campaign['gender_age_count']['unknown'];
    return json_encode([
      'cols' => [
        ['type' => 'string'],
        ['type' => 'number'],
      ],
      'rows' => [
        ['c' => [
          ['v' => '男性'],
          ['v' => $maleCount, 'f' => $maleCount . '人'],
        ]],
        ['c' => [
          ['v' => '女性'],
          ['v' => $femaleCount, 'f' => $femaleCount . '人'],
        ]],
        ['c' => [
          ['v' => '不明'],
          ['v' => $unknownCount, 'f' => $unknownCount . '人'],
        ]],
      ],
    ]);
  }
/**
 * 年齢と性別
 */
  public function age() {
    $data = [
      'cols' => [
        ['type' => 'string'],
        ['type' => 'number', 'label' => '男性'],
        ['role' => 'tooltip', 'type' => 'string'],
        ['type' => 'number', 'label' => '女性'],
        ['role' => 'tooltip', 'type' => 'string'],
        ['role' => 'style', 'type' => 'string'],
      ],
      'rows' => []
    ];
    for($i = 10; $i < 70; $i += 10) {
      switch($i) {
        case 10:
          $label = '10代以下';
          break;
        case 60:
          $label = '60代以上';
          break;
        default:
          $label = $i . '代';
      }
      $data['rows'][] = ['c' => [
        ['v' => $label],
        [
          'v' => $this->_campaign['gender_age_count']['male'][$i],
          'f' => $this->_campaign['gender_age_count']['male'][$i] . '人',
        ],
        ['v' => $this->_campaign['gender_age_count']['male'][$i] . '人'],
        [
          'v' => $this->_campaign['gender_age_count']['female'][$i],
          'f' => $this->_campaign['gender_age_count']['female'][$i] . '人',
        ],
        ['v' => $this->_campaign['gender_age_count']['female'][$i] . '人'],
      ]];
    }
    $data['rows'][] = ['c' => [
      ['v' => '不明'],
      [],
      [],
      [
        'v' => $this->_campaign['gender_age_count']['unknown'],
        'f' => $this->_campaign['gender_age_count']['unknown'] . '人',
      ],
      ['v' => $this->_campaign['gender_age_count']['unknown'] . '人'],
      ['v' => 'fill-color:' . $this->_colors['gray']],
    ]];
    return json_encode($data);
  }
/**
 * 参加時間
 */
  public function time() {
    $data = [
      'cols' => [
        ['type' => 'string'],
        ['type' => 'number', 'label' => '参加者'],
        ['role' => 'tooltip', 'type' => 'string'],
      ],
      'rows' => []
    ];
    for($i = 0; $i < 24; $i++) {
      $data['rows'][] = ['c' => [
        ['v' => $i],
        [
          'v' => $this->_campaign['time'][$i],
          'f' => $this->_campaign['time'][$i] . '人',
        ],
        ['v' => $this->_campaign['time'][$i] . '人'],
      ]];
    }
    return json_encode($data);
  }
/**
 * 友達、フォロワー数
 */
  public function friend($entryType) {
    $keys = [
      'facebook' => 'facebook_friend_count',
      'twitter' => 'twitter_followers_count',
    ];
    $data = [
      'cols' => [
        ['type' => 'string'],
        ['type' => 'number'],
        ['role' => 'tooltip', 'type' => 'string'],
      ],
      'rows' => []
    ];
    foreach($this->_campaign[$keys[$entryType]] as $key => $value) {
      $label = $key > 1000 ? $key . '人〜' : '〜' . $key . '人';
      $data['rows'][] = ['c' => [
        ['v' => $label],
        ['v' => $value],
        ['v' => $value . '人'],
      ]];
    }
    return json_encode($data);
  }
/**
 * 環境
 */
  public function env($type = null) {
    // typeに応じた元データの設定
    if($type) {
      $type = explode('.', $type);
      $rawData = $this->_campaign['env'][$type[0]][$type[1]];
    } else {
      $rawData = [
        'デスクトップ' => $this->_campaign['env']['desktop']['count'],
        'モバイル' => $this->_campaign['env']['mobile']['count'],
        'タブレット' => $this->_campaign['env']['tablet']['count'],
      ];
    }
    // グラフ用データの生成
    $data = [
      'cols' => [
        ['type' => 'string'],
        ['type' => 'number'],
      ],
      'rows' => []
    ];
    foreach($rawData as $key => $value) {
      $data['rows'][] = ['c' => [
        ['v' => $key],
        ['v' => $value, 'f' => $value . '人'],
      ]];
    }
    return json_encode($data);
  }
}
