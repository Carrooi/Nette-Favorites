<?php

namespace CarrooiTests\FavoritesApp\Model\Entities;

use Carrooi\Favorites\Model\Entities\FavoriteItem;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="favorite_item")
 *
 * @author David Kudera
 */
class CustomFavoriteItem extends FavoriteItem
{


	/** @var \CarrooiTests\FavoritesApp\Model\Entities\Article */
	private $article;


	/**
	 * @return \CarrooiTests\FavoritesApp\Model\Entities\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}


	/**
	 * @param \CarrooiTests\FavoritesApp\Model\Entities\Article $article
	 * @return $this
	 */
	public function setArticle(Article $article = null)
	{
		$this->article = $article;
		return $this;
	}

}
