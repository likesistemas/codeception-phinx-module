<?php

use Phinx\Migration\AbstractMigration;

final class MyNewMigration extends AbstractMigration {
	public function change() {
		$table = $this->table('user_logins');
		$table->addColumn('user_id', 'integer')
			  ->addColumn('created', 'datetime')
			  ->create();
	}
}
