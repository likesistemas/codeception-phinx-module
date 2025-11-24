<?php

namespace Like\Codeception;

use Codeception\Module;
use Codeception\TestInterface;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Phinx extends Module {
	public function _before(TestInterface $test): void {
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

	private function getConfigBool(string $config): bool {
		$seed = $this->_getConfig($config);
		if ($seed === null) {
			$seed = true;
		}

		return boolval($seed);
	}

	/**
     * @param string $environment
     * @param bool $seed
     */
    private function phinx($environment, $seed): void {
		$config = $this->findConfigPath();
		if ($config === null) {
			return;
		}

		$app = new PhinxApplication();
		$app->setAutoExit(false);

		$output = new BufferedOutput();

		$this->run($app, $output, 'migrate', $config, $environment);

		if ($seed) {
			$this->run($app, $output, 'seed:run', $config, $environment);
		}
	}

	private function run(PhinxApplication $phinx, BufferedOutput $output, string $commandName, string $config, $environment): void {
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

	protected function getPathConfig(string $path): string {
		return __DIR__ . '/' . $path;
	}

	protected function getPathsConfig(): array {
		return [
			'../../../../tests/phinx.php',
			'../../../tests/phinx.php',
			'../../tests/phinx.php',
			'../tests/phinx.php',
			'./tests/phinx.php',
			'../../../../phinx.php',
			'../../../phinx.php',
			'../../phinx.php',
			'../phinx.php',
			'./phinx.php',
		];
	}

	protected function findConfigPath(): ?string {
		$paths = $this->getPathsConfig();

		$notFound = [];

		foreach ($paths as $path) {
			$src = $this->getPathConfig($path);
			if (file_exists($src) && ($realPath = realpath($src)) !== false) {
				return $realPath;
			}

			$notFound[] = $src;
		}

		trigger_error('Phinx configuration not found. Paths: `' . implode('`, `', $notFound) . '`', E_USER_NOTICE);

		return null;
	}
}
