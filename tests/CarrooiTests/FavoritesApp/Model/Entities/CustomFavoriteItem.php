<?php

namespace CarrooiTests\FavoritesApp\Model\Entities;

use Carrooi\Favorites\Model\Entities\FavoriteItem;
use Doctrine\Common\Collections\ArrayCollection;
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


	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	private $articles;


	public function __construct()
	{
		$this->articles = new ArrayCollection;
	}


	/**
	 * @return \CarrooiTests\FavoritesApp\Model\Entities\Article[]
	 */
	public function getArticles()
	{
		return $this->articles->toArray();
	}


	/**
	 * @param \CarrooiTests\FavoritesApp\Model\Entities\Article $article
	 * @return $this
	 */
	public function addArticle(Article $article)
	{
		$this->articles->add($article);
		return $this;
	}


	/**
	 * @param \CarrooiTests\FavoritesApp\Model\Entities\Article $article
	 * @return $this
	 */
	public function removeArticle(Article $article)
	{
		$this->articles->removeElement($article);
		return $this;
	}

}
