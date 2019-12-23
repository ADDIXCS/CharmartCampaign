<?php
class AddVoteCountColumnToCampaignsAndEntriesAndAudiencesTable extends CakeMigration {

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
        'audiences' => array(
          'vote_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'account_id'),
        ),
        'campaigns' => array(
          'vote_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'account_id'),
        ),
        'entries' => array(
          'vote_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'result_id'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'audiences' => array('vote_count',),
        'campaigns' => array('vote_count',),
        'entries' => array('vote_count',),
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
      $this->callback->out('vote_countのアップデートを開始');
      $Vote = ClassRegistry::init('Vote');
      $Vote->bindModel(array('belongsTo' => array('Entry')));
      $votes = $Vote->find('all');
      $Vote->setCounterCache();
      $votesCount = count($votes);
      $i = 1;
      foreach($votes as $vote) {
        $Vote->save(array(
          'id' => $vote['Vote']['id'],
          'audience_id' => $vote['Entry']['audience_id'],
          'modified' => false
        ));
        $this->callback->out($i . '/' . $votesCount . ' 完了');
        $i++;
      }
    }
    return true;
  }
}
