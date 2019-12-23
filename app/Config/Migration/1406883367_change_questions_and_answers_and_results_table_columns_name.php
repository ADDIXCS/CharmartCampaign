<?php
class ChangeQuestionsAndAnswersAndResultsTableColumnsName extends CakeMigration {

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
      'rename_field' => array(
        'answers' => array(
          'answer_text' => 'text',
          'answer_point' => 'point',
        ),
        'questions' => array(
          'question_image' => 'image',
          'question_text' => 'text',
          'question_choice_type' => 'type',
          'question_order' => 'order',
        ),
        'results' => array(
          'result_point_min' => 'point_min',
          'result_point_max' => 'point_max',
          'result_image' => 'image',
          'result_title' => 'title',
          'result_description' => 'description',
        ),
      ),
    ),
    'down' => array(
      'rename_field' => array(
        'answers' => array(
          'text' => 'answer_text',
          'point' => 'answer_point',
        ),
        'questions' => array(
          'image' => 'question_image',
          'text' => 'question_text',
          'type' => 'question_choice_type',
          'order' => 'question_order',
        ),
        'results' => array(
          'point_min' => 'result_point_min',
          'point_max' => 'result_point_max',
          'image' => 'result_image',
          'title' => 'result_title',
          'description' => 'result_description',
        ),
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
