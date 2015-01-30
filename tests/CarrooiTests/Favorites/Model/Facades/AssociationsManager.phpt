<?php

/**
 * Test: Carrooi\Favorites\Model\Facades\AssociationsManager
 *
 * @testCase CarrooiTests\Favorites\Model\Facades\AssociationsManagerTest
 * @author David Kudera
 */

namespace CarrooiTests\Favorites\Model\Facades;

use Carrooi\Favorites\Model\Facades\AssociationsManager;
use CarrooiTests\Favorites\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class AssociationsManagerTest extends TestCase
{


	public function testGetRealClass()
	{
		$manager = new AssociationsManager;
		$manager->addAssociation('Nette\Object', 'object');

		$class = 'Carrooi\Favorites\Model\Facades\FavoriteItemsFacade';

		Assert::same($class, $manager->getRealClass($class));

		Assert::notSame(null, $manager->getAssociation($class));
		Assert::same('object', $manager->getField($class));
	}

}


run(new AssociationsManagerTest);
