<?php

/**
 * Test: Carrooi\Favorites\DI\FavoritesExtension
 *
 * @testCase CarrooiTests\Favorites\DI\FavoritesExtensionTest
 * @author David Kudera
 */

namespace CarrooiTests\Favorites\DI;

use CarrooiTests\Favorites\TestCase;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class FavoritesExtensionTest extends TestCase
{


	/** @var \Carrooi\Favorites\Model\Facades\FavoriteItemsFacade */
	private $favorites;


	/**
	 * @param string $customConfig
	 * @return \Nette\DI\Container
	 */
	protected function createContainer($customConfig = null)
	{
		$container = parent::createContainer($customConfig);

		$this->favorites = $container->getByType('Carrooi\Favorites\Model\Facades\FavoriteItemsFacade');

		return $container;
	}


	public function testGetClass()
	{
		$this->createContainer();

		Assert::same('Carrooi\Favorites\Model\DefaultEntities\DefaultFavoriteItem', $this->favorites->getClass());
	}


	public function testGetClass_custom()
	{
		$entity = 'CarrooiTests\FavoritesApp\Model\Entities\CustomFavoriteItem';
		$config = FileMock::create('favorites: {favoriteItemClass: '. $entity. '}', 'neon');

		$this->createContainer($config);

		Assert::same($entity, $this->favorites->getClass());
	}


	public function testCreateEntity()
	{
		$this->createContainer();

		Assert::type('Carrooi\Favorites\Model\DefaultEntities\DefaultFavoriteItem', $this->favorites->createEntity());
	}


	public function testCreateEntity_custom()
	{
		$entity = 'CarrooiTests\FavoritesApp\Model\Entities\CustomFavoriteItem';
		$config = FileMock::create('favorites: {favoriteItemClass: '. $entity. '}', 'neon');

		$this->createContainer($config);

		Assert::type($entity, $this->favorites->createEntity());
	}


	public function testCustomAssociationsWithDefaultFavoriteItemEntity()
	{
		$config = FileMock::create('favorites: {associations: {Nette\Object: objects}}', 'neon');

		Assert::exception(function() use ($config) {
			$this->createContainer($config);
		}, 'Carrooi\Favorites\InvalidArgumentException', 'Can not use custom associations for default favorite entity.');
	}

}


run(new FavoritesExtensionTest);
