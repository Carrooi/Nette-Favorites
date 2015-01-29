<?php

namespace Carrooi\Favorites\Model\Entities;

/**
 *
 * @author David Kudera
 */
interface IFavoriteItemEntity
{


	/**
	 * @return int
	 */
	public function getId();


	/**
	 * @return \Carrooi\Favorites\Model\Entities\IUserEntity
	 */
	public function getUser();


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @return $this
	 */
	public function setUser(IUserEntity $user);

}
