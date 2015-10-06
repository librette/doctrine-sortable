<?php
namespace LibretteTests\Doctrine\Sortable;

use Kdyby\Doctrine\EntityManager;
use LibretteTests\Doctrine\Sortable\Model\Category;
use LibretteTests\Doctrine\Sortable\Model\DescribedCategory;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';


/**
 * @author David Matejka
 * @testCase
 */
class SortableTestCase extends TestCase
{

	use EMTest;

	/** @var EntityManager */
	private $em;


	public function setup()
	{
		$this->em = $this->createMemoryManager(Category::class);
	}


	public function testPersist()
	{
		$categories = $this->createCategories();
		foreach ($categories as $i => $category) {
			Assert::equal($i, $category->getPosition());
		}
	}


	public function testPersistSetPosition()
	{
		$categories = $this->createCategories();
		$categories[] = $cat = new Category('New category');
		$cat->setPosition(2);
		$this->em->persist($cat);
		$this->em->flush();
		$this->refresh($categories); //todo: update entities without refresh
		Assert::same(1, $categories[1]->getPosition());
		Assert::same(3, $categories[2]->getPosition());
		Assert::same(4, $categories[3]->getPosition());
		Assert::same(5, $categories[4]->getPosition());
		Assert::same(6, $categories[5]->getPosition());
		Assert::same(7, $categories[6]->getPosition());
		Assert::same(2, $categories[7]->getPosition());
	}


	public function testPersistSetPositionMultiple()
	{
		//failing
		$categories = $this->createCategories();
		$categories[] = $cat = new Category('New category');
		$cat->setPosition(2);
		$categories[] = $cat2 = new Category('New category 2');
		$cat2->setPosition(3);
		$this->em->persist($cat);
		$this->em->persist($cat2);
		$this->em->flush();
		$this->refresh($categories); //todo: update entities without refresh
		Assert::same(1, $categories[1]->getPosition());
		Assert::same(4, $categories[2]->getPosition());
		Assert::same(5, $categories[3]->getPosition());
		Assert::same(6, $categories[4]->getPosition());
		Assert::same(7, $categories[5]->getPosition());
		Assert::same(8, $categories[6]->getPosition());
		Assert::same(2, $categories[7]->getPosition());
		Assert::same(3, $categories[8]->getPosition());
	}


	public function testMoveUp()
	{
		$categories = $this->createCategories();
		$categories[5]->setPosition(2);
		$this->em->flush();
		$this->refresh($categories); //todo: update entities without refresh
		Assert::same(1, $categories[1]->getPosition());
		Assert::same(3, $categories[2]->getPosition());
		Assert::same(4, $categories[3]->getPosition());
		Assert::same(5, $categories[4]->getPosition());
		Assert::same(2, $categories[5]->getPosition());
		Assert::same(6, $categories[6]->getPosition());
	}


	public function testMoveDown()
	{
		$categories = $this->createCategories();
		$categories[2]->setPosition(5);
		$this->em->flush();
		$this->refresh($categories); //todo: update entities without refresh
		Assert::same(1, $categories[1]->getPosition());
		Assert::same(5, $categories[2]->getPosition());
		Assert::same(2, $categories[3]->getPosition());
		Assert::same(3, $categories[4]->getPosition());
		Assert::same(4, $categories[5]->getPosition());
		Assert::same(6, $categories[6]->getPosition());
	}


	public function testMoveMultipleSamePosition()
	{
		$categories = $this->createCategories();
		$categories[5]->setPosition(2);
		$categories[6]->setPosition(2);
		$this->em->flush();
		$this->refresh($categories); //todo: update entities without refresh
		Assert::same(1, $categories[1]->getPosition());
		Assert::same(4, $categories[2]->getPosition());
		Assert::same(5, $categories[3]->getPosition());
		Assert::same(6, $categories[4]->getPosition());
		Assert::same(3, $categories[5]->getPosition());
		Assert::same(2, $categories[6]->getPosition());
	}

	public function testRemove()
	{
		$categories = $this->createCategories();
		$this->em->remove($categories[3]);
		$this->em->flush();
		unset($categories[3]);
		$this->refresh($categories); //todo: update entities without refresh
		Assert::same(1, $categories[1]->getPosition());
		Assert::same(2, $categories[2]->getPosition());
		Assert::same(3, $categories[4]->getPosition());
		Assert::same(4, $categories[5]->getPosition());
		Assert::same(5, $categories[6]->getPosition());
	}


	public function testInheritance()
	{
		$this->createCategories();
		$described = new DescribedCategory('Child');
		$this->em->persist($described);
		$this->em->flush();
		$this->refresh([$described]);
		Assert::same(7, $described->getPosition());
	}


	public function testInheritanceWithScope()
	{
		$this->createCategories();
		$described = new DescribedCategory('Child');
		$described->setSortableScope(['type']);
		$this->em->persist($described);
		$this->em->flush();
		$this->refresh([$described]);
		Assert::same(1, $described->getPosition());
	}


	/**
	 * @return Category[]
	 */
	private function createCategories()
	{
		$categories = [];
		for ($i = 1; $i <= 6; $i++) {
			$categories[$i] = $cat = new Category('Category ' . $i);
			$this->em->persist($cat);
		}
		$this->em->flush();

		return $categories;
	}


	private function refresh($categories)
	{
		foreach ((array) $categories as $cat) {
			$this->em->refresh($cat);
		}
	}
}


run(new SortableTestCase());
