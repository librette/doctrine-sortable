<?php
namespace Librette\Doctrine\Sortable\DI;

use Kdyby\Events\DI\EventsExtension;
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
		$builder->addDefinition($this->prefix('sortableListener'))
			->setType(SortableListener::class)
			->addTag(EventsExtension::TAG_SUBSCRIBER);
	}

}
