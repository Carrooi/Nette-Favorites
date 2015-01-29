<?php

namespace Carrooi\Favorites\DI;

use Carrooi\Favorites\InvalidArgumentException;
use Kdyby\Doctrine\DI\IEntityProvider;
use Kdyby\Doctrine\DI\ITargetEntityProvider;
use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;

/**
 *
 * @author David Kudera
 */
class FavoritesExtension extends CompilerExtension implements IEntityProvider, ITargetEntityProvider
{


	/** @var array */
	private $defaults = [
		'userClass' => null,
		'favoriteItemClass' => 'Carrooi\Favorites\Model\Entities\DefaultFavoriteItem',
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

		$builder->addDefinition($this->prefix('events.relations'))
			->setClass('Carrooi\Favorites\Model\Events\FavoritesRelationSubscriber')
			->addTag(EventsExtension::TAG_SUBSCRIBER);

		$builder->addDefinition($this->prefix('facade.favorites'))
			->setClass('Carrooi\Favorites\Model\Facades\FavoriteItemsFacade')
			->setArguments([$this->favoriteItemClass]);
	}


	/**
	 * @return array
	 */
	function getEntityMappings()
	{
		return [
			'Carrooi\Favorites\Model\Entities' => __DIR__. '/../Model/Entities',
		];
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
