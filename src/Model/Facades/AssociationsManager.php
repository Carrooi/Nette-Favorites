<?php

namespace Carrooi\Favorites\Model\Facades;

use Carrooi\Favorites\InvalidStateException;
use Nette\Object;

/**
 *
 * @author David Kudera
 */
class AssociationsManager extends Object
{


	/** @var array */
	private $associations = [];


	/**
	 * @param string $className
	 * @param string $field
	 * @param string $setter
	 * @return $this
	 */
	public function addAssociation($className, $field, $setter = null)
	{
		$this->associations[$className] = [
			'field' => $field,
			'setter' => $setter,
		];

		return $this;
	}


	/**
	 * @param string $className
	 * @return string
	 */
	public function getRealClass($className)
	{
		if (isset($this->associations[$className])) {
			return $className;
		}

		$parents = class_parents($className);

		foreach ($parents as $parent) {
			if (isset($this->associations[$parent])) {
				return $parent;
			}
		}

		return null;
	}


	/**
	 * @param string $className
	 * @return bool
	 */
	public function hasAssociation($className)
	{
		return $this->getRealClass($className) !== null;
	}


	/**
	 * @param string $className
	 * @return string
	 */
	public function getAssociation($className)
	{
		$className = $this->getRealClass($className);
		return $this->hasAssociation($className) ? $this->associations[$className] : null;
	}


	/**
	 * @return array
	 */
	public function getAssociations()
	{
		return $this->associations;
	}


	/**
	 * @param string $className
	 * @return string
	 */
	public function getField($className)
	{
		$association = $this->getAssociation($className);
		if (!$association) {
			throw new InvalidStateException('Association '. $className. ' is not registered.');
		}

		return $association['field'];
	}


	/**
	 * @param string $className
	 * @return string
	 */
	public function getSetter($className)
	{
		$association = $this->getAssociation($className);
		if (!$association) {
			throw new InvalidStateException('Association '. $className. ' is not registered.');
		}

		return $association['setter'];
	}

}
