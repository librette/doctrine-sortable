<?php
namespace LibretteTests\Doctrine\Sortable;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Librette\Doctrine\Sortable\SortableListener;

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
		$connection = new Connection($conf, new \Doctrine\DBAL\Driver\PDO\SQLite\Driver());
		$config = new Configuration();
		$cache = new \Symfony\Component\Cache\Adapter\ArrayAdapter();
		$config->setMetadataCache($cache);
		$config->setQueryCache($cache);
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

