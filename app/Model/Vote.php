<?php
class Vote extends AppModel {
  public $actsAs = [
    'SorapsUser',
  ];
/**
 * counterCache用のモデルのbind
 */
  public function setCounterCache() {
    $counterScope = ['vote_count' => ['Vote.deleted' => false]];
    $this->bindModel(['belongsTo' => [
      'Item' => ['counterCache' => $counterScope],
      'Campaign' => ['counterCache' => $counterScope],
      'Entry' => ['counterCache' => $counterScope],
      'Entrant' => ['counterCache' => $counterScope],
      'Audience' => ['counterCache' => $counterScope],
    ]], false);
  }
}
