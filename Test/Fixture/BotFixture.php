<?php
/**
 * BotFixture
 *
 */
class BotFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'host' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'port' => array('type' => 'integer', 'null' => false, 'default' => null),
		'channel' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ssl' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'date' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'host' => 'Lorem ipsum dolor sit amet',
			'port' => 1,
			'channel' => 'Lorem ipsum dolor sit amet',
			'ssl' => 1,
			'active' => 1,
			'date' => 1386298583
		),
	);

}
