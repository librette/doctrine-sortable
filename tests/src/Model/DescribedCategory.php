<?php
namespace LibretteTests\Doctrine\Sortable\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class DescribedCategory extends Category
{

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $description;

}
