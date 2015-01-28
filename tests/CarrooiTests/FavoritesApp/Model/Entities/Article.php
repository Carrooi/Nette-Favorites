<?php

namespace CarrooiTests\FavoritesApp\Model\Entities;

use Carrooi\Favorites\Model\Entities\IFavoritableEntity;
use Carrooi\Favorites\Model\Entities\TFavorites;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 *
 * @ORM\Entity
 *
 * @author David Kudera
 */
class Article extends BaseEntity implements IFavoritableEntity
{

	use Identifier;

	use TFavorites;

}
