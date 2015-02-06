<?php
namespace LibretteTests\Doctrine\Sortable\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;
use Librette\Doctrine\Sortable\ISortable;
use Librette\Doctrine\Sortable\TSortable;

/**
 * @ORM\Entity
 */
class Category extends BaseEntity implements ISortable
{

	use Identifier;
	use TSortable;


	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $name;


	public function __construct($name)
	{
		$this->name = $name;
	}

}