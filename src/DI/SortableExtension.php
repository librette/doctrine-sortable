<?php
namespace Librette\Doctrine\Sortable\DI;

use Librette\Doctrine\Sortable\SortableListener;
use Nette\DI\CompilerExtension;

/**
 * @author David Matejka
 */
class SortableExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$listener = $builder->addDefinition($this->prefix('sortableListener'))
			->setType(SortableListener::class);

		/** @var \Nette\DI\Definitions\ServiceDefinition $manager */
		$manager = $builder->getByType(\Doctrine\Common\EventManager::class);
		$manager->addSetup('addEventSubscriber', [$listener]);
	}

}
