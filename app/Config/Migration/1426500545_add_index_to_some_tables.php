<?php
class AddIndexToSomeTables extends CakeMigration {

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
			'alter_field' => array(
				'accounts' => array(
					'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'answers' => array(
					'question_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'audiences' => array(
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'facebook_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'twitter_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'campaigns' => array(
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'enquete_options' => array(
					'enquete_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'enquetes' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entrants' => array(
					'audience_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'facebook_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'twitter_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entries' => array(
					'entrant_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'audience_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'gift_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'result_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'facebook_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'twitter_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entries_answers' => array(
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'answer_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'question_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entries_enquetes' => array(
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'enquete_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'enquete_option_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'gifts' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'items' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'likes' => array(
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'questions' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'results' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'votes' => array(
					'item_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entrant_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'audience_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
			'create_field' => array(
				'accounts' => array(
					'indexes' => array(
						'parent_id' => array('column' => 'parent_id', 'unique' => 0),
					),
				),
				'answers' => array(
					'indexes' => array(
						'question_id' => array('column' => 'question_id', 'unique' => 0),
					),
				),
				'audiences' => array(
					'indexes' => array(
						'account_id' => array('column' => 'account_id', 'unique' => 0),
						'facebook_id' => array('column' => 'facebook_id', 'unique' => 0),
						'twitter_id' => array('column' => 'twitter_id', 'unique' => 0),
					),
				),
				'campaigns' => array(
					'indexes' => array(
						'account_id' => array('column' => 'account_id', 'unique' => 0),
					),
				),
				'enquete_options' => array(
					'indexes' => array(
						'enquete_id' => array('column' => 'enquete_id', 'unique' => 0),
					),
				),
				'enquetes' => array(
					'indexes' => array(
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
					),
				),
				'entrants' => array(
					'indexes' => array(
						'audience_id' => array('column' => 'audience_id', 'unique' => 0),
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
						'account_id' => array('column' => 'account_id', 'unique' => 0),
						'facebook_id' => array('column' => 'facebook_id', 'unique' => 0),
						'twitter_id' => array('column' => 'twitter_id', 'unique' => 0),
					),
				),
				'entries' => array(
					'indexes' => array(
						'entrant_id' => array('column' => 'entrant_id', 'unique' => 0),
						'audience_id' => array('column' => 'audience_id', 'unique' => 0),
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
						'account_id' => array('column' => 'account_id', 'unique' => 0),
						'gift_id' => array('column' => 'gift_id', 'unique' => 0),
						'result_id' => array('column' => 'result_id', 'unique' => 0),
						'facebook_id' => array('column' => 'facebook_id', 'unique' => 0),
						'twitter_id' => array('column' => 'twitter_id', 'unique' => 0),
					),
				),
				'entries_answers' => array(
					'indexes' => array(
						'entry_id' => array('column' => 'entry_id', 'unique' => 0),
						'answer_id' => array('column' => 'answer_id', 'unique' => 0),
						'question_id' => array('column' => 'question_id', 'unique' => 0),
					),
				),
				'entries_enquetes' => array(
					'indexes' => array(
						'entry_id' => array('column' => 'entry_id', 'unique' => 0),
						'enquete_id' => array('column' => 'enquete_id', 'unique' => 0),
						'enquete_option_id' => array('column' => 'enquete_option_id', 'unique' => 0),
					),
				),
				'gifts' => array(
					'indexes' => array(
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
					),
				),
				'items' => array(
					'indexes' => array(
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
						'entry_id' => array('column' => 'entry_id', 'unique' => 0),
					),
				),
				'likes' => array(
					'indexes' => array(
						'entry_id' => array('column' => 'entry_id', 'unique' => 0),
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
						'account_id' => array('column' => 'account_id', 'unique' => 0),
					),
				),
				'questions' => array(
					'indexes' => array(
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
					),
				),
				'results' => array(
					'indexes' => array(
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
					),
				),
				'votes' => array(
					'indexes' => array(
						'item_id' => array('column' => 'item_id', 'unique' => 0),
						'campaign_id' => array('column' => 'campaign_id', 'unique' => 0),
						'entry_id' => array('column' => 'entry_id', 'unique' => 0),
						'entrant_id' => array('column' => 'entrant_id', 'unique' => 0),
						'audience_id' => array('column' => 'audience_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'accounts' => array(
					'parent_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'answers' => array(
					'question_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'audiences' => array(
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'facebook_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'twitter_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'campaigns' => array(
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'enquete_options' => array(
					'enquete_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'enquetes' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entrants' => array(
					'audience_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'facebook_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'twitter_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entries' => array(
					'entrant_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'audience_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'gift_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'result_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'facebook_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'twitter_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entries_answers' => array(
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'answer_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'question_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'entries_enquetes' => array(
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'enquete_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'enquete_option_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'gifts' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'items' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'likes' => array(
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'account_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'questions' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'results' => array(
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'votes' => array(
					'item_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'campaign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entry_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entrant_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'audience_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
			'drop_field' => array(
				'accounts' => array('', 'indexes' => array('parent_id')),
				'answers' => array('', 'indexes' => array('question_id')),
				'audiences' => array('', 'indexes' => array('account_id', 'facebook_id', 'twitter_id')),
				'campaigns' => array('', 'indexes' => array('account_id')),
				'enquete_options' => array('', 'indexes' => array('enquete_id')),
				'enquetes' => array('', 'indexes' => array('campaign_id')),
				'entrants' => array('', 'indexes' => array('audience_id', 'campaign_id', 'account_id', 'facebook_id', 'twitter_id')),
				'entries' => array('', 'indexes' => array('entrant_id', 'audience_id', 'campaign_id', 'account_id', 'gift_id', 'result_id', 'facebook_id', 'twitter_id')),
				'entries_answers' => array('', 'indexes' => array('entry_id', 'answer_id', 'question_id')),
				'entries_enquetes' => array('', 'indexes' => array('entry_id', 'enquete_id', 'enquete_option_id')),
				'gifts' => array('', 'indexes' => array('campaign_id')),
				'items' => array('', 'indexes' => array('campaign_id', 'entry_id')),
				'likes' => array('', 'indexes' => array('entry_id', 'campaign_id', 'account_id')),
				'questions' => array('', 'indexes' => array('campaign_id')),
				'results' => array('', 'indexes' => array('campaign_id')),
				'votes' => array('', 'indexes' => array('item_id', 'campaign_id', 'entry_id', 'entrant_id', 'audience_id')),
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
