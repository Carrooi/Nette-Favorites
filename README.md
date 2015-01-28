# Carrooi/Favorites

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

#### Find all items by user

```php
$user = $this->user->getCurrentSomehow();

$favoritesFacade->findAllItemsByUserAndType($user, Article::getClassName());
```

## Changelog

* 1.0.0
	+ First version
