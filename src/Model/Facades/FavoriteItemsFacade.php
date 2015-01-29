<?php

namespace Carrooi\Favorites\Model\Facades;

use Carrooi\Favorites\InvalidArgumentException;
use Carrooi\Favorites\ItemAlreadyInFavorites;
use Carrooi\Favorites\ItemNotInFavorites;
use Carrooi\Favorites\Model\Entities\FavoriteItem;
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


	/**
	 * @param \Kdyby\Doctrine\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->dao = $em->getRepository(FavoriteItem::getClassName());
	}


	/**
	 * @param \Carrooi\Favorites\Model\Entities\IUserEntity $user
	 * @param \Carrooi\Favorites\Model\Entities\IFavoritableEntity $item
	 * @return \Carrooi\Favorites\Model\Entities\FavoriteItem
	 */
	public function addItemToFavorites(IUserEntity $user, IFavoritableEntity $item)
	{
		if ($this->hasItemInFavorites($user, $item)) {
			throw new ItemAlreadyInFavorites('User '. $user->getId(). ' already has item '. get_class($item). '('. $item->getId(). ') in favorites.');
		}

		$favorite = new FavoriteItem;
		$favorite->setUser($user);

		$item->addFavorite($favorite);

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
	 * @return \Carrooi\Favorites\Model\Entities\FavoriteItem
	 */
	public function findOneByUserAndItem(IUserEntity $user, IFavoritableEntity $item)
	{
		$rsm = new ResultSetMapping;
		$rsm->addEntityResult(FavoriteItem::getClassName(), 'f');
		$rsm->addFieldResult('f', 'id', 'id');
		$rsm->addMetaResult('f', 'user_id', 'user');

		$favoriteTable = $this->dao->getEntityManager()->getClassMetadata(FavoriteItem::getClassName())->getTableName();
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

}
