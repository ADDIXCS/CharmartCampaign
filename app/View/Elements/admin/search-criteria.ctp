<?php
if(!empty($this->request->data['Audience'])) {
  $params = [
    'entry_type' => ['facebook' => 'Facebook', 'twitter' => 'Twitter'],
    'gender' => ['male' => '男性', 'female' => '女性'],
    'age' => ['before' => '', 'after' => '歳'],
    'entry_count' => ['before' => '参加', 'after' => '回'],
    'friend_count' => ['before' => '友達／フォロワー', 'after' => '人'],
    'past_date' => ['before' => '経過日数', 'after' => '日'],
    'engagement' => ['before' => 'エンゲージメント', 'after' => ''],
  ];
  $out = [];
  foreach($this->request->data['Audience'] as $key => $value) {
    switch($key) {
      case 'state':
        $out[$key] = $value;
        break;
      case 'entry_type':
      case 'gender':
        $_out = [];
        foreach($value as $_value) {
          $_out[] = $params[$key][$_value];
        }
        $out[$key] = implode(',', $_out);
        break;
      case 'age_min':
      case 'age_max':
      case 'entry_count_min':
      case 'entry_count_max':
      case 'friend_count_min':
      case 'friend_count_max':
      case 'past_date_min':
      case 'past_date_max':
      case 'engagement_min':
      case 'engagement_max':
        $comparison = strpos($key, '_min') !== false ? '以上' : '以下';
        $key = str_replace(['_min', '_max'], '', $key);
        if(array_key_exists($key, $out)) {
          $out[$key] .= $value . $params[$key]['after'] . $comparison;
        } else {
          $out[$key] = $params[$key]['before'] . $value . $params[$key]['after'] . $comparison;
        }
        break;
      case 'name':
        $out[$key] = '名前に「' . $value . '」を含む';
        break;
      case 'campaign':
        $out[$key] = '「' . $campaigns[$value] . '」に参加';
        break;
    }
  }
  echo implode(',', $out);
}

