<?php

/**
 * Test: Carrooi\Favorites\Model\Entities\FavoriteItem
 *
 * @testCase CarrooiTests\Favorites\Model\Entities\FavoriteItem
 * @author David Kudera
 */

namespace CarrooiTests\Favorites\Model\Entities;

use CarrooiTests\Favorites\TestCase;
use CarrooiTests\FavoritesApp\Model\Entities\Article;
use Tester\Assert;
use Tester\FileMock;

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
		$this->createContainer(__DIR__. '/../config.associations.neon');

		$user = $this->users->create();
		$article = $this->articles->create();

		$favorite = $this->favorites->addItemToFavorites($user, $article);		/** @var \CarrooiTests\FavoritesApp\Model\Entities\CustomFavoriteItem $favorite */

		Assert::count(1, $favorite->getArticles());
		Assert::type('CarrooiTests\FavoritesApp\Model\Entities\Article', $favorite->getArticles()[0]);
		Assert::same($article->getId(), $favorite->getArticles()[0]->getId());
	}

}


run(new FavoriteItemTest);
