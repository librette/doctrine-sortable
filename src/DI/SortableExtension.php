<?php
namespace Librette\Doctrine\Sortable\DI;

use Kdyby\Events\DI\EventsExtension;
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
		        ->setClass('Librette\Doctrine\Sortable\SortableListener')
		        ->addTag(EventsExtension::TAG_SUBSCRIBER);
	}

}
