<?php
namespace Librette\Doctrine\Sortable;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author David Matejka
 */
trait TSortable
{

	/**
	 * @var int
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $position;


	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}


	/**
	 * @param int
	 */
	public function setPosition($position)
	{
		$this->position = $position;
	}


	public function moveUp()
	{
		$this->position = max(1, $this->position - 1);
	}


	public function moveDown()
	{
		$this->position++;
	}


	public function moveAfter(ISortable $target)
	{
		if ($this->position < $target->getPosition()) {
			$this->position = $target->getPosition();
		} else {
			$this->position = $target->getPosition() + 1;
		}
	}


	public function moveBefore(ISortable $target)
	{
		if ($this->position < $target->getPosition()) {
			$this->position = $target->getPosition() - 1;
		} else {
			$this->position = $target->getPosition();
		}
	}

}
