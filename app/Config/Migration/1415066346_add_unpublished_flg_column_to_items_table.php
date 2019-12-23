<?php
class AddUnpublishedFlgColumnToItemsTable extends CakeMigration {

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
					'unpublished_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'after' => 'title'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'items' => array('unpublished_flg',),
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
