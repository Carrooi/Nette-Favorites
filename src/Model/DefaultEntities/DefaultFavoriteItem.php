<?php

namespace Carrooi\Favorites\Model\DefaultEntities;

use Carrooi\Favorites\Model\Entities\FavoriteItem;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="favorite_item")
 *
 * @author David Kudera
 */
class DefaultFavoriteItem extends FavoriteItem
{

}
