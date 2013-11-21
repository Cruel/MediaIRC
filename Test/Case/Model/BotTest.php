<?php
App::uses('Bot', 'Model');

/**
 * Bot Test Case
 *
 */
class BotTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.bot',
		'app.link'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Bot = ClassRegistry::init('Bot');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Bot);

		parent::tearDown();
	}

}
