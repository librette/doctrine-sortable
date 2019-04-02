<?php
namespace LibretteTests\Doctrine\Sortable;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
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

	protected function createMemoryManager($classNames = NULL, $createSchema = TRUE): EntityManager
	{
		$conf = [
			'driver' => 'pdo_sqlite',
			'memory' => TRUE,
		];
		$connection = new Connection($conf, new Driver());
		$config = new Configuration();
		$cache = new ArrayCache();
		$config->setMetadataCacheImpl($cache);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir(TEMP_DIR);
		$config->setProxyNamespace('TestProxy');
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver([__DIR__ . '/Model/', VENDOR_DIR], FALSE));
		$em = EntityManager::create($connection, $config);
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

