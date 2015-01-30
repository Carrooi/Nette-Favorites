<?php

namespace Carrooi\Favorites\DI;

use Carrooi\Favorites\InvalidArgumentException;
use Kdyby\Doctrine\DI\IEntityProvider;
use Kdyby\Doctrine\DI\ITargetEntityProvider;
use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers;

/**
 *
 * @author David Kudera
 */
class FavoritesExtension extends CompilerExtension implements IEntityProvider, ITargetEntityProvider
{


	/** @var array */
	private $defaults = [
		'userClass' => null,
		'favoriteItemClass' => 'Carrooi\Favorites\Model\DefaultEntities\DefaultFavoriteItem',
		'associations' => [],
	];

	/** @var array */
	private $associationDefaults = [
		'field' => null,
		'setterMethod' => null,
	];

	/** @var string */
	private $userClass;

	/** @var string */
	private $favoriteItemClass;


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if (!$config['userClass']) {
			throw new InvalidArgumentException('Please set your user class name');
		}

		$this->userClass = $config['userClass'];
		$this->favoriteItemClass = $config['favoriteItemClass'];

		if (!empty($config['associations']) && $this->favoriteItemClass === 'Carrooi\Favorites\Model\DefaultEntities\DefaultFavoriteItem') {
			throw new InvalidArgumentException('Can not use custom associations for default favorite entity.');
		}

		$associations = $builder->addDefinition($this->prefix('facade.associations'))
			->setClass('Carrooi\Favorites\Model\Facades\AssociationsManager');

		foreach ($config['associations'] as $className => $options) {
			if (is_string($options)) {
				$options = ['field' => $options];
			}

			$options = Helpers::merge($options, $this->associationDefaults);

			$associations->addSetup('addAssociation', [$className, $options['field'], $options['setter']]);
		}

		$builder->addDefinition($this->prefix('facade.favorites'))
			->setClass('Carrooi\Favorites\Model\Facades\FavoriteItemsFacade')
			->setArguments([$this->favoriteItemClass]);

		$builder->addDefinition($this->prefix('events.relations'))
			->setClass('Carrooi\Favorites\Model\Events\FavoritesRelationSubscriber')
			->setArguments([$this->favoriteItemClass])
			->addTag(EventsExtension::TAG_SUBSCRIBER);
	}


	/**
	 * @return array
	 */
	function getEntityMappings()
	{
		$mapping = [
			'Carrooi\Favorites\Model\Entities' => __DIR__. '/../Model/Entities',
		];

		if ($this->favoriteItemClass === 'Carrooi\Favorites\Model\DefaultEntities\DefaultFavoriteItem') {
			$mapping['Carrooi\Favorites\Model\DefaultEntities'] = __DIR__. '/../Model/DefaultEntities';
		}

		return $mapping;
	}


	/**
	 * @return array
	 */
	function getTargetEntityMappings()
	{
		return [
			'Carrooi\Favorites\Model\Entities\IUserEntity' => $this->userClass,
			'Carrooi\Favorites\Model\Entities\IFavoriteItemEntity' => $this->favoriteItemClass,
		];
	}

}
