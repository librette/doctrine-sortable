<?php
namespace Librette\Doctrine\Sortable;

/**
 * @author David Matejka
 */
interface ISortableScope extends ISortable
{

	/**
	 * @return array of field or association names
	 */
	public function getSortableScope();

}
