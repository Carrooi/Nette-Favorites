<?php

namespace CarrooiTests\FavoritesApp\Model\Facades;

use CarrooiTests\FavoritesApp\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 *
 * @author David Kudera
 */
class UsersFacade extends Object
{


	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;


	/**
	 * @param \Kdyby\Doctrine\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->dao = $em->getRepository(User::getClassName());
	}


	/**
	 * @return \CarrooiTests\FavoritesApp\Model\Entities\User
	 */
	public function create()
	{
		$user = new User;

		$this->dao->getEntityManager()->persist($user)->flush();

		return $user;
	}

}
