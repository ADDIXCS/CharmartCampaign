<?php
class EntryAnswer extends AppModel {
  public $useTable = 'entries_answers';
/**
 * counterCache用のモデルのbind
 */
  public function setCounterCache() {
    $counterScope = ['entry_count' => ['EntryAnswer.deleted' => false]];
    $this->bindModel(['belongsTo' => [
      'Answer' => ['counterCache' => $counterScope],
    ]], false);
  }
}
