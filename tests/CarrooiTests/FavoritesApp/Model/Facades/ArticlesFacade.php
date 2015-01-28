<?php

namespace CarrooiTests\FavoritesApp\Model\Facades;

use CarrooiTests\FavoritesApp\Model\Entities\Article;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 *
 * @author David Kudera
 */
class ArticlesFacade extends Object
{


	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;


	/**
	 * @param \Kdyby\Doctrine\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->dao = $em->getRepository(Article::getClassName());
	}


	/**
	 * @return \CarrooiTests\FavoritesApp\Model\Entities\Article
	 */
	public function create()
	{
		$article = new Article;

		$this->dao->getEntityManager()->persist($article)->flush();

		return $article;
	}

}
