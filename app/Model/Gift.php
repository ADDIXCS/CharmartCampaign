<?php
class Gift extends AppModel {
  public $actsAs = [
    'Ique.IqueS3' => ['image']
  ];
/**
 * バリデーションルール
 */
  public $validate = [
    'name' => 'notEmpty',
    'win_count' => [
      'notEmpty|numeric',
      'validWinCount' => [
        'rule' => 'validWinCount',
        'message' => '当選上限は当選数以上を指定してください',
      ],
    ],
    'win_probability' => [
      'notEmpty|numeric',
      'validWinProbability' => [
        'rule' => 'validWinProbability',
        'message' => '当選確率の合計が100％以下になるようにしてください',
      ],
    ],
    'win_daily' => [
      'numeric',
      'validWinDaily' => [
        'rule' => 'validWinDaily',
        'message' => '当選上限以下になるようにしてください',
      ],
    ],
  ];
/**
 * 当選上限が当選数以上かのバリデーションルール
 */
  public function validWinCount($check) {
    $gift = $this->findById($this->data[$this->alias]['id']);
    if($gift && current($check) < $gift['Gift']['entry_count']) {
      return false;
    }
    return true;
  }
/**
 * 当選確率の合計が100％以下かのバリデーションルール
 */
  public function validWinProbability($check) {
    $winProbabilities = 0;
    foreach(Router::getRequest()->data['Gift'] as $gift) {
      $winProbabilities += !$gift['delete_flg'] ? $gift['win_probability'] : 0;
    }
    if($winProbabilities > 100) {
      return false;
    }
    return true;
  }
/**
 * 一日の当選上限が当選上限以下かのバリデーションルール
 */
  public function validWinDaily($check) {
    return current($check) <= $this->data[$this->alias]['win_count'];
  }
/**
 * くじを引く
 *
 * @param string $campaignId
 * @return string 景品のid or false はずれの場合
 */
  public function draw($campaignId) {
    $gifts = $this->findAllByCampaignId($campaignId);
    $resultNo = mt_rand(1, 100);
    $winProbability = 0;
    foreach($gifts as $_gift) {
      $gift = $_gift['Gift'];
      $winProbability += $gift['win_probability'];
      if($resultNo <= $winProbability) {
        // 当選数の在庫が無い場合
        if($gift['entry_count'] >= $gift['win_count']) {
          return false;
        }
        // 一日の最大当選数を超えている場合
        if(!is_null($gift['win_daily'])) {
          $Entry = ClassRegistry::init('Entry');
          $dailyWinnerCount = $Entry->find('count', ['conditions' => [
            'Entry.gift_id' => $gift['id'],
            'Entry.created >=' => date('Y-m-d 00:00:00'),
          ]]);
          if($dailyWinnerCount >= $gift['win_daily']) {
            return false;
          }
        }
        return $gift['id'];
      }
    }
    return false;
  }
}
