<?php
class AddPlanLancersFlgColumnToAccountsTable extends CakeMigration {

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
				'accounts' => array(
					'plan_lancers_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'plan_shindan_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'accounts' => array('plan_lancers_flg',),
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
