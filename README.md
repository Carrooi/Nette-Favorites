# Carrooi/Favorites

[![Build Status](https://img.shields.io/travis/Carrooi/Nette-Favorites.svg?style=flat-square)](https://travis-ci.org/Carrooi/Nette-Favorites)
[![Donate](https://img.shields.io/badge/donate-PayPal-brightgreen.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BQN5R3E85DJRS)

Favorites module in Doctrine for Nette framework.

## Installation

```
$ composer require carrooi/favorites
$ composer update
```

Then just enable nette extension in your config.neon:

```neon
extensions:
	favorites: Carrooi\Favorites\DI\FavoritesExtension
```

## Configuration

```neon
extensions:
	favorites: Carrooi\Favorites\DI\FavoritesExtension

favorites:
	
	userClass: App\Model\Entities\User
```

As you can see, the only thing you need to do is set your `user` class which implements 
`Carrooi\Favorites\Model\Entities\IUserEntity` interface.

## Usage

Lets create our `User` implementation.

```php
namespace App\Model\Entities;

use Carrooi\Favorites\Model\Entities\IUserEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author David Kudera
 */
class User implements IUserEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var int
	 */
	private $id;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

}
```

Now imagine that you want to be able to add entity `Article` to favorites.

```php
namespace App\Model\Entities;

use Carrooi\Favorites\Model\Entities\IFavoritableEntity;
use Carrooi\Favorites\Model\Entities\TFavorites;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author David Kudera
 */
class Article implements IFavoritableEntity
{

	use TFavorites;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var int
	 */
	private $id;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

}
```

Please notice that you can use `TFavorites` trait, which implements all methods from `IFavoritableEntity` interface.

**Do not forget to update your database schema after every change.**

### Manipulation

You can use prepared `Carrooi\Favorites\Model\Facades\FavoritesFacade` service for manipulations with favorites.

#### Add to favorites

```php
$article = $this->articles->createSomehow();
$user = $this->users->getCurrentSomehow();

$favoritesFacade->addItemToFavorites($user, $article);
```

#### Remove from favorites

```php
$article = $this->articles->getCurrentSomehow();
$user = $this->users->getCurrentSomehow();

$favoritesFacade->removeItemFromFavorites($user, $article);
```

#### Is item in favorites

```php
$article = $this->articles->getCurrentSomehow();
$user = $this->users->getCurrentSomehow();

$favoritesFacade->hasItemInFavorites($user, $article);
```

#### Find all items by user and type

```php
$user = $this->user->getCurrentSomehow();

$favoritesFacade->findAllItemsByUserAndType($user, Article::getClassName());
```

#### Find all by user and type

Similar to previous method, but will return `FavoriteItem` entities, not `IFavoritableEntity`.

```php
$user = $this->user->getCurrentSomehow();

$favoritesFacade->findAllByUserAndType($user, Article::getClassName());
```

**That method can be used only in combination with custom associations. See bellow**

#### Find all favorites by user

```php
$user = $this->user->getCurrentSomehow();

$favoritesFacade->findAllByUser($user);
```

**That method can be used only in combination with custom associations. See bellow**

#### Count by user

```php
$user = $this->user->getCurrentSomehow();

$favoritesFacade->getCountByUser($user);
```

## Custom FavoriteItem entity

```neon
favorites:

	userClass: App\Model\Entities\User
	favoriteItemClass: App\Model\Entities\FavoriteItem
```

```php
namespace App\Model\Entities;

use Carrooi\Favorites\Model\Entities\FavoriteItem;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author David Kudera
 */
class FavoriteItem extends FavoriteItem
{

	// ...
	
}
```

This will come in handy when you'll want to use `FavoriteItem` entity in your queries with `JOIN`.

Just imagine that you want to have eg. `getArticle()` method in `FavoriteItem` entity.

```php
namespace App\Model\Entities;

use Carrooi\Favorites\Model\Entities\FavoriteItem;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author David Kudera
 */
class FavoriteItem extends FavoriteItem
{

	/** @var \App\Model\Entities\Article */
	private $article;

	/**
	 * @return \App\Model\Entities\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}

	/**
	 * @param \App\Model\Entities\Article $article
	 * @return $this
	 */
	public function setArticle(Article $article)
	{
		$this->article = $article;
		return $this;
	}
	
}
```

And add configuration:

```neon
favorites:

	userClass: App\Model\Entities\User
	favoriteItemClass: App\Model\Entities\FavoriteItem

	associations:
		App\Model\Entities\Article:
			field: article
			setter: setArticle
```

Now you have your own implementation of `FavoriteItem` entity.

**Please also notice that if you'll use this custom association mapping, this module will work with one-to-many relations.
Otherwise it will be many-to-many.**

## Changelog

* 1.0.2
	+ Add missing cascade removing for user [#1](https://github.com/Carrooi/Nette-Favorites/pull/1)

* 1.0.1
	+ Fixed tests running under nette 2.3
	+ Fix relations mapping

* 1.0.0
	+ First version
