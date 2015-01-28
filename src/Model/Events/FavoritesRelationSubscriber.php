<?php

namespace Carrooi\Favorites\Model\Events;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Kdyby\Events\Subscriber;

/**
 *
 * @author David Kudera
 */
class FavoritesRelationSubscriber implements Subscriber
{


	const FAVORITABLE_INTERFACE = 'Carrooi\Favorites\Model\Entities\IFavoritableEntity';


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

		if (!in_array(self::FAVORITABLE_INTERFACE, class_implements($metadata->getName()))) {
			return;
		}

		$namingStrategy = $eventArgs->getEntityManager()->getConfiguration()->getNamingStrategy();

		$metadata->mapManyToMany([
			'targetEntity' => 'Carrooi\Favorites\Model\Entities\FavoriteItem',
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
