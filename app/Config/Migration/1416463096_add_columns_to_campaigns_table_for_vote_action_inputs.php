<?php
class AddColumnsToCampaignsTableForVoteActionInputs extends CakeMigration {

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
        'campaigns' => array(
          'input_vote_name' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_street'),
          'input_vote_kana' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_name'),
          'input_vote_email' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_kana'),
          'input_vote_tel' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_email'),
          'input_vote_gender' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_tel'),
          'input_vote_birthday' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_gender'),
          'input_vote_postcode' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_birthday'),
          'input_vote_state' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_postcode'),
          'input_vote_city' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_state'),
          'input_vote_street' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2, 'after' => 'input_vote_city'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'campaigns' => array('input_vote_name', 'input_vote_kana', 'input_vote_email', 'input_vote_tel', 'input_vote_gender', 'input_vote_birthday', 'input_vote_postcode', 'input_vote_state', 'input_vote_city', 'input_vote_street',),
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
    return true;
  }
}
