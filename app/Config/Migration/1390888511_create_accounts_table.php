<?php
class CreateAccountsTable extends CakeMigration {

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
      'create_table' => array(
        'accounts' => array(
          'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
          'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
          'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
          'login_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
          'login_pass' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
          'role' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
          'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
          'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
          'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
          ),
          'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
        ),
      ),
    ),
    'down' => array(
      'drop_table' => array(
        'accounts'
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
      $adminId = uniqid();
      $masterId = uniqid();
      $iqueId = uniqid();
      $passwordHasher = new BlowfishPasswordHasher();
      $datas = array(
        array(
          'id' => $adminId,
          'name' => '支配者',
          'login_id' => 'admin',
          'login_pass' => $passwordHasher->hash('adminpass'),
          'role' => 'admin',
        ),
        array(
          'id' => $masterId,
          'name' => '管理者',
          'login_id' => 'master',
          'login_pass' => $passwordHasher->hash('masterpass'),
          'role' => 'master',
          'parent_id' => $adminId,
        ),
        array(
          'id' => $iqueId,
          'name' => 'IQUE直販',
          'login_id' => 'ique',
          'login_pass' => $passwordHasher->hash('iquepass'),
          'role' => 'agent',
          'parent_id' => $masterId,
        ),
      );
      $Account = ClassRegistry::init('Account');
      if($Account->saveAll($datas, array('validate' => false))) {
        echo '初期データの挿入が完了しました' . "\n";
      } else {
        echo '初期データの挿入に失敗しました' . "\n";
      }
    }
    return true;
  }
}
