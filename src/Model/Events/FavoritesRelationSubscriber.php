<?php

namespace Carrooi\Favorites\Model\Events;

use Carrooi\Favorites\Model\Facades\AssociationsManager;
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


	/**
	 * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs
	 */
	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		$metadata = $eventArgs->getClassMetadata();			/** @var \Kdyby\Doctrine\Mapping\ClassMetadata $metadata */

		if (in_array('Carrooi\Favorites\Model\Entities\IFavoritableEntity', class_implements($metadata->getName()))) {
			$namingStrategy = $eventArgs->getEntityManager()->getConfiguration()->getNamingStrategy();

			if ($this->associationsManager->hasAssociation($metadata->getName())) {
				$metadata->mapOneToMany([
					'targetEntity' => $this->favoriteItemClass,
					'fieldName' => 'favorites',
					'mappedBy' => $this->associationsManager->getField($metadata->getName()),
				]);
			} else {
				$metadata->mapManyToMany([
					'targetEntity' => $this->favoriteItemClass,
					'fieldName' => 'favorites',
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
			}

		}

		if ($metadata->getName() === $this->favoriteItemClass) {
			foreach ($this->associationsManager->getAssociations() as $className => $options) {
				$metadata->mapManyToOne([
					'targetEntity' => $className,
					'fieldName' => $options['field'],
					'inversedBy' => 'favorites',
					'joinColumns' => [[
						'onDelete' => 'CASCADE',
						'onUpdate' => 'CASCADE',
					]],
				]);
			}

		}
	}

}
