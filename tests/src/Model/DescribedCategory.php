<?php
namespace LibretteTests\Doctrine\Sortable\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
 */
class DescribedCategory extends Category
{
	use Identifier;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $description;

}
