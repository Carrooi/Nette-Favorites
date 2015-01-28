<?php

namespace CarrooiTests\FavoritesApp\Model\Entities;

use Carrooi\Favorites\Model\Entities\IUserEntity;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 *
 * @ORM\Entity
 *
 * @author David Kudera
 */
class User extends BaseEntity implements IUserEntity
{

	use Identifier;

}
