<?php
class EntryEnquete extends AppModel {
  public $useTable = 'entries_enquetes';
/**
 * counterCache用のモデルのbind
 */
  public function setCounterCache() {
    $counterScope = ['entry_count' => ['EntryEnquete.deleted' => false]];
    $this->bindModel(['belongsTo' => [
      'EnqueteOption' => ['counterCache' => $counterScope],
    ]], false);
  }
}

