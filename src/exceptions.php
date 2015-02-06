<?php
namespace Librette\Doctrine\Sortable;

/**
 * @author David Matejka
 */
class NotCompatibleNodesException extends \RuntimeException
{

	public static function differentClass(ISortable $a, ISortable $b)
	{
		return new static('Sortable nodes must be the same type. (' . get_class($a) . ' vs ' . get_class($b) . ')');
	}


	public static function differentScope()
	{
		return new static('Sortable nodes must have same scope.');
	}
}


class InvalidScopeException extends \LogicException
{

}
