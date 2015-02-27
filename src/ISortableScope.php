<?php
namespace Librette\Doctrine\Sortable;

/**
 * @author David Matejka
 */
interface ISortableScope
{

	/**
	 * @return array of field or association names
	 */
	public function getSortableScope();

}
