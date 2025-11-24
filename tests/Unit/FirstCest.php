<?php

namespace Tests\Unit;

use Tests\UnitTester;

class FirstCest {
	public function tryToTest(UnitTester $I): void {
		$I->seeNumRecords(0, 'user_logins');
	}
}
