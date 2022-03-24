<?php

namespace Like\NomeDaLib\Tests;

use Like\Codeception\Phinx\Phinx;
use PHPUnit\Framework\TestCase;

class PhinxTest extends TestCase {
	public function testInstance() {
		$this->assertTrue(class_exists(Phinx::class));
	}
}
