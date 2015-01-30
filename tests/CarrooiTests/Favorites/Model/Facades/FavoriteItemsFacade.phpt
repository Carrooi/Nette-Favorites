<?php

/**
 * Test: Carrooi\Favorites\Model\Facades\FavoriteItemsFacade
 *
 * @testCase CarrooiTests\Favorites\Model\Facades\FavoriteItemsFacadeTest
 * @author David Kudera
 */

namespace CarrooiTests\Favorites\Model\Facades;

use CarrooiTests\Favorites\TestCase;
use CarrooiTests\FavoritesApp\Model\Entities\Article;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class FavoriteItemsFacadeTest extends TestCase
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


	public function testAddItemToFavorites()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		$favorite = $this->favorites->addItemToFavorites($user, $article);

		Assert::same(1, $favorite->getId());
		Assert::same($user, $favorite->getUser());
		Assert::same($article->getFavorites(), [$favorite]);
	}


	public function testAddItemToFavorites_alreadyExists()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		$this->favorites->addItemToFavorites($user, $article);

		Assert::exception(function() use ($user, $article) {
			$this->favorites->addItemToFavorites($user, $article);
		}, 'Carrooi\Favorites\ItemAlreadyInFavorites', 'User 1 already has item CarrooiTests\FavoritesApp\Model\Entities\Article(1) in favorites.');
	}


	public function testFindOneByUserAndItem_notExists()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		Assert::null($this->favorites->findOneByUserAndItem($user, $article));
	}


	public function testFindOneByUserAndItem_exists()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		$favorite = $this->favorites->addItemToFavorites($user, $article);
		$found = $this->favorites->findOneByUserAndItem($user, $article);

		Assert::notSame(null, $found);
		Assert::same($favorite->getId(), $found->getId());
	}


	public function testFindOneByUserAndItem_customAssociation()
	{
		$this->database = 'associations';
		$this->createContainer('config.associations');

		$article = $this->articles->create();
		$user = $this->users->create();

		$favorite = $this->favorites->addItemToFavorites($user, $article);
		$found = $this->favorites->findOneByUserAndItem($user, $article);

		Assert::notSame(null, $found);
		Assert::same($favorite->getId(), $found->getId());

		$article = $this->articles->create();
		$favorite = $this->favorites->addItemToFavorites($user, $article);
		$found = $this->favorites->findOneByUserAndItem($user, $article);

		Assert::same($favorite->getId(), $found->getId());
	}


	public function testHasInFavorites_false()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		Assert::false($this->favorites->hasItemInFavorites($user, $article));
	}


	public function testHasInFavorites_true()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		$this->favorites->addItemToFavorites($user, $article);

		Assert::true($this->favorites->hasItemInFavorites($user, $article));
	}


	public function testRemoveItemFromFavorites_notExists()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		Assert::exception(function() use ($user, $article) {
			$this->favorites->removeItemFromFavorites($user, $article);
		}, 'Carrooi\Favorites\ItemNotInFavorites', 'User 1 has not got item CarrooiTests\FavoritesApp\Model\Entities\Article(1) in favorites.');
	}


	public function testRemoveItemFromFavorites()
	{
		$this->createContainer();

		$article = $this->articles->create();
		$user = $this->users->create();

		$this->favorites->addItemToFavorites($user, $article);

		$this->favorites->removeItemFromFavorites($user, $article);

		Assert::false($this->favorites->hasItemInFavorites($user, $article));
		Assert::count(0, $article->getFavorites());
	}


	public function testFindAllItemsByUserAndType()
	{
		$this->createContainer();

		$user = $this->users->create();

		for ($i = 0; $i < 5; $i++) {
			$article = $this->articles->create();
			$this->favorites->addItemToFavorites($user, $article);
		}

		$this->favorites->addItemToFavorites($this->users->create(), $this->articles->create());

		$favorites = $this->favorites->findAllItemsByUserAndType($user, Article::getClassName());

		Assert::count(5, $favorites);
	}


	public function testFindAllItemsByUserAndType_invalid()
	{
		$this->createContainer();

		Assert::exception(function() {
			$this->favorites->findAllItemsByUserAndType($this->users->create(), 'Nette\Object');
		}, 'Carrooi\Favorites\InvalidArgumentException', 'Could not find favorite items for Nette\Object entity.');
	}


	public function testFindAllByUserAndType_invalid()
	{
		$this->createContainer();

		Assert::exception(function() {
			$this->favorites->findAllByUserAndType($this->users->create(), 'Nette\Object');
		}, 'Carrooi\Favorites\InvalidArgumentException', 'Could not find favorite items for Nette\Object entity.');
	}


	public function testFindAllByUserAndType_missingCustomAssociation()
	{
		$this->createContainer();

		Assert::exception(function() {
			$this->favorites->findAllByUserAndType($this->users->create(), 'CarrooiTests\FavoritesApp\Model\Entities\Article');
		}, 'Carrooi\Favorites\InvalidArgumentException', 'Please register custom associations for CarrooiTests\FavoritesApp\Model\Entities\Article class.');
	}


	public function testFindAllByUserAndType()
	{
		$this->database = 'associations';
		$this->createContainer('config.associations');

		$user = $this->users->create();

		$articles = [
			$this->articles->create(),
			$this->articles->create(),
			$this->articles->create(),
		];

		$this->favorites->addItemToFavorites($user, $articles[0]);
		$this->favorites->addItemToFavorites($user, $articles[1]);
		$this->favorites->addItemToFavorites($user, $articles[2]);

		$this->favorites->addItemToFavorites($this->users->create(), $this->articles->create());

		$favorites = $this->favorites->findAllByUserAndType($user, 'CarrooiTests\FavoritesApp\Model\Entities\Article')->toArray();

		Assert::count(3, $favorites);
		Assert::same($articles[0]->getId(), $favorites[0]->getArticle()->getId());
		Assert::same($articles[1]->getId(), $favorites[1]->getArticle()->getId());
		Assert::same($articles[2]->getId(), $favorites[2]->getArticle()->getId());
	}


	public function testFindAllByUser()
	{
		$this->database = 'associations';
		$this->createContainer('config.associations');

		$user = $this->users->create();

		$articles = [
			$this->articles->create(),
			$this->articles->create(),
			$this->articles->create(),
		];

		$this->favorites->addItemToFavorites($user, $articles[0]);
		$this->favorites->addItemToFavorites($user, $articles[1]);
		$this->favorites->addItemToFavorites($user, $articles[2]);

		$this->favorites->addItemToFavorites($this->users->create(), $this->articles->create());

		$favorites = $this->favorites->findAllByUser($user)->toArray();

		Assert::count(3, $favorites);
		Assert::same($articles[0]->getId(), $favorites[0]->getArticle()->getId());
		Assert::same($articles[1]->getId(), $favorites[1]->getArticle()->getId());
		Assert::same($articles[2]->getId(), $favorites[2]->getArticle()->getId());
	}

}


run(new FavoriteItemsFacadeTest);
