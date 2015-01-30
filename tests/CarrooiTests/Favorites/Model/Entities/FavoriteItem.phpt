<?php

/**
 * Test: Carrooi\Favorites\Model\Entities\FavoriteItem
 *
 * @testCase CarrooiTests\Favorites\Model\Entities\FavoriteItem
 * @author David Kudera
 */

namespace CarrooiTests\Favorites\Model\Entities;

use CarrooiTests\Favorites\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class FavoriteItemTest extends TestCase
{


	/** @var \Carrooi\Favorites\Model\Facades\FavoriteItemsFacade */
	private $favorites;

	/** @var \CarrooiTests\FavoritesApp\Model\Facades\ArticlesFacade */
	private $articles;

	/** @var \CarrooiTests\FavoritesApp\Model\Facades\UsersFacade */
	private $users;


	/**
	 * @param string $customConfig
	 * @return \Nette\DI\Container
	 */
	protected function createContainer($customConfig = null)
	{
		$container = parent::createContainer($customConfig);

		$this->favorites = $container->getByType('Carrooi\Favorites\Model\Facades\FavoriteItemsFacade');
		$this->articles = $container->getByType('CarrooiTests\FavoritesApp\Model\Facades\ArticlesFacade');
		$this->users = $container->getByType('CarrooiTests\FavoritesApp\Model\Facades\UsersFacade');

		return $container;
	}


	public function testCustomAssociation()
	{
		$this->database = 'associations';
		$this->createContainer('config.associations');

		$user = $this->users->create();
		$article = $this->articles->create();

		$favorite = $this->favorites->addItemToFavorites($user, $article);		/** @var \CarrooiTests\FavoritesApp\Model\Entities\CustomFavoriteItem $favorite */

		Assert::type('CarrooiTests\FavoritesApp\Model\Entities\Article', $favorite->getArticle());
		Assert::same($article->getId(), $favorite->getArticle()->getId());
	}

}


run(new FavoriteItemTest);
