<?php
class AddEntryCountColumnToAnswersTable extends CakeMigration {

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
        'answers' => array(
          'entry_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'question_id'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'answers' => array('entry_count',),
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
      $this->callback->out('entry_countのアップデートを開始');
      $Answer = ClassRegistry::init('Answer');
      $EntryAnswer = ClassRegistry::init('EntryAnswer');
      $EntryAnswer->setCounterCache();
      $answers = $Answer->find('list');
      $answersCount = count($answers);
      $i = 1;
      foreach($answers as $answerId => $value) {
        $EntryAnswer->updateCounterCache(array('answer_id' => $answerId));
        $this->callback->out($i . '/' . $answersCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
