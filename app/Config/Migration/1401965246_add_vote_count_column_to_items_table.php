<?php
class AddVoteCountColumnToItemsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
  public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
  public $migration = array(
    'up' => array(
      'create_field' => array(
        'items' => array(
          'vote_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'campaign_id'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'items' => array('vote_count',),
      ),
    ),
  );

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
  public function before($direction) {
    return true;
  }

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
  public function after($direction) {
    if($direction == 'up') {
      $Item = ClassRegistry::init('Item');
      $Vote = ClassRegistry::init('Vote');
      $Vote->bindModel(array('belongsTo' => array('Item' => array(
        'counterCache' => array(
          'vote_count' => array('Vote.deleted' => false),
        ),
      ))), false);
      $items = $Item->find('list');
      $itemsCount = count($items);
      $i = 1;
      foreach($items as $itemId => $value) {
        $Vote->updateCounterCache(array('item_id' => $itemId));
        $this->callback->out($i . '/' . $itemsCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
