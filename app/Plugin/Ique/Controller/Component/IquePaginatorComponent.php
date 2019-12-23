<?php
class IquePaginatorComponent extends PaginatorComponent {
  public function validateSort(Model $object, array $options, array $whitelist = []) {
    if(empty($options['order']) && is_array($object->order)) {
      $options['order'] = $object->order;
    }
    if(isset($options['sort']) && $options['sort'] == 'rand') {
      // $options['order'] = 'rand((' . date('Ymd') . '))';
      $options['order'] = 'rand()';
      return $options;
    }
    return parent::validateSort($object, $options, $whitelist);
  }
}
