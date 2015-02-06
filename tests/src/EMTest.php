<?php
namespace LibretteTests\Doctrine\Sortable;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\ORM\Tools\SchemaTool;
use Kdyby;
use Librette\Doctrine\Sortable\SortableListener;
use Nette;
use Tester;

/**
 * @author David Matejka
 */
trait EMTest
{

	/**
	 * @return Kdyby\Doctrine\EntityManager
	 */
	protected function createMemoryManager($classNames = NULL, $createSchema = TRUE)
	{
		$conf = [
			'driver' => 'pdo_sqlite',
			'memory' => TRUE,
		];
		$connection = new Kdyby\Doctrine\Connection($conf, new Driver());
		$config = new Kdyby\Doctrine\Configuration();
		$cache = new ArrayCache();
		$config->setMetadataCacheImpl($cache);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir(TEMP_DIR);
		$config->setProxyNamespace('TestProxy');
		$config->setDefaultRepositoryClassName('Kdyby\Doctrine\EntityRepository');
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver([__DIR__ . '/Model/', VENDOR_DIR], FALSE));
		$em = Kdyby\Doctrine\EntityManager::create($connection, $config);
		$em->getEventManager()->addEventSubscriber(new SortableListener());
		if ($createSchema === FALSE) {
			return $em;
		}
		$schemaTool = new SchemaTool($em);
		if ($classNames !== NULL) {
			$meta = [];
			foreach ((array) $classNames as $className) {
				$meta[] = $em->getClassMetadata($className);
			}
		} else {
			$meta = $em->getMetadataFactory()->getAllMetadata();
		}
		$schemaTool->createSchema($meta);

		return $em;

	}

}

