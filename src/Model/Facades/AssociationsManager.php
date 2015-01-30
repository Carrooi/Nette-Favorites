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
	 * @param string $addMethod
	 * @param string $removeMethod
	 * @return $this
	 */
	public function addAssociation($className, $field, $addMethod = null, $removeMethod = null)
	{
		$this->associations[$className] = [
			'field' => $field,
			'addMethod' => $addMethod,
			'removeMethod' => $removeMethod,
		];

		return $this;
	}


	/**
	 * @param string $className
	 * @return bool
	 */
	public function hasAssociation($className)
	{
		return isset($this->associations[$className]);
	}


	/**
	 * @param string $className
	 * @return string
	 */
	public function getAssociation($className)
	{
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
	public function getAddMethod($className)
	{
		$association = $this->getAssociation($className);
		if (!$association) {
			throw new InvalidStateException('Association '. $className. ' is not registered.');
		}

		return $association['addMethod'];
	}


	/**
	 * @param string $className
	 * @return string
	 */
	public function getRemoveMethod($className)
	{
		$association = $this->getAssociation($className);
		if (!$association) {
			throw new InvalidStateException('Association '. $className. ' is not registered.');
		}

		return $association['removeMethod'];
	}

}
