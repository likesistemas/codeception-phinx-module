<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MyNewMigration extends AbstractMigration {
	public function change(): void {
		$table = $this->table('user_logins');
		$table->addColumn('user_id', 'integer')
			  ->addColumn('created', 'datetime')
			  ->create();
	}
}
