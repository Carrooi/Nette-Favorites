<?php

namespace Carrooi\Favorites\Model\Facades;

use Carrooi\Favorites\InvalidArgumentException;
use Carrooi\Favorites\ItemAlreadyInFavorites;
use Carrooi\Favorites\ItemNotInFavorites;
use Carrooi\Favorites\Model\Entities\IFavoritableEntity;
use Carrooi\Favorites\Model\Entities\IUserEntity;
use Doctrine\ORM\Query\ResultSetMapping;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Nette\Object;

/**
 *
 * @author David Kudera
 */
class FavoriteItemsFacade extends Object
{


	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;

	/** @var \Carrooi\Favorites\Model\Facades\AssociationsManager */
	private $associationsManager;

	/** @var string */
	private $class;


	/**
	 * @param string $class
	 * @param \Kdyby\Doctrine\EntityManager $em
	 * @param \Carrooi\Favorites\Model\Facades\AssociationsManager $associationsManager
	 */
	public function __construct($class, EntityManager $em, AssociationsManager $associationsManager)
	{
		$this->dao = $em->getRepository('Carrooi\Favorites\Model\Entities\IFavoriteItemEntity');
		$this->associationsManager = $associationsManager;
		$this->class = $class;
	}


	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}


	/**
	 * @return \Carrooi\Favorites\Model\Entities\IFavoriteItemEntity
	 */
	public function createEntity()
	{
		$class = $this->getClass();
		return new $class;
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @param \Carrooi\Favorites\Model\Entities\IFavoritableEntity $item
	 * @return \Carrooi\Favorites\Model\Entities\IFavoriteItemEntity
	 */
	public function addItemToFavorites(IUserEntity $user, IFavoritableEntity $item)
	{
		if ($this->hasItemInFavorites($user, $item)) {
			throw new ItemAlreadyInFavorites('User '. $user->getId(). ' already has item '. get_class($item). '('. $item->getId(). ') in favorites.');
		}

		$favorite = $this->createEntity();
		$favorite->setUser($user);

		$item->addFavorite($favorite);

		$class = get_class($item);
		if ($this->associationsManager->hasAssociation($class) && ($setter = $this->associationsManager->getSetter($class))) {
			$favorite->$setter($item);
		}

		$this->dao->getEntityManager()->persist([
			$favorite, $item,
		])->flush();

		return $favorite;
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @param \Carrooi\Favorites\Model\Entities\IFavoritableEntity $item
	 * @return bool
	 */
	public function hasItemInFavorites(IUserEntity $user, IFavoritableEntity $item)
	{
		return $this->findOneByUserAndItem($user, $item) !== null;
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @param \Carrooi\Favorites\Model\Entities\IFavoritableEntity $item
	 * @return $this
	 */
	public function removeItemFromFavorites(IUserEntity $user, IFavoritableEntity $item)
	{
		$favorite = $this->findOneByUserAndItem($user, $item);
		if (!$favorite) {
			throw new ItemNotInFavorites('User '. $user->getId(). ' has not got item '. get_class($item). '('. $item->getId(). ') in favorites.');
		}

		$item->removeFavorite($favorite);

		$this->dao->getEntityManager()->remove($favorite)->flush();

		return $this;
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @param \Carrooi\Favorites\Model\Entities\IFavoritableEntity $item
	 * @return \Carrooi\Favorites\Model\Entities\IFavoriteItemEntity
	 */
	public function findOneByUserAndItem(IUserEntity $user, IFavoritableEntity $item)
	{
		if ($this->associationsManager->hasAssociation($class = get_class($item))) {
			$field = $this->associationsManager->getField($class);

			return $this->dao->createQueryBuilder('f')
				->andWhere('f.user = :user')->setParameter('user', $user)
				->andWhere('f.'. $field. ' = :item')->setParameter('item', $item)
				->getQuery()
				->getOneOrNullResult();
		} else {
			$rsm = new ResultSetMapping;
			$rsm->addEntityResult('Carrooi\Favorites\Model\Entities\IFavoriteItemEntity', 'f');
			$rsm->addFieldResult('f', 'id', 'id');
			$rsm->addMetaResult('f', 'user_id', 'user');

			$favoriteTable = $this->dao->getEntityManager()->getClassMetadata('Carrooi\Favorites\Model\Entities\IFavoriteItemEntity')->getTableName();
			$articleMetadata = $this->dao->getEntityManager()->getClassMetadata(get_class($item))->getAssociationMapping('favorites');

			$joinTable = $articleMetadata['joinTable']['name'];
			$joinColumn = $articleMetadata['joinTable']['joinColumns'][0]['name'];

			$sql = "SELECT f.id, f.user_id FROM $favoriteTable AS f "
				. "INNER JOIN $joinTable AS i ON i.favorite_id = f.id "
				. "WHERE f.user_id = :userId AND i.$joinColumn = :itemId";

			$query = $this->dao->createNativeQuery($sql, $rsm);
			$query->setParameter('userId', $user->getId());
			$query->setParameter('itemId', $item->getId());

			return $query->getOneOrNullResult();
		}
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @param string $itemClass
	 * @return \Kdyby\Doctrine\ResultSet|\Carrooi\Favorites\Model\Entities\IFavoritableEntity[]
	 */
	public function findAllItemsByUserAndType(IUserEntity $user, $itemClass)
	{
		if (!in_array('Carrooi\Favorites\Model\Entities\IFavoritableEntity', class_implements($itemClass))) {
			throw new InvalidArgumentException('Could not find favorite items for '. $itemClass. ' entity.');
		}

		$dql = $this->dao->createQueryBuilder()
			->select('i')->from($itemClass, 'i')
			->join('i.favorites', 'f')
			->andWhere('f.user = :user')->setParameter('user', $user);

		return new ResultSet($dql->getQuery());
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @return \Kdyby\Doctrine\ResultSet|\Carrooi\Favorites\Model\Entities\IFavoriteItemEntity[]
	 */
	public function findAllByUser(IUserEntity $user)
	{
		$dql = $this->dao->createQueryBuilder('f')
			->andWhere('f.user = :user')->setParameter('user', $user);

		foreach ($this->associationsManager->getAssociations() as $class => $options) {
			$dql
				->leftJoin('f.'. $options['field'], $options['field'])
				->addSelect($options['field']);
		}

		return new ResultSet($dql->getQuery());
	}

}
