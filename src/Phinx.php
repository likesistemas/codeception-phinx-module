<?php

namespace Like\Codeception;

use Codeception\Module;
use Codeception\TestInterface;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Phinx extends Module {
	public function _before(TestInterface $test) {
		$populate = $this->getModule('Db')->_getConfig('populate');

		if ($populate) {
			$this->phinx();
		}
	}

	private function phinx() {
		$config = $this->findConfigPath();

		$app = new PhinxApplication();
		$app->setAutoExit(false);

		$output = new BufferedOutput();
		
		$this->run($app, $output, 'migrate', $config);
		$this->run($app, $output, 'seed:run', $config);
	}

	private function run(PhinxApplication $phinx, BufferedOutput $output, $commandName, $config, $environment='production') {
		$arguments = [
			'command'         => $commandName,
			'--environment'   => $environment,
			'--configuration' => $config,
			'-vvv' => '',
		];

		$ok = $phinx->run(new ArrayInput($arguments), $output);
		if ($ok !== 0) {
			trigger_error("Error on phinx execution.\n\n" . $output->fetch(), E_USER_ERROR);
		}
	}

	private function findConfigPath() {
		$paths = [
			'../../../../tests/phinx.php',
			'../../../../phinx.php',
			'../../phinx.php',
			'../phinx.php', // To use inside library tests
		];

		$notFound = [];

		foreach ($paths as $path) {
			$src = __DIR__ . '/' . $path;
			if (file_exists($src)) {
				return realpath($src);
			}

			$notFound[] = $src;
		}

		trigger_error('Phinx configuration not found. Paths: `' . join('`, `', $notFound) . '`', E_USER_NOTICE);
	}
}
