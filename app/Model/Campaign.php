<?php
App::uses('CampaignValue', 'Model/Value');
class Campaign extends AppModel {
  protected $_nameJa = 'キャンペーン';
  public $actsAs = [
    'Ique.IqueS3' => ['top_image', 'gift_image', 'finish_bnr']
  ];
/**
 * バリデーションルール
 */
  public $validate = [
    'title' => 'notEmpty|maxLength,50',
    'campaign_type' => 'notEmpty',
    'published_flg' => [
      'notEmpty|numeric',
      'inList' => [
        'rule' => ['inList', [0,1,2]],
      ],
    ],
    'start_date' => 'notEmpty|datetime,ymd',
    'end_date_flg' => 'boolean',
    'end_date' => [
      'notEmpty|datetime,ymd',
      'validEndDate' => [
        'rule' => 'validEndDate',
        'message' => '終了日は開始日よりも後にしてください',
      ],
    ],
    'top_image_flg' => 'boolean',
    'top_image' => 'imageFile|maxFileSize,2MB',
    'gift_name' => 'maxLength,500',
    'gift_image_flg' => 'boolean',
    'gift_image' => 'imageFile|maxFileSize,2MB',
    'announce_date_flg' => 'boolean',
    'announce_date' => 'datetime,ymd',
    'sponsor_name' => 'notEmpty|maxLength,50',
    'sponsor_url' => 'url',
    'sponsor_contact' => [
      'notEmpty',
      'validSponsorContact' => [
        'rule' => 'validSponsorContact',
        'message' => false,
      ],
    ],
    'sponsor_map' => 'url',
    'sponsor_tel' => 'phone',
    'entry_btn_label_flg' => 'boolean',
    'validate_entry_types' => [
      'validEntryTypes' => [
        'rule' => 'validEntryTypes',
        'message' => '一つ以上選択してください',
      ],
    ],
    'email_entry_flg' => 'boolean',
    'facebook_entry_flg' => 'boolean',
    'facebook_fangate_flg' => 'boolean',
    'twitter_entry_flg' => 'boolean',
    'twitter_fangate_flg' => 'boolean',
    'entry_share_flg' => 'boolean',
    'finish_message_flg' => 'boolean',
    'share_btn_label_flg' => 'boolean',
    'share_double_flg' => 'boolean',
    'finish_bnr_flg' => 'boolean',
    'finish_bnr' => 'imageFile|maxFileSize,2MB',
    'finish_bnr_url' => 'url',
    'entry_limit' => 'numeric',
    'age_limit' => 'inList[17,18,19,20,21]',
    'theme_color' => 'notEmpty|inList[black,gray,red,green,blue,yellow,purple,pink,pastel,natural]',
    'original_design_flg' => 'boolean',
    'item_per_page' => 'numeric|range,10,100',
    'coupon_limit' => 'numeric',
    'lancers_url' => 'notEmpty|url',
    'input_name'     => 'notEmpty|numeric|inList[0,1,2]',
    'input_kana'     => 'notEmpty|numeric|inList[0,1,2]',
    'input_tel'      => 'notEmpty|numeric|inList[0,1,2]',
    'input_gender'   => 'notEmpty|numeric|inList[0,1,2]',
    'input_birthday' => 'notEmpty|numeric|inList[0,1,2]',
    'input_postcode' => 'notEmpty|numeric|inList[0,1,2]',
    'input_state'    => 'notEmpty|numeric|inList[0,1,2]',
    'input_city'     => 'notEmpty|numeric|inList[0,1,2]',
    'input_street'   => 'notEmpty|numeric|inList[0,1,2]',
  ];
/**
 * 応募方法が最低一つ選択されているかのバリデーションルール
 */
  public function validEntryTypes($check) {
    $data = $this->data[$this->alias];
    return !empty($data['email_entry_flg']) || !empty($data['facebook_entry_flg']) || !empty($data['twitter_entry_flg']);
  }
/**
 * 終了日が開始日より後になっているか
 */
  public function validEndDate($check) {
    // end_date_flgがfalseの場合はチェックしない
    if(array_key_exists('end_date_flg', $this->data[$this->alias]) && !$this->data[$this->alias]['end_date_flg']) {
      return true;
    }
    return strtotime(current($check)) > strtotime($this->data[$this->alias]['start_date']);
  }
/**
 * URLかメールアドレス
 */
  public function validSponsorContact($check) {
    return IqueValidationBehavior::email($this, $check) || Validation::url(current($check));
  }
/**
 * キャンペーンの種類のバリデーションを追加
 */
  public function beforeValidate($options = []) {
    $this->validate['campaign_type'][] = [
      'rule' => ['inList', CampaignValue::$types],
    ];
    return parent::beforeValidate($options);
  }
/**
 * キャンペーンの種類の日本語名を追加
 *
 * @param array $results findメソッドの結果
 * @return array データを処理した$results
 */
  public function afterFind($results) {
    if(array_key_exists(0, $results)) {
      foreach($results as &$result) {
        if(array_key_exists('Campaign', $result)) {
          $result['Campaign'] = $this->_afterFind($result['Campaign']);
        }
      }
    } else {
      $results = $this->_afterFind($results);
    }
    return parent::afterFind($results);
  }
  protected function _afterFind($result) {
    // キャンペーンの種類の日本語名
    if(array_key_exists('campaign_type', $result)) {
      $result['campaign_type_ja'] = CampaignValue::${$result['campaign_type']}['name'];
    }
    return $result;
  }
/**
 * beforeSave
 */
  public function beforeSave() {
    // 終了日時の秒を59秒にする
    if(array_key_exists('end_date', $this->data[$this->alias])) {
      $this->data[$this->alias]['end_date'] = date('Y-m-d H:i:59', strtotime($this->data[$this->alias]['end_date']));
    }
    return parent::beforeSave();
  }
/**
 * counterCache用のモデルのbind
 */
  public function setCounterCache() {
    $counterScope = ['campaign_count' => ['Campaign.deleted' => false]];
    $this->bindModel(['belongsTo' => [
      'Account' => ['counterCache' => $counterScope],
    ]], false);
  }
}

