<?php

namespace Like\Codeception;

use Codeception\Module;
use Codeception\TestInterface;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Phinx extends Module {
	public function _before(TestInterface $test) {
		if (!$this->getConfigPopulate()) {
			return;
		}

		$environment = $this->getEnvironment($test);
		$this->phinx($environment, $this->getSeedConfig());
	}

	private function getDefaultEnvironment() {
		return $this->getModule('Db')->_getConfig('defaultEnvironment')
			?: 'production';
	}

	private function getEnvironment(TestInterface $test) {
		return $test->getMetadata()->getCurrent('env') ?: $this->getDefaultEnvironment();
	}

	private function getConfigPopulate() {
		return $this->getModule('Db')->_getConfig('populate');
	}

	private function getSeedConfig() {
		return $this->getConfigBool('seed');
	}

	private function getConfigBool($config) {
		$seed = $this->_getConfig($config);
		if ($seed === null) {
			$seed = true;
		}

		return boolval($seed);
	}

	/**
	 * @param string $environment
	 * @param bool $seed
	 *
	 * @return void
	 */
	private function phinx($environment, $seed) {
		$config = $this->findConfigPath();

		$app = new PhinxApplication();
		$app->setAutoExit(false);

		$output = new BufferedOutput();

		$this->run($app, $output, 'migrate', $config, $environment);

		if ($seed) {
			$this->run($app, $output, 'seed:run', $config, $environment);
		}
	}

	private function run(PhinxApplication $phinx, BufferedOutput $output, $commandName, $config, $environment) {
		$arguments = [
			'command' => $commandName,
			'--environment' => $environment,
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
