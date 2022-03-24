<?php

namespace Like\Codeception\Phinx;

use Codeception\Module;
use Codeception\TestInterface;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Phinx extends Module {
	public function _before(TestInterface $test) {
		$populate = $this->getModule('Db')->_getConfig('populate');

		if ($populate) {
			$this->phinx();
		}
	}

	private function phinx() {
		$config = realpath(__DIR__ . '/../../phinx.php');

		$app = new PhinxApplication();
		$app->setAutoExit(false);

		$output = new NullOutput();
		
		$this->run($app, $output, 'migrate', $config);
		$this->run($app, $output, 'seed:run', $config);
	}

	private function run(PhinxApplication $phinx, OutputInterface $output, $commandName, $config, $environment='production') {
		$arguments = [
			'command'         => $commandName,
			'--environment'   => $environment,
			'--configuration' => $config,
			'-vvv' => '',
		];

		$ok = $phinx->run(new ArrayInput($arguments), $output);
		if ($ok !== 0) {
			trigger_error('Error on phinx execution.', E_USER_ERROR);
		}
	}
}
