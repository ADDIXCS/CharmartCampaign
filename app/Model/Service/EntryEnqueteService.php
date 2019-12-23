<?php
class EntryEnqueteService extends Object {
/**
 * アンケートデータの整形
 */
  public function modifyEnqueteData($entries, $_enquetes) {
    if(!$_enquetes) {
      return $entries;
    }
    $enquetes = [];
    foreach($_enquetes as $enquete) {
      $enquetes[$enquete['id']] = [
        'type' => $enquete['type'],
        'text' => $enquete['text'],
        'EnqueteOption' => Hash::combine($enquete['EnqueteOption'], '{n}.id', '{n}.text'),
      ];
    }
    foreach($entries as &$entry) {
      $entryEnquete = [];
      foreach($entry['EntryEnquete'] as $_entryEnquete) {
        $enqueteId = $_entryEnquete['enquete_id'];
        switch($enquetes[$enqueteId]['type']) {
          case 'text':
          case 'textarea':
            $entryEnquete[$enqueteId] = $_entryEnquete['text'];
            break;
          case 'radio':
          case 'select':
            $enqueteOptionId = $_entryEnquete['enquete_option_id'];
            $entryEnquete[$enqueteId] = $enquetes[$enqueteId]['EnqueteOption'][$enqueteOptionId];
            break;
          case 'check':
            $enqueteOptionId = $_entryEnquete['enquete_option_id'];
            $entryEnquete[$enqueteId][] = $enquetes[$enqueteId]['EnqueteOption'][$enqueteOptionId];
            break;
        }
      }
      $entry['EntryEnquete'] = $entryEnquete;
    }
    return $entries;
  }
}
