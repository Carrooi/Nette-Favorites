<?php

namespace Carrooi\Favorites\Model\Entities;

/**
 *
 * @author David Kudera
 */
interface IFavoritableEntity
{


	/**
	 * @return int
	 */
	public function getId();


	/**
	 * @param \Carrooi\Favorites\Model\Entities\FavoriteItem $favorite
	 * @return $this
	 */
	public function addFavorite(FavoriteItem $favorite);


	/**
	 * @param \Carrooi\Favorites\Model\Entities\FavoriteItem $favorite
	 * @return $this
	 */
	public function removeFavorite(FavoriteItem $favorite);


	/**
	 * @return \Carrooi\Favorites\Model\Entities\FavoriteItem[]
	 */
	public function getFavorites();

}
