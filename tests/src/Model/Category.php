<?php
namespace LibretteTests\Doctrine\Sortable\Model;

use Doctrine\ORM\Mapping as ORM;
use Librette\Doctrine\Sortable\ISortable;
use Librette\Doctrine\Sortable\ISortableScope;
use Librette\Doctrine\Sortable\TSortable;

/**
 * @ORM\Entity
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 * @ORM\DiscriminatorMap({"category": "Category", "described": "DescribedCategory"})
 * @ORM\DiscriminatorColumn(name="type")
 */
class Category implements ISortable, ISortableScope
{

	use TSortable;

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $name;

	protected $sortableScope = [];


	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return array
	 */
	public function getSortableScope()
	{
		return $this->sortableScope;
	}


	/**
	 * @param array
	 */
	public function setSortableScope($sortableScope)
	{
		$this->sortableScope = $sortableScope;
	}


}
