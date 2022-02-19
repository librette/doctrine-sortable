<?php
namespace Librette\Doctrine\Sortable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Nette\SmartObject;

/**
 * @author David Matejka
 */
class SortableListener implements EventSubscriber
{
    use SmartObject;

	/** @var \ReflectionProperty[] */
	private $reflCache = [];



	public function getSubscribedEvents()
	{
		return [Events::preUpdate, Events::postPersist, Events::preRemove];
	}


	/**
	 * @param PreUpdateEventArgs
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		if (!$args->getEntity() instanceof ISortable) {
			return;
		}
		if (!$args->hasChangedField('position')) {
			return;
		}
		$oldPos = (int) $args->getOldValue('position');
		$newPos = max(1, (int) $args->getNewValue('position'));
		$entity = $args->getEntity();
		$rp = $this->getPropReflection($entity);
		$em = $args->getEntityManager();
		$qb = $this->createBaseQb($em, $entity);

		$this->configureUpdateQb($qb, $newPos, $oldPos);

		try {
			$res = $qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException $e) {
			$res = 0;
		}
		if ($newPos > $oldPos) {
			if ($res === 0) { //entity was last already, keep old position
				$newPos = $oldPos;
			} else {
				$newPos = min($newPos, $this->getMaxPosition($em, $entity));
			}
		}
		$rp->setValue($entity, $newPos);
		$uow = $em->getUnitOfWork();
		$meta = $em->getClassMetadata(get_class($entity));
		$uow->recomputeSingleEntityChangeSet($meta, $entity);
		$uow->getEntityPersister(get_class($entity))->update($entity);
		$uow->setOriginalEntityProperty(spl_object_hash($entity), 'position', $newPos);
		//$uow->scheduleExtraUpdate($entity, $uow->getEntityChangeSet($entity));
	}


	/**
	 * @param QueryBuilder
	 * @param int
	 * @param int
	 */
	private function configureUpdateQb(QueryBuilder $qb, $newPos, $oldPos)
	{
		$qb->update();
		if ($newPos < $oldPos) {
			$shiftFrom = $newPos;
			$shiftTo = $oldPos - 1;
			$sign = '+';
		} else {
			$shiftFrom = $oldPos + 1;
			$shiftTo = $newPos;
			$sign = '-';
		}
		$qb->andWhere('e.position >= :from');
		$qb->andWhere('e.position <= :to');
		$qb->setParameter('from', $shiftFrom);
		$qb->setParameter('to', $shiftTo);
		$qb->set('e.position', "e.position {$sign} 1");
	}


	/**
	 * @param LifecycleEventArgs
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		if (!$args->getEntity() instanceof ISortable) {
			return;
		}
		/** @var ISortable $entity */
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		$maxPos = $this->getMaxPosition($em, $entity);

		$meta = $em->getClassMetadata(get_class($entity));
		if (!$entity->getPosition() || $entity->getPosition() > $maxPos) {
			$rp = $this->getPropReflection($entity);
			$rp->setValue($entity, $maxPos);
			$uow = $em->getUnitOfWork();
			$uow->recomputeSingleEntityChangeSet($meta, $entity);
			//todo: use extraUpdates
			$uow->getEntityPersister(get_class($entity))->update($entity);
			$uow->setOriginalEntityProperty(spl_object_hash($entity), 'position', $maxPos);
		} else {
			$pos = $entity->getPosition();
			$qb = $this->createBaseQb($args->getEntityManager(), $entity);
			$qb->update()
				->andWhere('e.position >= :from')
				->setParameter('from', $pos)
				->andWhere('e.id <> :id')
				->setParameter('id', $entity->getId())
				->set('e.position', 'e.position + 1')
				->getQuery()->getResult();
		}
	}


	/**
	 * @param LifecycleEventArgs
	 */
	public function preRemove(LifecycleEventArgs $args)
	{
		/** @var ISortable $entity */
		$entity = $args->getEntity();
		if (!$entity instanceof ISortable) {
			return;
		}
		$qb = $this->createBaseQb($args->getEntityManager(), $entity);
		$qb->update();
		$qb->andWhere('e.position > :from')
			->setParameter('from', $entity->getPosition());
		$qb->set('e.position', 'e.position - 1');
		$qb->getQuery()->execute();
	}


	/**
	 * @param EntityManager
	 * @param ISortable
	 * @return QueryBuilder
	 */
	private function createBaseQb(EntityManager $em, ISortable $sortable)
	{
		$meta = $em->getClassMetadata(get_class($sortable));

		$qb = $em->getRepository($meta->rootEntityName)->createQueryBuilder('e');
		if ($sortable instanceof ISortableScope) {
			$this->addScope($em, $sortable, $qb);
		}

		return $qb;
	}


	/**
	 * @param ISortable
	 * @return \ReflectionProperty
	 */
	private function getPropReflection(ISortable $sortable)
	{
		$cls = get_class($sortable);
		if (isset($this->reflCache[$cls])) {
			return $this->reflCache[$cls];
		}
		$rc = new \ReflectionClass($sortable);
		$rp = $rc->getProperty('position');
		$rp->setAccessible(TRUE);
		$this->reflCache[$cls] = $rp;

		return $rp;
	}


	/**
	 * @param EntityManager
	 * @param ISortable
	 * @return int
	 */
	private function getMaxPosition(EntityManager $em, ISortable $entity)
	{
		$qb = $this->createBaseQb($em, $entity);
		$qb->select('MAX(e.position)');
		try {
			return $qb->getQuery()->getSingleScalarResult() + 1;
		} catch (NoResultException $e) {
			return 1;
		}
	}


	/**
	 * @param EntityManager
	 * @param ISortableScope
	 * @param QueryBuilder
	 */
	private function addScope(EntityManager $em, ISortableScope $sortable, QueryBuilder $qb)
	{
		$meta = $em->getClassMetadata(get_class($sortable));
		$rc = new \ReflectionClass($sortable);
		foreach ($sortable->getSortableScope() as $field) {
			if ($meta->hasField($field) || $meta->hasAssociation($field)) {
				$rp = $rc->getProperty($field);
				$rp->setAccessible(TRUE);
				$qb->andWhere($qb->expr()->eq('e.' . $field, ':p_' . $field));
				$qb->setParameter('p_' . $field, $rp->getValue($sortable));
			} elseif ($meta->discriminatorColumn['name'] === $field) {
				if (($type = array_search(get_class($sortable), $meta->discriminatorMap)) === FALSE) {
					$type = get_class($sortable);
				}
				$qb->andWhere('e INSTANCE OF :discr_type')
					->setParameter('discr_type', $type);
			} else {
				throw new InvalidScopeException("Scope field $field is neither field, association nor discriminator");
			}
		}
	}

}
