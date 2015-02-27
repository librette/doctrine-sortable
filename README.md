# doctrine-sortable

Sooner or later you will have to implement sorting of your entities.
For example categories, products on main page and so on. And why you should do this by yourself when everything you have to do is to copy & paste?


Installation
-----------

The best way to install Kdyby/Doctrine is using [Composer](http://getcomposer.org/):

```sh
$ composer require librette/doctrine-sortable
```

and enable librette extension in your `config.neon
````yml
extensions:
	# add this line at the end of your extensions list
	librette.doctrine.sortable: Librette\Doctrine\Sortable\DI;
```

Simplest entity
---------------


```php
namespace App;

use Kdyby\Doctrine\Entities\BaseEntity;
use Librette\Doctrine\Sortable\ISortable;
use Librette\Doctrine\Sortable\TSortable;

/**
 * @ORM\Entity
 */
class Article extends BaseEntity implements ISortable
{

	use TSortable;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $position;

	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

}
```

Trait TSortable
---------------

There is trait `TSortable` that implements basic sorting methods to your entity.
Everything you need is to call those methods in your service / presenter.

```php
$entity->moveUp();
$entity->moveDown();
$entity->moveAfter();
$entity->moveBefore();
```
