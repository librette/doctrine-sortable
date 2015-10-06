<?php
namespace LibretteTests\Doctrine\Sortable\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;
use Librette\Doctrine\Sortable\ISortable;
use Librette\Doctrine\Sortable\ISortableScope;
use Librette\Doctrine\Sortable\TSortable;

/**
 * @ORM\Entity
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 * @ORM\DiscriminatorMap({"category": "Category", "described": "DescribedCategory"})
 * @ORM\DiscriminatorColumn(name="type")
 */
class Category extends BaseEntity implements ISortable, ISortableScope
{

	use Identifier;
	use TSortable;


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
