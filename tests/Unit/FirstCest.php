<?php

use Tests\Support\UnitTester;

class FirstCest {
	public function tryToTest(UnitTester $I) {
		$I->seeNumRecords(0, 'user_logins');
	}
}
