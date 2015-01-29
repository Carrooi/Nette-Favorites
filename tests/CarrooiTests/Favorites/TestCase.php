<?php

namespace CarrooiTests\Favorites;

use Nette\Configurator;
use Tester\FileMock;
use Tester\TestCase as BaseTestCase;

/**
 *
 * @author David Kudera
 */
class TestCase extends BaseTestCase
{


	/** @var \Nette\DI\Container */
	private $container;


	public function tearDown()
	{
		$this->container = null;
	}


	/**
	 * @param string $customConfig
	 * @return \Nette\DI\Container
	 */
	protected function createContainer($customConfig = null)
	{
		if (!isset($this->container)) {
			copy(__DIR__. '/../FavoritesApp/Model/database', TEMP_DIR. '/database');

			$config = new Configurator;
			$config->setTempDirectory(TEMP_DIR);
			$config->addParameters(['appDir' => __DIR__. '/../FavoritesApp']);
			$config->addConfig(__DIR__. '/../FavoritesApp/config/config.neon');
			$config->addConfig(FileMock::create('parameters: {databasePath: %tempDir%/database}', 'neon'));

			if ($customConfig) {
				$config->addConfig($customConfig);
			}

			$this->container = $config->createContainer();
		}

		return $this->container;
	}

}
