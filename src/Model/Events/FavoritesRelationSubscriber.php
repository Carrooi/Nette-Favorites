<?php

namespace Carrooi\Favorites\Model\Events;

use Carrooi\Favorites\Model\Facades\AssociationsManager;
use Carrooi\Favorites\Model\Facades\FavoriteItemsFacade;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Kdyby\Events\Subscriber;

/**
 *
 * @author David Kudera
 */
class FavoritesRelationSubscriber implements Subscriber
{


	/** @var \Carrooi\Favorites\Model\Facades\AssociationsManager */
	private $associationsManager;

	/** @var string */
	private $favoriteItemClass;


	/**
	 * @param string $favoriteItemClass
	 * @param \Carrooi\Favorites\Model\Facades\AssociationsManager $associationsManager
	 */
	public function __construct($favoriteItemClass, AssociationsManager $associationsManager)
	{
		$this->associationsManager = $associationsManager;
		$this->favoriteItemClass = $favoriteItemClass;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return [
			Events::loadClassMetadata => 'loadClassMetadata',
		];
	}


	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		$metadata = $eventArgs->getClassMetadata();			/** @var \Kdyby\Doctrine\Mapping\ClassMetadata $metadata */

		if (in_array('Carrooi\Favorites\Model\Entities\IFavoritableEntity', class_implements($metadata->getName()))) {
			$namingStrategy = $eventArgs->getEntityManager()->getConfiguration()->getNamingStrategy();

			$metadata->mapManyToMany([
				'targetEntity' => 'Carrooi\Favorites\Model\Entities\FavoriteItem',
				'fieldName' => 'favorites',
				'inversedBy' => $this->associationsManager->hasAssociation($metadata->getName()) ? $this->associationsManager->getField($metadata->getName()) : null,
				'joinTable' => [
					'name' => strtolower($namingStrategy->classToTableName($metadata->getName())). '_favorite_item',
					'joinColumns' => [
						[
							'name' => $namingStrategy->joinKeyColumnName($metadata->getName()),
							'referencedColumnName' => $namingStrategy->referenceColumnName(),
							'onDelete' => 'CASCADE',
							'onUpdate' => 'CASCADE',
						],
					],
					'inverseJoinColumns' => [
						[
							'name' => 'favorite_id',
							'referencedColumnName' => $namingStrategy->referenceColumnName(),
							'onDelete' => 'CASCADE',
							'onUpdate' => 'CASCADE',
						],
					],
				],
			]);

		} elseif ($metadata->getName() === $this->favoriteItemClass) {
			foreach ($this->associationsManager->getAssociations() as $className => $options) {
				$metadata->mapManyToMany([
					'targetEntity' => $className,
					'fieldName' => $options['field'],
					'mappedBy' => 'favorites',
				]);
			}

		}
	}

}
