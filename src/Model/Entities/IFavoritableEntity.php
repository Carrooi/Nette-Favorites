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
	 * @param \Carrooi\Favorites\Model\Entities\IFavoriteItemEntity $favorite
	 * @return $this
	 */
	public function addFavorite(IFavoriteItemEntity $favorite);


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IFavoriteItemEntity $favorite
	 * @return $this
	 */
	public function removeFavorite(IFavoriteItemEntity $favorite);


	/**
	 * @return \Carrooi\Favorites\Model\Entities\IFavoriteItemEntity[]
	 */
	public function getFavorites();

}
